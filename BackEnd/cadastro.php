<?php
// cadastro.php
ini_set('display_errors', 1); // Habilita a exibição de erros
error_reporting(E_ALL); // Exibe todos os erros

session_start(); // Inicia a sessão (deve ser no início do arquivo)

include('conexao.php'); // Incluir arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Verifica se as senhas coincidem
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'As senhas não coincidem. Tente novamente.';
        header('Location: ../FrontEnd/tela2.php?error=password'); // Redireciona de volta para a página de cadastro
        exit(); // Certifique-se de que a execução do código seja interrompida
    }

    // Validação simples do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'E-mail inválido. Tente novamente.';
        header('Location: ../FrontEnd/tela2.php?error=exists');
        exit(); // Interrompe a execução
    }

    // Conectar ao banco de dados
    try {
        // Verificar se o usuário já existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $userExists = $stmt->fetchColumn() > 0;

        if ($userExists) {
            $_SESSION['error'] = 'Usuário ou e-mail já cadastrado.';
            header('Location: ../FrontEnd/tela3.php');
            exit(); // Interrompe a execução
        }

        // Hash da senha para segurança
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Inserir os dados no banco
        $stmt = $conn->prepare("INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);

        // Verificar se o cadastro foi bem-sucedido
        if ($stmt->rowCount() > 0) {
            // Login automático após cadastro
            $userId = $conn->lastInsertId(); // Obtém o ID do usuário recém-cadastrado

            // Armazena os dados do usuário na sessão
            $_SESSION['user'] = [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s') // Você pode ajustar esse valor de acordo com a sua tabela
            ];

            $_SESSION['success'] = 'Usuário cadastrado com sucesso!';
            header('Location: ../FrontEnd/tela3.php'); // Redireciona para a tela3
            exit(); // Certifique-se de interromper a execução do código
        } else {
            $_SESSION['error'] = 'Erro ao tentar inserir os dados.';
            header('Location: cadastro.php');
            exit(); // Interrompe a execução
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erro ao cadastrar: ' . $e->getMessage();
        header('Location: cadastro.php');
        exit(); // Interrompe a execução
    }
}
?>
