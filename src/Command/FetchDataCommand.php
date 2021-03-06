<?php
/**
 * 2019-06-28.
 */

declare(strict_types=1);

namespace App\Command;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class FetchDataCommand.
 */
final class FetchDataCommand extends Command
{
    private const SOURCE = 'https://trailers.apple.com/trailers/home/rss/newtrailers.rss';

    /**
     * @var string
     */
    protected static $defaultName = 'fetch:trailers';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $doctrine;

    /**
     * FetchDataCommand constructor.
     *
     * @param ClientInterface        $http
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $em
     * @param string|null            $name
     */
    public function __construct(
        ClientInterface $http,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        string $name = null
    ) {
        parent::__construct($name);
        $this->httpClient = $http;
        $this->logger = $logger;
        $this->doctrine = $em;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Fetch data from iTunes Movie Trailers')
            ->addArgument('source', InputArgument::OPTIONAL, 'Overwrite source');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info(sprintf('Start %s at %s', __CLASS__, (string) date_create()->format(DATE_ATOM)));
        $source = $this->getSourceOfInput($input) ?? static::SOURCE;

        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('Fetch data from %s', $source));

        $this->processXml($this->getRawXml($source), 10);

        $this->logger->info(sprintf('End %s at %s', __CLASS__, (string) date_create()->format(DATE_ATOM)));

        return 0;
    }

    private function getSourceOfInput(InputInterface $input): ?string
    {
        if ($source = $input->getArgument('source')) {
            if (!is_string($source)) {
                throw new RuntimeException('Source must be string');
            }

            return $source;
        }

        return null;
    }

    private function getRawXml(string $source): string
    {
        try {
            $response = $this->httpClient->sendRequest(new Request('GET', $source));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (($status = $response->getStatusCode()) !== 200) {
            throw new RuntimeException(sprintf('Response status is %d, expected %d', $status, 200));
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param string $data
     *
     * @param int $maxCount
     * @throws \Exception
     */
    private function processXml(string $data, int $maxCount): void
    {
        $xml = (new \SimpleXMLElement($data))->children();
        $namespace = $xml->getNamespaces(true)['content'];

        if (!property_exists($xml, 'channel')) {
            throw new RuntimeException('Could not find \'channel\' element in feed');
        }

        $counter = 0;

        foreach ($xml->channel->item as $item) {
            $this->doctrine->persist($this->prepareMovie($item, $namespace));

            if (++$counter === $maxCount) {
                break;
            }
        }

        $this->doctrine->flush();
    }

    private function prepareMovie(\SimpleXMLElement $item, string $namespace): Movie
    {
        return $this->getMovie((string) $item->title)
            ->setTitle((string) $item->title)
            ->setDescription((string) $item->description)
            ->setLink((string) $item->link)
            ->setPubDate($this->parseDate((string) $item->pubDate))
            ->setImage($this->selectImage($item, $namespace));
    }

    private function selectImage(\SimpleXMLElement $item, string $namespace): ?string
    {
        $html = (string) $item->children($namespace)->encoded[0];

        $matches = [];

        preg_match_all("/<img[^>]*?src=[\"\']?([^\"\'\s>]+)[\"\']?[^>]*?>/i", $html, $matches);

        return count($matches) < 2 ? null : $matches[1][0];
    }

    /**
     * @param string $date
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    protected function parseDate(string $date): \DateTime
    {
        return new \DateTime($date);
    }

    /**
     * @param string $title
     *
     * @return Movie
     */
    protected function getMovie(string $title): Movie
    {
        $item = $this->doctrine->getRepository(Movie::class)->findOneBy(['title' => $title]);

        if ($item === null) {
            $this->logger->info('Create new Movie', ['title' => $title]);
            $item = new Movie();
        } else {
            $this->logger->info('Move found', ['title' => $title]);
        }

        return $item;
    }
}
