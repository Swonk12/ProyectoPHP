<?php
    require "variables.php";
    function getDBConnection($dbConn=DB_CONN,$user=DB_USER,$pass=DB_PASS){
        $conn = null;
        try{
            $conn  = new PDO($dbConn,$user,$pass,[PDO::ATTR_PERSISTENT=>true]);
        }finally{
            return $conn;
        }
    }

    function verificaUser($user, $pass){
        $db = getDBConnection();
        $sql = "SELECT username, passHash, active FROM users WHERE username = ?";
        $IN = false;
     
        try{
            $preparada = $db->prepare($sql);
            $preparada->execute(array($user));
    
            if($preparada->rowCount() > 0){
                $fila = $preparada->fetch(PDO::FETCH_ASSOC);
                $hashAlmacenado = $fila['passHash'];
                $activated = $fila['active'];
    
                // Verifica la contraseña utilizando password_verify
                if(password_verify($pass, $hashAlmacenado) && $activated == 1){ // De normal deberia de estar en 1
                    // Login completado correctamente a partir de esta línea
                    session_start(); // Creamos la sesión
                    $IN = true;
                    try {
                        $fecha_actual = date("Y-m-d H:i:s");
                        $query = "UPDATE users SET lastSignIn = :lastFecha WHERE username = :usuario";
                        $preparada = $db->prepare($query);
                        $preparada->execute([
                            ':lastFecha' => $fecha_actual,
                            ':usuario' => $user
                        ]);
            
                    } catch(PDOException $e) {
                        return 'Error con la BD: ' . $e->getMessage();
                    }
                    $_SESSION['user'] = $user; // Guarda el username en la sesión
                    header('Location: home.php');
                    exit(); 
                }
            }
        } catch(PDOException $error){
            echo 'Error amb la BDs: ' . $error->getMessage();
        } finally {
            // Cerramos la conexión a la base de datos aquí
            return $IN;
        }
    }

    // Añade este usuario con los parametros introducidos a la db
    function afegirUser($user, $email, $nom, $cognom, $pass, $conn, $activationCode) {
        $db = getDBConnection();
        try {
            $fecha_actual = date("Y-m-d H:i:s");
            $query = "INSERT INTO users (username, mail, passHash, userFirstName, userLastName, creationDate, active, activationCode) VALUES (:user, :email, :pass, :nom, :cognom, :creationDate, 0, :activationCode)";
            $preparada2 = $db->prepare($query);
            $preparada2->execute([
                ':user' => $user,
                ':email' => $email,
                ':nom' => $nom,
                ':cognom' => $cognom,
                ':pass' => $pass,
                ':creationDate' => $fecha_actual,
                ':activationCode' => $activationCode
            ]);
            
            // Comprobar si se insertó correctamente
            if ($preparada2->rowCount() > 0) {
                return '<p>Usuario añadido correctamente.</p>';
            } else {
                return '<p class="error">Error al añadir usuario.</p>';
            }
        } catch(PDOException $e) {
            return 'Error con la BD: ' . $e->getMessage();
        }
    }
    
    // Verifica si el usuario ya existe en la db
    function userExists($user) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE username = :user";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':user' => $user]);
        return $usuaris->rowCount() > 0;
    }

    // Verifica si el email ya existe en la db
    function emailExists($email) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE mail = :email";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':email' => $email]);
        return $usuaris->rowCount() > 0;
    }

    // Retorna el codigo de verificación del usuario que le hayamos introducido
    function CodigoVerificaciónUsr($email, $user) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE mail = :email AND username = :user";
        $usuaris = $db->prepare($query);
        $usuaris->execute([
            ':email' => $email,
            ':user' => $user
            ]);
        $fila = $usuaris->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            return $fila['activationCode'];
        }
    }

    function CodigoVerificaciónUsrMail($email) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE mail = :email";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':email' => $email]);
        $fila = $usuaris->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            return $fila['activationCode'];
        }
    }

    function ActivarUsuario($email) {
        $db = getDBConnection();
        $fecha_activacion = date("Y-m-d H:i:s");
        $query = "UPDATE users SET active = '1', activationDate = :activationDate WHERE mail = :email";
        $statement = $db->prepare($query);
        $resultado = $statement->execute([
            ':email' => $email,
            ':activationDate' => $fecha_activacion
        ]);
    
        if ($resultado) {
            return "active";
        } else {
            // Manejar el caso de error, por ejemplo, lanzar una excepción
            throw new Exception("Error al activar el usuario.");
        }
    }
    
    // Actualizar la Password
    function codeSET($user,$email,$resetPassCode) {
        $db = getDBConnection();
        $query = "UPDATE users SET resetPassCode = :resetPassCode WHERE username = :user AND mail = :email";
        $usuaris = $db->prepare($query);
        $usuaris->execute([
            ':user' => $user,
            ':email' => $email,
            ':resetPassCode' => $resetPassCode
        ]);
        return $usuaris->rowCount() > 0;
    }

    function TimeSET($user, $email) {
        $db = getDBConnection();
        $expiryTime = date('Y-m-d H:i:s', strtotime('+30 minutes')); // Obtiene la hora actual y suma 30 minutos
        $query = "UPDATE users SET resetPassExpiry = :expiryTime WHERE username = :user AND mail = :email";
        $usuaris = $db->prepare($query);
        $usuaris->execute([
            ':user' => $user,
            ':email' => $email,
            ':expiryTime' => $expiryTime
        ]);
        return $usuaris->rowCount() > 0;
    }   

    function NewPass($user, $email, $pass, $resetTime, $resetCode) {
        $db = getDBConnection();
        
        // Consulta para actualizar la contraseña con la verificación de tiempo
        $query = "UPDATE users 
                  SET passHash = :pass 
                  WHERE username = :user AND mail = :email AND resetPassCode = :resetCode AND resetPassExpiry >= :resetTime";
    
        $usuaris = $db->prepare($query);
        $hashpass = password_hash($pass, PASSWORD_DEFAULT);
        $usuaris->execute([
            ':pass' => $hashpass,
            ':user' => $user,
            ':email' => $email,
            ':resetCode' => $resetCode,
            ':resetTime' => $resetTime
        ]);
    
        // Verificar si se actualizó la contraseña correctamente
        return $usuaris->rowCount() > 0;
    }

    function afegirCTF($name, $descripcion, $flag, $value, $FounderUser) {
        $db = getDBConnection();
        try {
            $fecha_actual = date("Y-m-d H:i:s");
            $query = "INSERT INTO ChallengeCTF (Name, Description, Flag, DatePublish,  Value, FounderUser) VALUES (:CTFName, :Descr, :Flag, :DatePublish, :Val, :FounderUser)";
            $preparada2 = $db->prepare($query);
            $preparada2->execute([
                ':CTFName' => $name,
                ':Descr' => $descripcion,
                ':Flag' => $flag,
                ':DatePublish' => $fecha_actual,
                ':Val' => $value,
                ':FounderUser' => $FounderUser
            ]);
            
            // Comprobar si se insertó correctamente
            if ($preparada2->rowCount() > 0) {
                return '<p>CTF añadido correctamente</p>';
            } else {
                return '<p class="error">Error al añadir el CTF.</p>';
            }
        } catch(PDOException $e) {
            return 'Error con la BD: ' . $e->getMessage();
        }
    }

    function CTFID($CTFName) {
        $db = getDBConnection();
        $query = "SELECT IdCTF FROM ChallengeCTF WHERE Name = :CTFName";
        $statement = $db->prepare($query);
        $statement->execute([':CTFName' => $CTFName]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return $row['IdCTF'];
        } else {
            return false;
        }
    }

    function afegirFile($id, $ruta_destino){
        $db = getDBConnection();
        try {
            $query = "INSERT INTO CTFFiles (URL, IdCTF) VALUES (:Pathing, :idCTF)";
            $preparada2 = $db->prepare($query);
            $preparada2->execute([
                ':idCTF' => $id,
                ':Pathing' => $ruta_destino
            ]);
            return $preparada2->rowCount() > 0;
        } catch(PDOException $e) {
            return 'Error con la BD: ' . $e->getMessage();
        }

    }
    
    function SelectAllUsers ($user) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE username = :user";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':user' => $user]);
        $fila = $usuaris->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            return $fila;
        }
    }

    function CTFinProcess($iduser) {
        try {
            $db = getDBConnection();
            $query = "SELECT * FROM CTFinProcess WHERE iduser = :iduser";
            $usuaris = $db->prepare($query);
            $usuaris->execute([':iduser' => $iduser]);
            
            // Obtener todas las filas como un array asociativo
            $results = $usuaris->fetchAll(PDO::FETCH_ASSOC);
            
            // Verificar si se recuperaron resultados
            if (!empty($results)) {
                return $results;
            } else {
                // No se encontraron registros para el iduser dado
                return null;
            }
        } catch (PDOException $e) {
            // Manejar errores de conexión o de consulta
            echo "Error al ejecutar la consulta: " . $e->getMessage();
            return null;
        }
    }
    
    function CTFExits() {
        $db = getDBConnection();
        $query = "SELECT * FROM ChallengeCTF";
        $usuaris = $db->prepare($query);
        $usuaris->execute();
        return $usuaris->rowCount() > 0;
    }

    function obtenerFlag($idCTF) {
        $db = getDBConnection();
        $query = "SELECT flag FROM challengectf WHERE idCTF = :idCTF";
        $statement = $db->prepare($query);
        $statement->execute([':idCTF' => $idCTF]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    function Upplayer($user,$points) {
        $db = getDBConnection();
        $query = "UPDATE users SET Puntuation = :points WHERE username = :usuario";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':points' => $points,
            ':usuario' => $user
        ]);
    }

    function rank() {
        try {
            $db = getDBConnection();
            $query = "SELECT username, Puntuation FROM users ORDER BY Puntuation DESC";
            $rank = $db->prepare($query);
            $rank->execute();
            
            // Obtener todas las filas como un array asociativo
            $results = $rank->fetchAll(PDO::FETCH_ASSOC);
            
            // Verificar si se recuperaron resultados
            if (!empty($results)) {
                return $results;
            } else {
                // No se encontraron registros para el iduser dado
                return null;
            }
        } catch (PDOException $e) {
            // Manejar errores de conexión o de consulta
            echo "Error al ejecutar la consulta: " . $e->getMessage();
            return null;
        }
    }

    function maskEmail($email) {
        $email_parts = explode("@", $email);
        $local_part = $email_parts[0];
        $domain_part = $email_parts[1];
    
        // Mask local part
        $local_part_length = strlen($local_part);
        $masked_local_part = substr($local_part, 0, 3) . str_repeat('*', $local_part_length - 3);
    
        // Mask domain part
        $domain_parts = explode('.', $domain_part);
        $masked_domain_part = str_repeat('*', strlen($domain_parts[0])) . '.' . $domain_parts[1];
    
        return $masked_local_part . '@' . $masked_domain_part;
    }

    function newName($nom, $user) {
        $db = getDBConnection();
        $query = "UPDATE users SET username = :newname WHERE username = :oldname";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':newname' => $user,
            ':oldname' => $nom
        ]);
    }

    function mailExists($mail) {
        $db = getDBConnection();
        $query = "SELECT * FROM users WHERE mail = :correo";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':correo' => $mail]);
        return $usuaris->rowCount() > 0;
    }

    function newmail($nom, $mail) {
        $db = getDBConnection();
        $query = "UPDATE users SET mail = :newmail WHERE username = :nombre";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':newmail' => $mail,
            ':nombre' => $nom
        ]);
    }

    function idUsuario($user) {
        $db = getDBConnection();
        $query = "SELECT iduser FROM users WHERE username = :user";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':user' => $user]);
        $fila = $usuaris->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            return $fila;
        }        
    }

    function LastCTF() {
        try {
            $db = getDBConnection();
            $query = "SELECT * FROM ChallengeCTF ORDER BY IdCTF DESC;";
            $usuaris = $db->prepare($query);
            $usuaris->execute();
            $fila = $usuaris->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($fila)) {
                return $fila;
            }
        } catch (PDOException $e) {
            // Manejar errores de conexión o de consulta
            echo "Error al ejecutar la consulta: " . $e->getMessage();
            return null;
        }

    }
    function UpCTF($user,$idCTF) {
        $db = getDBConnection();
        $dateCompleted = date('Y-m-d'); // Obtiene la fecha actual en formato YYYY-MM-DD
        $query = "INSERT INTO CompletedCTF (DateCompleted, iduser, IdCTF) VALUES ('$dateCompleted', :usuario, :id)";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':id' => $idCTF,
            ':usuario' => $user['iduser']
        ]);
    } 

    function CompCTF($user,$idCTFop) {
        $db = getDBConnection();
        $query = "SELECT * FROM CompletedCTF WHERE iduser = :usuario AND IdCTF = :id";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':id' => $idCTFop,
            ':usuario' => $user['iduser']
        ]);
        return $preparada->rowCount() > 0;
    }    

    function AnotherUPCTF($user,$idCTFop) {
        $db = getDBConnection();
        $query = "SELECT * FROM CompletedCTF WHERE iduser = :usuario AND IdCTF = :id";
        $preparada = $db->prepare($query);
        $preparada->execute([
            ':id' => $idCTFop,
            ':usuario' => $user['iduser']
        ]);
        return $preparada->rowCount() > 0;
    }    

    // ! politus
    function updateCategory($category) {
        $db = getDBConnection();
        try {
            $query = "INSERT INTO Category (Name) VALUES (:name)";
            $preparada2 = $db->prepare($query);
            $preparada2->execute([
                ':name' => $category,
            ]);
            return 'Categoría insertada correctamente.';
        } catch(PDOException $e) {
            return 'Error con la BD: ' . $e->getMessage();
        }
    }
    

    function CTFFilterCategory($category) {
        $db = getDBConnection();
        $query = "SELECT f.IdCTF, f.Name AS ChallengeName, f.Description, f.Flag, f.DatePublish, f.Value, f.FounderUser, y.IdCategory, y.Name AS CategoryName FROM Clasificate c JOIN ChallengeCTF f ON c.IdCTF = f.IdCTF JOIN Category y ON c.IdCategory = y.IdCategory WHERE y.Name = :category";
        $usuaris = $db->prepare($query);
        $usuaris->execute([':category' => $category]);
        $fila = $usuaris->fetchAll(PDO::FETCH_ASSOC);
        if ($fila) {
            return $fila;
        }
    }

    function IDCATEGORY ($category){
        $db = getDBConnection();
        $query = "SELECT IdCategory FROM Category WHERE Name = :CategoryName";
        $statement = $db->prepare($query);
        $statement->execute([':CategoryName' => $category]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $row['IdCategory'];
        } else {
            return false;
        }

    }

    function updateClasificate($idCTF, $idCategory) {
        $db = getDBConnection();
        try {
            $query = "INSERT INTO Clasificate (IdCTF, IdCategory) VALUES (:idCTF, :idCategory)";
            $statement = $db->prepare($query);
            $statement->execute([
                ':idCTF' => $idCTF,
                ':idCategory' => $idCategory,
            ]);
            return 'Clasificate insertada correctamente.';
        } catch(PDOException $e) {
            return 'Error al insertar en la tabla Clasificate: ' . $e->getMessage();
        }
    }
    
    function NewCTFinProcess($name, $date, $ctfid) {
        $db = getDBConnection();
        try {
            $query = "INSERT INTO ctfinprocess (Name, StartDate, IdCTF) VALUES (:Name, :StartDate, :IdCTF)";
            $statement = $db->prepare($query);
            $statement->execute([
                ':Name' => $name,
                ':StartDate' => $date,
                ':IdCTF' => $ctfid,
            ]);
            return 'Clasificate insertada correctamente.';
        } catch(PDOException $e) {
            return 'Error al insertar en la tabla Clasificate: ' . $e->getMessage();
        }

    }

    function getRutaFiles ($IdCTF){
        $db = getDBConnection();
        $query = "SELECT URL FROM ctffiles WHERE IdCTF = :IdCTF";
        $statement = $db->prepare($query);
        $statement->execute([':IdCTF' => $IdCTF]);
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($row) {
            return $row;
        } else {
            return false;
        }

    }

    function estadoProcessCTF ($IdCTF) {
        $db = getDBConnection();
        $query = "SELECT * FROM ctfinprocess WHERE IdCTF = :IdCTF";
        $statement = $db->prepare($query);
        $statement->execute([':IdCTF' => $IdCTF]);
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($row) {
            $row = true;
            return $row;
        } else {
            return false;
        }

    }


    function estadoCompletedCTF ($IdCTF) {
        $db = getDBConnection();
        $query = "SELECT * FROM completedctf WHERE IdCTF = :IdCTF";
        $statement = $db->prepare($query);
        $statement->execute([':IdCTF' => $IdCTF]);
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($row) {
            $row = true;
            return $row;
        } else {
            return false;
        }

    }

    function ObtenerPuntuacion ($IdCTF) {
        $db = getDBConnection();
        $query = "SELECT * FROM challengectf WHERE IdCTF = :IdCTF";
        $statement = $db->prepare($query);
        $statement->execute([':IdCTF' => $IdCTF]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row["Value"];
    }
?> 