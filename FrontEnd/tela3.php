<?php
session_start();

// Redirecionamento corrigido
if (!isset($_SESSION['user'])) {
    header("Location: ../BackEnd/login.php");
    exit();
}

$user = $_SESSION['user'];

if (!isset($_SESSION['senha_criptografada'])) {
    $_SESSION['senha_criptografada'] = password_hash("senhaExemplo", PASSWORD_DEFAULT);
}

$hashMensagemCriptografada = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        $hashMensagemCriptografada = password_hash($mensagem, PASSWORD_DEFAULT);
    } else {
        $hashMensagemCriptografada = "Por favor, insira uma mensagem válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Usuário - CripTheus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body, html {
            height: 100%;
            background-image: url('../img/fundo12.webp'); /* Corrigido */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
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
        
        /* HEADER - Igual ao da tela4 */
        .header-container {
            background-color: rgba(0, 0, 0, 0.9);
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 10px 30px;
            height: 70px;
        }
        
        .logo {
            width: 180px;
            position: absolute;
            left: 50px;
            top: -55px;
            display: block;
            z-index: 2;
        }
        
        .welcome-message {
            color: white;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            margin: 0 auto;
            text-align: center;
            flex-grow: 1;
        }
        
        .admin-info {
            position: fixed;
            top: 20px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            z-index: 1001;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: rgba(99, 98, 159, 0.8);
            padding: 8px 15px;
            border-radius: 30px;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #4a488c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            text-decoration: none;
            font-weight: bold;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 30px;
            margin: 100px auto 50px;
            width: 90%;
            max-width: 1200px;
            text-align: center;
            position: relative;
        }
        
        .user-info {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .user-info p {
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .user-info strong {
            color: #4a488c;
        }
        
        /* Nova seção de informações */
        .info-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #4a488c;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: left;
        }
        
        .info-section h3 {
            color: #4a488c;
            margin-bottom: 15px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-section p {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .security-features {
            background-color: #e9f7ef;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #d4edda;
        }
        
        .security-features h4 {
            color: #155724;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-list {
            padding-left: 20px;
        }
        
        .feature-list li {
            margin-bottom: 8px;
            position: relative;
            list-style-type: none;
            padding-left: 25px;
        }
        
        .feature-list li:before {
            content: "✓";
            color: #28a745;
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        
        .crypto-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #4a488c;
            margin-bottom: 20px;
            font-size: 1.8rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            height: 120px;
            resize: none;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group textarea:focus {
            border-color: #63629f;
            outline: none;
            box-shadow: 0 3px 15px rgba(99, 98, 159, 0.3);
        }
        
        .info {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
            display: block;
        }
        
        .btn {
            background-color: #63629f;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: #4a488c;
            transform: translateY(-2px);
        }
        
        .result-container {
            margin-top: 30px;
        }
        
        .result {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
            word-wrap: break-word;
            border: 1px solid #eee;
        }
        
        .result strong {
            color: #4a488c;
            display: block;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .header-container {
                padding: 10px 15px;
                height: 60px;
            }
            
            .logo {
                width: 90px;
                left: 15px;
            }
            
            .welcome-message {
                font-size: 16px;
                padding-left: 20px;
            }
            
            .admin-info {
                top: 15px;
                right: 15px;
            }
            
            .container {
                margin-top: 110px;
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .info-section, .user-info, .crypto-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <!-- Cabeçalho igual ao da tela4 -->
    <div class="header-container">
        <img src="../img/logo01.png" alt="Logo CripTheus" class="logo">
        <div class="welcome-message">Bem-vindo à CripTheus, seu site de criptografia!</div>
    </div>
    
    <!-- Informações do usuário e botão de logout -->
    <div class="admin-info">
        <div class="user-profile">
            <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <div><?= htmlspecialchars($user['username']) ?></div>
        </div>
        <a href="../BackEnd/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>

    <!-- Conteúdo principal -->
    <div class="container">
        <div class="user-info">
            <p><strong>Nome:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Data de Criação da Conta:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
            <p><strong>Senha Criptografada:</strong> <?= htmlspecialchars($_SESSION['senha_criptografada']) ?></p>
        </div>

        <!-- Nova seção de informações sobre criptografia -->
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> Sobre a Brincadeira de Criptografia</h3>
            <p>Esta ferramenta foi desenvolvida para demonstrar de forma lúdica como funcionam os princípios básicos da criptografia moderna. Aqui você pode experimentar na prática como uma mensagem simples se transforma em um código seguro.</p>
            
            <p>O sistema utiliza o algoritmo <strong>bcrypt</strong>, o mesmo usado para proteger senhas em sistemas profissionais. Cada vez que você criptografa uma mensagem, mesmo que seja a mesma, o resultado será diferente - isso é uma característica intencional de segurança!</p>
            
            <div class="security-features">
                <h4><i class="fas fa-shield-alt"></i> Recursos de Segurança</h4>
                <ul class="feature-list">
                    <li><strong>Hash único:</strong> Cada mensagem gera um código diferente</li>
                    <li><strong>Salt automático:</strong> Adição de valores aleatórios para maior segurança</li>
                    <li><strong>Processo irreversível:</strong> Não é possível decifrar o hash de volta para a mensagem original</li>
                    <li><strong>Proteção contra ataques:</strong> Algoritmo lento propositalmente para dificultar tentativas de quebra</li>
                </ul>
            </div>
            
            <p style="margin-top: 20px;"><strong>Como usar:</strong> Digite qualquer mensagem no campo abaixo e clique em "Criptografar" para ver como ela seria armazenada com segurança em um sistema real.</p>
        </div>

        <div class="crypto-section">
            <h2><i class="fas fa-lock"></i> Brincadeira de Criptografia</h2>

            <form method="post">
                <div class="form-group">
                    <label for="mensagem">Digite a mensagem:</label>
                    <textarea name="mensagem" id="mensagem" placeholder="Escreva aqui sua mensagem..." required></textarea>
                    <span class="info">O algoritmo de criptografia utilizado nesta ferramenta é bcrypt hash.</span>
                </div>
                <button type="submit" class="btn">
                    <i class="fas fa-lock"></i> Criptografar
                </button>
            </form>

            <div class="result-container">
                <div class="result">
                    <strong><i class="fas fa-key"></i> Mensagem criptografada:</strong>
                    <p id="resultText">
                        <?= !empty($hashMensagemCriptografada) ? htmlspecialchars($hashMensagemCriptografada) : "Aqui aparecerá a mensagem criptografada..." ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validação do formulário
        function validateMessage() {
            const message = document.getElementById('mensagem').value.trim();
            if (!message) {
                alert('Por favor, digite uma mensagem para criptografar.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>