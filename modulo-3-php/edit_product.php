<?php
require 'config.php';

// 1. Segurança: Apenas admin pode entrar aqui
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Verificar se tem ID na URL
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);
$message = '';
$error = '';

// 3. Processar o Formulário (Quando clicar em Salvar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);
    $image = trim($_POST['image']);

    try {
        // Comando SQL UPDATE
        $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock, $category, $image, $id]);
        
        $message = "Produto atualizado com sucesso!";
        // Atualiza os dados da variável para mostrar no form já alterado
        $product = ['id'=>$id, 'name'=>$name, 'description'=>$description, 'price'=>$price, 'stock'=>$stock, 'category'=>$category, 'image'=>$image];
        
        // Redirecionar após 1 segundo para o admin (opcional)
        header("refresh:1;url=admin.php");
        
    } catch (PDOException $e) {
        $error = "Erro ao atualizar: " . $e->getMessage();
    }
} else {
    // 4. Carregar os dados atuais do produto para preencher o formulário
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        die("Produto não encontrado.");
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
   <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, 
    tours e entre em contato." />
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour" />
    <title>Queen - Editar Produto</title>
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
                    <img src="src/images/logo_queen.png" alt="Queen" height="50">
                </a>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="admin.php">Voltar ao Painel</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0" style="font-family: 'Cinzel', serif;">
                            <i class="fas fa-edit"></i> Editar Produto #<?= $product['id'] ?>
                        </h4>
                    </div>
                    <div class="card-body">

                        <?php if ($message): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $message ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?= htmlspecialchars($product['name']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Preço (€)</label>
                                    <input type="number" step="0.01" class="form-control" name="price" 
                                           value="<?= htmlspecialchars($product['price']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" 
                                           value="<?= htmlspecialchars($product['stock']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" class="form-control" name="category" 
                                           value="<?= htmlspecialchars($product['category']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL da Imagem</label>
                                    <input type="text" class="form-control" name="image" 
                                           value="<?= htmlspecialchars($product['image']) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-dark text-white text-center p-4 mt-5">
        <div class="container">
            <p>&copy; 2025 Website Fã Clube Queen. Todos os direitos reservados.</p>
        </div>
    </footer>                        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>