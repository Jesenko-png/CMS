<?php
session_start();

require_once 'config/db.php';
require_once "includes/lang.php";

$search = $_GET['search'] ?? '';

if($search){

$stmt = $pdo->prepare("
SELECT * FROM products
WHERE name LIKE ? OR description LIKE ?
ORDER BY id DESC
");

$stmt->execute([
"%$search%",
"%$search%"
]);

}else{

$stmt = $pdo->query("
SELECT * FROM products
ORDER BY id DESC
");

}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food CMS</title>
<link rel="stylesheet" href="admin.css">
</head>
<body class="front-page">

<!-- NAVBAR -->
<div class="navbar">

<div class="logo">🍔 Food CMS</div>

<div class="nav-links">

<form method="GET" class="search-box">

<input 
type="text" 
name="search" 
placeholder="Search products..."
value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
autocomplete="off"
>

<button class="btn primary">Search</button>

</form>

<div class="lang-dropdown">


<div class="lang-menu">



</div>

</div>

<?php if(isset($_SESSION['user'])): ?>

<a href="auth/logout.php" class="btn danger"><?= $lang['logout'] ?></a>

<?php else: ?>

<a href="auth/login.php" class="btn primary"><?= $lang['login'] ?></a>

<?php endif; ?>

</div>

</div>

<div class="container">

<h1 class="page-title"><?= $lang['title_products'] ?></h1>

<?php if(empty($products)): ?>

<p style="margin-top:20px;">No products found.</p>

<?php endif; ?>

<div class="grid">

<?php foreach ($products as $product): ?>

<div class="product-card">

<div class="image-box">

<?php if (!empty($product['image']) && file_exists('public/uploads/'.$product['image'])): ?>

<img src="public/uploads/<?= htmlspecialchars($product['image']) ?>">

<?php else: ?>

<span class="no-image"><?= $lang['no_image'] ?></span>

<?php endif; ?>

</div>

<div class="product-info">

<h3><?= htmlspecialchars($product['name']) ?></h3>

<p class="product-desc">
<?= htmlspecialchars($product['description']) ?>
</p>

<div class="product-bottom">

<strong class="price">
<?= number_format($product['price'], 2) ?> €
</strong>

<form action="add_to_cart.php" method="post" class="add-to-cart-form">

<input type="hidden" name="product_id" value="<?= $product['id'] ?>">

<input type="number" name="quantity" value="1" min="1" class="quantity-input">

<button type="submit" class="btn success">
<?= $lang['add_to_cart'] ?>
</button>

</form>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>
 <?php require_once 'includes/footer.php';?>

</body>
</html>