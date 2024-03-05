<?php
session_start();
require 'php/conexion.php';

$idDB = $_SESSION['id'];
if (isset($_SESSION['logged']) === FALSE) {
	header("Location: login.php");
	exit();
}

// Actualizar la cookie en la DB
$actualizarCookie = $conexion->prepare("UPDATE usuarios SET cookie='0', token=NULL WHERE id=?");
$actualizarCookie->bind_param("i", $idDB);
$actualizarCookie->execute();

// Destruir la sesión
session_destroy();

// Eliminar las cookies
setcookie("id", time() - 3600);
setcookie("random", time() - 3600);

// Redirigir a la página de inicio de sesión y salir del script
header("Location: login.php");
exit();
?>