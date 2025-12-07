<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AidConnect</title>
</head>
<body>

<nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Si user connecté -->
        <a href="View/frontoffice/profile.php">Profile</a>
        <a href="Controller/logout.php">Logout</a>
    <?php else: ?>
        <!-- Si user NON connecté -->
        <a href="View/frontoffice/login.php">Login</a>
        <a href="View/frontoffice/signup.php">Sign Up</a>
    <?php endif; ?>
</nav>

<h1>Bienvenue sur AidConnect</h1>

</body>
</html>
