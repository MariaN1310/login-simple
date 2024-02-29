<?php
session_start();

if (isset($_SESSION['logged']) === FALSE) {
    header("Location: login.php");
}

session_destroy();
setcookie("id", time() - 3600);
setcookie("random", time() - 3600);

header("Location: index.php");
?>