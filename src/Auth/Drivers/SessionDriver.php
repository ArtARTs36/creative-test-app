<?php

namespace App\Auth\Drivers;

use App\Contracts\AuthDriver;
use App\Support\Session;

final class SessionDriver implements AuthDriver
{
    private const KEY_ID = '__user_id';

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function getUserId(): ?int
    {
        return $this->session->get(static::KEY_ID);
    }

    /**
     * @inheritDoc
     */
    public function login(int $id): void
    {
        $this->session->put(static::KEY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function logout(): void
    {
        $this->session->remove(static::KEY_ID);
    }
}
