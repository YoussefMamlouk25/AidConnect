<?php
require_once __DIR__ . '/../config.php';

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Config::getConnexion();
    }

    public function signup($name, $email, $password, $cin, $date_naissance, $tel, $region)
    {
        // Vérifier si email existe déjà
        $check = $this->db->prepare("SELECT id_user FROM user WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            return ['success' => false, 'error' => 'Email déjà utilisé'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO user (name, email, password_hash, cin, date_naissance, tel, region, date_creation, role)
            VALUES (:name, :email, :hash, :cin, :date_naissance, :tel, :region, CURDATE(), 'volunteer')
        ");

        $stmt->execute([
            ':name'           => $name,
            ':email'          => $email,
            ':hash'           => $hash,
            ':cin'            => $cin ?: null,
            ':date_naissance' => $date_naissance ?: null,
            ':tel'            => $tel ?: null,
            ':region'         => $region ?: null
        ]);

        return ['success' => true];
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Email introuvable'];
        }

        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Mot de passe incorrect'];
        }

        return ['success' => true, 'user' => $user];
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id_user = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $name, $email, $tel, $region)
    {
        $stmt = $this->db->prepare("
            UPDATE user
            SET name = :name,
                email = :email,
                tel = :tel,
                region = :region
            WHERE id_user = :id
        ");

        return $stmt->execute([
            ':id'     => $id,
            ':name'   => $name,
            ':email'  => $email,
            ':tel'    => $tel ?: null,
            ':region' => $region ?: null
        ]);
    }

    public function changePassword($id, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            UPDATE user
            SET password_hash = :hash
            WHERE id_user = :id
        ");

        return $stmt->execute([
            ':id'   => $id,
            ':hash' => $hash
        ]);
    }
}
