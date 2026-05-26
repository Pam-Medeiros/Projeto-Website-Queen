<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$order_id = intval($_GET['id']);

// Buscar detalhes do pedido
$stmt = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: profile.php");
    exit();
}

// Buscar itens do pedido
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Pamela Medeiros"/>
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, 
    tours e entre em contato."/>
    <meta name="keywords"content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Detalhes do Pedido #<?= $order_id ?></title>

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
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="font-family: 'Cinzel', serif; color: #4b0082;">
                <i class="fas fa-receipt"></i> Pedido #<?= $order_id ?>
            </h2>
            <a href="profile.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="row">
            <!-- Informações do Pedido -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-info-circle"></i> Informações</h5>
                        
                        <p><strong>Status:</strong><br>
                        <span class="badge bg-<?= $order['status'] == 'pending' ? 'warning' : 'success' ?> mt-1">
                            <?= $order['status'] == 'pending' ? 'Pendente' : 'Completo' ?>
                        </span></p>
                        
                        <p><strong>Data do Pedido:</strong><br>
                        <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                        
                        <p><strong>Cliente:</strong><br>
                        <?= htmlspecialchars($order['username']) ?></p>
                        
                        <p class="mb-0"><strong>Email:</strong><br>
                        <?= htmlspecialchars($order['email']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-shopping-bag"></i> Itens do Pedido</h5>
                        
                        <?php foreach ($items as $item): ?>
                        <div class="row border-bottom py-3 align-items-center">
                            <div class="col-md-2">
                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                     class="img-fluid rounded">
                            </div>
                            <div class="col-md-5">
                                <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                <p class="text-muted small mb-0">
                                    Preço unitário: €<?= number_format($item['price'], 2, ',', '.') ?>
                                </p>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="badge bg-secondary">Qtd: <?= $item['quantity'] ?></span>
                            </div>
                            <div class="col-md-3 text-end">
                                <strong>€<?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></strong>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total do Pedido:</h5>
                                    <h5 class="text-danger">€<?= number_format($order['total'], 2, ',', '.') ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white text-center p-4 mt-5">
        <div class="container">
            <p>&copy; 2025 Website Fã Clube Queen. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>