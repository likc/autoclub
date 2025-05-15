<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'minec761_likc');
define('DB_PASS', 'rw23xrd807ox');
define('DB_NAME', 'minec761_autoclub');

// Configurações do sistema
define('SITE_NAME', 'AutoClub Painel Administrativo');
define('SITE_URL', 'https://autoclub.jp/admin');

// Conexão com o banco de dados
function db_connect() {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        die('Erro de conexão: ' . $connection->connect_error);
    }
    
    $connection->set_charset("utf8mb4");
    
    return $connection;
}

// Função para sanitizar entradas
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para redirecionar
function redirect($url) {
    header("Location: $url");
    exit;
}

// Função para verificar se o usuário está logado
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

// Função para verificar se o usuário está logado e redirecioná-lo se não estiver
function check_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

// Função para gerar mensagem de alerta
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Função para exibir alerta
function show_alert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $type = $alert['type'];
        $message = $alert['message'];
        
        echo "<div class='alert alert-$type'>$message</div>";
        
        unset($_SESSION['alert']);
    }
}

// Função para fazer upload de imagem
function upload_image($file, $target_dir = "uploads/") {
    // Verificar se o diretório existe, senão cria
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Gerar nome único para o arquivo
    $timestamp = time();
    $filename = basename($file["name"]);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // CORREÇÃO: Garantir que a extensão sempre esteja presente no nome do arquivo
    if (empty($extension)) {
        // Detectar o tipo MIME e atribuir uma extensão apropriada
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file["tmp_name"]);
        
        switch ($mime_type) {
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            default:
                $extension = 'jpg'; // Default para imagens sem extensão
        }
    }
    
    // Gerar nome único com a extensão garantida
    $unique_name = $timestamp . "_" . bin2hex(random_bytes(8)) . "." . $extension;
    $target_file = $target_dir . $unique_name;
    
    // Verificar se é uma imagem real
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return [
            'success' => false,
            'message' => "O arquivo não é uma imagem."
        ];
    }
    
    // Verificar o tamanho do arquivo
    if ($file["size"] > 5000000) { // 5MB
        return [
            'success' => false,
            'message' => "O arquivo é muito grande. Tamanho máximo: 5MB."
        ];
    }
    
    // Permitir apenas certos formatos de arquivo
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($extension, $allowed_extensions)) {
        return [
            'success' => false,
            'message' => "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos."
        ];
    }
    
    // Tentar fazer o upload
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            'success' => true,
            'filename' => $unique_name,
            'path' => $target_file
        ];
    } else {
        return [
            'success' => false,
            'message' => "Ocorreu um erro ao fazer o upload da imagem."
        ];
    }
}