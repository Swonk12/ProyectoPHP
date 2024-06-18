<?php
require_once('db.php');
require_once('./Maling/resetPasswordSend.php');
// Verifica si s'han passat els paràmetres correctes
if(isset($_GET['user']) && isset($_GET['mail']) && isset($_GET['code'])) {

    // Crear cookie
    $datos_cookie = array(
        'resetPassuser' => $_GET['user'],
        'resetPassMail' => $_GET['mail'],
        'resetCode' => $_GET['code']
    );
    
    // Serializa los datos para almacenarlos en la cookie
    $valor_cookie = serialize($datos_cookie);
        
    // Establece la cookie
    setcookie('ResetPass', $valor_cookie, time() + 30 * 24 * 60 * 60, "/");

} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_COOKIE['ResetPass'])) {
        $datos_cookie = unserialize($_COOKIE['ResetPass']);
    
        // Accede a los valores individuales
        $resetPassuser = $datos_cookie['resetPassuser'];
        $resetPassMail = $datos_cookie['resetPassMail'];
        $resetCode = $datos_cookie['resetCode'];
    }

    $TimeOr = date('Y-m-d H:i:s');
    $resetCode = $_POST['code'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $WhatPass = NewPass($resetPassuser,$resetPassMail,$newPassword, $TimeOr, $resetCode);

    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/ResetPassword.css">
    <link rel="icon" href="./img/LOGO.jpg" />
    <title>Reset Password</title>
    <script>
        function validateForm() {
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            if (newPassword != confirmPassword) {
                alert("Las contraseñas no coinciden. Por favor, inténtalo de nuevo.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div id="gif-background"></div>
    <form action="resetPassword.php" method="post" onsubmit="return validateForm()">
        <h1>Reset Your Password</h1>
        <input type="text" id="code" name="code" placeholder="Reset Code" required>
        <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
<script src="./js/index.js"></script>
</html>