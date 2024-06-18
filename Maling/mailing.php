<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
require_once('db.php');

function CorreoVerificación($email, $user) {
    $mail = new PHPMailer();
    $mail->IsSMTP();

    // Configuración del servidor de Correo
    $mail->SMTPDebug = 0; // Deshabilitar salida detallada para producción
    $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP
    $mail->SMTPAuth   = true; // Habilitar autenticación SMTP
    $mail->Username   = 'pol.ruizm@educem.net'; // Usuario SMTP
    $mail->Password   = 'vkgx jfgc zaot zizb'; // Contraseña SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Habilitar encriptación TLS implícita
    $mail->Port       = 465; // Puerto TCP para conexión

    // Datos del correo electrónico
    $mail->SetFrom('pol.ruizm@educem.net', 'Pol');
    $mail->isHTML(true); // Formato de correo HTML
    $asunto = "Bienvenido " . $user . ", Gracias por registrarte en Eduhack";
    $mail->Subject = $asunto;
    $NumeroVerif = CodigoVerificaciónUsr($email, $user);

    $html = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e2e2e2; border-radius: 10px;'>
        <div style='text-align: center;'>
            <img src='../img/LOGO.jpg' alt='Eduhack' style='width: 150px; margin-bottom: 20px;'>
        </div>
        <h1 style='font-size: 24px; color: #4CAF50;'>¡Verifica tu cuenta ahora, $user!</h1>
        <p>Gracias por registrarte en Eduhack. Para completar el registro, por favor verifica tu cuenta haciendo clic en el enlace de abajo:</p>
        <div style='text-align: center; margin: 20px 0;'>
            <a href='http://localhost:3000/mailCheckAccount.php?NumeroVerif=$NumeroVerif&email=$email' 
               style='display: inline-block; padding: 15px 25px; font-size: 16px; color: white; background-color: #4CAF50; text-decoration: none; border-radius: 5px;'>Activa tu cuenta ahora</a>
        </div>
        <p>Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador:</p>
        <p style='word-break: break-all;'>http://localhost:3000/mailCheckAccount.php?NumeroVerif=$NumeroVerif&email=$email</p>
        <p>Si tienes alguna pregunta, no dudes en contactar con nuestro soporte.</p>
        <p>¡Gracias!</p>
        <p>El equipo de Eduhack</p>
    </div>";

    $mail->Body = $html;

    // Destinatario
    $address = $email; // email a quien se envia el correo (Pasado por la función)
    $nombre = $user; // Nombre a quien se envia el correo (Pasado por la función)
    $copia = 'pol.ruizm@educem.net'; // Persona en copia
    $mail->AddAddress($address, $nombre);
    $mail->addCC($copia); // Agregar copia (CC)
    //$mail->addAttachment('mailing.php'); // Adjuntar archivo - Descomentar si es necesario

    // Envío
    $result = $mail->Send();
    if(!$result){
        echo 'Error: ' . $mail->ErrorInfo;
    } else {
        echo "Correo enviado";
    }
}
?>
