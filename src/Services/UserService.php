<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class UserService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(string $name, string $password): User
    {
        $user = new User();

        $user->setName($name);
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function findByCredentials(string $login, string $password): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy([
            User::FIELD_NAME => $login,
            User::FIELD_PASSWORD => md5($password),
        ]);
    }
}
