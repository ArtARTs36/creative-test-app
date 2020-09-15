<?php

namespace App\Support;

final class Session
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function get(string $key)
    {
        if (!$this->exists($key)) {
            return null;
        }

        return $_SESSION[$key];
    }

    public function put(string $key, $value): self
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    public function exists(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): self
    {
        $_SESSION[$key] = null;

        return $this;
    }
}
