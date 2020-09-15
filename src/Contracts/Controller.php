<?php

namespace App\Contracts;

use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    protected function response(ResponseInterface $response, string $data): ResponseInterface
    {
        $response->getBody()->write($data);

        return $response;
    }
}
