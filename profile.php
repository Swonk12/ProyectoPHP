<?php
require_once 'db.php';

session_start(); // Conectamos la sesión
$nom = "";
$info = "";

if (isset($_SESSION['user'])) {
    $nom = $_SESSION['user'];
} else {
    header('Location: index.php');
}

$info = SelectAllUsers($nom);

$real = CTFExits();
if ($real == TRUE) {
    $CountCTFinProcess = 0;
    $CTFinProcess = CTFinProcess($info['iduser']);
    if ($CTFinProcess !== null) {
        $CountCTFinProcess = count($CTFinProcess);
    } else {
        $CountCTFinProcess = 0; 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/profile.css">
    <link rel="shortcut icon" href="./img/LOGO.png" type="image/x-png">
    <title>EduHacks</title>
</head>
<body>
    <header>
        <h1><strong>Perfil del usuario <?=$nom?> </strong></h1>
        <nav>
            <a href="home.php"><button class="boton-imagen"><img src="./img/FotoHome.png"></button></a>
        </nav>
    </header>
    <main>
        <section id="info">
            <h2>Información del usuario: </h2> 
            <img src="./img/anonymous-profile-icon-cartoon-style-vector.jpg" id="Avatar" alt="Avatar">  
            <br> 
            <?php echo "<strong>Usuario: </strong>" . $info['username']; ?>
            <br>
            <?php echo "<strong>Correo: </strong>" . maskEmail($info['mail']); ?>
            <br>
            <?php echo "<strong>Nom Complet: </strong>" . $info['userFirstName'] . $info['userLastName']; ?>
            <br>
            <?php echo "<strong>Data de Creació: </strong>" . $info['creationDate']; ?>
            <br>
            <?php echo "<strong>Puntuació: </strong>" . $info['Puntuation']; ?>
            <br>
            <?php if ($real == TRUE): ?>
                <br>
                <?php echo "<strong>CTF en procéss: </strong>" . $CountCTFinProcess; ?>
                <form action="profile.php" method="post">
                    <input type="submit" id="CTFCourse" name="CTFCourse" value="CTF in Course">
                </form>
            <?php endif; ?>
            <div id="change">
                <a href="newname.php"><button><span>Cambiar Nombre</span></button></a>
                <a href="newmail.php"><button><span>Cambiar Correo</span></button></a>
                <a href="ResetPasswordProcess.php"><button><span>Cambiar Password</span></button></a>
            </div>

            <div id="logout">
                <a href="logout.php"><button class="button logout"><span>Logout</span></button></a>
            </div>
            
        </section>
    </main>
</body>
</html>