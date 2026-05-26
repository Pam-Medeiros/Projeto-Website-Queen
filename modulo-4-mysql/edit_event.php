<?php
// 1. Inclui configurações e liga à BD
require_once 'config.php';

// 2. Segurança: Apenas administradores podem aceder a esta página!
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 3. Captura o ID do evento que vem no URL (ex: edit_event.php?id=5)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$message = '';
$error = '';

// AÇÃO: PROCESSAR A ATUALIZAÇÃO DO FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recolhe e limpa os dados enviados pelo formulário
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'];
    $location    = trim($_POST['location']);
    $price       = floatval($_POST['price']);
    $capacity    = intval($_POST['capacity']);
    $status      = $_POST['status'];
    $image       = trim($_POST['image']);

    try {
        // Prepara a query UPDATE.
        $stmt = $pdo->prepare("
            UPDATE events 
            SET title = ?, description = ?, event_date = ?, location = ?, price = ?, capacity = ?, status = ?, image = ? 
            WHERE id = ?
        ");
        
        // Executa a query passando os valores pela mesma ordem dos '?'
        $stmt->execute([$title, $description, $event_date, $location, $price, $capacity, $status, $image, $id]);
        $message = "Evento atualizado com sucesso!";
        
    } catch (PDOException $e) {
        $error = "Erro ao atualizar evento: " . $e->getMessage();
    }
}

// BUSCA DOS DADOS ATUAIS PARA PREENCHER O FORMULÁRIO
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Editar Evento - Queen Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body class="bg-light">

<!-- HEADER -->
<header class="navbar navbar-dark bg-dark sticky-top p-3 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="admin.php"><i class="bi bi-arrow-left me-2"></i> Voltar ao Painel</a>
    </div>
</header>

<!-- MAIN CONTENT -->
<main class="container mt-5 mb-5" style="max-width: 800px;">
    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white p-4">
            <h4 class="mb-0 text-warning">✏️ Editar Evento: <?= htmlspecialchars($event['title']) ?></h4>
        </div>
        
        <div class="card-body p-4">
            
            <!-- Mensagens de Sucesso ou Erro -->
            <?php if($message): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Formulário preenchido com os dados atuais ($event) -->
            <form method="POST" action="edit_event.php?id=<?= $event['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Título do Evento</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Data e Hora</label>
                        <!-- O input do tipo 'datetime-local' exige o formato YYYY-MM-DDTHH:MM. O 'T' no meio é obrigatório. -->
                        <input type="datetime-local" name="event_date" class="form-control" 
                               value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Localização</label>
                        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descrição</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Preço do Bilhete (€)</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= $event['price'] ?>" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Capacidade</label>
                        <input type="number" min="1" name="capacity" class="form-control" value="<?= $event['capacity'] ?>" required>
                        <small class="text-muted">Bilhetes vendidos: <?= $event['tickets_sold'] ?></small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="disponivel" <?= $event['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                            <option value="esgotado" <?= $event['status'] === 'esgotado' ? 'selected' : '' ?>>Esgotado</option>
                            <option value="cancelado" <?= $event['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">URL da Imagem</label>
                    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($event['image']) ?>">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin.php" class="btn btn-outline-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold"><i class="bi bi-save me-2"></i>Guardar Alterações</button>
                </div>
                
            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>