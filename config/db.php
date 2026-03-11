<?php
$host = 'sql103.infinityfree.com';
$db   = 'if0_41239817_food_cms';
$user = 'if0_41239817';
$pass = 'eYwKSyQejUGo';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Connection error_: " . $e->getMessage());
}
