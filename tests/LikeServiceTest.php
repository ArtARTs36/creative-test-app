<?php

namespace Tests;

use App\Entity\Movie;
use App\Entity\User;
use App\Services\LikeService;

final class LikeServiceTest extends TestCase
{
    /**
     * @covers \App\Services\LikeService::perform
     */
    public function testPerform(): void
    {
        $movie = $this->createMovie();
        $user = $this->createUser();

        $like = $this->getService()->perform($movie, $user);

        self::assertEquals($movie->getId(), $like->getMovie()->getId());
        self::assertEquals($user->getId(), $like->getUser()->getId());

        self::assertTrue($this->getService()->isLiked($movie, $user));
    }

    private function getService(): LikeService
    {
        return $this->getContainer()->get(LikeService::class);
    }

    private function createMovie(): Movie
    {
        $this->saveEntity($movie = new Movie());

        return $movie;
    }

    private function createUser(): User
    {
        $user = (new User())
            ->setName('Artem_' . rand(1, 99))
            ->setPassword('1234');

        $this->saveEntity($user);

        return $user;
    }
}
