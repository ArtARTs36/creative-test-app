<?php

namespace App\Support;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;

final class Validator
{
    public static function handle(ServerRequestInterface $request, array $rules): array
    {
        $data = $request->getParsedBody();
        $newData = [];

        foreach ($rules as $key => $type) {
            if (!isset($data[$key])) {
                throw new HttpException($request);
            }

            $newData[$key] = static::prepareValue($data[$key], $type);
        }

        return $newData;
    }

    private static function prepareValue($value, string $type)
    {
        if ($type === 'int') {
            return (int) $value;
        }

        if ($type === 'string') {
            return (string) $value;
        }

        if (in_array($type, ['float', 'double'])) {
            return (float) $value;
        }

        return (string) $value;
    }
}
