<?php
require 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o carrinho está vazio
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: loja.php");
    exit();
}

// Processar o checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // Calcular o total
        $total = 0;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                $total += $product['price'] * $quantity;
            }
        }

        // 1. Criar a Ordem
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total) VALUES (?, 'pending', ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $orderId = $pdo->lastInsertId();

        // 2. Inserir itens do carrinho e Atualizar estoque
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Busca o preço atual do produto para garantir integridade
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                // A) Salva o item no histórico do pedido
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $product_id, $quantity, $product['price']]);

                // B) Baixa do estoque
                $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmtUpdate->execute([$quantity, $product_id]);
            }
        }

        $pdo->commit();
        $_SESSION['cart'] = []; // Limpar carrinho
        
        // Redirecionar para página de sucesso
        header("Location: order_success.php?order_id=" . $orderId);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erro ao processar pedido: " . $e->getMessage();
    }
}

// Buscar produtos do carrinho para exibição
$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
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
    <title>Queen - Finalizar Compra</title>

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
                        <li class="nav-item"><a class="nav-link" href="cart.php">Carrinho</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <h1 class="mb-4 text-center" style="font-family: 'Cinzel', serif; color: #4b0082;">
            <i class="fas fa-credit-card"></i> Finalizar Compra
        </h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Informações do Cliente -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-user"></i> Informações do Cliente</h5>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Nome:</strong></label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($user['username']) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-credit-card"></i> Método de Pagamento</h5>
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i> Pagamento processado de forma segura
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-lock"></i> Este é um projeto educacional. 
                            Nenhum pagamento real será processado.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-shopping-bag"></i> Resumo do Pedido</h5>
                        
                        <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                            <strong>€<?= number_format($item['subtotal'], 2, ',', '.') ?></strong>
                        </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>€<?= number_format($total, 2, ',', '.') ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span>Envio:</span>
                            <strong class="text-success">GRÁTIS</strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-danger">€<?= number_format($total, 2, ',', '.') ?></h5>
                        </div>

                        <form method="POST">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-check-circle"></i> Confirmar Pedido
                            </button>
                        </form>
                        
                        <a href="cart.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
                        </a>
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