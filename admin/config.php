<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'minec761_likc');
define('DB_PASS', 'rw23xrd807ox');
define('DB_NAME', 'minec761_autoclub');

// Configurações do sistema
define('SITE_NAME', 'Painel Administrativo');
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

// Função para registrar atividade do administrador
function log_admin_activity($action, $action_type, $item_id = null, $item_type = null, $details = null) {
    if (!is_logged_in()) {
        return false;
    }
    
    $conn = db_connect();
    $admin_id = $_SESSION['admin_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $conn->prepare("
        INSERT INTO admin_logs (admin_id, action, action_type, item_id, item_type, details, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ississs", $admin_id, $action, $action_type, $item_id, $item_type, $details, $ip_address);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
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
        if (!mkdir($target_dir, 0755, true)) {
            return [
                'success' => false,
                'message' => "Erro ao criar diretório de upload."
            ];
        }
    }
    
    // Verificar se é uma imagem real
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return [
            'success' => false,
            'message' => "O arquivo não é uma imagem."
        ];
    }
    
    // Obter a extensão do arquivo
    $original_filename = $file["name"];
    $extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    
    // Se não conseguir detectar a extensão pelo nome, tentar detectar pelo MIME type
    if (empty($extension)) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file["tmp_name"]);
        
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
            default:
                $extension = 'jpg'; // Default para imagens sem extensão
        }
    }
    
    // Gerar nome único para o arquivo COM EXTENSÃO
    $timestamp = time();
    $unique_id = bin2hex(random_bytes(8));
    $unique_name = $timestamp . "_" . $unique_id . "." . $extension;
    $target_file = $target_dir . $unique_name;
    
    // Verificar o tamanho do arquivo
    if ($file["size"] > 5000000) { // 5MB
        return [
            'success' => false,
            'message' => "O arquivo é muito grande. Tamanho máximo: 5MB."
        ];
    }
    
    // Permitir apenas certos formatos de arquivo
    $allowed_extensions = ["jpg", "jpeg", "png", "gif", "webp"];
    if (!in_array($extension, $allowed_extensions)) {
        return [
            'success' => false,
            'message' => "Apenas arquivos JPG, JPEG, PNG, GIF e WEBP são permitidos."
        ];
    }
    
    // Tentar fazer o upload
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Verificar se o arquivo foi realmente movido
        if (file_exists($target_file)) {
            // Definir permissões do arquivo
            chmod($target_file, 0644);
            
            // IMPORTANTE: Retornar o nome completo COM a extensão
            return [
                'success' => true,
                'filename' => $unique_name,  // Este já tem a extensão incluída
                'path' => $target_file,
                'url' => SITE_URL . '/uploads/' . $unique_name
            ];
        } else {
            return [
                'success' => false,
                'message' => "Erro ao salvar o arquivo no servidor."
            ];
        }
    } else {
        $error = error_get_last();
        return [
            'success' => false,
            'message' => "Ocorreu um erro ao fazer o upload da imagem: " . ($error ? $error['message'] : 'Erro desconhecido')
        ];
    }
}
?>