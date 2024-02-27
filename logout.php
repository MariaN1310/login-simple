<?php
session_start();

if (isset($_SESSION['logged']) === FALSE) {
    header("Location: login.php");
}

unset($_SESSION['logged']);
session_destroy();
header("Location: index.php");
?>