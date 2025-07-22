<?php
require_once 'config.php';
require_once 'classes/User.php';

$user = new User();
$user->logout();

// Redirect to home page with a success message
header('Location: index.php?logged_out=1');
exit;
?>