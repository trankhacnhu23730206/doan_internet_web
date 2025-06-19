<?php
require_once 'JWT.php';

class AuthController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
            return;
        }

        $token = JWT::encode([
            "id" => $user['id'],
            "email" => $user['email'],
            "exp" => time() + 3600
        ], JWT_SECRET);

        echo json_encode(["accessToken" => $token]);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input"]);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["message" => "Email already exists"]);
            return;
        }

        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$data['email'], $hash]);

        echo json_encode(["message" => "Registered successfully"]);
    }

    public function me() {
        $user = authMiddleware();
        echo json_encode(["user" => $user]);
    }

    public function logout() {
        echo json_encode(["message" => "Logged out (just delete token client-side)"]);
    }
}
