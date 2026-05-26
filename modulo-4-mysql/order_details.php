<?php
require 'config.php';

// Verificar se o usuário está logado e se passou o ID da encomenda
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$order_id = intval($_GET['id']);
$user_id_logado = $_SESSION['user_id'];
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// 1. Buscar os detalhes da Encomenda e do Cliente
// Se for admin, buscamos apenas pelo ID da encomenda.
// Se não for admin, buscamos pelo ID da encomenda E garantir que pertence ao utilizador logado.
if ($is_admin) {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
} else {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
    $stmt->execute([$order_id, $user_id_logado]);
}

$order = $stmt->fetch();

// Se a encomenda não existir (ou o utilizador comum tentar aceder a uma que não é dele), redireciona
if (!$order) {
    // Se for admin, volta para o painel admin, se for user, volta para o perfil
    $redirect = $is_admin ? "admin.php" : "profile.php";
    header("Location: $redirect");
    exit();
}

// 2. Buscar itens do pedido inteligente (Eventos + Produtos)
$stmt_items = $pdo->prepare("
    SELECT oi.*, 
           COALESCE(p.name, e.title) AS name, 
           COALESCE(p.image, e.image) AS image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id AND oi.item_type = 'product'
    LEFT JOIN events e ON oi.product_id = e.id AND oi.item_type = 'event'
    WHERE oi.order_id = ?
");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Pamela Medeiros"/>
    <meta name="description" content="Detalhes da encomenda - Queen Fan Site."/>
    <meta name="keywords"content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Detalhes da Encomenda #<?= $order_id ?> - Queen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/css/style.css" />
</head>
<body class="bg-light">

    <header class="bg-dark text-white p-3">
        <div class="container">
            <nav class="navbar navbar-dark d-flex justify-content-between align-items-center">
                <a class="navbar-brand m-0" href="index.php">
                    <img src="src/images/logo_queen.png" alt="Logotipo oficial da banda Queen" height="50" />
                </a>
                <div class="d-flex justify-content-end">
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-outline-light btn-sm me-3">
                Painel Admin
                </a>
                <?php endif; ?>
            <a href="profile.php" class="btn btn-outline-light btn-sm me-3">
                ← Voltar ao Perfil
            </a>
            </nav>
            </div>
        </div>
    </header>

    <main class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="font-family: 'Cinzel', serif; color: #4b0082;">
                Detalhes da Encomenda #<?= $order_id ?>
            </h2>
        </div>

        <div class="row">
            <!-- Coluna da Esquerda: Dados do Cliente e Info do Pedido -->
            <div class="col-md-4 mb-4">
                
                <!-- Card do Status -->
                <div class="card shadow-sm border-0 border-top border-warning border-3 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-3 text-uppercase small fw-bold">Status da Encomenda</h5>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <?php if ($order['status'] === 'completed'): ?>
                                    <i class="fas fa-check-circle text-success fs-1"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock text-warning fs-1"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">
                                    <?= $order['status'] === 'completed' ? 'Concluída' : 'Pendente' ?>
                                </h4>
                                <small class="text-muted">Efetuada a <?= date('d/m/Y \à\s H:i', strtotime($order['order_date'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card do Cliente -->
                <div class="card shadow-sm border-0 border-top border-dark border-3">
                    <div class="card-body">
                        <h5 class="card-title text-muted mb-3 text-uppercase small fw-bold">
                            <i class="fas fa-user-circle me-2"></i> Dados do Cliente
                        </h5>
                        <p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($order['username']) ?></p>
                        <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita: Lista de Itens -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom border-warning d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="font-family: 'Cinzel', serif;">
                            <i class="fas fa-box-open text-warning"></i> Itens da Encomenda
                        </h5>
                        <span class="badge bg-dark rounded-pill"><?= count($items) ?> itens</span>
                    </div>
                    
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php 
                            $subtotal_geral = 0;
                            foreach ($items as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $subtotal_geral += $subtotal;
                            ?>
                                <li class="list-group-item p-4">
                                    <div class="row align-items-center">
                                        <!-- Imagem -->
                                        <div class="col-sm-2 mb-3 mb-sm-0 text-center">
                                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded border p-1" style="max-height: 80px; object-fit: contain;" onerror="this.src='src/images/default-product.jpg'">
                                        </div>
                                        
                                        <!-- Informação -->
                                        <div class="col-sm-6">
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                            <small class="text-muted">
                                                Quantidade: <?= $item['quantity'] ?> x €<?= number_format($item['price'], 2, ',', '.') ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Subtotal -->
                                        <div class="col-sm-4 text-sm-end mt-3 mt-sm-0 fw-bold fs-5">
                                            €<?= number_format($subtotal, 2, ',', '.') ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Rodapé com o Total -->
                    <div class="card-footer bg-light py-4 text-end">
                        <h5 class="text-muted mb-1">Total Pago</h5>
                        <h2 class="fw-bold text-danger mb-0">€<?= number_format($order['total'], 2, ',', '.') ?></h2>
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