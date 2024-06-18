<?php
    require_once('db.php');
    require ('./Maling/mailing.php');

    $user = isset($_POST['user']) ? filter_input(INPUT_POST,'user',FILTER_SANITIZE_EMAIL)  : 'noUser';
    $email = isset($_POST['email']) ? filter_input(INPUT_POST,'email',FILTER_SANITIZE_STRING) : 'noMail';
    $pass = isset($_POST['pass']) ? filter_input(INPUT_POST,'pass',FILTER_SANITIZE_EMAIL)  : 'noPass';
    $passVerify = isset($_POST['passVerify']) ? filter_input(INPUT_POST,'passVerify',FILTER_SANITIZE_STRING) : 'passVerify';
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['Login'])) {
            if($pass == $passVerify){
                // Verificar si el usuario ya existe
                if (userExists($user) || emailExists($email)) {
                    $error = '<p class="error">El usuario/email ya existe. Cambia el nombre del usuario/email !</p>';
                } else {
                    $numeroAleatorio = rand(0, 64);
                    // Abrir Sesion
                    session_start();
                    $_SESSION['correo'] = $email; // Guarda el correo del register en la sesión

                    // Agregar el usuario a la base de datos
                    $hashpass = password_hash($pass, PASSWORD_DEFAULT);
                    $error = afegirUser($user, $email, $_POST['nom'], $_POST['cognom'], $hashpass, $conn, $numeroAleatorio);
                    if (strpos($error, 'error') === false) {
                        CorreoVerificación($email, $user);
                        header("Location: index.php");
                        exit();
                    }
                }
            } else {
                $error = '<p class="error">La contraseña no coincide</p>';
            }
        }
    }

    $conn=null;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="initial-scale=1, maximum-scale=1">
        <title>Portal Login Eduhack</title>
        <link rel="stylesheet" type="text/css" href="./css/register.css" />
        <link rel="icon" href="./img/LOGO.png" />
    </head>
    <body>
        <div id="gif-background"></div>
        <div id="pagewrapper">
            <h1>Register Eduhack</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
                <input type="text" name="user" placeholder="Introduce tu Username" require>
                <input type="email" name="email" placeholder="Introduce tu correo" require>
                <input type="nombre" name="nom" placeholder="Introduce tu nombre">
                <input type="cognom" name="cognom" placeholder="Introduce tu apellido">
                <input type="password" name="pass" placeholder="Introduce tu contraseña" require>
                <input type="password" name="passVerify" placeholder="Verifica tu contraseña" require>
                <button type="submit" name="Login" value="Boton"><span><strong>Registrar</strong></span></button>
                <?=$error?>
            </form>
        </div>
    </body>
    <script src="./js/index.js"></script>
</html>