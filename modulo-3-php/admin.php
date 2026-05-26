<?php
require 'config.php';

// Proteção: Apenas administradores entram
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Processar ações de admin
$message = '';
$error = '';

// Remover usuário
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
        $message = "Usuário removido com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao remover usuário: " . $e->getMessage();
    }
}

// Atualizar status do pedido
if (isset($_GET['update_order'])) {
    $order_id = intval($_GET['update_order']);
    $new_status = $_GET['status'];
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $message = "Status do pedido atualizado!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar pedido: " . $e->getMessage();
    }
}

// Adicionar novo produto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);
    $image = trim($_POST['image']);

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category, $image]);
        $message = "Produto adicionado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao adicionar produto: " . $e->getMessage();
    }
}

// Remover produto
if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $message = "Produto removido com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao remover produto: " . $e->getMessage();
    }
}

// Buscar dados
$users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC")->fetchAll();
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// Estatísticas
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'")->fetchColumn();
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
    <title>Queen - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni&display=swap" rel="stylesheet">

    <!-- Style CSS -->
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
                        <li class="nav-item"><a class="nav-link" href="index.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link active" href="admin.php">Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mt-5">
        <h2 class="display-5 border-bottom pb-3 mb-4" style="font-family: 'Cinzel', serif; color: #4b0082;">
            <i class="fas fa-cog"></i> Painel de Administração
        </h2>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3><i class="fas fa-users"></i></h3>
                        <h2><?= $total_users ?></h2>
                        <p>Usuários</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3><i class="fas fa-shopping-cart"></i></h3>
                        <h2><?= $total_orders ?></h2>
                        <p>Pedidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h3><i class="fas fa-box"></i></h3>
                        <h2><?= $total_products ?></h2>
                        <p>Produtos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h3><i class="fas fa-euro-sign"></i></h3>
                        <h2>€<?= number_format($total_revenue ?: 0, 0) ?></h2>
                        <p>Receita</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs de Navegação -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
                    <i class="fas fa-users"></i> Usuários
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">
                    <i class="fas fa-shopping-cart"></i> Encomendas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button">
                    <i class="fas fa-box"></i> Produtos
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <!-- TAB: Usuários -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="fas fa-users"></i> Gerenciar Usuários</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Data Registro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= $user['role'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <?php if ($user['role'] !== 'admin'): ?>
                                                    <a href="admin.php?delete_user=<?= $user['id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Protegido</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Encomendas -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="fas fa-shopping-cart"></i> Gerenciar Encomendas</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Pedido #</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['id'] ?></strong></td>
                                            <td><?= htmlspecialchars($order['username']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                            <td><strong>€<?= number_format($order['total'], 2, ',', '.') ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?= $order['status'] == 'pending' ? 'warning' : 'success' ?>">
                                                    <?= $order['status'] == 'pending' ? 'Pendente' : 'Completo' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($order['status'] == 'pending'): ?>
                                                    <a href="admin.php?update_order=<?= $order['id'] ?>&status=completed"
                                                        class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Marcar Completo
                                                    </a>
                                                <?php else: ?>
                                                    <a href="admin.php?update_order=<?= $order['id'] ?>&status=pending"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fas fa-undo"></i> Reverter
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: Produtos -->
            <div class="tab-pane fade" id="products" role="tabpanel">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="fas fa-plus-circle"></i> Adicionar Novo Produto</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Preço (€)</label>
                                    <input type="number" step="0.01" class="form-control" name="price" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" class="form-control" name="category" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">URL da Imagem</label>
                                    <input type="text" class="form-control" name="image" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Adicionar Produto
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="fas fa-box"></i> Gerenciar Produtos</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Preço</th>
                                        <th>Stock</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= htmlspecialchars($product['name']) ?></td>
                                            <td><?= htmlspecialchars($product['category']) ?></td>
                                            <td>€<?= number_format($product['price'], 2, ',', '.') ?></td>
                                            <td><?= $product['stock'] ?></td>
                                            <td>
                                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin.php?delete_product=<?= $product['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Tem certeza que deseja remover este produto?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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