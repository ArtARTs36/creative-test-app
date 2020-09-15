<?php

namespace Tests;

use App\Services\UserService;

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

    /**
     * @covers \App\Services\UserService::findByCredentials
     */
    public function testFindByCredentials(): void
    {
        // 1. БД пустая, юзера нет

        self::assertNull($this->getService()->findByCredentials('random', 'random'));

        // 2. Создали юзера, попробовали найти с некорректным паролем

        $user = $this->getService()->create($login = 'User', $password = 55);

        self::assertNull($this->getService()->findByCredentials($login, 66));

        // 3. Пробуем найти с корректными логином и паролем

        self::assertNotEmpty($findUser = $this->getService()->findByCredentials($login, $password));
        self::assertEquals($user->getName(), $findUser->getName());
    }

    private function getService(): UserService
    {
        return $this->getContainer()->get(UserService::class);
    }
}
