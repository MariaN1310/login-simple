<?php
session_start();
require 'php/conexion.php';

if (isset($_REQUEST['Rcontrasena'])) {
	$newPass = $_REQUEST['contrasena'];
	$forgot = $_REQUEST['forgot'];
	$encriptar = password_hash($newPass, PASSWORD_BCRYPT, ["cost" => '11']);
	$conexion->query("UPDATE usuarios SET contrasena = '$encriptar' WHERE forgot = '$forgot'");
	$conexion->query("UPDATE usuarios SET forgot = '' WHERE contrasena = '$encriptar'");
	header("Location: login.php?enviado=2");
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Reestablecer Contraseña - Login-Simple</title>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="css/login-simple.css">
	</head>
	<body>
		<div class="forgotForm container">
		    <form method="post" class="was-validated">
		        <h2 class="text-center">Reestablecer Contraseña</h2>
                <div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
					<input type="password" class="form-control" id="lg_password" name="contrasena" placeholder="Nueva Contraseña" required>
				</div>
		        <div class="mb-3 d-grid gap-2 col-6 mx-auto">
		            <button type="submit" class="btn btn-primary" name="Rcontrasena">Recuperar</button>
		        </div>
		        <div class="clearfix">
		            <a href="login.php" class="float-end login">Iniciar Sesión</a>
		        </div>        
		    </form>
		</div>
	</body>
</html>