<?php
// Definir as variáveis de ambiente no código (se você não estiver usando .env)
putenv("DB_HOST=localhost");
putenv("DB_NAME=cryptoproject");
putenv("DB_USER=root");
putenv("DB_PASS=");

// Definir as credenciais de conexão diretamente 
$host = 'localhost';  
$dbname = 'cryptoproject';  
$username = 'root';  
$password = '';  

// Definir o DSN com suporte para UTF-8
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Criação da conexão usando PDO
    $conn = new PDO($dsn, $username, $password); // Aqui a variável foi alterada para $conn pois tava dando convergencia

    // Configurações adicionais, como tratamento de erros
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Avisa se a erro ao se conectar com o banco de dados
    error_log($e->getMessage()); 
    die("Erro na conexão com o banco de dados. Tente novamente mais tarde.");
}
?>
