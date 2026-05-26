<?php
require_once 'config.php';

// Busca os próximos 3 eventos
$stmt = $pdo->query("
    SELECT * FROM events 
    WHERE status != 'cancelado' AND event_date >= NOW() 
    ORDER BY event_date ASC 
    LIMIT 3
");
$recent_events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Início</title>
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
                        <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
                        <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
                        <li class="nav-item"><a class="nav-link" href="albuns.php">Álbuns</a></li>
                        <li class="nav-item"><a class="nav-link" href="tour.php">Tour</a></li>
                        <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cart.php">
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
      <?php if(isset($_SESSION['user_id'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <i class="fas fa-user-check"></i> 
          Bem-vindo de volta, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12 text-center">
          <h1 class="display-3">Bem-vindo ao Universo Queen!</h1>
          <p class="lead">
            Explore a história e o legado de uma das maiores bandas de rock de todos os tempos.
          </p>
        </div>
      </div>

      <hr class="my-4" />

      <div class="row align-items-center">
        <div class="col-md-8">
          <h2>A Lenda do Rock</h2>
          <p>
            Queen foi uma banda britânica de rock formada em Londres em 1970,
            composta por Freddie Mercury (vocais, piano), Brian May (guitarra,
            vocais), John Deacon (baixo) e Roger Taylor (bateria, vocais).
            Conhecida por sua diversidade musical, arranjos complexos, harmonias
            vocais e performances ao vivo energéticas, Queen se tornou uma das
            bandas mais vendidas e influentes da história.
          </p>
          <p>
            Desde seus primeiros sucessos como "Bohemian Rhapsody" até hinos de
            estádio como "We Will Rock You" e "We Are the Champions", a música
            do Queen transcendeu gerações e continua a inspirar milhões de fãs
            ao redor do mundo.
          </p><br>

          <a href="eventos.php" class="btn btn-warning me-2">
            <i class="fas fa-ticket-alt"></i> Próximos Eventos
          </a>
          <a href="loja.php" class="btn btn-warning">
            <i class="fas fa-store"></i> Visitar Loja
          </a>
        </div>
        <div class="col-md-4 text-center">
          <img src="src/images/Brasao.png" class="img-fluid rounded my-3" alt="Brasao Banda" />
        </div>
      </div><br>

    
      <!-- EVENTOS RECENTES -->
      <div class="row mt-5">
        <div class="col-12 text-center mb-3">
          <h2><i class="fas fa-ticket-alt text-warning"></i> Eventos Recentes</h2>
        </div>
      </div>

      <div class="row justify-content-center g-3 mb-4">
        <?php if(count($recent_events) > 0): ?>
          <?php foreach ($recent_events as $event): ?>
          <div class="col-6 col-md-3">
            <a href="eventos.php" class="text-decoration-none">
              <div class="text-center">
                <img src="<?= htmlspecialchars($event['image']) ?>"
                     alt="<?= htmlspecialchars($event['title']) ?>"
                     class="img-fluid rounded shadow-sm"
                     style="width: 100%; height: 220px; object-fit: contain; background-color: #f8f8f8; border: 1px solid #eee; border-radius: 8px;">
                <p class="mt-2 mb-0 fw-bold" style="color: #4a004a; font-family: 'Cinzel', serif; font-size: 0.80rem;">
                  <?= htmlspecialchars($event['title']) ?>
                </p>
              </div>
            </a>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12 text-center">
            <p class="text-muted">De momento não existem eventos agendados.</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="text-center mb-5">
        <a href="eventos.php" class="btn btn-outline-dark btn-sm px-4">Ver Todos os Eventos <i class="fas fa-arrow-right ms-1"></i></a>
      </div>
      <!-- FIM EVENTOS RECENTES -->

      <?php if(!isset($_SESSION['user_id'])): ?>
      <div class="row mt-5">
        <div class="col-12">
          <div class="card shadow-lg border-0 bg-light">
            <div class="card-body text-center p-5">
              <h3 class="mb-4">
                <i class="fas fa-user-plus"></i> Junte-se à Comunidade Queen!
              </h3>
              <p class="lead mb-4">
                Crie sua conta para acessar a loja exclusiva, fazer pedidos e muito mais!
              </p>
              <a href="register.php" class="btn btn-warning btn-lg me-2">
                <i class="fas fa-user-plus"></i> Criar Conta
              </a>
              <a href="login.php" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-sign-in-alt"></i> Já tenho conta
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
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