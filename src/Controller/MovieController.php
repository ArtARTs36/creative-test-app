<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Auth;
use App\Contracts\Controller;
use App\Entity\Movie;
use App\Entity\User;
use App\Repository\MovieRepository;
use App\Services\LikeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

final class MovieController extends Controller
{
    /** @var MovieRepository */
    private $repository;

    /** @var LikeService */
    private $likeService;

    public function __construct(EntityManagerInterface $em, Environment $twig, Auth $auth, LikeService $likeService)
    {
        $this->repository = $em->getRepository(Movie::class);
        $this->likeService = $likeService;

        parent::__construct($auth, $twig);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $movie = $this->repository->find((int) $request->getAttribute('id'));

        if (null === $movie) {
            return $this->warning($response, 'Мы не смогли найти заправшиваемый трейлер');
        }

        return $this->response($response, $this->render('movies/show.html.twig', [
            'movie' => $movie,
            'isLiked' => $this->auth->guest() ? false : $this->likeService->isLiked($movie, $this->auth->user()),
        ]));
    }

    public function like(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->likeOperation($request, $response, function (Movie $movie, User $user) {
            $this->likeService->perform($movie, $user);
        });
    }

    public function dislike(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->likeOperation($request, $response, function (Movie $movie, User $user) {
            $this->likeService->dislike($movie, $user);
        });
    }

    private function likeOperation(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \Closure $closure
    ): ResponseInterface {
        if (! $request->getAttribute('id')) {
            return $this->warning($response, 'Мы не смогли найти заправшиваемый трейлер');
        }

        if ($this->auth->guest()) {
            return $this->warning($response, 'Для совершения данного действия Вам необходимо авторизоваться');
        }

        $movie = $this->repository->find((int) $request->getAttribute('id'));

        if (null === $movie) {
            return $this->warning($response, 'Мы не смогли найти запрашиваемый трейлер');
        }

        $closure($movie, $this->auth->user());

        return $this->redirect($response, '/movies/' . $movie->getId());
    }
}
