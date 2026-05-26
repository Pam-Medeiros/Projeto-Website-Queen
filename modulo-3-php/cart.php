<?php
require 'config.php';

// Adicionar item ao carrinho
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $quantity = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $quantity;
    } else {
        $_SESSION['cart'][$id] = $quantity;
    }
    
    header("Location: cart.php");
    exit();
}

// Remover item do carrinho
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// Atualizar quantidade
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
    $id = intval($_GET['id']);
    $qty = intval($_GET['qty']);
    
    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty;
    } else {
        unset($_SESSION['cart'][$id]);
    }
    
    header("Location: cart.php");
    exit();
}

// Limpar carrinho
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

// Buscar produtos do carrinho
$cart_items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            
            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
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
    <title>Queen - Carrinho de Compras</title>

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
        <img src="src/images/logo_queen.png" alt="Logotipo oficial da banda Queen" height="50"/>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link" href="index.php">Início</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="sobre.php">Sobre</a> </li>
            <li class="nav-item">
            <a class="nav-link" href="albuns.php">Álbuns</a> </li>
            <li class="nav-item">
            <a class="nav-link" href="tour.php">Tour</a> </li>
            <li class="nav-item">
            <a class="nav-link" href="loja.php">Loja</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="contactos.php">Contactos</a> </li>
            <li class="nav-item">
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link active" href="cart.php">
                <i class="fas fa-shopping-cart"></i> Carrinho
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                <i class="fas fa-user"></i> Perfil
                </a>
            </li>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-warning" href="admin.php">
                <i class="fas fa-cog"></i> Admin
                </a>
            </li>
            <?php endif; ?>
            

            <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="login.php">
                <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </li>
            <?php endif; ?>
        </ul>
        </div>
    </nav>
    </div>
</header>

    <main class="container my-5">
        <h1 class="mb-4 text-center" style="font-family: 'Cinzel', serif; color: #4b0082;">
            <i class="fas fa-shopping-cart"></i> Carrinho de Compras
        </h1><br>

        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart" style="font-size: 100px; color: #ccc;"></i>
                <h3 class="mt-4 text-muted">Seu carrinho está vazio</h3>
                <p class="text-muted">Adicione produtos incríveis da loja Queen!</p>
                <a href="loja.php" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-store"></i> Ir para a Loja
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Itens no Carrinho</h5>
                                <a href="cart.php?action=clear" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                    <i class="fas fa-trash"></i> Limpar Carrinho
                                </a>
                            </div>
                            
                            <?php foreach ($cart_items as $item): ?>
                            <div class="row border-bottom py-3 align-items-center">
                                <div class="col-md-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="img-fluid rounded">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                    <p class="text-muted small mb-0">€<?= number_format($item['price'], 2, ',', '.') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control text-center" 
                                               value="<?= $item['quantity'] ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button"
                                                onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>€<?= number_format($item['subtotal'], 2, ',', '.') ?></strong>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a href="cart.php?action=remove&id=<?= $item['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Remover este item?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-lg border-0">
                        <div class="card-body">
                            <h5 class="mb-4"><i class="fas fa-calculator"></i> Resumo do Pedido</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>€<?= number_format($total, 2, ',', '.') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envio:</span>
                                <strong class="text-success">GRÁTIS</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <h5>Total:</h5>
                                <h5 class="text-danger">€<?= number_format($total, 2, ',', '.') ?></h5>
                            </div>

                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="checkout.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-credit-card"></i> Finalizar Compra
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i> Faça login para finalizar a compra
                                </div>
                                <a href="login.php" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-sign-in-alt"></i> Fazer Login
                                </a>
                            <?php endif; ?>
                            
                            <a href="loja.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Continuar Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center p-4 mt-5">
        <div class="container">
            <p>&copy; 2025 Website Fã Clube Queen. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(id, qty) {
            if (qty < 1) {
                if (confirm('Deseja remover este item do carrinho?')) {
                    window.location.href = 'cart.php?action=remove&id=' + id;
                }
            } else {
                window.location.href = 'cart.php?action=update&id=' + id + '&qty=' + qty;
            }
        }
    </script>
</body>
</html>