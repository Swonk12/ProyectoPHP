<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $UltimoCTF = LastCTF();
    $UltimoIdCTF = CTFID($UltimoCTF["Name"]);
    $archivos = getRutaFiles($UltimoIdCTF);


    $zipName = 'archivos_descarga.zip';

    // Crear un nuevo archivo ZIP
    $zipName = 'archivos_descarga.zip';

    // Abrir o crear un nuevo archivo ZIP manualmente
    $zip = fopen($zipName, 'w');
    if ($zip !== false) {
        // Recorrer cada archivo y agregarlo al archivo ZIP
        foreach ($archivos as $archivo) {
            if (file_exists($archivo)) {
                // Obtener el contenido del archivo
                $contenido = file_get_contents($archivo);
                // Agregar el contenido al archivo ZIP
                fwrite($zip, $contenido);
            }
        }
        fclose($zip);

        // Establecer encabezados para la descarga del archivo ZIP
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipName);
        header('Content-Length: ' . filesize($zipName));

        // Leer el archivo ZIP para descargarlo
        readfile($zipName);

        // Eliminar el archivo ZIP del servidor
        unlink($zipName);
        exit;
    } else {
        echo 'Error al crear el archivo ZIP.';
    }
}

?>
