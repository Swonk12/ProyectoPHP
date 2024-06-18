<?php
  session_start();
  setcookie('PHPSESSID','',time()-3600,'/');
  session_destroy();
  header('Location: index.php');
  exit();
?>