<?php

namespace Tests;

use Doctrine\ORM\EntityManagerInterface;
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

    final protected function saveEntity(object $entity)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get(EntityManagerInterface::class);

        $em->persist($entity);
        $em->flush();
    }
}
