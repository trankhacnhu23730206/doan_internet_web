<?php
namespace reviews;

class ReviewController {
    private $reviewGateWay;

    public function __construct(ReviewGateWay $reviewGateWay) {
        $this->reviewGateWay = $reviewGateWay;
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
