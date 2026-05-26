<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$message = ''; 
$error = '';

// --- AÇÃO: ADICIONAR EVENTO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'];
    $location    = trim($_POST['location']);
    $price       = floatval($_POST['price']);
    $capacity    = intval($_POST['capacity']);
    $status      = $_POST['status'];
    $image       = trim($_POST['image']) ?: 'src/images/default_event.jpg';
    try {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location, price, capacity, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $location, $price, $capacity, $status, $image]);
        $message = "Evento '$title' criado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao criar evento: " . $e->getMessage();
    }
}

// --- AÇÃO: EDITAR EVENTO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_event_id'])) {
    $id          = intval($_POST['edit_event_id']);
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'];
    $location    = trim($_POST['location']);
    $price       = floatval($_POST['price']);
    $capacity    = intval($_POST['capacity']);
    $status      = $_POST['status'];
    $image       = trim($_POST['image']);
    try {
        $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, price=?, capacity=?, status=?, image=? WHERE id=?");
        $stmt->execute([$title, $description, $event_date, $location, $price, $capacity, $status, $image, $id]);
        $message = "Evento '$title' atualizado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar evento: " . $e->getMessage();
    }
}

// --- AÇÃO: ELIMINAR EVENTO ---
if (isset($_GET['delete_event'])) {
    $event_id = intval($_GET['delete_event']);
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $message = "Evento removido com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao remover evento: " . $e->getMessage();
    }
}

// --- AÇÃO: ADICIONAR PRODUTO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category    = trim($_POST['category']);
    $image       = trim($_POST['image']) ?: 'src/images/default-product.jpg';
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category, $image]);
        $message = "Produto adicionado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao adicionar produto: " . $e->getMessage();
    }
}

// --- AÇÃO: EDITAR PRODUTO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product_id'])) {
    $id          = intval($_POST['edit_product_id']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category    = trim($_POST['category']);
    $image       = trim($_POST['image']);
    try {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $stock, $category, $image, $id]);
        $message = "Produto '$name' atualizado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar produto: " . $e->getMessage();
    }
}

// --- AÇÃO: ELIMINAR PRODUTO ---
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

// --- AÇÃO: ATUALIZAR STATUS DO PEDIDO ---
if (isset($_GET['update_order']) && isset($_GET['status'])) {
    $order_id   = intval($_GET['update_order']);
    $new_status = $_GET['status'];
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $message = "Status da encomenda #$order_id atualizado para '$new_status'!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar encomenda: " . $e->getMessage();
    }
}

// --- AÇÃO: EDITAR UTILIZADOR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $uid      = intval($_POST['edit_user_id']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    try {
        if (!empty($_POST['password'])) {
            $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
            $stmt->execute([$username, $email, $hash, $uid]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?");
            $stmt->execute([$username, $email, $uid]);
        }
        $message = "Utilizador #$uid atualizado com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao atualizar utilizador: " . $e->getMessage();
    }
}

// --- AÇÃO: ELIMINAR UTILIZADOR ---
if (isset($_GET['delete_user'])) {
    $del_user_id = intval($_GET['delete_user']);
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$del_user_id]);
        $message = "Utilizador removido com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao remover utilizador: " . $e->getMessage();
    }
}

// BUSCA DE DADOS
$events   = $pdo->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$users    = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
$orders   = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC")->fetchAll();
$total_users   = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_orders  = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_events  = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status = 'completed'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen." />
    <title>Queen - Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Libre+Bodoni:ital,wght@0,400..700;1,400..700&family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="src/css/style.css"/>
</head>
<body class="bg-light">

<header class="navbar navbar-dark bg-dark sticky-top p-3 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">QUEEN - Área Administrativa</a>
        <div>
            <span class="text-white me-3">Olá, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2">Ver Site</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm me-2">Sair</a>
        </div>
    </div>
</header>

<main class="container mt-4 mb-5">

    <?php if($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ESTATÍSTICAS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Utilizadores</h5>
                    <p class="display-6"><?= $total_users ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Eventos</h5>
                    <p class="display-6"><?= $total_events ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning text-dark mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Encomendas</h5>
                    <p class="display-6"><?= $total_orders ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Receita Total</h5>
                    <p class="display-6">€<?= number_format($total_revenue ?: 0, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-eventos" type="button">🎟️ Eventos</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-encomendas" type="button">📦 Encomendas</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-produtos" type="button">👕 Loja</button></li>
        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-users" type="button">👥 Utilizadores</button></li>
    </ul>

    <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow-sm">

        <!-- ABA EVENTOS -->
        <div class="tab-pane fade show active" id="tab-eventos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Gestão de Eventos</h4>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddEvent">
                    <i class="fas fa-plus"></i> Novo Evento
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Título</th><th>Data</th><th>Local</th><th>Ocupação</th><th>Status</th><th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($events as $e): ?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($e['title']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($e['event_date'])) ?></td>
                            <td><?= htmlspecialchars($e['location']) ?></td>
                            <td>
                                <?php
                                $vendidos = $e['tickets_sold'] ?? 0;
                                $capacidade = max(1, $e['capacity'] ?? 1);
                                $percentagem = ($vendidos / $capacidade) * 100;
                                ?>
                                <?= $vendidos ?> / <?= $e['capacity'] ?? 0 ?> (<?= number_format($percentagem, 1) ?>%)
                            </td>
                            <td>
                                <?php if($e['status'] === 'disponivel'): ?>
                                    <span class="badge bg-success">Disponível</span>
                                <?php elseif($e['status'] === 'esgotado'): ?>
                                    <span class="badge bg-danger">Esgotado</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Cancelado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-warning text-dark" title="Editar"
                                    data-bs-toggle="modal" data-bs-target="#modalEditEvent"
                                    data-id="<?= $e['id'] ?>"
                                    data-title="<?= htmlspecialchars($e['title']) ?>"
                                    data-date="<?= $e['event_date'] ?>"
                                    data-desc="<?= htmlspecialchars($e['description']) ?>"
                                    data-loc="<?= htmlspecialchars($e['location']) ?>"
                                    data-img="<?= htmlspecialchars($e['image']) ?>"
                                    data-price="<?= $e['price'] ?>"
                                    data-cap="<?= $e['capacity'] ?>"
                                    data-status="<?= $e['status'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_event=<?= $e['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apagar este evento?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(!$events): ?><tr><td colspan="7" class="text-center">Nenhum evento registado.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ABA ENCOMENDAS -->
        <div class="tab-pane fade" id="tab-encomendas" role="tabpanel">
            <h4 class="mb-3">Gestão de Encomendas</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr><th>ID Pedido</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td><strong>#<?= $o['id'] ?></strong></td>
                            <td><?= htmlspecialchars($o['username']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></td>
                            <td class="fw-bold">€<?= number_format($o['total'], 2, ',', '.') ?></td>
                            <td>
                                <?php if($o['status'] === 'completed'): ?>
                                    <span class="badge bg-success">Concluída</span>
                                <?php elseif($o['status'] === 'cancelled'): ?>
                                    <span class="badge bg-danger">Cancelada</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="order_details.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-info text-white" title="Ver Detalhes"><i class="fas fa-eye"></i></a>
                                <?php if($o['status'] === 'pending'): ?>
                                    <a href="?update_order=<?= $o['id'] ?>&status=completed" class="btn btn-sm btn-success" title="Marcar Completo"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ABA LOJA -->
        <div class="tab-pane fade" id="tab-produtos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Gestão de Merchandising (Loja)</h4>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                    <i class="fas fa-plus"></i> Novo Produto
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Imagem</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Stock</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><img src="<?= htmlspecialchars($p['image']) ?>" alt="img" width="40" height="40" style="object-fit:cover; border-radius:4px;"></td>
                            <td class="fw-bold"><?= htmlspecialchars($p['name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['category']) ?></span></td>
                            <td>€<?= number_format($p['price'], 2, ',', '.') ?></td>
                            <td><?= $p['stock'] ?></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-warning text-dark" title="Editar Produto"
                                    data-bs-toggle="modal" data-bs-target="#modalEditProduct"
                                    data-id="<?= $p['id'] ?>"
                                    data-name="<?= htmlspecialchars($p['name']) ?>"
                                    data-desc="<?= htmlspecialchars($p['description']) ?>"
                                    data-price="<?= $p['price'] ?>"
                                    data-stock="<?= $p['stock'] ?>"
                                    data-cat="<?= htmlspecialchars($p['category']) ?>"
                                    data-img="<?= htmlspecialchars($p['image']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete_product=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apagar produto?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ABA UTILIZADORES -->
        <div class="tab-pane fade" id="tab-users" role="tabpanel">
            <h4 class="mb-3">Gestão de Utilizadores</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Nome de Utilizador</th><th>Email</th><th>Data de Registo</th><th>Papel (Role)</th><th class="text-end">Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td><?= $u['role'] === 'admin' ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-primary">User</span>' ?></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-warning text-dark" title="Editar Utilizador"
                                    data-bs-toggle="modal" data-bs-target="#modalEditUser"
                                    data-id="<?= $u['id'] ?>"
                                    data-username="<?= htmlspecialchars($u['username']) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <?php if($u['role'] !== 'admin'): ?>
                                    <a href="?delete_user=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminar permanentemente este utilizador?')">
                                        <i class="fas fa-ban"></i> Excluir
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
</main>

<!-- MODAL ADICIONAR EVENTO -->
<div class="modal fade" id="modalAddEvent" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-plus"></i> Registar Novo Evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Título *</label><input type="text" name="title" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Data e Hora *</label><input type="datetime-local" name="event_date" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea name="description" class="form-control" rows="3"></textarea></div>
            <div class="col-md-6"><label class="form-label">Localização *</label><input type="text" name="location" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">URL da Imagem</label><input type="text" name="image" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" step="0.01" min="0" name="price" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Capacidade *</label><input type="number" min="1" name="capacity" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="disponivel" selected>Disponível</option>
                <option value="esgotado">Esgotado</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <input type="hidden" name="add_event" value="1">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Salvar Evento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR EVENTO -->
<div class="modal fade" id="modalEditEvent" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Editar Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_event_id" id="editEventId">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Título *</label><input type="text" name="title" id="editEventTitle" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Data e Hora *</label><input type="datetime-local" name="event_date" id="editEventDate" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea name="description" id="editEventDesc" class="form-control" rows="3"></textarea></div>
            <div class="col-md-6"><label class="form-label">Localização *</label><input type="text" name="location" id="editEventLoc" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">URL da Imagem</label><input type="text" name="image" id="editEventImg" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" step="0.01" name="price" id="editEventPrice" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Capacidade *</label><input type="number" min="1" name="capacity" id="editEventCap" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Status</label>
              <select name="status" id="editEventStatus" class="form-select">
                <option value="disponivel">Disponível</option>
                <option value="esgotado">Esgotado</option>
                <option value="cancelado">Cancelado</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning fw-bold">Atualizar Evento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL ADICIONAR PRODUTO -->
<div class="modal fade" id="modalAddProduct" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="fas fa-plus"></i> Adicionar Novo Produto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nome do Produto *</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Categoria</label><input type="text" name="category" class="form-control" value="Geral"></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" step="0.01" min="0" name="price" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Stock *</label><input type="number" min="0" name="stock" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Caminho da Imagem</label><input type="text" name="image" class="form-control" placeholder="src/images/..."></div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <input type="hidden" name="add_product" value="1">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Salvar Produto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR PRODUTO -->
<div class="modal fade" id="modalEditProduct" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Editar Produto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_product_id" id="editProductId">
          <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Nome do Produto *</label><input type="text" name="name" id="editProductName" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Categoria</label><input type="text" name="category" id="editProductCat" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea name="description" id="editProductDesc" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" step="0.01" name="price" id="editProductPrice" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Stock *</label><input type="number" min="0" name="stock" id="editProductStock" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Caminho da Imagem</label><input type="text" name="image" id="editProductImg" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning fw-bold">Atualizar Produto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDITAR UTILIZADOR -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold"><i class="fas fa-user-edit"></i> Editar Utilizador #<span id="displayUserId"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_user_id" id="editUserId">
          <div class="mb-3"><label class="form-label fw-bold">Nome de Utilizador</label><input type="text" name="username" id="editUserUsername" class="form-control" required></div>
          <div class="mb-3"><label class="form-label fw-bold">Email</label><input type="email" name="email" id="editUserEmail" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label fw-bold">Nova Password <small class="text-muted">(deixar vazio para não alterar)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Nova senha...">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning fw-bold">Guardar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
<script>
// Preencher Modal Editar Evento
const modalEditEvent = document.getElementById('modalEditEvent');
if (modalEditEvent) {
    modalEditEvent.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('editEventId').value    = btn.getAttribute('data-id');
        document.getElementById('editEventTitle').value  = btn.getAttribute('data-title');
        document.getElementById('editEventDesc').value   = btn.getAttribute('data-desc');
        document.getElementById('editEventLoc').value    = btn.getAttribute('data-loc');
        document.getElementById('editEventImg').value    = btn.getAttribute('data-img');
        document.getElementById('editEventPrice').value  = btn.getAttribute('data-price');
        document.getElementById('editEventCap').value    = btn.getAttribute('data-cap');
        document.getElementById('editEventStatus').value = btn.getAttribute('data-status');
        let dateVal = btn.getAttribute('data-date').replace(' ', 'T');
        document.getElementById('editEventDate').value   = dateVal;
    });
}

// Preencher Modal Editar Produto
const modalEditProduct = document.getElementById('modalEditProduct');
if (modalEditProduct) {
    modalEditProduct.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('editProductId').value    = btn.getAttribute('data-id');
        document.getElementById('editProductName').value  = btn.getAttribute('data-name');
        document.getElementById('editProductDesc').value  = btn.getAttribute('data-desc');
        document.getElementById('editProductPrice').value = btn.getAttribute('data-price');
        document.getElementById('editProductStock').value = btn.getAttribute('data-stock');
        document.getElementById('editProductCat').value   = btn.getAttribute('data-cat');
        document.getElementById('editProductImg').value   = btn.getAttribute('data-img');
    });
}

// Preencher Modal Editar Utilizador
const modalEditUser = document.getElementById('modalEditUser');
if (modalEditUser) {
    modalEditUser.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('displayUserId').innerText = btn.getAttribute('data-id');
        document.getElementById('editUserId').value        = btn.getAttribute('data-id');
        document.getElementById('editUserUsername').value  = btn.getAttribute('data-username');
        document.getElementById('editUserEmail').value     = btn.getAttribute('data-email');
    });
}
</script>
</body>
</html>
