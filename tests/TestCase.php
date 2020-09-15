<?php

namespace Tests;

use UltraLite\Container\Container;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private $container = null;

    final protected function getContainer(): Container
    {
        if (null === $this->container) {
            $this->container = include __DIR__ . '/../bootstrap.php';
        }

        return $this->container;
    }
}
