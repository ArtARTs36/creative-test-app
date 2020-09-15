<?php

namespace App\Contracts;

interface AuthDriver
{
    /**
     * @return int|null
     */
    public function getUserId(): ?int;

    /**
     * @param int $id
     * @return void
     */
    public function login(int $id): void;

    /**
     * Logout Current User
     */
    public function logout(): void;
}
