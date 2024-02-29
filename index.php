<?php
session_start();
require 'php/conexion.php';

if (!isset($_SESSION['logged'])) {
	if (isset($_COOKIE["id"]) && isset($_COOKIE["random"])) {
		$cookie_id = $_COOKIE["id"];
		$cookie_random = $_COOKIE["random"];

		$sql = $conexion->prepare("SELECT * FROM usuarios WHERE id = ? AND cookie = ?");
		$sql->bind_param("is", $cookie_id, $cookie_random);
		$sql->execute();
		$login = $sql->get_result()->fetch_assoc();

		if ($login) {
			$_SESSION['logged'] = "Logged";
			$_SESSION['usuario'] = $login['usuario'];
			$_SESSION['id'] = $login['id'];
			$_SESSION['email'] = $login['email'];

			// Generar un nuevo valor aleatorio para la cookie
			$random_new = mt_rand(1000000, 999999999);

			// Actualizar la cookie en la base de datos
			$actualizarCookies = $conexion->prepare("UPDATE usuarios SET cookie = ? WHERE id = ?");
			$actualizarCookies->bind_param("si", $random_new, $cookie_id);
			$actualizarCookies->execute();

			// Establecer las nuevas cookies
			setcookie("id", $cookie_id , time()+(60*60*24*30), "/", "", true, true);
			setcookie("random", $random_new, time()+(60*60*24*30), "/", "", true, true);
		}
	}
}

if (!isset($_SESSION['logged'])) {
	header("Location: login.php");
	exit;
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