<?php
session_start();
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false]);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['quantity'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id = (int)$data['id'];
$quantity = max(0, (int)$data['quantity']);

if ($quantity === 0) {
    unset($_SESSION['cart'][$id]);
} else {
    $_SESSION['cart'][$id] = $quantity;
}

$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $map = [];
    foreach ($rows as $r) {
        $map[$r['id']] = $r['price'];
    }

    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (isset($map[$pid])) {
            $total += $map[$pid] * $qty;
        }
    }
}

echo json_encode([
    'success' => true,
    'total' => $total
]);
