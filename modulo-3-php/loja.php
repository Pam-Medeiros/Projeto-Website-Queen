<?php
require 'config.php'; 

// 1. Puxar produtos do banco de dados
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// 2. Puxar categorias para o filtro
$categories = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
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
    <title>Queen - Loja</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/css/style.css" />
</head>

<body>

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
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobre.php">Sobre</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="albuns.php">Álbuns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tour.php">Tour</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="loja.php">Loja</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contactos.php">Contactos</a>
                        </li>

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
        <h1 class="mb-4 text-center" style="font-family: 'Cinzel', serif;">
            <i class="fas fa-store"></i> Produtos exclusivos </h1><br>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 loja-produtos">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            <img src="<?= htmlspecialchars($product['image']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                style="height: 250px; object-fit: contain; padding: 10px;">
                            <div class="card-body text-center d-flex flex-column">
                                <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
                                <h5 class="card-title" style="font-family: 'Cinzel';">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= htmlspecialchars($product['description']) ?>
                                </p>
                                <div class="product-price">
                                    €<?= number_format($product['price'], 2, ',', '.') ?>
                                </div>

                                <a href="cart.php?action=add&id=<?= $product['id'] ?>&qty=1"
                                    class="btn btn-warning w-100 mt-2">
                                    <i class="fas fa-cart-plus me-2"></i>Adicionar ao Carrinho
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>Nenhum produto disponível</h3>
                        <p>Os produtos serão carregados em breve.</p>
                    </div>
                </div>
            <?php endif; ?>
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