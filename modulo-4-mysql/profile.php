<?php
require 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = ""; 

// --- LÓGICA DE ATUALIZAÇÃO DE PERFIL COM SENHA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['password']; 
    $user_id = $_SESSION['user_id'];

    try {
        if (!empty($new_password)) {
            // Se o utilizador digitou algo na senha, atualiza NOME, EMAIL e SENHA (com hash)
            $password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt_update->execute([$new_username, $new_email, $password_hashed, $user_id]);
        } else {
            // Se a senha estiver vazia, atualiza apenas NOME e EMAIL (mantém a senha atual)
            $stmt_update = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt_update->execute([$new_username, $new_email, $user_id]);
        }
        
        $_SESSION['username'] = $new_username; // Atualiza o nome no menu/sessão
        $message = "Dados atualizados com sucesso!";
    } catch (PDOException $e) {
        $message = "Erro ao atualizar: " . $e->getMessage();
    }
}


// Buscar informações atualizadas do usuário (para preencher o formulário)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Buscar pedidos do usuário
$stmt_orders = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as total_items 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.order_date DESC
");
$stmt_orders->execute([$_SESSION['user_id']]);
$orders = $stmt_orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Pamela Medeiros"/>
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Meu Perfil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/css/style.css" />
</head>
<body class="bg-light">

    <header class="bg-dark text-white p-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">
                    <img src="src/images/logo_queen.png" alt="Logotipo oficial da banda Queen" height="50" />
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item"><a class="nav-link" href="albuns.php">Álbuns</a></li>
                        <li class="nav-item"><a class="nav-link" href="tour.php">Tour</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cart.php">
                                    <i class="fas fa-shopping-cart"></i> Carrinho
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="profile.php">
                                    <i class="fas fa-user"></i> Perfil
                                </a>
                            </li>

                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link text-warning" href="admin.php">
                                        <i class="fas fa-cog"></i> Admin
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a>
                            </li>

                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">
                                    <i class="fas fa-user-plus"></i> Registrar
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <!-- Informações do Usuário (Painel da Esquerda) -->
            <div class="col-md-4 mb-4">
    <div class="card shadow-sm border-0 border-warning border-3">
        <div class="card-body text-center p-4">
            <?php if (!empty($message)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size: 0.8rem;">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="profile.php">
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Nome de Utilizador</label>
                <input type="text" name="username" class="form-control text-center" 
                    value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control text-center" 
                    value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Nova Senha</label>
                <input type="password" name="password" class="form-control text-center" 
                    placeholder="Deixe em branco para manter a atual">
            </div>

            <button type="submit" name="update_profile" class="btn btn-success w-100 mb-3">
                <i class="fas fa-save"></i> Guardar Alterações
            </button>
            </form>

            <hr>

            <?php if ($user['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-warning w-100 mb-2">
                    <i class="fas fa-cog"></i> Painel Admin
                </a>
            <?php endif; ?>
            
            <a href="logout.php" class="btn btn-outline-danger w-100">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</div>
            <!-- Histórico de Pedidos (Painel da Direita) -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-warning">
                        <h4 class="mb-0" style="font-family: 'Cinzel', serif;">
                            <i class="fas fa-shopping-bag"></i> Histórico de Compras
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Pedido #</th>
                                            <th>Data</th>
                                            <th>Itens</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td class="fw-bold">#<?= $order['id'] ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                                <td><?= $order['total_items'] ?> itens</td>
                                                <td class="fw-bold text-success">€<?= number_format($order['total'], 2, ',', '.') ?></td>
                                                <td>
                                                    <?php if ($order['status'] === 'completed'): ?>
                                                        <span class="badge bg-success">Concluído</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Pendente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Detalhes
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-box-open text-muted mb-3" style="font-size: 60px;"></i>
                                <h5 class="text-muted">Ainda não fizeste nenhuma compra.</h5>
                                <div class="mt-4">
                                    <a href="eventos.php" class="btn btn-warning mx-2">Ver Eventos</a>
                                    <a href="loja.php" class="btn btn-warning mx-2">Visitar Loja</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

     <footer class="bg-dark text-white text-center p-4 mt-5">
      <div class="container">
        <p>Informações de Contacto | Siga-nos nas redes sociais:</p>
        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-white me-2"><i class="fab fa-youtube"></i></a>
        <p class="mt-2">&copy; 2025 Website Fã Clube Queen. Todos os direitos reservados.</p>
      </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="src/js/script.js"></script>
  </body>
</html>