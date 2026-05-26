<?php
require 'config.php';

// Verificar se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: eventos.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

// Se o evento não existir, redirecionar
if (!$event) {
    header('Location: eventos.php');
    exit;
}

// Calcular bilhetes disponíveis
$tickets_sold      = (int)($event['tickets_sold'] ?? 0);
$capacity          = (int)$event['capacity'];
$bilhetes_disponiveis = $capacity - $tickets_sold;
$percentagem_ocupacao = $capacity > 0 ? round(($tickets_sold / $capacity) * 100, 1) : 0;

// Cor da barra conforme ocupação
if ($percentagem_ocupacao >= 90)      $bar_color = 'bg-danger';
elseif ($percentagem_ocupacao >= 60)  $bar_color = 'bg-warning';
else                                   $bar_color = 'bg-success';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen Detalhes</title>

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

    <main class="container mt-5 mb-5">

        <div class="row g-5">
            <!-- Coluna da Imagem -->
            <div class="col-md-5 text-center">
                <img src="<?= htmlspecialchars($event['image']) ?>"
                     alt="<?= htmlspecialchars($event['title']) ?>"
                     class="img-fluid rounded shadow"
                     style="max-height: 420px; object-fit: contain; background-color: #f8f8f8; width: 100%;">
            </div>

            <!-- Coluna dos Detalhes -->
            <div class="col-md-7">
                <?php
                    $badge_class = 'bg-success';
                    $badge_text  = 'Disponível';
                    if ($event['status'] === 'esgotado')   { $badge_class = 'bg-danger';  $badge_text = 'Esgotado'; }
                    if ($event['status'] === 'cancelado')  { $badge_class = 'bg-secondary'; $badge_text = 'Cancelado'; }
                ?>
                <span class="badge <?= $badge_class ?> mb-2"><?= $badge_text ?></span>

                <h1 class="text-purple mb-3" style="font-family: 'Cinzel', serif;">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>

                <hr class="border-warning border-2 opacity-75 mb-3">

                <!-- Informações principais -->
                <ul class="list-unstyled fs-5 mb-4">
                    <li class="mb-2">
                        <i class="fas fa-calendar-alt text-warning me-2"></i>
                        <strong>Data:</strong> <?= date('d/m/Y', strtotime($event['event_date'])) ?>
                        &nbsp;|&nbsp;
                        <i class="fas fa-clock text-warning me-1"></i>
                        <?= date('H:i', strtotime($event['event_date'])) ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-warning me-2"></i>
                        <strong>Local:</strong> <?= htmlspecialchars($event['location']) ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-users text-warning me-2"></i>
                        <strong>Capacidade:</strong> <?= number_format($event['capacity'], 0, ',', '.') ?> lugares
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-ticket-alt text-warning me-2"></i>
                        <strong>Bilhetes disponíveis:</strong>
                        <?php if ($bilhetes_disponiveis > 0): ?>
                            <span class="text-success fw-bold"><?= $bilhetes_disponiveis ?></span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Esgotado</span>
                        <?php endif; ?>
                    </li>
                </ul>

                <!-- Barra de ocupação -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted fw-bold">Ocupação</small>
                        <small class="text-muted">
                            <?= $tickets_sold ?> / <?= $capacity ?> (<?= $percentagem_ocupacao ?>%)
                        </small>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 6px;">
                        <div class="progress-bar <?= $bar_color ?>"
                             role="progressbar"
                             style="width: <?= $percentagem_ocupacao ?>%"
                             aria-valuenow="<?= $percentagem_ocupacao ?>"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- Descrição -->
                <?php if (!empty($event['description'])): ?>
                <div class="mb-4">
                    <h5 class="fw-bold"><i class="fas fa-info-circle text-warning me-2"></i>Sobre o Evento</h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Preço e botão de compra -->
                <div class="d-flex align-items-center gap-3 flex-wrap mt-3">
                    <span class="fs-2 fw-bold text-danger">
                        €<?= number_format($event['price'], 2, ',', '.') ?>
                    </span>

                    <?php if ($event['status'] === 'disponivel' && $bilhetes_disponiveis > 0): ?>
                        <a href="cart.php?action=add&id=<?= $event['id'] ?>&type=event" class="btn btn-warning btn-lg px-5 fw-bold">
                            <i class="fas fa-shopping-cart me-2"></i> Comprar Bilhete
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg px-5" disabled>
                            <i class="fas fa-ban me-2"></i> Indisponível
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Voltar -->
                <div class="mt-4">
                    <a href="eventos.php" class="btn btn-outline-dark">
                        <i class="fas fa-arrow-left me-2"></i> Voltar aos Eventos
                    </a>
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
    <script src="src/js/script.js"></script>
</body>
</html>
