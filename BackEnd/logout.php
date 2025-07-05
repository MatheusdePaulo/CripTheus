<?php
session_start();

// Destrói completamente a sessão
$_SESSION = array();
session_destroy();

// Redireciona para a tela de login
header("Location: ../FrontEnd/tela1.php");
exit();
?>