<?php
    require "db.php";
    if(isset($_GET['NumeroVerif']) && isset($_GET['email'])) {
        $CodigRealDB = CodigoVerificaciónUsrMail($_GET['email']);
        if($_GET['NumeroVerif'] == $CodigRealDB){
            // Actualizar Estado de la cuenta
            $resultado = ActivarUsuario($_GET['email']);
            if($resultado == "active"){
                $redireccion = header("Location: home.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo</title>
</head>
<body> 
    <h1>PAGINA DE VERIFICACIÓN</h1>
    <?=$redireccion?>
</body>
</html>
