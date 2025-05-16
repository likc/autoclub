<?php
session_start();
require_once 'config.php';

// Registrar logout
if (is_logged_in()) {
    log_admin_activity("Logout realizado", "other");
}

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
redirect('login.php');
?>