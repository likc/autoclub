<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// Parâmetros de paginação
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtro por categoria
$category_filter = '';
$category_param = '';
if (isset($_GET['category']) && $_GET['category'] != 'all') {
    $category_slug = sanitize($_GET['category']);
    $category_filter = "JOIN car_category_relations ccr ON cars.id = ccr.car_id 
                         JOIN car_categories cc ON ccr.category_id = cc.id 
                         WHERE cc.slug = ?";
    $category_param = $category_slug;
}

// Busca
$search_filter = '';
$search_param = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = sanitize($_GET['search']);
    
    if (empty($category_filter)) {
        $search_filter = "WHERE model LIKE ? OR year LIKE ?";
        $search_param = "%$search_term%";
    } else {
        $search_filter = $category_filter . " AND (model LIKE ? OR year LIKE ?)";
        $search_param = "%$search_term%";
    }
}

// Contar o total de resultados
$count_query = "SELECT COUNT(*) as total FROM cars ";
if (!empty($search_filter)) {
    $count_query .= $search_filter;
    
    $stmt = $conn->prepare($count_query);
    
    if (empty($category_filter)) {
        $stmt->bind_param("ss", $search_param, $search_param);
    } else if (empty($search_param)) {
        $stmt->bind_param("s", $category_param);
    } else {
        $stmt->bind_param("sss", $category_param, $search_param, $search_param);
    }
} else if (!empty($category_filter)) {
    $count_query .= $category_filter;
    
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("s", $category_param);
} else {
    $stmt = $conn->prepare($count_query);
}

$stmt->execute();
$total_results = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_results / $limit);

// Obter todos os carros com paginação e filtros
$query = "SELECT cars.* FROM cars ";

if (!empty($search_filter)) {
    $query .= $search_filter;
} else if (!empty($category_filter)) {
    $query .= $category_filter;
}

$query .= " ORDER BY cars.id DESC LIMIT ?, ?";

$stmt = $conn->prepare($query);

if (empty($search_filter) && empty($category_filter)) {
    $stmt->bind_param("ii", $offset, $limit);
} else if (!empty($search_filter) && empty($category_filter)) {
    $stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);
} else if (empty($search_filter) && !empty($category_filter)) {
    $stmt->bind_param("sii", $category_param, $offset, $limit);
} else {
    $stmt->bind_param("sssii", $category_param, $search_param, $search_param, $offset, $limit);
}

$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obter todas as categorias para o filtro
$categories_query = "SELECT * FROM car_categories ORDER BY name ASC";
$categories = $conn->query($categories_query)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Veículos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Veículos</li>
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
                        <h3 class="card-title">Lista de Veículos</h3>
                        
                        <div class="card-tools">
                            <a href="car_add.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Adicionar Novo
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="" method="GET" class="form-inline">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Buscar por modelo, ano..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="float-md-right">
                                    <div class="btn-group">
                                        <a href="cars.php" class="btn btn-default <?php echo !isset($_GET['category']) || $_GET['category'] == 'all' ? 'active' : ''; ?>">Todos</a>
                                        <?php foreach ($categories as $category): ?>
                                            <?php if ($category['slug'] !== 'all'): ?>
                                                <a href="cars.php?category=<?php echo $category['slug']; ?>" class="btn btn-default <?php echo isset($_GET['category']) && $_GET['category'] == $category['slug'] ? 'active' : ''; ?>">
                                                    <?php echo $category['name']; ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th style="width: 100px;">Imagem</th>
                                        <th>Modelo</th>
                                        <th>Ano</th>
                                        <th>Quilometragem</th>
                                        <th>Preço Mensal</th>
                                        <th>Parcelas</th>
                                        <th style="width: 150px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($cars)): ?>
                                        <?php foreach ($cars as $car): ?>
                                            <tr>
                                                <td><?php echo $car['id']; ?></td>
                                                <td>
                                                    <img src="uploads/<?php echo $car['image']; ?>" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <?php echo $car['model']; ?>
                                                    <?php if ($car['is_new']): ?>
                                                        <span class="badge badge-success">Novo</span>
                                                    <?php elseif ($car['is_popular']): ?>
                                                        <span class="badge badge-info">Popular</span>
                                                    <?php elseif (!empty($car['custom_highlight'])): ?>
                                                        <?php 
                                                        // Usar a cor personalizada se definida, senão usar a cor padrão
                                                        $highlight_color = !empty($car['highlight_color']) ? $car['highlight_color'] : '#d6a300'; 
                                                        ?>
                                                        <span class="badge" style="background-color: <?php echo $highlight_color; ?>; color: white;">
                                                            <?php echo htmlspecialchars($car['custom_highlight']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $car['year']; ?></td>
                                                <td><?php echo number_format($car['mileage'], 0, ',', '.'); ?> km</td>
                                                <td>¥<?php echo number_format($car['monthly_payment'], 0, ',', '.'); ?></td>
                                                <td><?php echo $car['installment_type']; ?></td>
                                                <td>
                                                    <div class="btn-group-action">
                                                        <a href="car_edit.php?id=<?php echo $car['id']; ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo $car['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Modal de Confirmação de Exclusão -->
                                                    <div class="modal fade" id="deleteModal<?php echo $car['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $car['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $car['id']; ?>">Confirmar Exclusão</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Tem certeza que deseja excluir o veículo <strong><?php echo $car['model']; ?> (<?php echo $car['year']; ?>)</strong>?</p>
                                                                    <p class="text-danger">Esta ação não pode ser desfeita!</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                    <a href="car_delete.php?id=<?php echo $car['id']; ?>" class="btn btn-danger">Excluir</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Nenhum veículo encontrado.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1<?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">&laquo;</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">&raquo;</a>
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