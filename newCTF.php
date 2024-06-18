<?php
require_once 'db.php';

if (isset($_COOKIE['PHPSESSID'])) {
    session_start();
    if (isset($_SESSION['user'])) {
        $nom = $_SESSION['user'];
    }
}
// Verificar si se ha enviado el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar los campos del formulario
    $name = isset($_POST['nombre']) ? filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING) : 'noName';
    $descripcion = isset($_POST['descripcion']) ? filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING) : 'noDescripcion';
    $flag = isset($_POST['flag']) ? filter_input(INPUT_POST, 'flag', FILTER_SANITIZE_STRING) : 'noFlag';
    $value = isset($_POST['value']) ? filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING) : 'noValue';
    $category = isset($_POST['category']) ? filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING) : 'noValue';
    $FounderUser = isset($nom) ? filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING) : 'noUser';

    // Añadir CTF 
    afegirCTF($name, $descripcion, $flag, $value, $FounderUser);

    updateCategory($category);

    $idCategory = IDCATEGORY($category);
    $idCTF = CTFID($name);

    updateClasificate($idCTF, $idCategory);

    // Verificar si se han enviado archivos
    if(isset($_FILES['archivos']) || CTFName($name) == false)  {
        // Obtener el número de archivos subidos
        $num_archivos = count($_FILES['archivos']['name']);

        // Saber ID
        $id = CTFID($name);

        // Iterar sobre cada archivos subido
        for($i = 0; $i < $num_archivos; $i++) {
            $nombre_archivos = $_FILES['archivos']['name'][$i];
            $nombre_temporal = $_FILES['archivos']['tmp_name'][$i];
            $error_archivos = $_FILES['archivos']['error'][$i];

            // Verificar si no hubo errores al subir el archivos
            if($error_archivos === 0) {
                $ruta_destino = './Archivos/' . $id . '/';
                if (!file_exists($ruta_destino)) {
                    mkdir($ruta_destino, 0777, true); // Crea la carpeta y sus subcarpetas si no existen
                }

                // Mover el archivos desde la ubicación temporal al destino deseado
                $ruta_destino = './Archivos/' . $id . '/' . $nombre_archivos;
                move_uploaded_file($nombre_temporal, $ruta_destino);

                // Añadir al DB
                afegirFile($id, $ruta_destino);
                

                echo "El archivos $nombre_archivos se ha subido correctamente.<br>";
                header('Location: home.php');

            } else {
                echo "Hubo un error al subir el archivos $nombre_archivos.<br>";
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
    <link rel="stylesheet" href="./css/newCTF.css">
    <link rel="shortcut icon" href="./img/LOGO.png" type="image/x-png"> 
    <title>EduHacks</title>
</head>
<body>
    <main>
    <div id="gif-background"></div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
        <h1>CREACIÓN DE CTF</h1>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" placeholder="Introduce tu nombre" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" placeholder="Introduce una descripción sobre el reto CTF" required></textarea><br><br>

        <label for="flag">Flag:</label><br>
        <input type="text" id="flag" name="flag" placeholder="Introduce el flag" required><br><br>

        <label for="value">Puntos:</label><br>
        <input type="text" id="value" name="value" placeholder="Puntos del CTF" required><br><br>

        <label for="value">Category:</label><br>
        <input type="text" id="value" name="category" placeholder="Introduce una Categoria" required><br><br>

        <label for="archivos">Subir archivos:</label>
        <input type="file" name="archivos[]" multiple>
        <input type="hidden" name="user" value="<?= htmlspecialchars($nom) ?>">
        <br>
        <br>
        <button type="submit" value="Crear"><span><strong>Crear</strong></span></button>
    
    </form>
    </main>
</body>
<script src="./js/index.js"></script>
</html>