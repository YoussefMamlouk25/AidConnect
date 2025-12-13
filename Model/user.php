<?php
require_once __DIR__ . '/../config.php';

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Config::getConnexion();
    }

    /* ================= GET ALL USERS ================= */
    public function getAllUsers()
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user ORDER BY id_user DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* ================= GET PASSWORD COLUMN NAME ================= */
    public function getPasswordColumn(): string
    {
        // Since we know the column name is 'password', we can return it directly
        return 'password';
        
        // Or if you want to be dynamic and check the actual column name:
        // $stmt = $this->db->query("SHOW COLUMNS FROM user LIKE 'password'");
        // $columnInfo = $stmt->fetch();
        // return $columnInfo['Field'] ?? 'password';
    }

    /* ================= GET USER ================= */
    public function getUserById(int $id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user WHERE id_user = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /* ================= SIGNUP ================= */
    public function signup(
        string $name,
        string $email,
        string $password,
        ?string $cin,
        ?string $date_naissance,
        ?string $tel,
        ?string $region,
        string $token
    ): array {
        $check = $this->db->prepare(
            "SELECT id_user FROM user WHERE email = ?"
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            return ['success' => false, 'error' => 'Email déjà utilisé.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO user
            (name, email, password, cin, date_naissance, tel, region, verify_token, verified, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 'user')
        ";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            $name,
            $email,
            $hashed,
            $cin ?: null,
            $date_naissance ?: null,
            $tel ?: null,
            $region ?: null,
            $token
        ]);

        return ['success' => $ok];
    }

    /* ================= LOGIN ================= */
    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM user WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Email ou mot de passe incorrect.'];
        }

        return ['success' => true, 'user' => $user];
    }

    /* ================= VERIFY EMAIL ================= */
    public function verifyEmail(string $token): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE user
             SET verified = 1, verify_token = NULL
             WHERE verify_token = ?"
        );
        $stmt->execute([$token]);

        return $stmt->rowCount() > 0;
    }

    /* ================= UPDATE PROFILE (FINAL, SAFE) ================= */
    public function updateProfile(
        int $id,
        string $name,
        string $email,
        ?string $tel,
        ?string $region,
        ?string $cin,
        ?string $date_naissance,
        ?string $profile_pic
    ): bool {
        $sql = "
            UPDATE user SET
                name = ?,
                email = ?,
                tel = ?,
                region = ?,
                cin = ?,
                date_naissance = ?,
                profile_pic = ?
            WHERE id_user = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $tel,
            $region,
            $cin,
            $date_naissance,
            $profile_pic,
            $id
        ]);
    }

    /* ================= ADMIN UPDATE USER ================= */
    public function updateUserByAdmin(
        int $id,
        string $name,
        string $email,
        ?string $tel,
        ?string $region,
        string $role
    ): bool {
        $sql = "
            UPDATE user SET
                name = ?,
                email = ?,
                tel = ?,
                region = ?,
                role = ?
            WHERE id_user = ?
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $name,
            $email,
            $tel,
            $region,
            $role,
            $id
        ]);
    }

    /* ================= CHANGE PASSWORD ================= */
    public function changePassword(int $id, string $password): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "UPDATE user SET password = ? WHERE id_user = ?"
        );

        return $stmt->execute([$hashed, $id]);
    }

    /* ================= USER STATISTICS ================= */
    public function getUserStats(int $userId): array
    {
        // Posts count
        $stmtPost = $this->db->prepare(
            "SELECT COUNT(*) FROM post WHERE id_user = ?"
        );
        $stmtPost->execute([$userId]);
        $posts = (int) $stmtPost->fetchColumn();

        // Offers count
        $stmtOffer = $this->db->prepare(
            "SELECT COUNT(*) FROM offer WHERE id_user = ?"
        );
        $stmtOffer->execute([$userId]);
        $offers = (int) $stmtOffer->fetchColumn();

        return [
            'posts'  => $posts,
            'offers' => $offers
        ];
    }

    /* ================= DELETE USER ================= */
    public function deleteUser(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM user WHERE id_user = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Error deleting user $id: " . $e->getMessage());
            return false;
        }
    }

    /* ================= CHECK IF EMAIL EXISTS (EXCEPT CURRENT USER) ================= */
    public function emailExistsExceptUser(string $email, int $excludeUserId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id_user FROM user WHERE email = ? AND id_user != ?"
        );
        $stmt->execute([$email, $excludeUserId]);
        return $stmt->fetch() !== false;
    }

    /* ================= GET USER COUNT ================= */
    public function getUserCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM user");
        return (int) $stmt->fetchColumn();
    }
}