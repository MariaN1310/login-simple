<?php
session_start();
require 'php/conexion.php';

if (isset($_REQUEST['registrar'])) {
    $pase = true; // Variable para rastrear si se pasan todas las validaciones

    // Verificar si se proporcionó un correo electrónico
    if (!isset($_POST['email'])) {
        echo "<script type=\"text/javascript\">alert('Debe proporcionar un correo electrónico.'); history.go(-1);</script>";
        $pase = false;
    } else {
        $email = $_POST['email'];
        $_SESSION['email'] = $email;
    }

    // Verificar si se proporcionó un nombre de usuario
    if (!isset($_REQUEST['usuario'])) {
        echo "<script type=\"text/javascript\">alert('Debe proporcionar un nombre de usuario.'); history.go(-1);</script>";
        $pase = false;
    } else {
        $usuario = $_REQUEST['usuario'];
        $_SESSION['usuario'] = $usuario;
    }

    // Verificar si el correo electrónico ya existe en la base de datos
    $correoExiste = $conexion->prepare("SELECT email FROM usuarios WHERE email = ?");
    $correoExiste->bind_param("s", $email);
    $correoExiste->execute();
    $correoExiste->store_result(); // Almacenar el resultado
    if ($correoExiste->num_rows === 1) {
        echo "<script type=\"text/javascript\">alert('El correo electrónico ya existe. Intente uno diferente o inicie sesión.'); history.go(-1);</script>";
        $pase = false;
    }

    // Verificar si el nombre de usuario ya existe en la base de datos
    $usuarioExiste = $conexion->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
    $usuarioExiste->bind_param("s", $usuario);
    $usuarioExiste->execute();
    $usuarioExiste->store_result(); // Almacenar el resultado
    if ($usuarioExiste->num_rows === 1) {
        echo "<script type=\"text/javascript\">alert('El nombre de usuario ya existe. Intente uno diferente o inicie sesión.'); history.go(-1);</script>";
        $pase = false;
    }

    // Si todas las validaciones pasan, continuar con el registro
    if ($pase) {
        $contrasena = $_REQUEST['contrasena'];
        $contrasenaConfirmar = $_REQUEST['contrasenaConfirmar'];

        if ($contrasena !== $contrasenaConfirmar) {
            echo "<script type=\"text/javascript\">alert('Las contraseñas no son iguales. Intente de nuevo.'); history.go(-1);</script>";
        } else {
            $encriptar = password_hash($contrasena, PASSWORD_BCRYPT, ["cost" => 11]);

            $sqlInsertar = $conexion->prepare("INSERT INTO usuarios (email, usuario, contrasena) VALUES (?, ?, ?)");
            $sqlInsertar->bind_param("sss", $email, $usuario, $encriptar);
            if ($sqlInsertar->execute()) {
                // Obtener el ID del usuario recién registrado
                $sqlID = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
                $sqlID->bind_param("s", $usuario);
                $sqlID->execute();
                $id_result = $sqlID->get_result();
                $id_row = $id_result->fetch_assoc();
                $id = $id_row['id'];

                // Generar un número aleatorio y actualizar la cookie y el token en la DB
                $numero_aleatorio = mt_rand(1000000, 999999999);
                $token_new = bin2hex(random_bytes(32));

                $ssql = $conexion->prepare("UPDATE usuarios SET cookie = ?, token = ? WHERE id = ?");
                $ssql->bind_param("isi", $numero_aleatorio, $token_new, $id);
                if ($ssql->execute()) {
                    // Establecer las neuvas cookies
                    setcookie("id", $id, time()+(60*60*24*30), "/");
                    setcookie("random", $numero_aleatorio, time()+(60*60*24*30));

                    // Iniciar sesión y redirigir al usuario
                    $_SESSION['logged'] = "Logged";
                    $_SESSION['usuario'] = $usuario;
                    $_SESSION['id'] = $id;
                    $_SESSION["token"] = $token_new;
                    header("Location: index.php");
                    exit;
                } else {
                    echo "<script type=\"text/javascript\">alert('Error al actualizar la cookie en la base de datos.'); history.go(-1);</script>";
                }
            } else {
                echo "<script type=\"text/javascript\">alert('Error al registrar el usuario. Inténtelo de nuevo más tarde.'); history.go(-1);</script>";
            }
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
					<input type="email" class="form-control" id="reg_email" onkeypress="return ValidarEspacio(event,this)" name="email" placeholder="Correo Electronico" required>
				</div>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
					<input type="text" class="form-control" id="reg_username" onkeypress="return ValidarEspacio(event,this)" name="usuario" placeholder="Usuario" minlength="5" maxlength="40" pattern="[A-Za-z0-9]+" required>
					<small id="help" class="form-text text-muted">
						Letras y números - Sin espacios - Mínimo: 5 - Máximo: 40
					</small>
				</div>
				
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
					<input type="password" class="form-control" id="reg_password" name="contrasena" onkeypress="return ValidarEspacio(event,this)" placeholder="Contraseña" minlength="6" maxlength="30" pattern="[A-Za-z0-9]+" required>
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
			function ValidarEspacio(e, campo) {
				key = e.keyCode ? e.keyCode : e.which;
				if (key == 32) {
					alert("No se pueden usar espacios en este campo.");
					return false;
				}
			}
		</script>
	</body>
</html>