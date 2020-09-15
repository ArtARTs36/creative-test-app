<?php

namespace App\Contracts;

use App\Auth\Auth;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;

abstract class Controller
{
    protected $auth;

    protected $twig;

    public function __construct(Auth $auth, Environment $twig)
    {
        $this->auth = $auth;
        $this->twig = $twig;
    }

    protected function render(string $path, array $context = []): string
    {
        return $this->twig->render($path, array_merge(
            $context,
            [
                'user' => $this->auth->user(),
            ]
        ));
    }

    protected function response(ResponseInterface $response, string $data): ResponseInterface
    {
        $response->getBody()->write($data);

        return $response;
    }

    protected function redirect(ResponseInterface $response, string $url): ResponseInterface
    {
        return $response->withHeader('Location', $url);
    }

    protected function warning(ResponseInterface $response, string $text)
    {
        $response->getBody()->write($this->render('alert.html.twig', [
            'text' => $text,
        ]));

        return $response;
    }
}
