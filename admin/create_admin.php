<?php
session_start();
require_once 'config.php';

// Verificar se já existe um administrador
$conn = db_connect();
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$admin_count = $result->fetch_assoc()['count'];

// Se já existir admin, apenas admins logados podem criar novos admins
if ($admin_count > 0) {
    check_login();
    
    // Verificar se é admin para acessar a página
    if (isset($_SESSION['admin_id']) && !is_admin()) {
        set_alert('danger', 'Você não tem permissão para criar administradores.');
        redirect('dashboard.php');
    }
}

// Processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $name = sanitize($_POST['name']);
    $role = sanitize($_POST['role']); // Novo campo de cargo
    
    // Validação básica
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Nome de usuário é obrigatório.";
    }
    
    if (empty($password)) {
        $errors[] = "Senha é obrigatória.";
    } elseif (strlen($password) < 6) {
        $errors[] = "A senha deve ter pelo menos 6 caracteres.";
    }
    
    if (empty($name)) {
        $errors[] = "Nome é obrigatório.";
    }
    
    // Validar o cargo (deve ser 'admin' ou 'moderator')
    if ($role !== 'admin' && $role !== 'moderator') {
        $errors[] = "Cargo inválido selecionado.";
    }
    
    // Verificar se o nome de usuário já existe
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Este nome de usuário já está em uso.";
    }
    
    // Se não houver erros, criar o administrador
    if (empty($errors)) {
        // Hash da senha
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO admins (username, password, name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password_hash, $name, $role);
        
        if ($stmt->execute()) {
            $success = "Usuário criado com sucesso!";
            
            // Registrar atividade se estiver logado
            if (is_logged_in()) {
                $role_text = ($role === 'admin') ? 'administrador' : 'moderador';
                log_admin_activity("Criou novo " . $role_text . ": " . $name, "add", $conn->insert_id, "admin");
            }
            
            // Se não houver admin logado, redirecionar para o login
            if (!is_logged_in()) {
                set_alert('success', 'Sua conta de administrador foi criada. Agora você pode fazer login.');
                redirect('login.php');
            }
        } else {
            $errors[] = "Erro ao criar usuário: " . $conn->error;
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
    <title>Criar Usuário - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        
        .form-container {
            background-color: var(--dark-medium);
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
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
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--light);
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus {
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
        
        button:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="logo">
            <img src="../img/logo.png" alt="<?php echo SITE_NAME; ?>">
        </div>
        
        <h1>Criar Novo Usuário</h1>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
                <small style="color: #aaa;">A senha deve ter pelo menos 6 caracteres.</small>
            </div>
            
            <div class="form-group">
                <label for="name">Nome Completo</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Cargo</label>
                <select id="role" name="role" required>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="moderator" <?php echo (isset($_POST['role']) && $_POST['role'] === 'moderator') ? 'selected' : ''; ?>>Moderador</option>
                </select>
            </div>
            
            <button type="submit">Criar Usuário</button>
        </form>
        
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar para o Dashboard
            </a>
        <?php else: ?>
            <a href="login.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Voltar para o Login
            </a>
        <?php endif; ?>
    </div>
</body>
</html>