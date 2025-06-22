<?php
namespace reviews;

class ReviewController {
    private ReviewService $reviewGateWay;

    public function __construct(ReviewService $reviewGateWay) {
        $this->reviewGateWay = $reviewGateWay;
    }

    
    public function processRequestReviews(string $method, ?string $id){
        if ($id) {
           $this->processResourceRequestReview($method, $id);
        } else {
            echo json_encode(['error'=> '404 not url found reviews']);
        }
    }

    private function processResourceRequestReview(string $method, ?string $id){
        switch ($method) {
            case "POST":
                if ($id === "comment") {
                    $this->commentReview();
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;
            
            case "PUT": 
                if ($id === "comment") {
                    $this->updateRiviewByUserProd();
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;
                
            case "DELETE":
                $id = $_GET['id'] ?? null;
                $user_id = $_GET['user_id'] ?? null;
                if ($id && $user_id) {
                    $this->deleteComment($id, $user_id);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Invalid auth route"]);
                }
                break;

            case "GET":
                $product_id = $_GET['product_id'] ?? null;
                if ($product_id) {
                    $product_id_int = (int)$product_id;
                    $this->listReviewByProductId($product_id_int);

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

    }


    public function listReviewByProductId($product_id) {
        $reviews = $this->reviewGateWay->getAllReviewsByProduct($product_id);
        echo json_encode($reviews);
    }

    public function commentReview() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $id = $this->reviewGateWay->createReviewByUserProd(data: $data);
        http_response_code(response_code: 201);       
        echo json_encode(["message"=>"$id is added comment"]);
    }

    public function updateRiviewByUserProd() {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $reviewUpdated= $this->reviewGateWay->updateRiviewByUserProd(data: $data);
        http_response_code(response_code: 200);       
        echo json_encode($reviewUpdated);
    }

    public function deleteComment($id, $user_id) {
        $success = $this->reviewGateWay->delete($id, $user_id);

        if ($success == 0) {
            echo json_encode(value: ['message' => " delete comment failed "]);
            return;
        }

        echo json_encode(value: ['message' => "delete success fully $id and $user_id"]);
    }
}
