<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../View/frontoffice/login.php?error=Veuillez vous connecter.");
    exit;
}

require_once '../config.php';

$userId = $_SESSION['user_id'];

// Receive fields
$name           = trim($_POST['name']);
$email          = trim($_POST['email']);
$tel            = trim($_POST['tel']);
$cin            = trim($_POST['cin']);
$region         = trim($_POST['region']);
$dateNaissance  = trim($_POST['date_naissance']);

// Handle profile picture upload
$profilePicPath = null;

if (!empty($_FILES['profile_pic']['name'])) {

    $targetDir = "../uploads/profile/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Detect real extension
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $ext = strtolower($ext);

    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        header("Location: ../View/frontoffice/profile_edit.php?error=Format non supporté");
        exit;
    }

    // Build final file name with extension
    $fileName = "user_" . $userId . "_" . time() . "." . $ext;
    $targetFile = $targetDir . $fileName;

    // Move file
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
        // SAVE RELATIVE PATH for loading in profile.php
        $profilePicPath = "uploads/profile/" . $fileName;
    }
}

// If no new picture uploaded, keep old one
$stmtOld = $conn->prepare("SELECT profile_pic FROM user WHERE id_user = ?");
$stmtOld->execute([$userId]);
$oldPic = $stmtOld->fetchColumn();

if (!$profilePicPath) {
    $profilePicPath = $oldPic;
}

// Update user fields
$stmt = $conn->prepare("
    UPDATE user 
    SET 
        name=?, 
        email=?, 
        tel=?, 
        cin=?, 
        region=?, 
        date_naissance=?, 
        profile_pic=?
    WHERE id_user=?
");

$stmt->execute([$name, $email, $tel, $cin, $region, $dateNaissance, $profilePicPath, $userId]);

// Update session
$_SESSION['name'] = $name;

header("Location: ../View/frontoffice/profile.php?success=1");
exit;
