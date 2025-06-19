<?php
require_once 'JWT.php';
require_once 'config.php';

function authMiddleware() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Missing Authorization header"]);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $user = JWT::decode($token, JWT_SECRET);

    if (!$user || $user['exp'] < time()) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid or expired token"]);
        exit;
    }

    return $user;
}
