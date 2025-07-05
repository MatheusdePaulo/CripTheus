<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CripTheus</title>
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
        }
        
        /* Container principal */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        /* Card de login */
        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-title {
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
        
        .login-button {
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
        
        .login-button:hover {
            background-color: #4a488c;
            transform: translateY(-2px);
        }
        
        .register-link {
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #4a488c;
            text-decoration: none;
            font-weight: bold;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* Mensagens de erro */
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
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
            
            .login-card {
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
        <div class="welcome-message">Bem-vindo ao CripTheus</div>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-container">
        <div class="login-card">
            <h1 class="login-title"><i class="fas fa-sign-in-alt"></i> Login</h1>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    $error = $_GET['error'];
                    echo $error === 'invalid' ? 'Credenciais inválidas!' : 
                          ($error === 'empty' ? 'Preencha todos os campos!' : 'Erro no login!');
                    ?>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" action="../BackEnd/login.php" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Nome de usuário</label>
                    <input type="text" name="username" id="username" placeholder="Digite seu usuário" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                </div>
                
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> ENTRAR
                </button>
                
                <div class="register-link">
                    Não tem uma conta? <a href="tela2.php">Registre-se aqui</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validação básica do formulário
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos!');
            }
        });
    </script>
</body>
</html>