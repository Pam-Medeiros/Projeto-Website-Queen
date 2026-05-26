<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Verificar se o pedido pertence ao usuário logado
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}
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
    <title>Queen - Pedido Confirmado!</title>

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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 text-center p-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 100px;"></i>
                        </div>
                        
                        <h1 class="mb-3" style="font-family: 'Cinzel', serif; color: #4b0082;">
                            Pedido Confirmado!
                        </h1>
                        
                        <p class="lead mb-4">
                            Obrigado pela sua compra! Seu pedido foi processado com sucesso.
                        </p>
                        
                        <div class="alert alert-info">
                            <h5><i class="fas fa-receipt"></i> Número do Pedido: <strong>#<?= $order_id ?></strong></h5>
                            <p class="mb-0">Total: <strong>€<?= number_format($order['total'], 2, ',', '.') ?></strong></p>
                        </div>
                        
                        <p class="text-muted mb-4">
                            Um email de confirmação foi enviado para você com os detalhes do pedido.
                        </p>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="profile.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-user"></i> Ver Meus Pedidos
                            </a>
                            <a href="loja.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-store"></i> Continuar Comprando
                            </a>
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