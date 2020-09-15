<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contracts\Controller;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Twig\Environment;

/**
 * Class HomeController.
 */
final class HomeController extends Controller
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * HomeController constructor.
     *
     * @param Environment             $twig
     * @param EntityManagerInterface  $em
     */
    public function __construct(Environment $twig, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->em = $em;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     *
     * @throws HttpBadRequestException
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('home/index.html.twig', [
                'trailers' => $this->em->getRepository(Movie::class)->findAll(),
                'currentTime' => new \DateTime(),
                'controllerClass' => __CLASS__,
                'controllerMethod' => __METHOD__,
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        return $this->response($response, $data);
    }
}
