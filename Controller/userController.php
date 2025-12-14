<?php
session_start();

require_once '../config.php';
require_once '../Model/user.php';

$userModel = new UserModel();

// action from URL
$action = $_GET['action'] ?? 'list';

/************************
 * LIST USERS
 ************************/
if ($action === 'list') {
    // Check if user is admin
    if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
        exit;
    }
    
    $users = $userModel->getAllUsers();
    require '../View/backoffice/users.php';
    exit;
}

/************************
 * DELETE USER
 ************************/
if ($action === 'delete') {
    // Check if user is admin
    if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
        exit;
    }
    
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $userModel->deleteUser($id);
        header('Location: ../View/backoffice/users.php?deleted=1');
        exit;
    }
    
    header('Location: ../View/backoffice/users.php?error=' . urlencode('ID utilisateur manquant.'));
    exit;
}

/************************
 * EDIT (redirect to edit_user.php)
 ************************/
if ($action === 'edit') {
    // Check if user is admin
    if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
        exit;
    }
    
    $id = $_GET['id'] ?? null;
    if ($id) {
        header('Location: ../View/backoffice/edit_user.php?id=' . urlencode($id));
        exit;
    }
    
    header('Location: ../View/backoffice/users.php?error=' . urlencode('ID utilisateur manquant.'));
    exit;
}

