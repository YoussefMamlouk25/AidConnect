<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../View/frontoffice/login.php?error=Accès refusé");
    exit;
}

require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM user WHERE id_user = ?");
    $stmt->execute([$id]);
}

header("Location: ../View/backoffice/users.php");
exit;
