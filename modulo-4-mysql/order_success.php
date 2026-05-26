<?php
require 'config.php';

// Verificar se o usuário está logado e se existe um ID de pedido
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Buscar detalhes do pedido
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
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Encomenda Concluída - Queen Fan Site</title>

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
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-1 border border-warning border-2">
                    <div class="card-body text-center p-5">
                        
                        <!-- Ícone de Sucesso -->
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                        </div>
                        
                        <!-- Título da Confirmação -->
                        <h2 class="mb-3" style="font-family: 'Cinzel', serif; color: #4b0082;">
                            Pagamento Confirmado!
                        </h2>
                        
                        <p class="lead mb-5 text-muted">
                            A tua encomenda foi processada com sucesso. Obrigado pela tua compra!
                        </p>
                        
                        <!-- Caixa de Resumo do Pedido -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body py-4">
                                <div class="row">
                                    <div class="col-6 border-end">
                                        <p class="text-muted mb-1 small text-uppercase">Número da Encomenda</p>
                                        <h4 class="mb-0 fw-bold">#<?= $order_id ?></h4>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1 small text-uppercase">Valor Total Pago</p>
                                        <h4 class="mb-0 fw-bold text-danger">€<?= number_format($order['total'], 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-muted small mb-5">
                            <i class="fas fa-envelope me-1"></i> Enviámos um email com o recibo e os detalhes da tua compra.
                        </p>
                        
                        <!-- Botões de Ação -->
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <a href="order_details.php?id=<?= $order_id ?>" class="btn btn-warning px-4 py-2 fw-bold">
                                <i class="fas fa-receipt me-2"></i> Ver Recibo
                            </a>
                            <a href="index.php" class="btn btn-warning px-4 py-2">
                                Voltar ao Início
                            </a>
                        </div>
                        
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