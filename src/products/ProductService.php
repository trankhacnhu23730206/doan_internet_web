<?php
namespace products;

use Database;
use PDO;

class ProductService {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();

    }

    public function getAll() {
        $sql = "SELECT * from product";
        $stmt = $this->conn->query($sql);   

        $data = [];

        while ($row = $stmt->fetch((PDO::FETCH_ASSOC))) {

            $row["is_available"] = (bool)$row["is_available"];

            $data[] = $row;
        }

        return $data;
    }

    public function create (array $data): string {
        $sql = "INSERT INTO product(name, size, is_available)
                VALUES (:name, :size, :is_available)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $data["size"] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":is_available", (bool) $data["is_available"] ?? false, PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId(); 

    }

    public function get(string $id) {
        $sql = "SELECT * FROM product WHERE id = :id";

        $stmt = $this->conn->prepare(query: $sql);
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }


    public function getProductByName($product_name) {
        $sql = "SELECT * FROM product WHERE name = :product_name";

        $stmt = $this->conn->prepare(query: $sql);
        $stmt->bindValue("product_name", $product_name, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

     public function update(array $current, array $new): int
    {
        $sql = "UPDATE product
                SET name = :name, size = :size, is_available = :is_available
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $stmt->bindValue(":size", $new["size"] ?? $current["size"], PDO::PARAM_INT);
        $stmt->bindValue(":is_available", $new["is_available"] ?? $current["is_available"], PDO::PARAM_BOOL);
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    

    public function delete(string $id)
    {
        $sql = "DELETE FROM product
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }


}