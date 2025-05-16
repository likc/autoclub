<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

$conn = db_connect();

// Parâmetros de paginação
$limit = 50;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtros
$filter_admin = isset($_GET['admin']) && is_numeric($_GET['admin']) ? (int)$_GET['admin'] : null;
$filter_action = isset($_GET['action_type']) && !empty($_GET['action_type']) ? sanitize($_GET['action_type']) : null;
$filter_date = isset($_GET['date']) && !empty($_GET['date']) ? sanitize($_GET['date']) : null;

// Construir a query com filtros
$query_conditions = [];
$query_params = [];
$param_types = '';

if ($filter_admin) {
    $query_conditions[] = "al.admin_id = ?";
    $query_params[] = $filter_admin;
    $param_types .= "i";
}

if ($filter_action) {
    $query_conditions[] = "al.action_type = ?";
    $query_params[] = $filter_action;
    $param_types .= "s";
}

if ($filter_date) {
    $query_conditions[] = "DATE(al.created_at) = ?";
    $query_params[] = $filter_date;
    $param_types .= "s";
}

$where_clause = !empty($query_conditions) ? "WHERE " . implode(" AND ", $query_conditions) : "";

// Contar o total de logs
$count_query = "SELECT COUNT(*) as total FROM admin_logs al $where_clause";
$stmt = $conn->prepare($count_query);

if (!empty($query_params)) {
    $stmt->bind_param($param_types, ...$query_params);
}

$stmt->execute();
$total_logs = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_logs / $limit);

// Obter os logs
$query = "
    SELECT al.*, a.username as admin_username, a.name as admin_name
    FROM admin_logs al
    JOIN admins a ON al.admin_id = a.id
    $where_clause
    ORDER BY al.created_at DESC
    LIMIT ?, ?
";

// Adicionar os parâmetros de paginação
$query_params[] = $offset;
$query_params[] = $limit;
$param_types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($param_types, ...$query_params);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obter todos os administradores para o filtro
$admins_query = "SELECT id, username, name FROM admins ORDER BY name ASC";
$admins = $conn->query($admins_query)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Log de Atividades</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Log de Atividades</li>
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
                        <h3 class="card-title">Filtrar Logs</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="admin">Administrador</label>
                                        <select class="form-control" id="admin" name="admin">
                                            <option value="">Todos</option>
                                            <?php foreach ($admins as $admin): ?>
                                                <option value="<?php echo $admin['id']; ?>" <?php echo ($filter_admin == $admin['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($admin['name']); ?> (<?php echo htmlspecialchars($admin['username']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="action_type">Tipo de Ação</label>
                                        <select class="form-control" id="action_type" name="action_type">
                                            <option value="">Todos</option>
                                            <option value="login" <?php echo ($filter_action == 'login') ? 'selected' : ''; ?>>Login</option>
                                            <option value="add" <?php echo ($filter_action == 'add') ? 'selected' : ''; ?>>Adição</option>
                                            <option value="edit" <?php echo ($filter_action == 'edit') ? 'selected' : ''; ?>>Edição</option>
                                            <option value="delete" <?php echo ($filter_action == 'delete') ? 'selected' : ''; ?>>Exclusão</option>
                                            <option value="other" <?php echo ($filter_action == 'other') ? 'selected' : ''; ?>>Outros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Data</label>
                                        <input type="date" class="form-control" id="date" name="date" value="<?php echo $filter_date; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group mb-0 w-100">
                                        <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                                        <a href="admin_logs.php" class="btn btn-default">Limpar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Histórico de Atividades</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data/Hora</th>
                                    <th>Administrador</th>
                                    <th>Ação</th>
                                    <th>Tipo</th>
                                    <th>Detalhes</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log['id']; ?></td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($log['admin_name']); ?></td>
                                            <td>
                                                <?php 
                                                switch ($log['action_type']) {
                                                    case 'login':
                                                        echo '<span class="badge badge-info">Login</span>';
                                                        break;
                                                    case 'add':
                                                        echo '<span class="badge badge-success">Adição</span>';
                                                        break;
                                                    case 'edit':
                                                        echo '<span class="badge badge-warning">Edição</span>';
                                                        break;
                                                    case 'delete':
                                                        echo '<span class="badge badge-danger">Exclusão</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Outro</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($log['item_type'] == 'car') {
                                                    echo 'Veículo';
                                                } elseif ($log['item_type'] == 'admin') {
                                                    echo 'Administrador';
                                                } elseif ($log['item_type'] == 'setting') {
                                                    echo 'Configuração';
                                                } else {
                                                    echo htmlspecialchars($log['item_type'] ?? '-');
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum registro de atividade encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?php echo $filter_admin ? '&admin=' . $filter_admin : ''; ?><?php echo $filter_action ? '&action_type=' . $filter_action : ''; ?><?php echo $filter_date ? '&date=' . $filter_date : ''; ?>">&laquo;</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $filter_admin ? '&admin=' . $filter_admin : ''; ?><?php echo $filter_action ? '&action_type=' . $filter_action : ''; ?><?php echo $filter_date ? '&date=' . $filter_date : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo $filter_admin ? '&admin=' . $filter_admin : ''; ?><?php echo $filter_action ? '&action_type=' . $filter_action : ''; ?><?php echo $filter_date ? '&date=' . $filter_date : ''; ?>">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>