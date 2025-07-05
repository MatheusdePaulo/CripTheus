<?php
session_start();
include('../BackEnd/conexao.php');

// Verificar se já existe um admin
$stmt = $conn->query("SELECT COUNT(*) FROM usuarios WHERE is_admin = 1");
$adminExists = $stmt->fetchColumn() > 0;

// Se já existe admin, redirecionar
if ($adminExists) {
    header("Location: login.php");
    exit();
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    
    // Validações
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Nome de usuário é obrigatório";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail inválido";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "A senha deve ter pelo menos 8 caracteres";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "As senhas não coincidem";
    }
    
    // Se não houver erros, criar o admin
    if (empty($errors)) {
        // Verificar se usuário já existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $userExists = $stmt->fetchColumn() > 0;
        
        if (!$userExists) {
            // Hash da senha
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Inserir como admin
            $stmt = $conn->prepare("INSERT INTO usuarios (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
            $stmt->execute([$username, $email, $hashedPassword]);
            
            if ($stmt->rowCount() > 0) {
                $success = "Administrador criado com sucesso!";
                // Redirecionar após 3 segundos
                header("refresh:3;url=login.php");
            } else {
                $errors[] = "Erro ao criar administrador";
            }
        } else {
            $errors[] = "Usuário ou e-mail já cadastrado";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta Administrativa - CripTheus</title>
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
            background-image: url('/img/fundo12.webp');
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
        
        /* HEADER - Padrão do sistema */
        .header-container {
            background-color: rgba(0, 0, 0, 0.9);
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 15px 30px;
            height: 70px;
        }
        
        .logo {
            width: 120px;
            height: auto;
            position: absolute;
            left: 30px;
        }
        
        .welcome-message {
            color: white;
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            margin: 0 auto;
            text-align: center;
        }
        
        /* Container principal */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        /* Card de cadastro admin */
        .admin-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        
        .admin-title {
            color: #4a488c;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .admin-subtitle {
            color: #63629f;
            margin-bottom: 30px;
            font-size: 1.2rem;
        }
        
        .admin-icon {
            font-size: 3rem;
            color: #4a488c;
            margin-bottom: 20px;
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
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            border-color: #63629f;
            outline: none;
            box-shadow: 0 3px 15px rgba(99, 98, 159, 0.3);
        }
        
        .admin-button {
            background-color: #63629f;
            color: white;
            padding: 15px;
            width: 100%;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .admin-button:hover {
            background-color: #4a488c;
            transform: translateY(-2px);
        }
        
        /* Mensagens de erro */
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
            background-color: #ffebee;
            padding: 15px;
            border-radius: 10px;
            text-align: left;
        }
        
        .success-message {
            color: #28a745;
            margin-bottom: 20px;
            font-weight: bold;
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 10px;
        }
        
        /* Requisitos de senha */
        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            text-align: left;
            font-size: 0.9rem;
            color: #666;
        }
        
        .password-requirements h4 {
            margin-bottom: 8px;
            color: #4a488c;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .requirement i {
            margin-right: 8px;
            font-size: 0.8rem;
        }
        
        .text-success {
            color: #28a745;
        }
        
        /* Barra de força da senha */
        .password-strength {
            height: 5px;
            background: #eee;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            background: #e74c3c;
            transition: all 0.3s;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            text-align: right;
        }
        
        /* Versão mobile */
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
                font-size: 18px;
                padding-left: 20px;
            }
            
            .admin-card {
                padding: 30px 20px;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <!-- Cabeçalho padrão -->
    <div class="header-container">
        <img src="/img/logo01.png" alt="Logo CripTheus" class="logo">
        <div class="welcome-message">Configuração Inicial</div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-container">
        <div class="admin-card">
            <div class="admin-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="admin-title">Criar Conta Administrativa</h1>
            <p class="admin-subtitle">Primeiro acesso ao sistema</p>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <p><?= $success ?></p>
                    <p>Redirecionando para login...</p>
                </div>
            <?php else: ?>
                <form id="adminForm" method="POST">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Nome de Administrador</label>
                        <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> E-mail Administrativo</label>
                        <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Senha de Administrador</label>
                        <input type="password" id="password" name="password" placeholder="Crie uma senha forte" required>
                        
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="passwordStrengthText">Força da senha: muito fraca</div>
                        
                        <div class="password-requirements">
                            <h4>Requisitos de senha:</h4>
                            <div class="requirement" id="req-length">
                                <i class="fas fa-circle"></i> Mínimo de 8 caracteres
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <i class="fas fa-circle"></i> Pelo menos 1 letra maiúscula
                            </div>
                            <div class="requirement" id="req-number">
                                <i class="fas fa-circle"></i> Pelo menos 1 número
                            </div>
                            <div class="requirement" id="req-special">
                                <i class="fas fa-circle"></i> Pelo menos 1 caractere especial
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirmar Senha</label>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirme sua senha" required>
                    </div>
                    
                    <button type="submit" class="admin-button">
                        <i class="fas fa-user-plus"></i> CRIAR CONTA ADMINISTRATIVA
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Validação de força da senha
        const passwordInput = document.getElementById('password');
        const passwordStrengthBar = document.getElementById('passwordStrengthBar');
        const passwordStrengthText = document.getElementById('passwordStrengthText');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Atualiza os ícones de requisitos
            document.getElementById('req-length').querySelector('i').className = 
                password.length >= 8 ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            
            document.getElementById('req-uppercase').querySelector('i').className = 
                /[A-Z]/.test(password) ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            
            document.getElementById('req-number').querySelector('i').className = 
                /\d/.test(password) ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            
            document.getElementById('req-special').querySelector('i').className = 
                /[!@#$%^&*]/.test(password) ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            
            // Calcula força da senha
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/\d/.test(password)) strength += 25;
            if (/[!@#$%^&*]/.test(password)) strength += 25;
            
            // Atualiza a barra de força
            passwordStrengthBar.style.width = strength + '%';
            
            // Atualiza cor e texto
            if (strength < 25) {
                passwordStrengthBar.style.backgroundColor = '#e74c3c';
                passwordStrengthText.textContent = 'Força da senha: muito fraca';
            } else if (strength < 50) {
                passwordStrengthBar.style.backgroundColor = '#ff9800';
                passwordStrengthText.textContent = 'Força da senha: fraca';
            } else if (strength < 75) {
                passwordStrengthBar.style.backgroundColor = '#f1c40f';
                passwordStrengthText.textContent = 'Força da senha: média';
            } else if (strength < 100) {
                passwordStrengthBar.style.backgroundColor = '#2ecc71';
                passwordStrengthText.textContent = 'Força da senha: forte';
            } else {
                passwordStrengthBar.style.backgroundColor = '#27ae60';
                passwordStrengthText.textContent = 'Força da senha: muito forte';
            }
        });
        
        // Validação do formulário
        document.getElementById('adminForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            // Verifica se as senhas coincidem
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }
            
            // Valida requisitos da senha
            if (password.length < 8 || !/[A-Z]/.test(password) || !/\d/.test(password) || !/[!@#$%^&*]/.test(password)) {
                e.preventDefault();
                alert('A senha não atende a todos os requisitos de segurança!');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>