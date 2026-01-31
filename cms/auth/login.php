<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'admin') {
            header("Location: ../admin/products.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
    } else {
        $error = "Pogrešan email ili lozinka";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="../admin.css">
</head>
<body>

<div class="auth-container">
<h1>Login</h1>

<?php if ($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="post" class="auth-form">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Lozinka" required>
    <button type="submit">Login</button>
</form>

<p style="margin-top:10px;">
    <a href="register.php" class="btn primary">Registracija</a>
</p>
</div>

</body>
</html>
