<?php
namespace auth;


require_once 'config.php';
require_once 'JWT.php';

class AuthController {
    private AuthService $authGateway;

    public function __construct(AuthService $authGateway) {
        $this->authGateway = $authGateway;
    }

    public function processRequestAuth(string $method, ?string $id){
        if ($id) {
           $this->processResourceRequestAuth($method, $id);
        } else {
            echo json_encode(['error'=> '404 not url found product page']);
        }
    }


    private function processResourceRequestAuth(string $method, ?string $id){
        switch($method) {
            case "POST":                                
                if ($id === "login") {
                    $this->login();
                } elseif ($id === "register") {
                    $this->register();
                } else {
                    http_response_code(response_code: 404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;

            case "PUT":
                if ($id === "update-infor") {
                    $this->updateInfor();
                } else {
                    http_response_code(response_code: 404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;
;

           default:
                http_response_code(response_code: 405);
                header("Allow: GET, PATCH, DELETE");    
        }
    }

    private function login() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $user = $this->authGateway->loginAdmin($data["email"]);
        $token = JWT::encode([
            "id" => $user['id'],
            "email" => $user['email'],
            "exp" => time() + 3600
        ], JWT_SECRET);

        echo json_encode(["accessToken" => $token]);
    }

    private function register() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $id = $this->authGateway->createAdmin(data: $data);
        http_response_code(201);       
        echo json_encode(["message" => "Registered successfully $id"]);
    }


    private function updateInfor() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $currentInfor = $this->authGateway->getAdminByEmail($data["email"]);
        $id = $this->authGateway->updateAdmin($currentInfor, $data);
        http_response_code(201);       
        echo json_encode(["message" => "Update Infor successfully $id"]);
    }

    private function me() {
        $user = authMiddleware();
        echo json_encode(["user" => $user]);
    }

    private function logout() {
        echo json_encode(["message" => "Logged out (just delete token client-side)"]);
    }
}
