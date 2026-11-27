<?php
//Adios amigos
require __DIR__ . '/../back/bootstrap.php';

demarrer_session();
$_SESSION = [];
setcookie(session_name(), '', time() - 3600, '/');
session_destroy();

header('Location: /');   
exit;
