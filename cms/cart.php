<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $map = [];
    foreach ($products as $p) {
        $map[$p['id']] = $p;
    }

    foreach ($_SESSION['cart'] as $id => $qty) {
        if (isset($map[$id])) {
            $item = $map[$id];
            $item['quantity'] = $qty;
            $item['subtotal'] = $qty * $item['price'];
            $total += $item['subtotal'];
            $cart_items[] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Korpa</title>
<link rel="stylesheet" href="/cms/admin.css">
</head>
<body>

<div class="container">
    <h1>Vaša korpa</h1>

    <div style="margin-bottom:20px;">
        <a href="index.php" class="btn primary">Nastavi kupovinu</a>
        <a href="auth/logout.php" class="btn danger" style="float:right;">Logout</a>
    </div>

<?php if (empty($cart_items)): ?>
    <p>Korpa je prazna.</p>
<?php else: ?>

<table style="width:100%; border-collapse:collapse;">
<tr>
    <th>Proizvod</th>
    <th>Cijena (€)</th>
    <th>Količina</th>
    <th>Subtotal (€)</th>
    <th></th>
</tr>

<?php foreach ($cart_items as $item): ?>
<tr>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td><?= number_format($item['price'], 2) ?> €</td>
    <td>
        <input
            type="number"
            class="quantity-input"
            data-id="<?= $item['id'] ?>"
            data-price="<?= $item['price'] ?>"
            value="<?= $item['quantity'] ?>"
            min="0"
            style="width:60px;"
        >
    </td>
    <td class="subtotal" data-id="<?= $item['id'] ?>">
        <?= number_format($item['subtotal'], 2) ?> €
    </td>
    <td>
        <a href="cart_remove.php?id=<?= $item['id'] ?>" class="btn danger">
            Ukloni
        </a>
    </td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="5" style="text-align:right;">
        <form action="checkout.php" method="POST">
            <button class="btn success">Završi kupovinu</button>
        </form>
    </td>
</tr>


<tr>
    <td colspan="3" style="text-align:right;"><strong>Ukupno:</strong></td>
    <td colspan="2" id="cart-total">
        <strong><?= number_format($total, 2) ?> €</strong>
    </td>
</tr>
</table>

<?php endif; ?>
</div>

<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', function () {

        const id = this.dataset.id;
        const price = parseFloat(this.dataset.price);
        const quantity = parseInt(this.value);

        this.disabled = true;
        this.style.opacity = "0.5";

        fetch('/cms/cart_update.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id, quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelector('.subtotal[data-id="'+id+'"]').textContent =
                    (quantity * price).toFixed(2) + ' €';

                document.getElementById('cart-total').textContent =
                    data.total.toFixed(2) + ' €';
            }
        })
        .finally(() => {
            this.disabled = false;
            this.style.opacity = "1";
        });
    });
});
</script>


</body>
</html>
