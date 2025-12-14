<?php
// Version de test pour déboguer le problème de profile.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "<!DOCTYPE html><html><head><title>Test Profile</title></head><body>";
echo "<h1>Test Profile Debug</h1>";

echo "<h2>Session Info:</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "id_user: " . (isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 'NOT SET') . "\n";
echo "name: " . (isset($_SESSION['name']) ? $_SESSION['name'] : 'NOT SET') . "\n";
echo "role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "\n";
echo "</pre>";

if (!isset($_SESSION['id_user'])) {
    echo "<p style='color:red;'>ERREUR: id_user n'est pas dans la session!</p>";
    echo "<p><a href='login.php'>Aller à la page de connexion</a></p>";
} else {
    require_once '../../Model/user.php';
    $model = new UserModel();
    $user = $model->getUserById($_SESSION['id_user']);
    
    echo "<h2>User Data:</h2>";
    echo "<pre>";
    var_dump($user);
    echo "</pre>";
    
    if ($user === false || !is_array($user)) {
        echo "<p style='color:red;'>ERREUR: getUserById a retourné false ou n'est pas un tableau!</p>";
    } else {
        echo "<p style='color:green;'>SUCCÈS: Utilisateur trouvé!</p>";
        echo "<p><a href='profile.php'>Aller à la vraie page profile</a></p>";
    }
}

echo "</body></html>";
?>

