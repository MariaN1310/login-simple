<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
require 'php/conexion.php';

if (isset($_REQUEST['iniciar'])) {
	$usuario = $_REQUEST['usuario'];
	$password = $_REQUEST['contrasena'];

	$sql = $conexion->query("SELECT * FROM usuarios WHERE usuario = '$usuario'");
	while ($login = $sql->fetch_assoc()) {

		if ($usuario == isset($login['usuario']) && password_verify($password, $login['contrasena'])) {
			$_SESSION['logged'] = "Logged";
			$_SESSION['usuario'] = $login['usuario'];
			$_SESSION['id'] = $login['id'];
			$_SESSION['email'] = $login['email'];
	
			$random = mt_rand(1000000,999999999);
			
			$random_asignado = "SELECT cookie FROM usuarios WHERE cookie = $random";
			$resultado = mysqli_query($conexion, $random_asignado);
			$fila = mysqli_fetch_assoc($resultado);
			$valor_cookie = $fila['cookie'];

			if ($valor_cookie != $random) {
				
				$ssql = $conexion->query("UPDATE usuarios set cookie='$random' where id='$idDB'");
			
				setcookie("id", $_SESSION['id'] , time()+(60*60*24*30));
				setcookie("random", $random, time()+(60*60*24*30));
				
				header("Location: index.php");
			}
		} elseif ($usuario != isset($login['usuario'])) {
			echo "<div class='error mt-3'><span>El Nombre de Usuario que has Introducido es Incorrecto</span></div>";
		} elseif (password_verify($password, $login['contrasena']) === FALSE) {
			echo "<div class='error mt-3'><span>La Contraseña que has Introducido es Incorrecta</span></div>";
		}
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
				<div class="clearfix">
					<a href="#" class="float-end forgot">&#xBF;Olvid&#xF3; su contrase&#xF1;a?</a>
				</div>
			</form>
		</div>
		<div class="forgotForm container" style="display: none;">
			<form method="post">
				<h2 class="text-center">Recuperar Contraseña</h2>
				<p>Se le enviarán las instrucciones por E-mail</p>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
					<input type="email" class="form-control" id="fp_email" name="emailF" placeholder="E-Mail" required>
				</div>
				<div class="mb-3 d-grid gap-2 col-6 mx-auto">
					<button type="submit "class="btn btn-primary" name="forgotBTN">Enviar Mail</button>
				</div>
				<div class="clearfix">
					<a href="#" class="float-end login">Iniciar Sesión</a>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			$(".forgot").click(function(event) {
				$(".loginForm").slideUp("slow");
				$(".forgotForm").slideDown("slow");
			});
			$(".login").click(function(event) {
				$(".forgotForm").slideUp("slow");
				$(".loginForm").slideDown("slow");
			});
		</script>
	</body>
</html>