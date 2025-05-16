<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission();

$conn = db_connect();
$current_admin_id = $_SESSION['admin_id'];

// Processar alteração de cargo
if (isset($_GET['action']) && $_GET['action'] === 'change_role' && isset($_GET['id']) && isset($_GET['role'])) {
    $admin_id = (int)$_GET['id'];
    $new_role = sanitize($_GET['role']);
    
    // Verificar se o admin está tentando mudar seu próprio cargo
    if ($admin_id === $current_admin_id) {
        set_alert('danger', 'Você não pode alterar seu próprio cargo!');
        redirect('admins.php');
    }
    
    // Validar o novo cargo
    if ($new_role !== 'admin' && $new_role !== 'moderator') {
        set_alert('danger', 'Cargo inválido!');
        redirect('admins.php');
    }
    
    // Obter o nome do admin para o log
    $stmt = $conn->prepare("SELECT name, role FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();
    $admin_name = $admin_data['name'];
    $current_role = $admin_data['role'];
    
    // Verificar se o cargo está realmente mudando
    if ($current_role === $new_role) {
        set_alert('info', 'O usuário já possui este cargo.');
        redirect('admins.php');
    }
    
    // Atualizar o cargo
    $stmt = $conn->prepare("UPDATE admins SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $admin_id);
    
    if ($stmt->execute()) {
        // Registrar atividade
        $role_text = ($new_role === 'admin') ? 'administrador' : 'moderador';
        log_admin_activity("Alterou o cargo de {$admin_name} para {$role_text}", "edit", $admin_id, "admin");
        
        set_alert('success', "Cargo de {$admin_name} alterado para {$role_text} com sucesso!");
    } else {
        set_alert('danger', 'Erro ao alterar cargo: ' . $conn->error);
    }
    
    redirect('admins.php');
}

// Processar exclusão de administrador
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $admin_id = (int)$_GET['id'];
    
    // Verificar se o admin está tentando excluir a si mesmo
    if ($admin_id === $current_admin_id) {
        set_alert('danger', 'Você não pode excluir seu próprio usuário!');
        redirect('admins.php');
    }
    
    // Verificar se é o último administrador
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM admins WHERE role = 'admin'");
    $stmt->execute();
    $admin_count = $stmt->get_result()->fetch_assoc()['total'];
    
    // Verificar o cargo do usuário a ser excluído
    $stmt = $conn->prepare("SELECT role, name FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();
    $admin_role = $admin_data['role'];
    $admin_name = $admin_data['name'];
    
    // Se for o último administrador, não pode excluir
    if ($admin_count <= 1 && $admin_role === 'admin') {
        set_alert('danger', 'Não é possível excluir o último administrador do sistema!');
        redirect('admins.php');
    }
    
    // Excluir o administrador
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    
    if ($stmt->execute()) {
        // Registrar atividade com o cargo correto
        $role_text = ($admin_role === 'admin') ? 'administrador' : 'moderador';
        log_admin_activity("Excluiu o {$role_text}: " . $admin_name, "delete", $admin_id, "admin");
        
        set_alert('success', 'Usuário excluído com sucesso!');
    } else {
        set_alert('danger', 'Erro ao excluir usuário: ' . $conn->error);
    }
    
    redirect('admins.php');
}

// Obter todos os administradores
$admins_query = "SELECT * FROM admins ORDER BY id ASC";
$admins = $conn->query($admins_query)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gerenciar Usuários</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Usuários</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Usuários</h3>
                        
                        <div class="card-tools">
                            <a href="create_admin.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Adicionar Novo
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome de Usuário</th>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td><?php echo $admin['id']; ?></td>
                                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['name']); ?></td>
                                        <td>
                                            <?php if ($admin['role'] === 'admin'): ?>
                                                <span class="badge badge-primary">Administrador</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Moderador</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($admin['id'] == $current_admin_id): ?>
                                                <span class="badge badge-info">Usuário Atual</span>
                                            <?php else: ?>
                                                <!-- Botão para mudar cargo -->
                                                <?php if ($admin['role'] === 'admin'): ?>
                                                    <a href="admins.php?action=change_role&id=<?php echo $admin['id']; ?>&role=moderator" 
                                                       class="btn btn-warning btn-sm" 
                                                       onclick="return confirm('Tem certeza que deseja alterar o cargo deste usuário para Moderador?');">
                                                        <i class="fas fa-user-shield"></i> Mudar para Moderador
                                                    </a>
                                                <?php else: ?>
                                                    <a href="admins.php?action=change_role&id=<?php echo $admin['id']; ?>&role=admin" 
                                                       class="btn btn-info btn-sm" 
                                                       onclick="return confirm('Tem certeza que deseja alterar o cargo deste usuário para Administrador?');">
                                                        <i class="fas fa-user-tie"></i> Mudar para Administrador
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- Botão de exclusão -->
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#deleteAdminModal<?php echo $admin['id']; ?>">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                                
                                                <!-- Modal de Confirmação de Exclusão -->
                                                <div class="modal fade" id="deleteAdminModal<?php echo $admin['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmar Exclusão</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Tem certeza que deseja excluir o usuário <strong><?php echo htmlspecialchars($admin['name']); ?></strong>?</p>
                                                                <p class="text-danger">Esta ação não pode ser desfeita!</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                <a href="admins.php?action=delete&id=<?php echo $admin['id']; ?>" class="btn btn-danger">Excluir</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($admins)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>