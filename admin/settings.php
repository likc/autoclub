<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// Obter dados do administrador atual
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Verificar se o usuário é admin
$is_admin = ($admin['role'] === 'admin');

// Processar formulário de atualização do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = sanitize($_POST['name']);
    $username = sanitize($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validação
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "O nome é obrigatório.";
    }
    
    if (empty($username)) {
        $errors[] = "O nome de usuário é obrigatório.";
    }
    
    // Verificar se o nome de usuário já existe (exceto para o usuário atual)
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $admin_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Este nome de usuário já está em uso por outro administrador.";
    }
    
    // Se a senha atual for fornecida, significa que o usuário quer alterar a senha
    if (!empty($current_password)) {
        // Verificar se a senha atual está correta
        if (!password_verify($current_password, $admin['password'])) {
            $errors[] = "A senha atual está incorreta.";
        }
        
        // Validar a nova senha
        if (empty($new_password)) {
            $errors[] = "A nova senha é obrigatória para alterar a senha.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "A nova senha deve ter pelo menos 6 caracteres.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "A confirmação da nova senha não corresponde.";
        }
    }
    
    // Se não houver erros, atualizar o perfil
    if (empty($errors)) {
        // Se a senha atual for fornecida, atualizar tudo incluindo a senha
        if (!empty($current_password)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET name = ?, username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $username, $new_password_hash, $admin_id);
        } else {
            // Senão, atualizar apenas o nome e o nome de usuário
            $stmt = $conn->prepare("UPDATE admins SET name = ?, username = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $username, $admin_id);
        }
        
        if ($stmt->execute()) {
            // Atualizar os dados da sessão
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_username'] = $username;
            
            set_alert('success', 'Perfil atualizado com sucesso!');
            redirect('settings.php');
        } else {
            set_alert('danger', 'Erro ao atualizar o perfil: ' . $conn->error);
        }
    } else {
        // Armazenar erros para exibição
        $_SESSION['form_errors'] = $errors;
    }
}

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Meu Perfil</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Meu Perfil</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Perfil do Administrador -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações do Perfil</h3>
                    </div>
                    
                    <?php if (isset($_SESSION['form_errors'])): ?>
                        <div class="alert alert-danger m-3">
                            <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['form_errors']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Nome de Usuário</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Cargo</label>
                                <input type="text" class="form-control" value="<?php echo ($admin['role'] === 'admin') ? 'Administrador' : 'Moderador'; ?>" readonly>
                            </div>
                            <hr>
                            <h5>Alterar Senha</h5>
                            <p class="text-muted small">Deixe em branco para manter a senha atual</p>
                            <div class="form-group">
                                <label for="current_password">Senha Atual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            <div class="form-group">
                                <label for="new_password">Nova Senha</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Informações e Links Úteis -->
            <div class="col-md-6">
                <?php if ($is_admin): ?>
                <!-- Link para criar novo administrador (apenas para admins) -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Gerenciar Administradores</h3>
                    </div>
                    <div class="card-body">
                        <p>Crie contas de administrador adicionais para gerenciar o sistema.</p>
                        <a href="create_admin.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Adicionar Novo Administrador
                        </a>
                    </div>
                </div>
                
                <!-- Link para configurações do site (apenas para admins) -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Configurações do Site</h3>
                    </div>
                    <div class="card-body">
                        <p>Gerencie as configurações gerais do site, textos, informações de contato e muito mais.</p>
                        <a href="site_settings.php" class="btn btn-primary">
                            <i class="fas fa-cogs"></i> Ir para Configurações do Site
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Informação sobre a sessão (para todos os usuários) -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Informações da Sessão</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Usuário:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                        <p><strong>Cargo:</strong> <?php echo ($admin['role'] === 'admin') ? 'Administrador' : 'Moderador'; ?></p>
                        <p><strong>ID:</strong> <?php echo $admin_id; ?></p>
                        <hr>
                        <a href="logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Sair do Sistema
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>