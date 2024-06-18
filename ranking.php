<?php
require_once 'db.php';

session_start(); // Conectamos la sesión
$nom = "";

if (isset($_SESSION['user'])) {
    $nom = $_SESSION['user'];
} else {
    header('Location: index.php');
}

$real = CTFExits();
if ($real == TRUE) {
    $rank = rank();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/ranking.css">
    <link rel="shortcut icon" href="./img/LOGO.png" type="image/x-png">
    <title>EduHacks</title>
</head>
<body>
    <header>
        <nav>
            <a href="home.php"><button class="boton-imagen"><img src="./img/FotoHome.png"></button></a>
        </nav>
    </header>
    <main>
        <section id="info">
            <h2>Ranking:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Usuario</th>
                        <th>Puntuación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($real == TRUE) {
                        if (!empty($rank)) {
                            $position = 1;
                            foreach ($rank as $user) {
                                echo "<tr>";
                                echo "<td>" . $position . "</td>";
                                echo "<td>" . $user['username'] . "</td>";
                                echo "<td>" . $user['Puntuation'] . "</td>";
                                echo "</tr>";
                                $position++;
                            }
                        } else {
                            echo "<tr><td colspan='3'>No hay usuarios registrados</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay ningún CTF registrado</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
