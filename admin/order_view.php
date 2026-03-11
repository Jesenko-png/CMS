<?php
session_start();
require_once __DIR__.'/../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order){
    echo "Narudžba ne postoji!";
    exit;
}


$stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id=?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Narudžba #<?= $order['id'] ?></title>
<link rel="stylesheet" href="../admin.css">
</head>
<body>
<div class="container">
<h1>Narudžba #<?= $order['id'] ?></h1>
<p>Korisnik: <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['user_email']) ?>)</p>
<p>Ukupno: <?= number_format($order['total'],2) ?> €</p>
<p>Vrijeme: <?= $order['created_at'] ?></p>

<h2>Stavke</h2>
<table style="width:100%; border-collapse:collapse; margin-top:20px;">
<tr>
<th>Proizvod</th>
<th>Cijena (€)</th>
<th>Količina</th>
<th>Subtotal (€)</th>
</tr>
<?php foreach($items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= number_format($item['price'],2) ?> €</td>
<td><?= $item['quantity'] ?></td>
<td><?= number_format($item['price'] * $item['quantity'],2) ?> €</td>
</tr>
<?php endforeach; ?>
</table>

<a href="orders.php" class="btn primary">Nazad na sve narudžbe</a>

</div>
</body>
</html>

