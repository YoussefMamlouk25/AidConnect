<?php

class UserModel {

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // SIGN UP
    public function signup($name, $email, $cin, $date_naissance, $password, $tel, $region)
    {
        // Check if email exists
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'error' => 'Email déjà utilisé'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));

        // Insert new user
        $stmt = $this->conn->prepare("
            INSERT INTO user (name, email, cin, date_naissance, password, tel, region, verified, verify_token)
            VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)
        ");
        $stmt->execute([$name, $email, $cin, $date_naissance, $hashed, $tel, $region, $token]);

        // Send email
        require_once '../Controller/send_mail.php';
        sendVerificationEmail($email, $name, $token);

        return ['success' => true];
    }

    // LOGIN
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() == 0) {
            return ['success' => false, 'error' => 'Email incorrect'];
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['verified'] == 0) {
            return ['success' => false, 'error' => 'Veuillez vérifier votre email avant de vous connecter.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Mot de passe incorrect'];
        }

        return ['success' => true, 'user' => $user];
    }
}
