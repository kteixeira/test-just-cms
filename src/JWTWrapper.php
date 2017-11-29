<?php

namespace TestJustCms;
use \Firebase\JWT\JWT;
use TestJustCms\Controllers\ApplicationController;

/**
 * Gerenciamento de tokens JWT
 */
class JWTWrapper
{
    /**
     * Gerando um novo token JWT
     * @param array $options
     * @param $issuedAt
     * @param $expire
     * @param $key
     * @return string
     */
    public static function encode(array $options, $issuedAt, $expire, $key)
    {
        $tokenParam = [
            'iat'  => $issuedAt,
            'iss'  => $options['iss'],
            'exp'  => $expire,
            'nbf'  => $issuedAt - 1,
            'data' => $options['userdata'],
        ];

        return JWT::encode($tokenParam, $key);
    }

    /**
     * Decodificando token jwt
     * @param $jwt
     * @param $key
     * @return object
     */
    public static function decode($jwt, $key)
    {
        return JWT::decode($jwt, $key, ['HS256']);
    }

    /**
     * Autenticação para a busca do Token
     * @param $data
     */
    public static function getToken($data)
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600;

        $applicationController = new ApplicationController();
        $application = $applicationController->auth($data);

        if(is_null($application))
            return response(['error' => 'true',
                'message' => 'Login inválido'], 400);

        if($application->expire_token <= time())
            $application::update($application->id, ['key' => self::keyGenerator(), 'expire_token' => $expire]);

        $jwt = self::encode([
            'iss' => 'http://localhost',
            'userdata' => [
                'id'   => $application->id,
                'name' => $application->name,
                'key'  => $application->key
            ]
        ], $issuedAt, $application->expire_token, $application->key);

        return response(['error' => false, 'access_token' => $jwt], 200);
    }

    /**
     * Validação do Token inserido
     * @param $headers
     */
    public static function validateToken($headers)
    {
        if(!isset($headers['access_token']) || is_null($headers['access_token']))
        {
            return response(['error' => 'true', 'message' => 'Token não informado.'], 400);
        }

        $decoded = base64_decode($headers['access_token']);
        $secretKey = substr($decoded, strripos($decoded, '"key":') + 7, 12);

        try {
            self::decode($headers['access_token'], $secretKey);
        } catch (\Exception $e){
            return response(['error' => 'true', 'message' => 'Acesso não autorizado.'], 401);
        }
    }

    /**
     * Gerador de chaves randômicas
     * @return bool|string
     */
    private static function keyGenerator()
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(12/strlen($x)))), 1, 12);
    }
}

