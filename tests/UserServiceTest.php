<?php

namespace Tests;

use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;

final class UserServiceTest extends TestCase
{
    /**
     * @covers \App\Services\UserService::create
     */
    public function testCreate(): void
    {
        $name = 'Artem';
        $password = '123456';

        $user = $this->getService()->create($name, $password);

        self::assertEquals($name, $user->getName());
        self::assertEquals(md5($password), $user->getPassword());
    }

    private function getService(): UserService
    {
        return $this->getContainer()->get(UserService::class);
    }
}
