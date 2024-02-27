<?php
session_start();
require 'php/conexion.php';

if (!isset($_SESSION['logged'])) {
	if (isset($_COOKIE["id"]) && isset($_COOKIE["num_aleatorio"])){
		
		$cookie1 = $_COOKIE["id"];
		$cookie2 = $_COOKIE["num_aleatorio"];
		$ssql = $conexion->query("SELECT * from usuarios where id = '$cookie1' AND cookie = '$cookie2'");

		while ($login = $ssql->fetch_assoc()) {
			$idDB = $login['id'];
			$usuarioDB = $login['usuario'];
			$emailDB = $login['email'];
			$cookieDB = $login['cookie'];
		}
		
		if (($cookieDB == $cookie2) && ($idDB == $cookie1)) {
			$_SESSION['logged'] = "Logged";
			$_SESSION['usuario'] = $usuarioDB;
			$_SESSION['id'] = $idDB;
			$_SESSION['email'] = $emailDB;
		}
		setcookie("id", $cookie1 , time()+(60*60*24*365));
		setcookie("num_aleatorio", $cookie2, time()+(60*60*24*365));
	}
}

if (isset($_SESSION['logged']) === FALSE) {
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Index - Login-Simple</title>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	</head>
	<body>
		Logueo OK.<br>
        Información de SESSION:<br>
    
        <?php
        echo "Logged: ".$_SESSION['logged']."<br>";
        echo "Usuario: ".$_SESSION['usuario']."<br>";
        echo "ID: ".$_SESSION['id']."<br>";
        echo "E-mail: ".$_SESSION['email']."<br><br>";

        echo "Numero Aleatorio Cookie: ".$_COOKIE["num_aleatorio"]."<br>";

        ?>
	</body>
</html>