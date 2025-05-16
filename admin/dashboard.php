<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// Contagem de carros
$result = $conn->query("SELECT COUNT(*) as total FROM cars");
$total_cars = $result->fetch_assoc()['total'];

// Carros por categoria
$stmt = $conn->prepare("
    SELECT c.name, c.slug, COUNT(ccr.car_id) as count 
    FROM car_categories c
    LEFT JOIN car_category_relations ccr ON c.id = ccr.category_id
    WHERE c.slug != 'all'
    GROUP BY c.id
");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Carros recentes
$stmt = $conn->prepare("
    SELECT * FROM cars 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute();
$recent_cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Cartões com estatísticas -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_cars; ?></h3>
                            <p>Total de Veículos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <a href="cars.php" class="small-box-footer">
                            Ver todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <?php foreach ($categories as $index => $category): ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-<?php echo $index == 0 ? 'success' : ($index == 1 ? 'warning' : 'danger'); ?>">
                            <div class="inner">
                                <h3><?php echo $category['count']; ?></h3>
                                <p>Carros <?php echo $category['name']; ?></p>
                            </div>
                            <div class="icon">
                                <?php
                                // Corrigido: verifica se a chave 'slug' existe no array
                                $icon_class = 'car'; // Ícone padrão
                                if (isset($category['slug'])) {
                                    if ($category['slug'] == 'kei') {
                                        $icon_class = 'truck-pickup';
                                    } else if ($category['slug'] == 'placa-branca') {
                                        $icon_class = 'car-side';
                                    }
                                }
                                ?>
                                <i class="fas fa-<?php echo $icon_class; ?>"></i>
                            </div>
                            <a href="cars.php?category=<?php echo isset($category['slug']) ? $category['slug'] : ''; ?>" class="small-box-footer">
                                Ver todos <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Carros adicionados recentemente -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Veículos Adicionados Recentemente</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagem</th>
                                        <th>Modelo</th>
                                        <th>Ano</th>
                                        <th>Preço Mensal</th>
                                        <th>Data de Adição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_cars as $car): ?>
                                        <tr>
                                            <td><?php echo $car['id']; ?></td>
                                            <td>
                                                <img src="uploads/<?php echo $car['image']; ?>" alt="<?php echo $car['model']; ?>" class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td><?php echo $car['model']; ?></td>
                                            <td><?php echo $car['year']; ?></td>
                                            <td>¥<?php echo number_format($car['monthly_payment'], 0, ',', '.'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($car['created_at'])); ?></td>
                                            <td>
                                                <a href="car_edit.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="car_delete.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este veículo?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($recent_cars)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhum veículo encontrado.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<!-- Botões de acesso rápido -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Acesso Rápido</h3>
            </div>
            <div class="card-body">
                <a href="car_add.php" class="btn btn-primary btn-lg mr-2">
                    <i class="fas fa-plus-circle"></i> Adicionar Novo Veículo
                </a>
                <a href="cars.php" class="btn btn-info btn-lg mr-2">
                    <i class="fas fa-car"></i> Gerenciar Veículos
                </a>
                <a href="site_settings.php" class="btn btn-success btn-lg">
                    <i class="fas fa-cogs"></i> Configurações do Site
                </a>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>