<?php
session_start();
session_destroy();

header("Location: ../View/frontoffice/login.php");
exit;
