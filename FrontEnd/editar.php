<?php
session_start();
include('../BackEnd/conexao.php'); // Inclui o arquivo de conexão com o banco de dados.

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Recupera os dados do usuário
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['edit_id'])) {
    $editId = $_POST['edit_id'];
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);

    // Atualiza os dados do usuário
    $stmt = $conn->prepare("UPDATE usuarios SET username = :username, email = :email WHERE id = :id");
    $stmt->bindParam(':username', $newUsername);
    $stmt->bindParam(':email', $newEmail);
    $stmt->bindParam(':id', $editId);
    $stmt->execute();
    $_SESSION['success'] = 'Dados do usuário atualizados com sucesso!';
    header("Location: tela4.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('/img/fundo12.webp');
            opacity: 0.95;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: -1;
        }

        .welcome-bar {
            background-color: #000000;
            opacity: 0.9;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            width: 100%;
            position: absolute;
            top: -100px;
            z-index: 1;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 100px;
            width: 80%;
            max-width: 900px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            z-index: 1;
        }

        .logo {
            width: 180px;
            position: absolute;
            left: 50px;
            top: -155px;
            display: block;
            z-index: 2;
        }

        .user-info {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: left;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #63629f;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #4a488c;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            display: block;
            width: 100px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .result-container {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <img src="/img/logo01.png" alt="Logo CripTheus" class="logo">
    <div class="welcome-bar">
       CripTheus, Bem-vindo Administrador!
    </div>
    <div class="container">
        <h2>Editar Usuário</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="result-container">
                <p><?php echo $_SESSION['success']; ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo $user['id']; ?>" />
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required />
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required />
            </div>
            
            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="tela4.php" class="logout-btn">Voltar</a>
    </div>
</body>
</html>
