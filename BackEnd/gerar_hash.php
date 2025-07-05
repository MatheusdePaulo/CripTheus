<?php
$password = 'admin123'; // Coloque a senha que você quer para o admin
$hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Cria o hash da senha

echo $hashedPassword; // Exibe o hash gerado
?>