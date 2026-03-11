<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/db.php';
require_once "../includes/lang.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/PHPMailer.php';
require '../vendor/phpmailer/SMTP.php';
require '../vendor/phpmailer/Exception.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if ($password !== $confirm) {

$error = $lang['password_mismatch'];

} else {

$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {

$error = $lang['email_exists'];

} else {

$hash = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$pdo->prepare("
INSERT INTO users (name,email,password,role,verify_token)
VALUES (?,?,?,?,?)
")->execute([$name,$email,$hash,'user',$token]);

$verify_link = "https://jesenko.free.nf/cms/auth/verify.php?token=".$token;

$subject = $lang['verify_subject'];

$message =
$lang['hello']." $name,<br><br>".
$lang['verify_text']."<br><br>".
"<a href='$verify_link'>$verify_link</a><br><br>".
$lang['regards']."<br>Food CMS";

$mail = new PHPMailer(true);

try {

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'idrizovicjesenko1@gmail.com';
$mail->Password = 'fekmimiqwkwdiapb';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('yourgmail@gmail.com', 'Food CMS');
$mail->addAddress($email, $name);

$mail->isHTML(true);
$mail->CharSet = 'UTF-8';

$mail->Subject = $subject;
$mail->Body = $message;

$mail->send();

$success = $lang['register_success_verify'];

} catch (Exception $e) {

$error = "Mailer Error: ".$mail->ErrorInfo;

}

}

}

}
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
<head>
<meta charset="UTF-8">
<title><?= $lang['register'] ?> – Food CMS</title>
<link rel="stylesheet" href="/cms/admin.css">
</head>
<body class="auth-page">

<div class="auth-lang">

	<a href="/cms/index.php?lang=<?= $lang_code ?>" class="btn primary">
		<?= $lang['home'] ?>
</a>

</div>

</div>
</div>

<div class="auth-wrapper">

<div class="auth-container">

<h1><?= $lang['register'] ?></h1>

<?php if ($error): ?>
<div class="error"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="post" class="auth-form">

<input type="text" name="name" placeholder="<?= $lang['name'] ?>" required>

<input type="email" name="email" placeholder="<?= $lang['email'] ?>" required>

<input type="password" name="password" placeholder="<?= $lang['password'] ?>" required>

<input type="password" name="confirm" placeholder="<?= $lang['confirm_password'] ?>" required>

<button type="submit" class="btn success">
<?= $lang['register'] ?>
</button>

</form>

<p style="margin-top:10px;">
<?= $lang['have_account'] ?>
<a href="login.php?lang=<?= $lang_code ?>" class="btn primary" style="padding:5px 10px;">
<?= $lang['login'] ?>
</a>
</p>

</div>

</div>
<?php require_once '../includes/footer.php'; ?>
</body>
</html>