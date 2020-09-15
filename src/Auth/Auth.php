<?php

namespace App\Auth;

use App\Contracts\AuthDriver;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

final class Auth
{
    private $driver;

    private $em;

    public function __construct(AuthDriver $driver, EntityManagerInterface $em)
    {
        $this->driver = $driver;
        $this->em = $em;
    }

    public function user(): ?User
    {
        $id = $this->driver->getUserId();

        if (null === $id) {
            return null;
        }

        return $this->getRepo()->find($id);
    }

    public function logout(): self
    {
        $this->driver->logout();

        return $this;
    }

    public function login(User $user): self
    {
        $this->driver->login($user->getId());

        return $this;
    }

    private function getRepo(): ObjectRepository
    {
        return $this->em->getRepository(User::class);
    }
}
