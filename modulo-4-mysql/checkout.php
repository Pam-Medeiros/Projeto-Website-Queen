<?php
require 'config.php';

// 1. Verificar se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Verificar se o carrinho está vazio
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: loja.php");
    exit();
}

// 3. Processar o checkout quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        // Calcular o total real baseado nos dados da sessão
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        // A. Criar a Ordem (Pedido)
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, total) VALUES (?, 'pending', ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $orderId = $pdo->lastInsertId();

        // B. Inserir os itens do pedido
        foreach ($_SESSION['cart'] as $item) {
            // Se for produto, decrementa o stock (opcional)
            if ($item['type'] == 'product') {
                $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $stmtStock->execute([$item['qty'], $item['id'], $item['qty']]);
            }

            // Insere na tabela order_items
           $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, item_type) VALUES (?, ?, ?, ?, ?)");
            $stmtItem->execute([$orderId, $item['id'], $item['qty'], $item['price'], $item['type']]);
        }

        // SE FOR EVENTO, AUMENTA A CONTAGEM DE BILHETES VENDIDOS
            if ($item['type'] == 'event') {
                $stmtEvent = $pdo->prepare("UPDATE events SET tickets_sold = tickets_sold + ? WHERE id = ?");
                $stmtEvent->execute([$item['qty'], $item['id']]);
            }

        $pdo->commit();

        // Limpar o carrinho após a compra
        $_SESSION['cart'] = [];

        // Redirecionar para página de sucesso
        header("Location: order_success.php?order_id=" . $orderId);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Erro ao processar o pedido: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Pamela Medeiros" />
    <meta name="description" content="Website dedicado à lendária banda de rock Queen. Conheça a história, discografia, tours e entre em contato."/>
    <meta name="keywords" content="Queen, banda, rock, Freddie Mercury, Brian May, Roger Taylor, John Deacon, música, história, álbuns, tour"/>
    <title>Queen - Finalizar Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body>

<header class="bg-dark text-white p-3">
    <div class="container text-center">
        <a href="index.php"><img src="src/images/logo_queen.png" alt="Logo" height="50"></a>
    </div>
</header>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="text-center mb-4" style="font-family: 'Cinzel', serif;">Resumo do Pedido</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <ul class="list-group list-group-flush mb-4">
                        <?php 
                        $resumoTotal = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $sub = $item['price'] * $item['qty'];
                            $resumoTotal += $sub;
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">Qtd: <?= $item['qty'] ?> x €<?= number_format($item['price'], 2) ?></small>
                                </div>
                                <span class="text-muted">€<?= number_format($sub, 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <strong>Total (EUR)</strong>
                            <strong class="text-danger">€<?= number_format($resumoTotal, 2, ',', '.') ?></strong>
                        </li>
                    </ul>

                    <form method="POST">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Ao clicar em confirmar, o seu pedido será processado com o método de pagamento padrão.
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-check"></i> Confirmar e Pagar
                        </button>
                    </form>
                    
                    <a href="cart.php" class="btn btn-link w-100 mt-3 text-muted">Voltar ao carrinho</a>
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