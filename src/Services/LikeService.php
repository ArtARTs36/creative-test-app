<?php

namespace App\Services;

use App\Entity\Like;
use App\Entity\Movie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class LikeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function isLiked(Movie $movie, User $user): bool
    {
        return $this->find($movie, $user) !== null;
    }

    public function dislike(Movie $movie, User $user): void
    {
        $like = $this->find($movie, $user);

        if (null === $like) {
            return;
        }

        $this->em->remove($like);
        $this->em->flush($like);
    }

    public function perform(Movie $movie, User $user): Like
    {
        if ($like = $this->find($movie, $user)) {
            return $like;
        }

        $this->em->persist($like = $this->make($movie, $user));
        $this->em->flush($like);

        return $like;
    }

    private function find(Movie $movie, User $user): ?Like
    {
        return $this->em->getRepository(Like::class)->findOneBy([
            Like::FIELD_MOVIE => $movie->getId(),
            Like::FIELD_USER => $user->getId(),
        ]);
    }

    private function make(Movie $movie, User $user): Like
    {
        return (new Like())
            ->setMovie($movie)
            ->setUser($user);
    }
}
