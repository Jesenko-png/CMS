<?php

if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}

$lang_code = $_SESSION['lang'] ?? 'en';

require __DIR__ . "/../lang/$lang_code.php";