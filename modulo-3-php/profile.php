<?php
require 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Buscar informações do usuário
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, 
    tours e entre em contato." />
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour" />
    <title>Queen - Meu Perfil</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni&display=swap" rel="stylesheet">

    <!-- Style CSS -->
    <link rel="stylesheet" href="src/css/style.css" />
</head>

<body>

    <header class="bg-dark text-white p-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">
                    <img src="src/images/logo_queen.png" alt="Queen" height="50">
                </a>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                        <a class="nav-link" href="sobre.php">Sobre</a> </li>
                        <li class="nav-item"></li>
                        <a class="nav-link" href="albuns.php">Álbuns</a> </li>
                        <li class="nav-item"></li>
                        <a class="nav-link" href="tour.php">Tour</a> </li>
                        <li class="nav-item"></li>
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item">
                        <a class="nav-link" href="contactos.php">Contactos</a> </li>
                        <li class="nav-item"><a class="nav-link active" href="profile.php">Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <!-- Informações do Usuário -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-user-circle" style="font-size: 80px; color: #4b0082;"></i>
                        </div>
                        <h3 style="font-family: 'Cinzel', serif; color: #4b0082;">
                            <?= htmlspecialchars($user['username']) ?>
                        </h3>
                        <p class="text-muted mb-2">
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                        </p>
                        <p class="mb-2">
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                <?= $user['role'] === 'admin' ? 'Administrador' : 'Usuário' ?>
                            </span>
                        </p>
                        <small class="text-muted">
                            Membro desde: <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </small>

                        <hr>

                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="admin.php" class="btn btn-danger w-100 mb-2">
                                <i class="fas fa-cog"></i> Painel Admin
                            </a>
                        <?php endif; ?>

                        <a href="logout.php" class="btn btn-secondary w-100">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pedidos -->
            <div class="col-md-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-4" style="font-family: 'Cinzel', serif; color: #4b0082;">
                            <i class="fas fa-shopping-bag"></i> Meus Pedidos
                        </h3>

                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Pedido #</th>
                                            <th>Data</th>
                                            <th>Itens</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><strong>#<?= $order['id'] ?></strong></td>
                                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                                <td><?= $order['total_items'] ?> item(s)</td>
                                                <td><strong><?= number_format($order['total'], 2, ',', '.') ?>€</strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $order['status'] == 'pending' ? 'warning' : 'success' ?>">
                                                        <?= $order['status'] == 'pending' ? 'Pendente' : 'Completo' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart" style="font-size: 60px; color: #ccc;"></i>
                                <p class="mt-3 text-muted">Você ainda não fez nenhum pedido.</p>
                                <a href="loja.php" class="btn btn-primary">
                                    <i class="fas fa-store"></i> Ir para a Loja
                                </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>