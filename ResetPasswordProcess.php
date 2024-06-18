<?php
    require_once('db.php');
    require_once('./Maling/resetPasswordSend.php');

    $user = isset($_POST['user']) ? filter_input(INPUT_POST,'user',FILTER_SANITIZE_EMAIL)  : 'noUser';
    $email = isset($_POST['email']) ? filter_input(INPUT_POST,'email',FILTER_SANITIZE_STRING) : 'noMail';
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['send'])) {
            if (userExists($user) && emailExists($email)) {
                $resetPassCode = bin2hex(random_bytes(16));
                TimeSET($_POST['user'], $_POST['email']);
                PasswordReset($_POST['email'], $_POST['user'], $resetPassCode);
                $error = '<p class="send">El correo a sido enviado correctamente</p>';
                codeSET($_POST['user'], $_POST['email'],$resetPassCode);
            } else {
                $error = '<p class="error">El usuario/email no estan en la base de datos</p>';
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
        <link rel="stylesheet" href="./css/ResetPasswordProcess.css">
        <link rel="icon" href="./img/LOGO.jpg" />
    </head>
    <body>
        <div id="gif-background"></div>
        <div id="pagewrapper">
            <h1>Reset Password</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
                <input type="text" name="user" placeholder="Introduce tu Username" require>
                <input type="email" name="email" placeholder="Introduce tu correo" require>
                <button type="submit" name="send" value="Boton"><span><strong>Send Reset Password Email</strong></span></button>
                <?=$error?>
            </form>
        </div>
    </body>
    <script src="./js/index.js"></script>
</html>