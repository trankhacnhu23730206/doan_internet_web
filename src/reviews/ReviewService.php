<?php
namespace reviews;
use Database;
use PDO;


class ReviewService {
    private $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAllReviewsByProduct($product_id) {
        $sql = "SELECT r.id, r.user_id, u.email AS user_name, r.rating, r.comment, r.created_at FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id ORDER BY r.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":product_id", $product_id, type: PDO::PARAM_INT);
        $stmt->execute();

        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $reviews;
    }

    public function createReviewByUserProd($data) {
        $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) 
                                      VALUES (:product_id, :user_id, :rating, :comment)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":product_id", $data["product_id"], type: PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":rating", $data["rating"], PDO::PARAM_INT);
        $stmt->bindValue(":comment", $data["comment"], type: PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId(); 

    }

    public function delete($id, $user_id) {
        $sql = "DELETE FROM reviews WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function updateRiviewByUserProd($data) {
        $sql = "UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindValue(":rating", $data["rating"], PDO::PARAM_INT);
        $stmt->bindValue(":comment", $data["comment"], type: PDO::PARAM_STR);

        $stmt->execute();
        if ($stmt->rowCount() === 0) {
        return null;
        }

        return $stmt->rowCount();

    }
}
