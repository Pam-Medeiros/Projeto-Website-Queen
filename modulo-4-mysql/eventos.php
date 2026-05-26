<?php
require 'config.php'; 

$search_title = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_date = isset($_GET['event_date']) ? $_GET['event_date'] : '';

$query = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($search_title !== '') {
    $query .= " AND title LIKE ?";
    $params[] = "%$search_title%";
}

if ($search_date !== '') {
    $query .= " AND event_date = ?";
    $params[] = $search_date;
}

$query .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="author" content="Pamela Medeiros"/>
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Bilhetes e Eventos</title>

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
                        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link active" href="eventos.php">Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item"><a class="nav-link" href="albuns.php">Álbuns</a></li>
                        <li class="nav-item"><a class="nav-link" href="tour.php">Tour</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Carrinho</a></li>
                            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Perfil</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li class="nav-item"><a class="nav-link text-warning" href="admin.php"><i class="fas fa-cog"></i> Admin</a></li>
                            <?php endif; ?>
                            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Registrar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container mt-5">
        <h1 class="text-center mb-4"><i class="fas fa-ticket-alt"></i> Próximos Eventos</h1><br>

        <!-- Formulário de Pesquisa -->
        <div class="container mb-5">
            <form method="GET" action="eventos.php" class="row g-3 bg-light p-4 rounded shadow-sm border border-warning">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-dark">Pesquisar por Título</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Ex: Queen Live..." value="<?= htmlspecialchars($search_title ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Pesquisar por Data</label>
                    <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($search_date ?? '') ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">Filtrar</button>
                    <?php if (!empty($search_title) || !empty($search_date)): ?>
                        <a href="eventos.php" class="btn btn-outline-secondary">Limpar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Listagem de Eventos -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-gold">
                        <!-- Imagem clicável que leva para o detalhe -->
                        <a href="evento_detalhe.php?id=<?= $event['id'] ?>">
                            <img src="<?= htmlspecialchars($event['image']) ?>" class="card-img-top p-2" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 250px; object-fit: contain; background-color: #fff;">
                        </a>

                        <div class="card-body">
                            <!-- Título clicável que leva para o detalhe -->
                            <h5 class="card-title text-purple">
                                <a href="evento_detalhe.php?id=<?= $event['id'] ?>" class="text-decoration-none text-purple">
                                    <?= htmlspecialchars($event['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($event['event_date'])) ?></p>
                            <p class="card-text"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></p>
                            <h4 class="text-danger">€<?= number_format($event['price'], 2, ',', '.') ?></h4>

                            <!-- Botões: Ver Detalhes + Comprar -->
                            <div class="d-flex gap-2 mt-2">
                                <a href="evento_detalhe.php?id=<?= $event['id'] ?>" class="btn btn-outline-secondary w-50">
                                    <i class="fas fa-info-circle"></i> Detalhes
                                </a>
                                <a href="cart.php?action=add&id=<?= $event['id'] ?>&type=event" class="btn btn-warning w-50">
                                    <i class="fas fa-shopping-cart"></i> Comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center mt-4">De momento não existem eventos agendados.</p>
            </div>
        <?php endif; ?>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/script.js"></script>
</body>
</html>
