<?php
session_start();
require_once "includes/lang.php";
if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
<head>
<meta charset="UTF-8">
<title><?= $lang['order_success_title'] ?></title>
<link rel="stylesheet" href="/cms/admin.css">
</head>
<body>

<div class="container" style="text-align:center;">

<h1>✅ <?= $lang['thank_you'] ?></h1>

<p><?= $lang['order_received'] ?></p>

<a href="index.php?lang=<?= $lang_code ?>" class="btn primary">
<?= $lang['back_products'] ?>
</a>

</div>

</body>
</html>