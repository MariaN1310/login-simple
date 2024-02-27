<?php
session_start();
require 'php/conexion.php';
$fecha = date("Y-m-d H:i:s");
$fecha3 = $fecha.'.000000';

if (isset($_REQUEST['registrar'])) {
	$email;$comment;
	if (isset($_POST['email'])) {
		$email = $_POST['email'];
	}

	$pase = TRUE;
	if (isset($_REQUEST['registrar'])) {
		$_SESSION['email'] = $_REQUEST['email'];
		$email = $_SESSION['email'];
		$_SESSION['usuario'] = $_REQUEST['usuario'];
		$usuario = $_SESSION['usuario'];
		

		$correoExiste = $conexion->query("SELECT email FROM usuarios WHERE email = '$email'");
		if ($correoExiste->num_rows === 1) {
			echo "<script type=\"text/javascript\">
				alert('El Correo Electronico ya Existe. Intente uno diferente o Inicie Sesión.');
				history.go(-1);
			 </script>";
			$pase = FALSE;
		}

		$usuario = $_REQUEST['usuario'];
		$usuarioExiste = $conexion->query("SELECT usuario FROM usuarios WHERE usuario = '$usuario'");
		if ($usuarioExiste->num_rows === 1) {
			echo "<script type=\"text/javascript\">
				alert('El Nombre de Usuario ya Existe. Intente uno diferente o Inicie Sesión.');
				history.go(-1);
			</script>";
			$pase = FALSE;
		}
	}
	
	if (isset($_REQUEST['registrar']) && $pase === TRUE) {
		if ($_REQUEST['contrasena'] === $_REQUEST['contrasenaConfirmar']) {
			$email = $_REQUEST['email'];
			$usuario = $_REQUEST['usuario'];
			$password = $_REQUEST['contrasena'];
			
			$encriptar = password_hash($password, PASSWORD_BCRYPT, ["cost" => '11']);

			if ($conexion->query("INSERT INTO usuarios (email, usuario, contrasena) VALUES ('$email', '$usuario', '$encriptar')") === TRUE) {
				$_SESSION['logged'] = "Logged";
				$_SESSION['usuario'] = $usuario;

				$id = $conexion->query("SELECT id FROM usuarios WHERE usuario='$usuario'");
				while ($id2 = $id->fetch_assoc()) {
					$_SESSION['id'] = $id2['id'];
				}

				$idoriginal = $_SESSION['id'];
				mt_srand (time());
				$numero_aleatorio = mt_rand(1000000,999999999);
				$ssql = $conexion->query("UPDATE usuarios set cookie='$numero_aleatorio' where id='$idoriginal'");

				setcookie("id", $_SESSION['id'] , time()+(60*60*24*365));
				setcookie("num_aleatorio", $numero_aleatorio, time()+(60*60*24*365));
				
				header("Location: index.php");
			}
		} else {
			echo "<script type=\"text/javascript\">
				alert('Las Contraseñas no son iguales. Intente de nuevo.');
				history.go(-1);
			</script>";
			$pase = FALSE;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Registro - Login-Simple</title>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="css/login-simple.css">
	</head>
	<body>
		<div class="register container">
			<form method="post">
				<h2 class="text-center">Registro</h2>       
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-envelope"></i></span>
					<input type="email" class="form-control" id="reg_email" onkeypress="return Validarbarra(event,this)" name="email" placeholder="Correo Electronico" required>
				</div>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
					<input type="text" class="form-control" id="reg_username" onkeypress="return Validarbarra(event,this)" name="usuario" placeholder="Usuario" minlength="5" maxlength="40" pattern="[A-Za-z0-9]+" required>
					<small id="help" class="form-text text-muted">
						Letras y números - Sin espacios - Mínimo: 5 - Máximo: 40
					</small>
				</div>
				
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
					<input type="password" class="form-control" id="reg_password" name="contrasena" onkeypress="return Validarbarra(event,this)" placeholder="Contraseña" minlength="6" maxlength="30" pattern="[A-Za-z0-9]+" required>
					<small id="help" class="form-text text-muted">
						Letras y números - Sin espacios - Mínimo: 6 - Máximo: 30
					</small>
				</div>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
					<input type="password" class="form-control" id="reg_password_confirm" name="contrasenaConfirmar" placeholder="Confirme Contraseña" minlength="6" maxlength="30" pattern="[A-Za-z0-9]+" required>
				</div>
				<div class="mb-3 d-grid gap-2 col-6 mx-auto">
					<button type="submit" class="btn btn-primary" name="registrar"> Registrar </button>
				</div>
				<div class="clearfix">
					<a href="login.php" class="float-end">Inicie Sesión</a>
				</div>
			</form>
		</div>
		<script>
			function Validarbarra(e, campo) {
				key = e.keyCode ? e.keyCode : e.which;
				if (key == 32) {
					alert("No se pueden usar espacios en este campo.");
					return false;
				}
			}
		</script>
	</body>
</html>