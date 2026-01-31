<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
<meta charset="UTF-8">
<title>Narudžba uspješna</title>
<link rel="stylesheet" href="/cms/admin.css">
</head>
<body>

<div class="container" style="text-align:center;">
    <h1>✅ Hvala na kupovini!</h1>
    <p>Vaša narudžba je uspješno zaprimljena.</p>
    <a href="index.php" class="btn primary">Nazad na proizvode</a>
</div>

</body>
</html>
