<?php
session_start();
include('../BackEnd/conexao.php');

// Verificação de permissões
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
} 

$isAdmin = isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1;

if (!$isAdmin) {
    header("Location: tela3.php");
    exit();
}

// Recupera os dados do usuário da sessão
$user = $_SESSION['user'];

// Função para remover um usuário
if (isset($_GET['remove_id'])) {
    $removeId = $_GET['remove_id'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $removeId);
    $stmt->execute();
    $_SESSION['success'] = 'Usuário removido com sucesso!';
    header("Location: tela4.php");
    exit();
}

// Configurações de paginação
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// Consulta para total de usuários
$sqlTotal = "SELECT COUNT(*) as total FROM usuarios";
$stmtTotal = $conn->query($sqlTotal);
$totalUsers = $stmtTotal->fetchColumn();

// Total de páginas
$totalPages = ceil($totalUsers / $perPage);
if ($totalPages == 0) $totalPages = 1;
if ($page > $totalPages) $page = $totalPages;

// Determina a direção da ordenação
$order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc';

// Determina o critério de ordenação
$allowedOrderBy = ['id', 'username', 'email', 'created_at'];
$orderBy = isset($_GET['orderby']) && in_array($_GET['orderby'], $allowedOrderBy) 
    ? $_GET['orderby'] 
    : 'username';

// Consulta para listar os usuários com a ordenação escolhida
$sql = "SELECT * FROM usuarios ORDER BY $orderBy $order LIMIT :offset, :perPage";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - CripTheus</title>
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
        
        /* NOVO HEADER - Logo e Mensagem juntos */
        .header-container {
            background-color: rgba(0, 0, 0, 0.9);
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 10px 30px;
            height: 70px; /* Altura reduzida da barra */
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
            margin: 0 auto; /* Centraliza o texto */
            text-align: center;
            flex-grow: 1; /* Ocupa o espaço disponível */
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 150px auto 50px; /* Ajustado para acomodar o novo header */
            width: 90%;
            max-width: 1200px;
            text-align: center;
            position: relative;
            margin-top: 100px;
        }
        
        .admin-info {
            position: fixed;
            top: 20px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            z-index: 1001; /* Acima do header */
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
        
        h1 {
            color: #4a488c;
            margin-bottom: 25px;
            font-size: 2.2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #63629f, #4a488c);
            border-radius: 15px;
            padding: 25px 15px;
            text-align: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .table-container {
            margin-top: 30px;
            overflow-x: auto;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #63629f;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .action-cell {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        
        .btn-edit {
            background-color: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 8px;
        }
        
        .pagination a, 
        .pagination span {
            padding: 10px 18px;
            border-radius: 30px;
            background-color: #f1f1f1;
            color: #333;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-block;
            font-weight: bold;
            min-width: 40px;
            text-align: center;
        }
        
        .pagination a:hover {
            background-color: #63629f;
            color: white;
        }
        
        .pagination .active {
            background-color: #63629f;
            color: white;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 30px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: bold;
        }
        
        .search-box {
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
        }
        
        .search-box input {
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 30px;
            width: 100%;
            max-width: 500px;
            font-size: 1rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            border-color: #63629f;
            outline: none;
            box-shadow: 0 3px 15px rgba(99, 98, 159, 0.3);
        }
        
        .section-title {
            color: #4a488c;
            margin: 30px 0 15px;
            font-size: 1.5rem;
            text-align: left;
            border-bottom: 2px solid #63629f;
            padding-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .header-container {
                padding: 10px 15px;
                height: 60px;
                flex-direction: row;
            }
            
            .logo {
                width: 90px;
                left: 15px;
                position: relative;
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
            }
        }
        
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <!-- Logo -->
     <div class="header-container">
        <img src="/img/logo01.png" alt="Logo CripTheus" class="logo">
        <div class="welcome-message">CripTheus, Bem-vindo Administrador!</div>
    </div>
    
    <!-- Informações do admin e botão de logout -->
    <div class="admin-info">
        <div class="user-profile">
            <div class="user-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <div><?= htmlspecialchars($user['username']) ?></div>
        </div>
        <a href="../FrontEnd/tela1.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>

    <!-- Conteúdo principal -->
    <div class="container">
        <h1>Painel Administrativo</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?= $totalUsers ?></div>
                <div class="stat-label">Usuários Registrados</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $totalPages ?></div>
                <div class="stat-label">Páginas</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $perPage ?></div>
                <div class="stat-label">Usuários por Página</div>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Pesquisar usuários...">
        </div>

        <h2 class="section-title">Gerenciamento de Usuários</h2>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="?orderby=id&order=<?= ($orderBy == 'id' && $order == 'asc') ? 'desc' : 'asc' ?>&page=<?= $page ?>" style="color: white;">ID</a>
                        </th>
                        <th>
                            <a href="?orderby=username&order=<?= ($orderBy == 'username' && $order == 'asc') ? 'desc' : 'asc' ?>&page=<?= $page ?>" style="color: white;">Username</a>
                        </th>
                        <th>
                            <a href="?orderby=email&order=<?= ($orderBy == 'email' && $order == 'asc') ? 'desc' : 'asc' ?>&page=<?= $page ?>" style="color: white;">Email</a>
                        </th>
                        <th>
                            <a href="?orderby=created_at&order=<?= ($orderBy == 'created_at' && $order == 'asc') ? 'desc' : 'asc' ?>&page=<?= $page ?>" style="color: white;">Data de Registro</a>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td class="action-cell">
                                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?remove_id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                                    <i class="fas fa-trash"></i> Remover
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&orderby=<?= $orderBy ?>&order=<?= $order ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&orderby=<?= $orderBy ?>&order=<?= $order ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&orderby=<?= $orderBy ?>&order=<?= $order ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Pesquisa em tempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#userTableBody tr');
            
            rows.forEach(row => {
                const username = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                
                if (username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Confirmação para exclusão
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja remover este usuário?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>