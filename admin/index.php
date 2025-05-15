<?php
session_start();
require_once 'config.php';

// Redirecionar para dashboard se estiver logado, senão para login
if (is_logged_in()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;