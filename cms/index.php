<?php
session_start();

require_once 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}


if ($_SESSION['user']['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Food CMS</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="container">

<h1>Proizvodi</h1>

<div style="margin-bottom:20px;">
    <a href="cart.php" class="btn primary">Pogledaj korpu</a>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="auth/logout.php" class="btn danger" style="float:right;">Logout</a>
    <?php endif; ?>
</div>

<div class="grid">
<?php foreach ($products as $product): ?>
    <div class="product-card">

        <div class="image-box">
            <?php if (!empty($product['image']) && file_exists('public/uploads/'.$product['image'])): ?>
                <img src="public/uploads/<?= htmlspecialchars($product['image']) ?>">
            <?php else: ?>
                <span class="no-image">No image</span>
            <?php endif; ?>
        </div>

        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <strong><?= number_format($product['price'], 2) ?> €</strong>

        <form action="add_to_cart.php" method="post" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
            <button type="submit" class="btn success">Dodaj u korpu</button>
        </form>

    </div>
<?php endforeach; ?>
</div>

</div>
</body>
</html>
