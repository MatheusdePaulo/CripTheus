<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        // Gera e retorna o hash criptografado
        echo password_hash($mensagem, PASSWORD_DEFAULT);
    } else {
        echo "Por favor, insira uma mensagem válida.";
    }
}
?>