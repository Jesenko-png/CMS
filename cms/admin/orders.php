<?php
session_start();
require_once __DIR__.'/../config/db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Narudžbe - Admin Panel</title>
<link rel="stylesheet" href="../admin.css">
</head>
<body>
<div class="container">
<h1>Sve narudžbe</h1>

<?php if(empty($orders)): ?>
<p>Još nema narudžbi.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; margin-top:20px;">
<tr>
<th>ID</th>
<th>Korisnik</th>
<th>Email</th>
<th>Ukupno (€)</th>
<th>Vrijeme</th>
<th>Detalji</th>
</tr>
<?php foreach($orders as $order): ?>
<tr>
<td><?= $order['id'] ?></td>
<td><?= htmlspecialchars($order['user_name']) ?></td>
<td><?= htmlspecialchars($order['user_email']) ?></td>
<td><?= number_format($order['total'],2) ?> €</td>
<td><?= $order['created_at'] ?></td>
<td>
    <a href="order_view.php?id=<?= $order['id'] ?>" class="btn primary">Pogledaj</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

</div>
</body>
</html>

