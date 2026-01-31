<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $error = "Lozinke se ne poklapaju!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email već postoji!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare(
                "INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)"
            )->execute([$name,$email,$hash,'user']);
            $success = "Registracija uspješna! Možete se prijaviti.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Registracija</title>
<link rel="stylesheet" href="/cms/admin.css">
</head>
<body>
<div class="auth-container">
    <h1>Registracija</h1>

    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

    <form method="post" class="auth-form">
        <input type="text" name="name" placeholder="Ime" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Lozinka" required>
        <input type="password" name="confirm" placeholder="Potvrdi lozinku" required>
        <button type="submit" class="btn success">Registracija</button>
    </form>

    <p style="margin-top:10px;">Već imate račun? 
        <a href="login.php" class="btn primary" style="padding:5px 10px;">Login</a>
    </p>
</div>
</body>
</html>

