<?php
session_start();
require_once 'config.php';

// Se já estiver logado, redirecionar para o dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Função para verificar tentativas de login
function check_brute_force($ip, $conn) {
    // Criar tabela de tentativas de login se não existir
    $create_table = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempt_time DATETIME NOT NULL,
        username VARCHAR(100) DEFAULT NULL
    )";
    $conn->query($create_table);
    
    // Limpar tentativas antigas (mais de 15 minutos)
    $cleanup = "DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL 15 MINUTE";
    $conn->query($cleanup);
    
    // Verificar número de tentativas recentes
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE ip_address = ? AND attempt_time > NOW() - INTERVAL 15 MINUTE");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();
    
    // Definir limite de tentativas (5 tentativas em 15 minutos)
    $attempt_limit = 5;
    
    if ($count >= $attempt_limit) {
        // Obter tempo para desbloquear
        $stmt = $conn->prepare("SELECT MAX(attempt_time) as last_attempt FROM login_attempts WHERE ip_address = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $last_attempt = strtotime($result->fetch_assoc()['last_attempt']);
        $unlock_time = $last_attempt + (15 * 60); // 15 minutos
        $time_left = $unlock_time - time();
        $stmt->close();
        
        if ($time_left > 0) {
            return [
                'blocked' => true,
                'minutes' => ceil($time_left / 60),
                'seconds' => $time_left % 60
            ];
        }
    }
    
    return ['blocked' => false];
}

// Função para registrar tentativa falha
function log_attempt($ip, $username, $conn) {
    $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempt_time, username) VALUES (?, NOW(), ?)");
    $stmt->bind_param("ss", $ip, $username);
    $stmt->execute();
    $stmt->close();
}

// Limpar tentativas quando o login for bem-sucedido
function clear_attempts($ip, $conn) {
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $recaptcha_token = $_POST['recaptcha_token'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $conn = db_connect();
    
    // Verificar bloqueio de força bruta
    $brute_check = check_brute_force($ip_address, $conn);
    
    if ($brute_check['blocked']) {
        $error = "Muitas tentativas de login. Tente novamente em {$brute_check['minutes']} minutos.";
    } else {
        // Verificar reCAPTCHA v3
        $recaptcha_valid = false;
        if (!empty($recaptcha_token)) {
            $secret = '6LdsljwrAAAAAGp0Pf5bV6uFAITGUweTJm1jejHG';
            $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$recaptcha_token);
            $response_data = json_decode($verify_response);
            
            if ($response_data->success && $response_data->score >= 0.5) {
                $recaptcha_valid = true;
            }
        }
        
        if (!$recaptcha_valid) {
            $error = "Falha na verificação de segurança. Por favor, tente novamente.";
            log_attempt($ip_address, $username, $conn);
        } else {
            // Buscar usuário no banco de dados
            $stmt = $conn->prepare("SELECT id, username, password, name FROM admins WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verificar senha
                if (password_verify($password, $user['password'])) {
                    // Login bem-sucedido
$_SESSION['admin_id'] = $user['id'];
$_SESSION['admin_name'] = $user['name'];
$_SESSION['admin_username'] = $user['username'];

// Registrar login no log
log_admin_activity("Login realizado com sucesso", "login");

set_alert('success', 'Login realizado com sucesso!');
redirect('dashboard.php');
                    
                    // Registrar login bem-sucedido (opcional)
                    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, ip_address) VALUES (?, 'login', ?)");
                    $stmt->bind_param("is", $user['id'], $ip_address);
                    $stmt->execute();
                    
                    set_alert('success', 'Login realizado com sucesso!');
                    redirect('dashboard.php');
                } else {
                    $error = "Senha incorreta!";
                    log_attempt($ip_address, $username, $conn);
                }
            } else {
                $error = "Usuário não encontrado!";
                log_attempt($ip_address, $username, $conn);
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LdsljwrAAAAAAk-uEHPer4mjYNKAUQfAaVD9eO9"></script>
    <script>
    function onSubmit(token) {
        document.getElementById("recaptcha_token").value = token;
        document.getElementById("login-form").submit();
    }

    function handleSubmit(event) {
        event.preventDefault();
        
        const submitBtn = document.getElementById("submit-btn");
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
        
        grecaptcha.ready(function() {
            grecaptcha.execute('6LdsljwrAAAAAAk-uEHPer4mjYNKAUQfAaVD9eO9', {action: 'login'}).then(function(token) {
                document.getElementById("recaptcha_token").value = token;
                document.getElementById("login-form").submit();
            });
        });
    }

    // Configurar o evento de submit quando a página carregar
    window.onload = function() {
        document.getElementById("login-form").addEventListener("submit", handleSubmit);
    };
    </script>
    <style>
        :root {
            --primary: #d69c1e;
            --primary-light: #e6ae30;
            --dark: #151515;
            --dark-medium: #222222;
            --light: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: var(--dark-medium);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            height: 60px;
        }
        
        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--light);
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(214, 156, 30, 0.3);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: var(--light);
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover:not(:disabled) {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .recaptcha-notice {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../img/logo.png" alt="<?php echo SITE_NAME; ?>">
        </div>
        
        <h1>Painel Administrativo</h1>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form id="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Nome de usuário</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <!-- Campo oculto para armazenar o token do reCAPTCHA -->
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">
            
            <button type="submit" id="submit-btn">Entrar</button>
        </form>
        
        <div class="recaptcha-notice">
            Este site é protegido pelo Google reCAPTCHA para garantir que você não é um robô.
            <a href="https://policies.google.com/privacy" target="_blank" style="color: #d69c1e;">Política de Privacidade</a> e
            <a href="https://policies.google.com/terms" target="_blank" style="color: #d69c1e;">Termos de Serviço</a> do Google.
        </div>
        
        <div class="footer">
            &copy; <?php echo date('Y'); ?> - AutoClub - Painel Administrativo
        </div>
    </div>
</body>
</html>