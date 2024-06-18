<?php
require_once 'db.php';
$IN = true;
$error = '';
$user = isset($_POST['user']) ? filter_input(INPUT_POST, 'user', FILTER_SANITIZE_EMAIL) : 'noUser';
$pass = isset($_POST['pass']) ? filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING) : 'noPass';
if ($user !== 'noUser' && $pass !== 'noPass') {
    $IN = verificaUser($user, $pass);
}
 
if ($IN === false) {
    $error = '<p class="error">Error en les credencials</p>';
}

// Al presionar el boton de Registrar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['registro'])) {
        header("Location: register.php");
        exit();
    }
}

if (isset($_COOKIE['PHPSESSID'])) {
    session_start();
    if (isset($_SESSION['user'])) {
        header('Location: home.php');
    }
}

if (isset($_COOKIE['ResetPass'])) {
    $error = '<p class="PassReset">Password Modificada</p>';
    setcookie('ResetPass', '', time() - 3600, '/');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login Eduhack</title>
    <link rel="stylesheet" type="text/css" href="./css/index.css">
    <link rel="icon" href="img/LOGO.jpg" />
</head>
<body>

    <?php
// Generar el contenido del popup
$popup_content = "Clicar en el botón para Resetear la Password!!";
?>
    <div id="gif-background"></div>
    <div id="pagewrapper">
        <div class="logo">
            <img src="./img/LOGO.jpg" alt="Logo">
        </div>
        <h1>Login Eduhack</h1>
        <form method="post">
            <input type="text" name="user" placeholder="Enter your UserName">
            <input type="password" name="pass" placeholder="Enter your password">
            <button type="submit" class="button"><span>Login</span></button>
            <button type="submit" class="button" name="registro" value="Boton2" formaction="register.php"><span>Register</span></button>
            <button type="button" id="mostrarPopupBtn"><span>Forgot Password?</span></button>
            <div id="popupContainer">
                <!-- Contenido del popup generado por PHP -->
                <?php echo $popup_content; ?>
                <br>
                <!-- Campo para introducir el correo -->
                <form>
                    <!-- Botón para Enviar el correo -->
                    <button type="submit" id="Botones" formaction="ResetPasswordProcess.php">Send Reset Password Email</button>
                    <br>
                    <!-- Botón para cerrar el popup -->
                    <button type="button" id="cerrarPopupBtn">Cerrar</button>
                </form>
            </div>
            <?=$error?>
        </form>
    </div>


</body>
<script src="./js/index.js"></script>
</html>
