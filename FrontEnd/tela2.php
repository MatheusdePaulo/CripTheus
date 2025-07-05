<?php
session_start();
// Redireciona se já estiver logado E não for admin
if (isset($_SESSION['user']) && !(isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1)) {
    header("Location: tela3.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - CripTheus</title>
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
            background-image: url('../img/fundo12.webp'); <!-- Corrigido -->
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
        
        /* Card de cadastro */
        .register-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .register-title {
            color: #4a488c;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: bold;
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
        
        .register-button {
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
        }
        
        .register-button:hover {
            background-color: #4a488c;
            transform: translateY(-2px);
        }
        
        .login-link {
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #4a488c;
            text-decoration: none;
            font-weight: bold;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        /* Mensagens de erro/sucesso */
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
            display: none;
        }
        
        .success-message {
            color: #28a745;
            margin-bottom: 20px;
            font-weight: bold;
            display: none;
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
            
            .register-card {
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
        <img src="../img/logo01.png" alt="Logo CripTheus" class="logo"> <!-- Corrigido -->
        <div class="welcome-message">Crie sua conta no CripTheus</div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-container">
        <div class="register-card">
            <h1 class="register-title"><i class="fas fa-user-plus"></i> Cadastro</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="display: block;">
                    <?php 
                    $error = $_GET['error'];
                    echo $error === 'password' ? 'As senhas não coincidem!' : 
                          ($error === 'exists' ? 'Usuário já existe!' : 
                          ($error === 'email' ? 'E-mail inválido!' : 'Erro no cadastro!'));
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display: block;">
                    Cadastro realizado com sucesso! <a href="tela1.php">Faça login</a>
                </div>
            <?php endif; ?>
            
            <form id="registrationForm" action="../BackEnd/cadastro.php" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Nome de usuário</label>
                    <input type="text" name="username" id="username" placeholder="Digite seu usuário" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <input type="email" name="email" id="email" placeholder="Digite seu e-mail" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                    
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
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm-password"><i class="fas fa-lock"></i> Confirmar Senha</label>
                    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirme sua senha" required>
                    <div class="error-message" id="password-error">As senhas não coincidem</div>
                </div>
                
                <button type="submit" class="register-button">
                    <i class="fas fa-user-plus"></i> CADASTRAR
                </button>
                
                <div class="login-link">
                    Já tem uma conta? <a href="tela1.php">Faça login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validação de senha em tempo real
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const reqLength = document.getElementById('req-length').querySelector('i');
            const reqUppercase = document.getElementById('req-uppercase').querySelector('i');
            const reqNumber = document.getElementById('req-number').querySelector('i');
            
            // Atualiza os ícones de requisitos
            reqLength.className = password.length >= 8 ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            reqUppercase.className = /[A-Z]/.test(password) ? 'fas fa-check-circle text-success' : 'fas fa-circle';
            reqNumber.className = /\d/.test(password) ? 'fas fa-check-circle text-success' : 'fas fa-circle';
        });
        
        // Validação de confirmação de senha
        document.getElementById('confirm-password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorElement = document.getElementById('password-error');
            
            if (password && confirmPassword && password !== confirmPassword) {
                errorElement.style.display = 'block';
            } else {
                errorElement.style.display = 'none';
            }
        });
        
        // Validação do formulário
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const errorElement = document.getElementById('password-error');
            
            // Verifica se as senhas coincidem
            if (password !== confirmPassword) {
                e.preventDefault();
                errorElement.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }
            
            // Validação adicional dos requisitos de senha
            if (password.length < 8 || !/[A-Z]/.test(password) || !/\d/.test(password)) {
                e.preventDefault();
                alert('A senha não atende a todos os requisitos!');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>