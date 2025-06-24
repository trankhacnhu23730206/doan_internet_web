<?php

declare(strict_types= 1);

use products\ProductController;
use products\ProductService;
use auth\AuthController;
use auth\AuthService;
use reviews\ReviewController;
use reviews\ReviewService;

spl_autoload_register(function ($class): void {
    require __DIR__ . "/src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");
header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
$id = $parts[2] ?? null;

// DATABASE
$database = new Database("localhost", "product_db", "root", "Trankhacnhu132!");

// ROUTING
switch ($parts[1]) {
    case "products":
        $productGateway = new ProductService($database);
        $productController = new ProductController($productGateway);
        $productController->processRequest($_SERVER["REQUEST_METHOD"], $id);
        break;

    case "auth":
        require_once __DIR__ . "/src/auth/AuthController.php";
        $gatewayAuth = new AuthService($database);
        $authController = new AuthController(authGateway: $gatewayAuth);
        $authController->processRequestAuth($_SERVER["REQUEST_METHOD"], $id);
        break;

    case "reviews":
        require_once __DIR__ . "/src/reviews/ReviewController.php";
        $reviewService = new ReviewService($database);
        $reviewController = new ReviewController($reviewService);
        $reviewController->processRequestReviews($_SERVER["REQUEST_METHOD"], $id);
        break;

    default:
        http_response_code(404);
        echo json_encode(["message" => "Not found"]);
        break;
}