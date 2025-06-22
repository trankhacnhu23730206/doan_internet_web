<?php

use auth\AuthGateway;

require_once 'config.php';
require_once 'JWT.php';

class AuthController {
    private AuthGateway $authGateway;

    public function __construct(AuthGateway $authGateway) {
        $this->authGateway = $authGateway;
    }

    public function login() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $user = $this->authGateway->loginAdmin($data["email"]);
        $token = JWT::encode([
            "id" => $user['id'],
            "email" => $user['email'],
            "exp" => time() + 3600
        ], JWT_SECRET);

        echo json_encode(["accessToken" => $token]);
    }

    public function register() {
        $data = (array) json_decode(file_get_contents("php://input"), true);


        $id = $this->authGateway->createAdmin(data: $data);


        http_response_code(201);       
        echo json_encode(["message" => "Registered successfully $id"]);
    }

    public function me() {
        $user = authMiddleware();
        echo json_encode(["user" => $user]);
    }

    public function logout() {
        echo json_encode(["message" => "Logged out (just delete token client-side)"]);
    }
}
