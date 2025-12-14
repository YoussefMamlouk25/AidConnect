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

    private function normalizeProfileField($user = null)
    {
        // Si $user est null, false, ou pas un tableau, retourner tel quel
        if ($user === null || $user === false || !is_array($user)) {
            return $user;
        }

        // Si pas de colonne profile, retourner tel quel
        if ($this->profileColumn === null) {
            return $user;
        }

        // Normaliser le champ profile_pic
        $user['profile_pic'] = $user[$this->profileColumn] ?? ($user['profile_pic'] ?? null);

        return $user;
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

    /* 🔍 Check if email exists */
    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM user WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function signup($name, $email, $password, $cin, $date_naissance, $tel, $region)
    {
        // Vérifier si email existe déjà
        if ($this->emailExists($email)) {
            return ['success' => false, 'error' => 'Email déjà utilisé'];
        if ($check->fetch()) {
            return ['success' => false, 'error' => 'Email déjà utilisé.'];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Générer le code de vérification
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeExpires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $stmt = $this->db->prepare(sprintf("
            INSERT INTO user (name, email, %s, cin, date_naissance, tel, region, date_creation, role, verification_code, email_verified, verification_code_expires)
            VALUES (:name, :email, :hash, :cin, :date_naissance, :tel, :region, NOW(), 'volunteer', :verification_code, 0, :code_expires)
        ", $this->passwordColumn));

        $stmt->execute([
            ':name'            => $name,
            ':email'           => $email,
            ':hash'            => $hash,
            ':cin'             => $cin ?: null,
            ':date_naissance'  => $date_naissance ?: null,
            ':tel'             => $tel ?: null,
            ':region'          => $region ?: null,
            ':verification_code' => $verificationCode,
            ':code_expires'    => $codeExpires
        ]);

        return [
            'success' => true,
            'user_id' => $this->db->lastInsertId(),
            'verification_code' => $verificationCode
        ];
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

        if (!$user) {
            return ['success' => false, 'error' => 'Email introuvable'];
        }

        $passwordField = $this->passwordColumn;

        if (empty($user[$passwordField]) || !password_verify($password, $user[$passwordField])) {
            return ['success' => false, 'error' => 'Mot de passe incorrect'];
        }

        // Normaliser le champ email_verified (peut être 0/1 ou true/false)
        if (isset($user['email_verified'])) {
            $user['email_verified'] = (int)$user['email_verified'];
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

    public function updateUserByAdmin($id, $name, $email, $tel, $region, $role)
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

    /* ✏ Update user (simpler version without role) */
    public function updateUser($id, $name, $email, $cin, $tel, $region)
    {
        $stmt = $this->db->prepare("
            UPDATE user SET 
                name = :name,
                email = :email,
                cin = :cin,
                tel = :tel,
                region = :region
            WHERE id_user = :id
        ");

        return $stmt->execute([
            ':id'     => $id,
            ':name'   => $name,
            ':email'  => $email,
            ':cin'    => $cin ?: null,
            ':tel'    => $tel ?: null,
            ':region' => $region ?: null
        ]);
    }

    /**
     * Vérifie le code de vérification email
     * 
     * @param string $email Email de l'utilisateur
     * @param string $code Code de vérification
     * @return array Résultat de la vérification
     */
    public function verifyEmail($email, $code)
    {
        $stmt = $this->db->prepare("
            SELECT id_user, verification_code, verification_code_expires, email_verified 
            FROM user 
            WHERE email = :email 
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Email introuvable'];
        }

        if ($user['email_verified'] == 1) {
            return ['success' => false, 'error' => 'Email déjà vérifié'];
        }

        if (empty($user['verification_code'])) {
            return ['success' => false, 'error' => 'Aucun code de vérification trouvé'];
        }

        if ($user['verification_code'] !== $code) {
            return ['success' => false, 'error' => 'Code de vérification incorrect'];
        }

        // Vérifier si le code a expiré
        if (strtotime($user['verification_code_expires']) < time()) {
            return ['success' => false, 'error' => 'Code de vérification expiré. Veuillez demander un nouveau code.'];
        }

        // Marquer l'email comme vérifié
        $updateStmt = $this->db->prepare("
            UPDATE user 
            SET email_verified = 1, 
                verification_code = NULL, 
                verification_code_expires = NULL 
            WHERE id_user = :id
        ");
        $updateStmt->execute([':id' => $user['id_user']]);

        return ['success' => true, 'message' => 'Email vérifié avec succès'];
    }

    /**
     * Régénère un nouveau code de vérification
     * 
     * @param string $email Email de l'utilisateur
     * @return array Nouveau code de vérification
     */
    public function resendVerificationCode($email)
    {
        $stmt = $this->db->prepare("SELECT id_user, name, email_verified FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Email introuvable'];
        }

        if ($user['email_verified'] == 1) {
            return ['success' => false, 'error' => 'Email déjà vérifié'];
        }

        // Générer un nouveau code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeExpires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $updateStmt = $this->db->prepare("
            UPDATE user 
            SET verification_code = :code, 
                verification_code_expires = :expires 
            WHERE id_user = :id
        ");
        $updateStmt->execute([
            ':code' => $verificationCode,
            ':expires' => $codeExpires,
            ':id' => $user['id_user']
        ]);

        return [
            'success' => true,
            'verification_code' => $verificationCode,
            'name' => $user['name']
        ];
    }

    /**
     * Vérifie si l'email est vérifié (pour la connexion)
     * 
     * @param string $email Email de l'utilisateur
     * @return bool True si vérifié
     */
    public function isEmailVerified($email)
    {
        $stmt = $this->db->prepare("SELECT email_verified FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        return $user && $user['email_verified'] == 1;
    }

    /**
     * Compte les posts publics de l'utilisateur
     * @param int $userId
     * @return int
     */
    public function getUserPublicPostsCount($userId) {
        try {
            // Vérifier si la table 'post' existe d'abord
            $tableExists = $this->db->query("SHOW TABLES LIKE 'post'")->fetch();
            
            if (!$tableExists) {
                return 0;
            }
            
            // Vérifier la structure de la table 'post'
            $stmt = $this->db->prepare("SHOW COLUMNS FROM post");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Déterminer les noms de colonnes possibles
            $statusColumn = in_array('status', $columns) ? 'status' : 
                           (in_array('etat', $columns) ? 'etat' : 
                           (in_array('state', $columns) ? 'state' : null));
            
            $idColumn = in_array('id_user', $columns) ? 'id_user' : 
                       (in_array('user_id', $columns) ? 'user_id' : 
                       (in_array('userId', $columns) ? 'userId' : 'id_user'));
            
            // Construire la requête en fonction des colonnes disponibles
            if ($statusColumn) {
                $sql = "SELECT COUNT(*) as count FROM post WHERE $idColumn = ? AND $statusColumn = 'public'";
            } else {
                // Si pas de colonne status, compter tous les posts
                $sql = "SELECT COUNT(*) as count FROM post WHERE $idColumn = ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compte les offres publiques de l'utilisateur
     * @param int $userId
     * @return int
     */
    public function getUserPublicOffersCount($userId) {
        try {
            // Vérifier si la table 'offer' existe d'abord
            $tableExists = $this->db->query("SHOW TABLES LIKE 'offer'")->fetch();
            
            if (!$tableExists) {
                return 0;
            }
            
            // Vérifier la structure de la table 'offer'
            $stmt = $this->db->prepare("SHOW COLUMNS FROM offer");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Déterminer les noms de colonnes possibles
            $statusColumn = in_array('status', $columns) ? 'status' : 
                           (in_array('etat', $columns) ? 'etat' : 
                           (in_array('state', $columns) ? 'state' : 
                           (in_array('is_active', $columns) ? 'is_active' : null)));
            
            $idColumn = in_array('id_user', $columns) ? 'id_user' : 
                       (in_array('user_id', $columns) ? 'user_id' : 
                       (in_array('userId', $columns) ? 'userId' : 'id_user'));
            
            // Construire la requête en fonction des colonnes disponibles
            if ($statusColumn) {
                // Vérifier les valeurs possibles pour le statut
                $valueCheck = $this->db->prepare("SELECT DISTINCT $statusColumn FROM offer LIMIT 5");
                $valueCheck->execute();
                $values = $valueCheck->fetchAll(PDO::FETCH_COLUMN);
                
                if (in_array('active', $values) || in_array('Active', $values)) {
                    $sql = "SELECT COUNT(*) as count FROM offer WHERE $idColumn = ? AND $statusColumn = 'active'";
                } elseif (in_array('public', $values) || in_array('Public', $values)) {
                    $sql = "SELECT COUNT(*) as count FROM offer WHERE $idColumn = ? AND $statusColumn = 'public'";
                } else {
                    // Si pas de valeur standard, compter toutes les offres
                    $sql = "SELECT COUNT(*) as count FROM offer WHERE $idColumn = ?";
                }
            } else {
                // Si pas de colonne status, compter tous les posts
                $sql = "SELECT COUNT(*) as count FROM offer WHERE $idColumn = ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Error counting offers: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Compte les jours actifs de l'utilisateur
     * @param int $userId
     * @return int
     */
    public function getUserActiveDays($userId) {
        try {
            $days = 1; // Minimum 1 jour (inscription)
            
            // Vérifier les tables existantes
            $tables = ['post', 'offer', 'activity', 'user_activity'];
            $dateQueries = [];
            
            // Ajouter la date d'inscription de l'utilisateur
            $stmt = $this->db->prepare("SELECT date_creation FROM user WHERE id_user = ?");
            $stmt->execute([$userId]);
            $userDate = $stmt->fetch();
            if ($userDate && $userDate['date_creation']) {
                $days = 1;
            }
            
            // Vérifier les posts
            $postTable = $this->db->query("SHOW TABLES LIKE 'post'")->fetch();
            if ($postTable) {
                // Vérifier les colonnes de date dans la table post
                $stmt = $this->db->prepare("SHOW COLUMNS FROM post");
                $stmt->execute();
                $postColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $dateColumn = '';
                foreach (['created_at', 'date_creation', 'date', 'creation_date', 'timestamp'] as $col) {
                    if (in_array($col, $postColumns)) {
                        $dateColumn = $col;
                        break;
                    }
                }
                
                if ($dateColumn) {
                    $dateQueries[] = "SELECT $dateColumn as activity_date FROM post WHERE id_user = ?";
                }
            }
            
            // Vérifier les offres
            $offerTable = $this->db->query("SHOW TABLES LIKE 'offer'")->fetch();
            if ($offerTable) {
                // Vérifier les colonnes de date dans la table offer
                $stmt = $this->db->prepare("SHOW COLUMNS FROM offer");
                $stmt->execute();
                $offerColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $dateColumn = '';
                foreach (['created_at', 'date_creation', 'date', 'creation_date', 'timestamp'] as $col) {
                    if (in_array($col, $offerColumns)) {
                        $dateColumn = $col;
                        break;
                    }
                }
                
                if ($dateColumn) {
                    $dateQueries[] = "SELECT $dateColumn as activity_date FROM offer WHERE id_user = ?";
                }
            }
            
            // Si nous avons des requêtes, exécuter l'union
            if (!empty($dateQueries)) {
                $unionQuery = implode(" UNION ALL ", $dateQueries);
                $unionQuery = "SELECT COUNT(DISTINCT DATE(activity_date)) as days FROM ($unionQuery) as activities";
                
                $stmt = $this->db->prepare($unionQuery);
                
                // Préparer les paramètres (même userId pour chaque requête UNION)
                $params = [];
                for ($i = 0; $i < count($dateQueries); $i++) {
                    $params[] = $userId;
                }
                
                $stmt->execute($params);
                $result = $stmt->fetch();
                $activityDays = (int)($result['days'] ?? 0);
                
                // Prendre le maximum entre 1 et les jours d'activité
                $days = max($days, $activityDays);
            }
            
            return $days;
        } catch (Exception $e) {
            error_log("Error counting active days: " . $e->getMessage());
            return 1; // Retourner au moins 1 jour
        }
    }

    /**
     * Calcule le niveau d'activité de l'utilisateur
     * @param int $userId
     * @return string
     */
    public function getUserActivityLevel($userId) {
        $posts = $this->getUserPublicPostsCount($userId);
        $offers = $this->getUserPublicOffersCount($userId);
        $total = $posts + $offers;
        
        if ($total >= 10) return 'Élevé';
        if ($total >= 3) return 'Moyen';
        return 'Faible';
    }

    /**
     * Calcule le nombre total d'activités
     * @param int $userId
     * @return int
     */
    public function getUserTotalActivities($userId) {
        return $this->getUserPublicPostsCount($userId) + $this->getUserPublicOffersCount($userId);
    }

    /**
     * Calcule la progression d'activité
     * @param int $userId
     * @return array ['progress' => int, 'goal' => int]
     */
    public function getUserActivityProgress($userId) {
        $goal = 15; // Objectif de 15 activités
        $total = $this->getUserTotalActivities($userId);
        $progress = $total > 0 ? round(($total / $goal) * 100) : 0;
        
        // Limiter à 100%
        if ($progress > 100) $progress = 100;
        
        return [
            'progress' => $progress,
            'goal' => $goal,
            'total' => $total
        ];
    }

    /**
     * Récupère toutes les statistiques de l'utilisateur
     * @param int $userId
     * @return array
     */
    public function getUserStats($userId) {
        $activityProgress = $this->getUserActivityProgress($userId);
        
        return [
            'public_posts' => $this->getUserPublicPostsCount($userId),
            'public_offers' => $this->getUserPublicOffersCount($userId),
            'activity_level' => $this->getUserActivityLevel($userId),
            'active_days' => $this->getUserActiveDays($userId),
            'total_activities' => $activityProgress['total'],
            'activity_goal' => $activityProgress['goal'],
            'activity_progress' => $activityProgress['progress']
        ];
    /* ================= GET USER COUNT ================= */
    public function getUserCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM user");
        return (int) $stmt->fetchColumn();
    }
}