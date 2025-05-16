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
            // Registrar atividade se estiver logado
            if (is_logged_in()) {
                $role_text = ($role === 'admin') ? 'administrador' : 'moderador';
                log_admin_activity("Criou novo " . $role_text . ": " . $name, "add", $conn->insert_id, "admin");
            }
            
            // Se não houver admin logado, redirecionar para o login
            if (!is_logged_in()) {
                set_alert('success', 'Sua conta de administrador foi criada. Agora você pode fazer login.');
                redirect('login.php');
            } else {
                set_alert('success', 'Novo usuário criado com sucesso!');
                redirect('admins.php');
            }
        } else {
            $errors[] = "Erro ao criar usuário: " . $conn->error;
        }
    }
}

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Adicionar Novo Usuário</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admins.php">Usuários</a></li>
                    <li class="breadcrumb-item active">Adicionar Novo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações do Usuário</h3>
                    </div>
                    
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger m-3">
                            <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Nome de Usuário</label>
                                <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nome Completo</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="role">Cargo</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="moderator" <?php echo (isset($_POST['role']) && $_POST['role'] === 'moderator') ? 'selected' : ''; ?>>Moderador</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Criar Usuário</button>
                            <a href="admins.php" class="btn btn-default">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Informações</h3>
                    </div>
                    <div class="card-body">
                        <p><i class="fas fa-info-circle"></i> Ao criar um novo usuário, você está concedendo acesso ao painel administrativo.</p>
                        
                        <h5 class="mt-4">Diferenças entre os cargos:</h5>
                        <ul>
                            <li><strong>Administrador:</strong> Acesso completo a todas as funcionalidades, incluindo gerenciamento de usuários, configurações do sistema e logs.</li>
                            <li><strong>Moderador:</strong> Acesso limitado. Pode gerenciar veículos e conteúdo, mas não pode modificar configurações do sistema ou gerenciar outros usuários.</li>
                        </ul>
                        
                        <div class="alert alert-warning mt-4">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Atenção!</h5>
                            <p>Lembre-se de usar senhas fortes e não compartilhar credenciais de acesso.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>