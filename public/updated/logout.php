<?php
session_start();
setcookie('remember_token', '', time() - 3600, '/', '', true, true);
session_destroy();
header('Location: login.php');
exit;
