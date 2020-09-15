<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contracts\Controller;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Twig\Environment;

final class MovieController extends Controller
{
    /** @var MovieRepository */
    private $repository;

    /** @var Environment */
    private $twig;

    public function __construct(EntityManagerInterface $em, Environment $twig)
    {
        $this->repository = $em->getRepository(Movie::class);
        $this->twig = $twig;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $movie = $this->repository->find((int) $request->getAttribute('id'));

        if (null === $movie) {
            throw new HttpNotFoundException($request);
        }

        return $this->response($response, $this->twig->render('movies/show.html.twig', [
            'movie' => $movie,
        ]));
    }
}
