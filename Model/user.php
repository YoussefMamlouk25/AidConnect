<?php
require_once __DIR__ . '/../config.php';

class UserModel
{
    private $db;
<<<<<<< HEAD
    private $passwordColumn;
    private $profileColumn;
    private $columnsCache = null;
=======
<<<<<<< HEAD
    private $passwordColumn;
    private $profileColumn;
    private $columnsCache = null;
=======
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1

    public function __construct()
    {
        $this->db = Config::getConnexion();
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
        $this->passwordColumn = $this->detectColumn(['password_hash', 'password'], true);
        $this->profileColumn  = $this->detectColumn(
            ['profile_pic', 'profile_picture', 'avatar', 'photo', 'picture', 'image'],
            false,
            '/profile|avatar|photo|picture|image/i'
        );
    }

    private function getUserColumns()
    {
        if ($this->columnsCache === null) {
            $stmt = $this->db->query("SHOW COLUMNS FROM user");
            $this->columnsCache = array_map(
                static fn($row) => $row['Field'] ?? '',
                $stmt->fetchAll()
            );
        }

        return $this->columnsCache;
    }

    private function detectColumn(array $candidates, $required = true, $fallbackPattern = null)
    {
        foreach ($candidates as $column) {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM user LIKE :column");
            $stmt->execute([':column' => $column]);
            if ($stmt->fetch()) {
                return $column;
            }
        }

        if (!$required && $fallbackPattern !== null) {
            foreach ($this->getUserColumns() as $columnName) {
                if ($columnName !== '' && preg_match($fallbackPattern, $columnName)) {
                    return $columnName;
                }
            }
        }

        if ($required) {
            throw new RuntimeException("Colonne introuvable dans la table user.");
        }

        return null;
    }

    private function normalizeProfileField(array $user = null)
    {
        if ($user === null || $this->profileColumn === null) {
            return $user;
        }

        $user['profile_pic'] = $user[$this->profileColumn] ?? ($user['profile_pic'] ?? null);

        return $user;
    }

    private function normalizeProfileFieldList(array $users)
    {
        if ($this->profileColumn === null) {
            return $users;
        }

        foreach ($users as &$user) {
            $user = $this->normalizeProfileField($user);
        }

        return $users;
<<<<<<< HEAD
=======
=======
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
    }

    public function signup($name, $email, $password, $cin, $date_naissance, $tel, $region)
    {
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
        // Vérifier si email existe déjà
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
        $check = $this->db->prepare("SELECT id_user FROM user WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            return ['success' => false, 'error' => 'Email déjà utilisé'];
        }

<<<<<<< HEAD
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(sprintf("
            INSERT INTO user (name, email, %s, cin, date_naissance, tel, region, date_creation, role)
            VALUES (:name, :email, :hash, :cin, :date_naissance, :tel, :region, NOW(), 'volunteer')
        ", $this->passwordColumn));
=======
<<<<<<< HEAD
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(sprintf("
            INSERT INTO user (name, email, %s, cin, date_naissance, tel, region, date_creation, role)
            VALUES (:name, :email, :hash, :cin, :date_naissance, :tel, :region, NOW(), 'volunteer')
        ", $this->passwordColumn));
=======
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO user (name, email, password_hash, cin, date_naissance, tel, region, date_creation, role)
            VALUES (:name, :email, :hash, :cin, :date_naissance, :tel, :region, CURDATE(), 'volunteer')
        ");
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1

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

<<<<<<< HEAD
        $passwordField = $this->passwordColumn;

        if (empty($user[$passwordField]) || !password_verify($password, $user[$passwordField])) {
=======
<<<<<<< HEAD
        $passwordField = $this->passwordColumn;

        if (empty($user[$passwordField]) || !password_verify($password, $user[$passwordField])) {
=======
        if (empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
            return ['success' => false, 'error' => 'Mot de passe incorrect'];
        }

        return ['success' => true, 'user' => $user];
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id_user = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
<<<<<<< HEAD
        $user = $stmt->fetch();
        return $this->normalizeProfileField($user);
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id_user DESC");
        $users = $stmt->fetchAll();
        return $this->normalizeProfileFieldList($users);
    }

    public function updateProfile($id, $name, $email, $tel, $region, $cin = null, $date_naissance = null, $profile_pic = null)
    {
        $fields = [
            'name = :name',
            'email = :email',
            'tel = :tel',
            'region = :region',
            'cin = :cin',
            'date_naissance = :date_naissance'
        ];

        if ($this->profileColumn !== null) {
            $fields[] = $this->profileColumn . ' = :profile_pic';
        }

        $sql = "UPDATE user SET " . implode(",\n                ", $fields) . "\n            WHERE id_user = :id";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':id'             => $id,
            ':name'           => $name,
            ':email'          => $email,
            ':tel'            => $tel ?: null,
            ':region'         => $region ?: null,
            ':cin'            => $cin ?: null,
            ':date_naissance' => $date_naissance ?: null
        ];

        if ($this->profileColumn !== null) {
            $params[':profile_pic'] = $profile_pic;
        }

        return $stmt->execute($params);
    }

    public function changePassword($id, $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(sprintf("
            UPDATE user
            SET %s = :hash
            WHERE id_user = :id
        ", $this->passwordColumn));

        return $stmt->execute([
            ':id'   => $id,
            ':hash' => $hash
        ]);
    }

    public function updateUserByAdmin($id, $name, $email, $tel, $region, $role)
=======
<<<<<<< HEAD
        $user = $stmt->fetch();
        return $this->normalizeProfileField($user);
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id_user DESC");
        $users = $stmt->fetchAll();
        return $this->normalizeProfileFieldList($users);
    }

    public function updateProfile($id, $name, $email, $tel, $region, $cin = null, $date_naissance = null, $profile_pic = null)
    {
        $fields = [
            'name = :name',
            'email = :email',
            'tel = :tel',
            'region = :region',
            'cin = :cin',
            'date_naissance = :date_naissance'
        ];

        if ($this->profileColumn !== null) {
            $fields[] = $this->profileColumn . ' = :profile_pic';
        }

        $sql = "UPDATE user SET " . implode(",\n                ", $fields) . "\n            WHERE id_user = :id";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':id'             => $id,
            ':name'           => $name,
            ':email'          => $email,
            ':tel'            => $tel ?: null,
            ':region'         => $region ?: null,
            ':cin'            => $cin ?: null,
            ':date_naissance' => $date_naissance ?: null
        ];

        if ($this->profileColumn !== null) {
            $params[':profile_pic'] = $profile_pic;
        }

        return $stmt->execute($params);
    }

    public function changePassword($id, $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(sprintf("
            UPDATE user
            SET %s = :hash
            WHERE id_user = :id
        ", $this->passwordColumn));

        return $stmt->execute([
            ':id'   => $id,
            ':hash' => $hash
        ]);
    }

    public function updateUserByAdmin($id, $name, $email, $tel, $region, $role)
=======
        return $stmt->fetch();
    }

    public function updateProfile($id, $name, $email, $tel, $region)
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
    {
        $stmt = $this->db->prepare("
            UPDATE user
            SET name = :name,
                email = :email,
                tel = :tel,
<<<<<<< HEAD
                region = :region,
                role = :role
=======
<<<<<<< HEAD
                region = :region,
                role = :role
=======
                region = :region
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
            WHERE id_user = :id
        ");

        return $stmt->execute([
            ':id'     => $id,
            ':name'   => $name,
            ':email'  => $email,
            ':tel'    => $tel ?: null,
<<<<<<< HEAD
            ':region' => $region ?: null,
            ':role'   => $role
=======
<<<<<<< HEAD
            ':region' => $region ?: null,
            ':role'   => $role
        ]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id_user = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
=======
            ':region' => $region ?: null
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
        ]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id_user = :id");
        return $stmt->execute([':id' => $id]);
    }
}
<<<<<<< HEAD
?>
=======
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
