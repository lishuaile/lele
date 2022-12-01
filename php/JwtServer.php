<?php

namespace App\server;

use Firebase\JWT\Key;
class JwtServer
{
    public function EncodeToken($data){
        $key = 'example_key';
        $payload = [
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
            'iat' => 1356999524,
            'nbf' => 1357000000
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
    public function DecodeToekn(){
        $key = 'example_key';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return $decoded;
    }
}
