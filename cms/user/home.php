<?php
require "../config/db.php";
session_start();

$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<h2>Proizvodi</h2>

<?php foreach($products as $p): ?>
<div>
    <img src="/food-cms/public/uploads/<?= $p['image'] ?>" width="150" alt="<?= $p['name'] ?>">
    <h3><?= $p['name'] ?></h3>
    <p><?= $p['description'] ?></p>
    <p><?= $p['price'] ?> KM</p>
</div>
<hr>
<?php endforeach; ?>
