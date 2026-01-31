<?php
if (!isset($_SESSION['user'])) {
  header("Location: /auth/login.php");
  exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
  exit("Nemaš pristup");
}
