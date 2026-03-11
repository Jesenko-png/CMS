<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists(__DIR__ . '/../public/uploads/' . $img)) {
        unlink(__DIR__ . '/../public/uploads/' . $img);
    }
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    header("Location: products.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $ext;
        $target = __DIR__ . '/../public/uploads/' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    if (isset($_POST['edit']) && $id) {
        if ($imageName) {
            $old = $pdo->prepare("SELECT image FROM products WHERE id=?");
            $old->execute([$id]);
            $oldImg = $old->fetchColumn();
            if ($oldImg && file_exists(__DIR__ . '/../public/uploads/' . $oldImg)) {
                unlink(__DIR__ . '/../public/uploads/' . $oldImg);
            }
            $pdo->prepare(
                "UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?"
            )->execute([$name, $description, $price, $imageName, $id]);
        } else {
            $pdo->prepare(
                "UPDATE products SET name=?, description=?, price=? WHERE id=?"
            )->execute([$name, $description, $price, $id]);
        }
    }

    if (isset($_POST['add'])) {
        if (!$imageName) die("Slika je obavezna.");
        $pdo->prepare(
            "INSERT INTO products (name, description, price, image) VALUES (?,?,?,?)"
        )->execute([$name, $description, $price, $imageName]);
    }

    header("Location: products.php");
    exit;
}


$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Admin – Proizvodi</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/cms/admin.css?v=2">
</head>
<body class="admin-page">

<!-- ADMIN NAVBAR -->
<div class="admin-navbar">
    <div class="admin-logo">🍔 Food CMS Admin</div>

    <div class="admin-links">
       <a href="../index.php" class="btn primary">Webshop</a>
        <a href="../auth/logout.php" class="btn danger">Logout</a>
    </div>
</div>


<div class="container">

<h1 class="page-title">Manage Products</h1>


<!-- ADD PRODUCT -->
<div class="card add-product-card">

<h2>Add New Product</h2>

<form class="add-form" method="post" enctype="multipart/form-data">

<input name="name" placeholder="Product Name" required>

<textarea name="description" placeholder="Product Description"></textarea>

<input name="price" type="number" step="0.01" placeholder="Price" required>

<input type="file" name="image" required>

<button type="submit" name="add" class="btn success">Add Product</button>

</form>

</div>


<h2 class="section-title">Product List</h2>


<div class="grid">

<?php foreach ($products as $p): ?>

<div class="product-card">

<div class="image-box">

<?php if ($p['image'] && file_exists(__DIR__ . '/../public/uploads/' . $p['image'])): ?>

<img src="/cms/public/uploads/<?= htmlspecialchars($p['image']) ?>" alt="">

<?php else: ?>

<div class="no-image">No Image</div>

<?php endif; ?>

</div>


<form method="post" enctype="multipart/form-data" class="product-form">

<input type="hidden" name="id" value="<?= $p['id'] ?>">

<input name="name" value="<?= htmlspecialchars($p['name']) ?>" placeholder="Name">

<textarea name="description" placeholder="Product Description"><?= htmlspecialchars($p['description']) ?></textarea>

<input name="price" type="number" step="0.01" value="<?= $p['price'] ?>" placeholder="Price">

<input type="file" name="image">

<div class="actions">

<button type="submit" name="edit" class="btn success">Save</button>

<a href="products.php?delete=<?= $p['id'] ?>" class="btn danger"
onclick="return confirm('Delete product?')">Delete</a>

</div>

</form>

</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>
