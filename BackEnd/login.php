<?php
session_start();
include('conexao.php'); // Inclui o arquivo de conexão com o banco de dados.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Verifica no banco de dados se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado e se a senha está correta
    if ($user && password_verify($password, $user['password'])) {
        // Armazena as informações do usuário na sessão
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'created_at' => $user['created_at'],
            'is_admin' => $user['is_admin']
        ];
        
        // Redireciona para a tela de administração ou página de usuário comum
        if ($user['is_admin'] == 1) {
            header("Location: ../FrontEnd/tela4.php");
        } else {
            header("Location: ../FrontEnd/tela3.php");
        }
        exit();
    } else {
        echo "Usuário ou senha inválidos. Tente novamente.";
    }
}
?>
