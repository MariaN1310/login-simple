<?php
session_start();
require 'php/conexion.php';

if (!isset($_SESSION['logged'])) {
	if (isset($_COOKIE["id"]) && isset($_COOKIE["random"])) {
		
		$cookie_id = $_COOKIE["id"];
		$cookie_random = $_COOKIE["random"];
		$ssql = $conexion->query("SELECT * from usuarios where id = '$cookie_id' AND cookie = '$cookie_random'");

		while ($login = $ssql->fetch_assoc()) {
			if (($login['cookie'] == $cookie_random) && ($login['id'] == $cookie_id)) {
				$_SESSION['logged'] = "Logged";
				$_SESSION['usuario'] = $login['usuario'];
				$_SESSION['id'] = $login['id'];
				$_SESSION['email'] = $login['email'];
				
				$random_new = mt_rand(1000000,999999999);
				$ssql2 = $conexion->query("UPDATE usuarios set cookie='$random_new' where id='$cookie_id'"); //cambiar nombre variable

				setcookie("id", $cookie_id , time()+(60*60*24*30));
				setcookie("random", $random_new, time()+(60*60*24*30));
			}
		}
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
		Información de SESSION<br><br>
	
		<?php
		echo "Logged: ".$_SESSION['logged']."<br>";
		echo "Usuario: ".$_SESSION['usuario']."<br>";
		echo "ID Cliente: ".$_SESSION['id']."<br>";
		echo "E-mail: ".$_SESSION['email']."<br><br>";

		echo "Numero Aleatorio Cookie: ".$_COOKIE["random"]."<br><br>";
		?>

		<a class="" href="logout.php">Cerrar Sesión</a>
	</body>
</html>