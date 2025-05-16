<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

$conn = db_connect();

// Parâmetros de paginação
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Construir a query com filtros
$query_conditions = [];
$query_params = [];
$param_types = '';

// Filtro de pesquisa
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitize($_GET['search']);
    $query_conditions[] = "(client_name LIKE ? OR vehicle_name LIKE ? OR vehicle_plate LIKE ?)";
    $query_params[] = "%$search%";
    $query_params[] = "%$search%";
    $query_params[] = "%$search%";
    $param_types .= "sss";
}

// Filtro de data
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $date_from = sanitize($_GET['date_from']);
    $query_conditions[] = "date >= ?";
    $query_params[] = $date_from;
    $param_types .= "s";
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $date_to = sanitize($_GET['date_to']);
    $query_conditions[] = "date <= ?";
    $query_params[] = $date_to;
    $param_types .= "s";
}

$where_clause = !empty($query_conditions) ? "WHERE " . implode(" AND ", $query_conditions) : "";

// Contar o total de registros
$count_query = "SELECT COUNT(*) as total FROM contracts $where_clause";
$stmt = $conn->prepare($count_query);

if (!empty($query_params)) {
    $stmt->bind_param($param_types, ...$query_params);
}

$stmt->execute();
$total_contracts = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_contracts / $limit);

// Obter os contratos
$query = "
    SELECT c.*, a.name as admin_name
    FROM contracts c
    LEFT JOIN admins a ON c.created_by = a.id
    $where_clause
    ORDER BY c.created_at DESC
    LIMIT ?, ?
";

// Adicionar os parâmetros de paginação
$query_params[] = $offset;
$query_params[] = $limit;
$param_types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($param_types, ...$query_params);
$stmt->execute();
$contracts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gerenciamento de Contratos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Contratos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Card de Pesquisa e Filtros -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtrar Contratos</h3>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Pesquisar</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                    placeholder="Nome do cliente, veículo ou placa" 
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">Data Inicial</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                    value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">Data Final</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                    value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group mb-0 w-100">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="contracts.php" class="btn btn-default ml-1">Limpar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Principal com Lista de Contratos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Contratos</h3>
                <div class="card-tools">
                    <a href="contract_edit.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Novo Contrato
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Veículo</th>
                            <th>Placa</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                            <th>Criado por</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contracts)): ?>
                            <?php foreach ($contracts as $contract): ?>
                                <tr>
                                    <td><?php echo $contract['id']; ?></td>
                                    <td><?php echo htmlspecialchars($contract['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($contract['vehicle_name']); ?></td>
                                    <td><?php echo htmlspecialchars($contract['vehicle_plate']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($contract['date'])); ?></td>
                                    <td>¥<?php echo number_format($contract['total_value'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($contract['admin_name']); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="contract_view.php?id=<?php echo $contract['id']; ?>" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="contract_edit.php?id=<?php echo $contract['id']; ?>" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal<?php echo $contract['id']; ?>"
                                                title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Modal de Confirmação de Exclusão -->
                                        <div class="modal fade" id="deleteModal<?php echo $contract['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tem certeza que deseja excluir o contrato de <strong><?php echo htmlspecialchars($contract['client_name']); ?></strong>?</p>
                                                        <p class="text-danger">Esta ação não pode ser desfeita!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <a href="contract_delete.php?id=<?php echo $contract['id']; ?>" class="btn btn-danger">Excluir</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum contrato encontrado.</td>
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
                                <a class="page-link" href="?page=1<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . urlencode($_GET['date_from']) : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . urlencode($_GET['date_to']) : ''; ?>">&laquo;</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . urlencode($_GET['date_from']) : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . urlencode($_GET['date_to']) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . urlencode($_GET['date_from']) : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . urlencode($_GET['date_to']) : ''; ?>">&raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>