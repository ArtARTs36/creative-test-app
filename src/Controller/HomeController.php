<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Auth;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * HomeController constructor.
     *
     * @param Environment             $twig
     * @param EntityManagerInterface  $em
     */
    public function __construct(Environment $twig, EntityManagerInterface $em, Auth $auth)
    {
        $this->em = $em;

        parent::__construct($auth, $twig);
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
            $data = $this->render('home/index.html.twig', [
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
