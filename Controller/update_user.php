<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../View/frontoffice/login.php?error=Accès refusé");
    exit;
}

require_once '../config.php';

$id = $_POST['id_user'];
$name = $_POST['name'];
$email = $_POST['email'];
$tel = $_POST['tel'];
$region = $_POST['region'];
$role = $_POST['role'];

$stmt = $conn->prepare("
    UPDATE user SET 
        name=?, email=?, tel=?, region=?, role=?
    WHERE id_user=?
");

$stmt->execute([$name, $email, $tel, $region, $role, $id]);

header("Location: ../View/backoffice/users.php?updated=1");
exit;
