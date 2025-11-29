<?php

class UserModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // SIGNUP
    public function signup($name, $email, $cin, $date_naissance, $password, $tel, $region) {

        // Check email exists
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Email déjà utilisé.'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO user 
                (name, email, password_hash, cin, date_naissance, tel, region, role, date_creation)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'volunteer', NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name, $email, $hash, $cin, $date_naissance, $tel, $region]);

        return ['success' => true];
    }

    // LOGIN
    public function login($email, $password) {
        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 0) {
            return ['success' => false, 'error' => 'Email incorrect.'];
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Mot de passe incorrect.'];
        }

        return ['success' => true, 'user' => $user];
    }
}
