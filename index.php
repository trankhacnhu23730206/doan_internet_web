<?php

declare(strict_types= 1);

use products\ProductController;
use products\ProductGateway;
use auth\AuthGateway;

spl_autoload_register(function ($class): void {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$id = $parts[2] ?? null;

$database = new Database("localhost", "product_db", "root", "Trankhacnhu132!");

// ROUTING
switch ($parts[1]) {
    case "products":
        $productGateway = new ProductGateway($database);
        $productController = new ProductController($productGateway);
        $productController->processRequest($_SERVER["REQUEST_METHOD"], $id);
        break;

    case "auth":
        require_once __DIR__ . "/src/auth/AuthController.php";
        $gatewayAuth = new AuthGateway($database);
        $authController = new AuthController(authGateway: $gatewayAuth);

        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST":
                if ($id === "login") {
                    $authController->login();
                } elseif ($id === "register") {
                    $authController->register();
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;

            case "GET":
                echo json_encode(value: ["message"=> "get authen to check"]);
                break;

            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed"]);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Not found"]);
        break;
}