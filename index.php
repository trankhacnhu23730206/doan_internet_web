<?php

declare(strict_types= 1);

use products\ProductController;
use products\ProductGateway;
use auth\AuthGateway;
use auth\AuthController;
use reviews\ReviewController;
use reviews\ReviewGateWay;

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
                    http_response_code(response_code: 404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;

            case "PUT":
                $authController->updateInfor();
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

    case "reviews":
        $reviewGateWay = new ReviewGateWay($database);
        $reviewController = new ReviewController($reviewGateWay);
       switch ($_SERVER["REQUEST_METHOD"]) {

            case "POST":
                if ($id === "comment") {
                    $reviewController->commentReview();
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;
            
            case "PUT": 
                if ($id === "comment") {
                    $reviewController->updateRiviewByUserProd();
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;
                
            case "DELETE":
                $id = $_GET['id'] ?? null;
                $user_id = $_GET['user_id'] ?? null;
                if ($id && $user_id) {
                    $reviewController->deleteComment($id, $user_id);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;

            case "GET":
                $product_id = $_GET['product_id'] ?? null;
                if ($product_id) {
                    $product_id_int = (int)$product_id;
                    $reviewController->listReviewByProductId($product_id_int);

                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
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