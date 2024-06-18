<?php
    require_once 'db.php';

    session_start(); // Conectamos la sesión
    $nom = "";
    if (isset($_SESSION['user'])) {
        $nom = $_SESSION['user'];
        // $mailTest = $_SESSION['correo']; // Solo funciona si viene del register ya que se introduce en la sesion en ese momento
    } else {
        header('Location: index.php');
    }

    $category = isset($_POST['category']) ? filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING) : 'noValue';

    $CTFClasificate = CTFFilterCategory($category);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['flag']) && $_POST['flag'] != null){
            $flag_usuario = $_POST['flag'];
            $flagOrg = obtenerFlag($_POST['idCTF']);
            $idUser = idUsuario($_SESSION['user']);
            $Repititive = AnotherUPCTF($idUser,$_POST['idCTF']);
            if ($Repititive == false) {
                if ($flag_usuario == $flagOrg['flag']) {
                    echo "¡Flag correcta!";
                    // ! Sumar puntos al Usuario
                    $info = SelectAllUsers($nom); // ! Obtener puntuación que tiene el usuario
                    $points = $info['Puntuation'] + ObtenerPuntuacion($_POST['idCTF']);
                    Upplayer($_SESSION['user'],$points);
                    UpCTF($idUser,$_POST['idCTF']);
                } else {
                    echo "Flag incorrecta. Inténtalo de nuevo.";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/home.css">
    <link rel="shortcut icon" href="./img/LOGO.png" type="image/x-png"> 
    <title>EduHacks</title>
</head>
<body>
    <header>
        <h1><strong>¡ Bienvenido <?= $nom ?> ! </strong></h1>
        <nav>
            <a href="home.php"><button class="boton-imagen"><img src="./img/FotoHome.png"></button></a>
            <a href="newCTF.php?nom=<?= urlencode($nom) ?>"><button class="boton-imagen"><img src="./img/FotoNewCTF.png"></button></a>
            <a href="profile.php"><button class="boton-imagen"><img src="./img/FotoProfile.png"></button></a>
            <a href="ranking.php"><button class="boton-imagen"><img src="./img/ranking.jpg"></button></a>
        </nav>
    </header>
    <main>
        <?php
            $real = CTFExits();
            if ($real == TRUE) {
                $i = 0;
                $question = FALSE;
                do {
                    $UltimoCTF = LastCTF();
                    $count = count($UltimoCTF);
                    $UltimoIdCTF = $UltimoCTF[$i]['IdCTF'];
                    $idUser = idUsuario($_SESSION['user']);
                    $CTFCompleted = CompCTF($idUser,$UltimoIdCTF);
                    if ($CTFCompleted == false){
                        $question = TRUE;
                        break;
                    }
                    $i++;
                } while ($i < $count);
            
                if ($question == TRUE){
                    echo '<h2>Last CTF</h2>';
                    echo '<section id="CTF">';

                    
                    if(isset($_POST['Empezar'])) {
                        $date = date("Y-m-d");
                        NewCTFinProcess($UltimoCTF[$i]["Name"], $date, $UltimoCTF[$i]["IdCTF"]);
                    }

                    $estadoCompletedCTF = estadoCompletedCTF($UltimoIdCTF);     
                    $estadoProcessCTF = estadoProcessCTF($UltimoIdCTF);

                    if ($estadoCompletedCTF == true){
                        echo '<h3 class="LastCTF"> Estado: Completado </h3>';
                    } elseif ( $estadoProcessCTF == true) {
                        echo '<h3 class="LastCTF"> Estado: En proceso </h3>';
                    } else {
                        echo '<h3 class="LastCTF"> Estado: Sin empezar </h3>';
                    }


                    echo '<h3 class="LastCTF">' . $UltimoCTF[$i]["Name"] . ' // Publish Date: ' . $UltimoCTF[$i]["DatePublish"] . ' // Value: ' . $UltimoCTF[$i]["Value"] . "</h3>\n";
                    echo '<h3 class="LastCTF">' . $UltimoCTF[$i]["Description"] . "</h3>";
                    echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST" enctype="multipart/form-data">';
                    echo '<label for="miAreaDeTexto">Flag:  </label>';
                    echo '<input id="IntroducirFlag" name="flag"></input>';
                    echo '<input type="hidden" name="idCTF" value="' . htmlspecialchars($UltimoCTF[$i]["IdCTF"]) . '">';
                    echo '<button type="submit">Send flag</button>';
                    echo '<button name="Empezar" type="submit">Empezar</button>';

                    $archivos = getRutaFiles($UltimoIdCTF);


                    // ! Dividir Ruta

                    function custom_strlen($string) {
                        $length = 0;
                        while (isset($string[$length])) {
                            $length++;
                        }
                        return $length;
                    }

                    for($z=0;$z < count($archivos);$z++){

                        $indice = custom_strlen($archivos[$z]["URL"]) - 1;

                        while ($indice >= 0 && $archivos[$z]["URL"][$indice] !== '/') {
                            $indice--;
                        }
                        if ($indice >= 0) {
                            $nombreArchivo = substr($archivos[$z]["URL"], $indice + 1);
                        } else {
                            $nombreArchivo = $archivos[$z];
                        }
                        
    
                        // ! Boton descarga archivo
                        echo '<a href="' . $archivos[0]["URL"] . '" download="' . $nombreArchivo . '" class="Descargar">Descargar archivo' . $z+1 .'    </a>';
                    }

                    echo '</form>';
                    echo '</section>';

                } else {
                    echo '<h2>No hay CTF, Has completado todos los CTF</h2>';
                }
            } 
        ?>

        <div id="category">
            <form method="post">
                <label for="value">Filter Category:</label><br>
                <input type="text" id="value" name="category" placeholder="Introduce una Categoria" required><br><br>
                <a href="./home.php"><button>Search</button></a>
            </form>
        </div>

        <?php
            if($category != "noValue"){
                echo '<h2>CTFs '. $category .'</h2>';
                for($i=0;$i<count($CTFClasificate);$i++){
                    echo '<section id="CTF">';

                    $estadoProcessCTF2 = estadoProcessCTF(CTFID($CTFClasificate[$i]["ChallengeName"]));
                    $estadoCompletedCTF2 = estadoCompletedCTF(CTFID($CTFClasificate[$i]["ChallengeName"]));
    
                    if ($estadoProcessCTF2 == true){
                        echo '<h3 class="LastCTF"> Estado: En curso </h3>';
                    }
                    elseif ($estadoCompletedCTF2 == true){
                        echo '<h3 class="LastCTF"> Estado: Completado </h3>';
                    } else {
                        echo '<h3 class="LastCTF"> Estado: Sin empezar </h3>';
                    }

                    echo '<h3 class="LastCTF">' . $CTFClasificate[$i]["ChallengeName"] . ' // Publish Date: ' . $CTFClasificate[$i]["DatePublish"] . ' // Value: ' . $CTFClasificate[$i]["Value"] . "</h3>\n";
                    echo '<h3 class="LastCTF">' . $CTFClasificate[$i]["Description"] . "</h3>";
                    echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST" enctype="multipart/form-data">';
                    echo '<label for="miAreaDeTexto">Flag:  </label>';
                    echo '<textarea id="IntroducirFlag" name="flag" rows="0" cols="80"></textarea>';
                    echo '<button type="submit">Send flag</button>';
                    echo '<button name="EmpezarFilter" type="submit">Empezar</button>';

                    // Funciona cuando quiere
                    if(isset($_POST['EmpezarFilter'])) {
                        $date = date("Y-m-d");
                        NewCTFinProcess($CTFClasificate[$i]["ChallengeName"], $date, $UltimoIdCTF);
                    }

                    echo '</form>';
                    echo '</section>';
                }
            }
        ?>

    </main>
</body>
</html>