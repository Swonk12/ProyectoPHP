<?php
    require_once('db.php');

    session_start(); // Conectamos la sesión
    $nom = "";
    $nom = $_SESSION['user'];
    $error = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['nom'];
        if(isset($_POST['Login'])) {
            if(!userExists($user)){
                newName($nom, $user);
                $_SESSION['user'] = $user;
                header("Location: profile.php");
            } else {
                $error = '<p class="error">El usuario ya existe my frind</p>';
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
        <title>New Username</title>
        <link rel="stylesheet" type="text/css" href="./css/newname.css" />
        <link rel="icon" href="./img/LOGO.png" />
    </head>
    <body>
        <div id="gif-background"></div>
        <div id="pagewrapper">
            <h1>New Username: </h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
                <input type="nombre" name="nom" placeholder="Introduce tu nuevo Username">
                <button type="submit" name="Login" value="Boton"><span><strong>Set Name</strong></span></button>
                <?=$error?>
            </form>
        </div>
    </body>
    <script src="./js/index.js"></script>
</html>