<?php
session_start();
require_once 'config.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
redirect('login.php');
?>