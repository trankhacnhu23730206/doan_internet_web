<?php
namespace auth;
use Database;
use PDO;
class AuthGateway {
    private PDO $conn;

    public function __construct(Database $database) {
        $this->conn = $database->getConnection();

    }
    public function loginAdmin($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare(query: $sql);

        $stmt->bindValue(":email", $email,PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;

    }

    public function createAdmin (array $data) {
        $sql = "INSERT INTO users (email, password) 
        VALUES (:email, :password)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"], PDO::PARAM_STR);
        $stmt->execute();

        return $this->conn->lastInsertId(); 


    }
    
    public function updateAdmin($current,$new)
    {
      $sql = "UPDATE users
                SET username = :username, password = :password
                WHERE email = :email";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":username", $new["username"] ?? $current["username"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $new["password"] ?? $current["password"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    public function getAdminByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare(query: $sql);

        $stmt->bindValue(":email", $email,PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;

    }

   
    

    public function deleteAdmin(string $id)
    {


    }


}