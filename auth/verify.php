<?php

require_once "../config/db.php";

if(!isset($_GET['token'])){
die("Invalid verification link.");
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token=?");
$stmt->execute([$token]);

$user = $stmt->fetch();

if(!$user){
die("Invalid or expired verification link.");
}

$stmt = $pdo->prepare("
UPDATE users
SET verified=1, verify_token=NULL
WHERE id=?
");

$stmt->execute([$user['id']]);

echo "
<h2>Email verified successfully!</h2>
<p>You can now login.</p>
<a href='login.php'>Login</a>
";