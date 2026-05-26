<?php
require 'config.php';

$error = '';
$success = '';

// Processar o formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirecionar conforme o papel do usuário
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: profile.php");
                }
                exit();
            } else {
                $error = "Nome de usuário/email ou senha incorretos.";
            }
        } catch (PDOException $e) {
            $error = "Erro ao processar login: " . $e->getMessage();
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
    <title>Queen - Login</title>

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
            <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
            <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
            <li class="nav-item"><a class="nav-link" href="loja.php">Loja</a></li>
            <li class="nav-item"><a class="nav-link" href="albuns.php">Álbuns</a></li>
            <li class="nav-item"><a class="nav-link" href="tour.php">Tour</a></li>
            <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
            <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
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
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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
                <a class="nav-link active" href="login.php">
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
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="font-family: 'Cinzel', serif; color: #4b0082;">
                            <i class="fas fa-user-circle"></i> Login
                        </h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> <?= $success ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Nome de Usuário ou Email
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Senha
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </button>
                        </form>

                        <hr>

                        <p class="text-center mb-0">
                            Não tem uma conta? 
                            <a href="register.php" style="color: #4b0082; font-weight: bold;">
                                Registre-se aqui
                            </a>
                        </p>
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