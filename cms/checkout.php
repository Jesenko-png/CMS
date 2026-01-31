<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$cart = $_SESSION['cart'];

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$map = [];

foreach ($products as $p) {
    $map[$p['id']] = $p['price'];
    $total += $p['price'] * $cart[$p['id']];
}


$stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
$stmt->execute([$user_id, $total]);
$order_id = $pdo->lastInsertId();


$stmt = $pdo->prepare("
    INSERT INTO order_items (order_id, product_id, price, quantity)
    VALUES (?, ?, ?, ?)
");

foreach ($cart as $pid => $qty) {
    $stmt->execute([
        $order_id,
        $pid,
        $map[$pid],
        $qty
    ]);
}

$user_email = $_SESSION['user']['email'];
$user_name = $_SESSION['user']['name'];

$to = $user_email;
$subject = "Potvrda narudžbe #".$order_id;
$message = "Pozdrav $user_name,\n\nHvala na kupovini! Vaša narudžba #$order_id je primljena.\n\nUkupno: ".number_format($total,2)." €\n\nPozdrav,\nFood CMS Team";
$headers = "From: info@foodcms.local";


mail($to, $subject, $message, $headers);

unset($_SESSION['cart']);

header("Location: order_success.php");
exit;

