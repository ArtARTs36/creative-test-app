<?php

namespace App\Controller;

use App\Auth\Auth;
use App\Contracts\Controller;
use App\Services\UserService;
use App\Support\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

final class UserController extends Controller
{
    private $service;

    public function __construct(UserService $service, Environment $twig, Auth $auth)
    {
        $this->service = $service;

        parent::__construct($auth, $twig);
    }

    /**
     * Показать форму регистрации
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function showRegisterForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->response($response, $this->render('users/register.html.twig'));
    }

    /**
     * Показать форму авторизации
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function showLoginForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->response($response, $this->render('users/login.html.twig'));
    }

    /**
     * Создать пользователя
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Slim\Exception\HttpException
     */
    public function store(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = Validator::handle($request, [
            'login' => 'string',
            'password' => 'string',
        ]);

        try {
            $this->auth->login($this->service->create(...array_values($data)));
        } catch (\Exception $exception) {
            return $this->warning($response, 'Вероятно, пользователь с таким логином уже существует');
        }

        return $this->redirect($response, '/');
    }

    /**
     * Выход пользователя из системы
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->auth->logout();

        return $this->redirect($response, '/');
    }

    /**
     * Авторизация
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Slim\Exception\HttpException
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response)
    {
        $data = Validator::handle($request, [
            'login' => 'string',
            'password' => 'string',
        ]);

        $user = $this->service->findByCredentials(...array_values($data));

        if (null === $user) {
            return $this->warning($response, 'Мы не нашли пользователя с таким логином или паролем');
        }

        $this->auth->login($user);

        return $this->redirect($response, '/');
    }
}
