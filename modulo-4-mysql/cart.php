<?php
require_once 'config.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. Adicionar item ao carrinho
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // O type pode ser 'product' ou 'event'
    $type = isset($_GET['type']) && $_GET['type'] == 'event' ? 'event' : 'product';
    $quantity = 1;

    // Criar uma chave única para o carrinho baseada no tipo e ID (ex: event_1, product_5)
    $cartKey = $type . '_' . $id;

    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['qty'] += $quantity;
    } else {
        // Busca o item na BD correta (events ou products)
        if ($type === 'event') {
            $stmt = $pdo->prepare("SELECT title as name, price, image FROM events WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("SELECT name, price, image FROM products WHERE id = ?");
        }
        
        $stmt->execute([$id]);
        $itemData = $stmt->fetch();
        
        if ($itemData) {
            $_SESSION['cart'][$cartKey] = [
                'id'    => $id,
                'name'  => $itemData['name'],
                'price' => $itemData['price'],
                'image' => $itemData['image'], 
                'qty'   => $quantity,
                'type'  => $type
            ];
        }
    }
    header("Location: cart.php");
    exit();
}

// 2. Remover item
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $cartKey = $_GET['id']; 
    unset($_SESSION['cart'][$cartKey]);
    header("Location: cart.php");
    exit();
}

// 3. Atualizar quantidade
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['qty'])) {
    $cartKey = $_GET['id'];
    $qty = intval($_GET['qty']);
    if ($qty > 0 && isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['qty'] = $qty;
    } else {
        unset($_SESSION['cart'][$cartKey]);
    }
    header("Location: cart.php");
    exit();
}

// 4. Limpar carrinho
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Carrinho de Compra</title>
   <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni:ital,wght@0,400..700;1,400..700&family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap" rel="stylesheet" />
    
    <!-- Style CSS -->
    <link rel="stylesheet" href="src/css/style.css"/>
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
                        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item"><a class="nav-link" href="albuns.php">Álbuns</a></li>
                        <li class="nav-item"><a class="nav-link" href="tour.php">Tour</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link active" href="cart.php">
                                    <i class="fas fa-shopping-cart"></i> Carrinho </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">
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
    
    <div class="text-center mb-5">
        <h1 class="mb-4 text-center" style="font-family: 'Cinzel', serif;">
            <i class="fas fa-shopping-cart"></i> Carrinho de Compras
        </h1>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3" style="color: var(--queen-purple);">O teu carrinho está vazio</h3>
            <p class="text-muted">Explora a nossa loja ou compra bilhetes para os eventos.</p>
            <a href="eventos.php" class="btn btn-details mt-3 mx-2" style="background-color: var(--queen-gold); font-weight: bold;">Ver Eventos</a>
            <a href="loja.php" class="btn btn-details mt-3 mx-2" style="background-color: var(--queen-gold); font-weight: bold;">Visitar Loja</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- LISTA DE ITENS -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Itens no Carrinho</h5>
                        <a href="cart.php?action=clear" class="btn btn-sm btn-outline-danger" onclick="return confirm('Esvaziar o carrinho?')">Limpar Tudo</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($_SESSION['cart'] as $key => $item): 
                                $subtotal = $item['price'] * $item['qty'];
                                $total += $subtotal;
                            ?>
                            <li class="list-group-item p-3">
                                <div class="row align-items-center">
                                    <!-- Imagem com fallback -->
                                    <div class="col-2 col-md-1">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded" onerror="this.src='src/images/default-product.jpg'">
                                    </div>
                                    
                                    <!-- Nome e Badge (Bilhete vs Produto) -->
                                    <div class="col-4 col-md-4">
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                        <?php if ($item['type'] === 'event'): ?>
                                            <span class="badge bg-info text-dark mt-1"><i class="bi bi-ticket-perforated"></i> Bilhete</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary mt-1"><i class="bi bi-bag"></i> Produto</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Quantidade (+ / -) -->
                                    <div class="col-3 col-md-3">
                                        <div class="input-group input-group-sm">
                                            <a href="cart.php?action=update&id=<?= $key ?>&qty=<?= $item['qty'] - 1 ?>" class="btn btn-outline-secondary">-</a>
                                            <input type="text" class="form-control text-center" value="<?= $item['qty'] ?>" readonly>
                                            <a href="cart.php?action=update&id=<?= $key ?>&qty=<?= $item['qty'] + 1 ?>" class="btn btn-outline-secondary">+</a>
                                        </div>
                                    </div>
                                    
                                    <!-- Preço Subtotal -->
                                    <div class="col-2 col-md-3 text-end fw-bold fs-5">
                                        €<?= number_format($subtotal, 2, ',', '.') ?>
                                    </div>
                                    
                                    <!-- Botão Remover (X) -->
                                    <div class="col-1 text-end">
                                        <a href="cart.php?action=remove&id=<?= $key ?>" class="text-danger fw-bold text-decoration-none fs-5" title="Remover item">&times;</a>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- RESUMO DO PEDIDO -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="card-title border-bottom border-warning pb-2 mb-4" style="font-family:'Cinzel',serif;">Resumo</h4>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5">Total:</span>
                            <span class="fs-4 fw-bold text-danger">€<?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn w-100 py-2 fs-5" style="background-color: var(--queen-gold); color: var(--queen-black); font-weight: bold; text-transform: uppercase;">
                            Finalizar Compra
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
</main>

</main><br>
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