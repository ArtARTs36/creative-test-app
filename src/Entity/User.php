<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="users", indexes={@Index(columns={"name"})})
 */
final class User
{
    public const FIELD_NAME = 'name';
    public const FIELD_PASSWORD = 'password';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = 1;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private $password;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setPassword(string $password): self
    {
        $this->password = md5($password);

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
