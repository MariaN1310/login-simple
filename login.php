<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
require 'php/conexion.php';

if (isset($_REQUEST['iniciar'])) {
	$usuario = $_REQUEST['usuario'];
	$password = $_REQUEST['contrasena'];

	// Consulta preparada para evitar inyección SQL
	$sql = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ?");
	$sql->bind_param("s", $usuario);
	$sql->execute();
	$login = $sql->get_result()->fetch_assoc();

	if ($login && password_verify($password, $login['contrasena'])) {
		$_SESSION['logged'] = "Logged";
		$_SESSION['usuario'] = $login['usuario'];
		$_SESSION['id'] = $login['id'];
		$_SESSION['email'] = $login['email'];

		$max_intentos = 5; // Número máximo de intentos permitidos
		$intentos = 0;
		$cookie_encontrada = false;
		
		do {
			$random = mt_rand(1000000,999999999);
			$resultado = mysqli_query($conexion, "SELECT cookie FROM usuarios WHERE cookie = '$random'");
			$fila = mysqli_fetch_assoc($resultado);
			$valor_cookie = ($fila !== null) ? $fila['cookie'] : null; // Si no se encuentra la cookie, se asigna null
			$intentos++;
			if ($valor_cookie === null) {
				$cookie_encontrada = true;
			}
		} while (!$cookie_encontrada && $intentos < $max_intentos); // Continuar si no se encuentra la cookie y no se supera el límite de intentos
		
		// Verificar si no se encontró una cookie después de los intentos
		if (!$cookie_encontrada) {
			echo "<script>window.location.reload();</script>"; // Actualizar la página
		}

		// Actualización de cookie en la DB
		$actualizarCookie = $conexion->prepare("UPDATE usuarios SET cookie=? WHERE id=?");
		$actualizarCookie->bind_param("si", $random, $_SESSION['id']);
		$actualizarCookie->execute();
		
		// Establecer las nuevas cookies
		setcookie("id", $_SESSION['id'], time()+(60*60*24*30), "/", "", true, true);
		setcookie("random", $random, time()+(60*60*24*30), "/", "", true, true);

		$token = bin2hex(random_bytes(32));
		$_SESSION['token'] = $token;

		// Actualización del token en la base de datos
		$actualizarToken = $conexion->prepare("UPDATE usuarios SET token=? WHERE id=?");
		$actualizarToken->bind_param("si", $token, $_SESSION['id']);
		$actualizarToken->execute();

		// Redirigir a la página de inicio de sesión y salir del script
		header("Location: index.php");
		exit;

	} else {
		echo "<div class='error mt-3'><span>Usuario y/o contraseña incorrectos</span></div>";
	}
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Login - Login-Simple</title>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="css/login-simple.css">
	</head>
	<body>
		<div class="loginForm container">
			<form method="post" class="was-validated">
				<h2 class="text-center">Iniciar Sesi&#xF3;n</h2> 
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
					<input type="text" class="form-control" id="lg_username" name="usuario" placeholder="Usuario" required>
				</div>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
					<input type="password" class="form-control" id="lg_password" name="contrasena" placeholder="Contrase&#xF1;a" required>
				</div>
				<div class="mb-3 d-grid gap-2 col-6 mx-auto">
					<button type="submit" class="btn btn-primary" name="iniciar">Iniciar Sesi&#xF3;n</button>
				</div>
				<div class="clearfix">
					<a href="registro.php" class="float-end">Registrese</a>
				</div>
			</form>
		</div>
	</body>
</html>