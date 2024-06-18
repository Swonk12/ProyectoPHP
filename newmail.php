<?php
    require_once('db.php');

    session_start(); // Conectamos la sesión
    $nom = "";
    $nom = $_SESSION['user'];
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mail = $_POST['mail'];
        if(isset($_POST['Login'])) {
            if(!mailExists($mail)){
                newmail($nom, $mail);
                header("Location: profile.php");
            } else {
                $error = '<p class="error">El correo ya existe my frind</p>';
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
        <title>New mailing</title>
        <link rel="stylesheet" type="text/css" href="./css/newname.css" />
        <link rel="icon" href="./img/LOGO.png" />
    </head>
    <body>
        <div id="gif-background"></div>
        <div id="pagewrapper">
            <h1>New email: </h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
                <input type="nombre" name="mail" placeholder="Introduce tu nuevo correo">
                <button type="submit" name="Login" value="Boton"><span><strong>Set email</strong></span></button>
                <?=$error?>
            </form>
        </div>  
    </body>
    <script src="./js/index.js"></script>
</html>