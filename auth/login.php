<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

require_once "../includes/lang.php";

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
<html lang="<?= $lang_code ?>">
<head>
<meta charset="UTF-8">
<title>Login – Food CMS</title>
<link rel="stylesheet" href="../admin.css">
</head>
<body class="auth-page">

<main class="container">

<div class="auth-lang">


<a href="/cms/index.php?lang=<?= $lang_code ?>" class="btn primary">
<?= $lang['home'] ?>
</a>

</div>

<div class="auth-wrapper">

<div class="auth-container">

<h1 class="auth-title">🍔 Food CMS</h1>

<p class="auth-subtitle"><?= $lang['login_text'] ?></p>

<?php if ($error): ?>
<div class="error"><?= $lang['wrong_login'] ?></div>
<?php endif; ?>

<form method="post" class="auth-form">

<input type="email" name="email" placeholder="<?= $lang['email'] ?>" required>

<input type="password" name="password" placeholder="<?= $lang['password'] ?>" required>

<button type="submit" class="btn success"><?= $lang['login'] ?></button>

</form>

<div class="auth-links">
<p><?= $lang['no_account'] ?></p>
<a href="register.php" class="btn primary"><?= $lang['register'] ?></a>
</div>

</div>

</div>

</main>

<?php require_once '../includes/footer.php'; ?>

</body>