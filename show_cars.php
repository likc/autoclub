<?php
/**
 * Show Cars - Script para exibir os carros na página principal
 */

// Configuração do banco de dados
$db_host = 'localhost';
$db_user = 'minec761_likc';
$db_pass = 'rw23xrd807ox';
$db_name = 'minec761_autoclub';

// Conectar ao banco de dados
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

// Obter os carros do banco de dados
$query = "SELECT * FROM cars ORDER BY id DESC LIMIT 8";
$result = $conn->query($query);

// Array para armazenar os carros
$cars = [];

// Processar resultados
if ($result->num_rows > 0) {
    while ($car = $result->fetch_assoc()) {
        // Obter categorias do carro
        $car_id = $car['id'];
        $categories_query = "
            SELECT cc.slug FROM car_categories cc
            JOIN car_category_relations ccr ON cc.id = ccr.category_id
            WHERE ccr.car_id = $car_id
        ";
        
        $categories_result = $conn->query($categories_query);
        $categories = [];
        
        while ($category = $categories_result->fetch_assoc()) {
            $categories[] = $category['slug'];
        }
        
        $car['categories'] = $categories;
        $cars[] = $car;
    }
}

// Fechar a conexão
$conn->close();
?>

<!-- Carrossel com 2 andares por página -->
<div class="simple-car-slider">
    <div class="slides-container">
        <div class="car-slide active" data-slide="1">
            <div class="car-grid row">
                <?php
                $count = 0;
                
                foreach ($cars as $car):
                    if ($count === 4):
                        echo '</div><div class="car-grid row mt-4">';
                    endif;
                    
                    $categories_str = implode(' ', $car['categories']);
                ?>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4 car-item" data-category="<?php echo $categories_str; ?>">
                        <div class="car-card">
                            <?php if ($car['is_new'] || $car['is_popular']): ?>
                                <div class="car-card__badge">
                                    <?php if ($car['is_new']): ?>
                                        <span class="badge badge-primary">Novo</span>
                                    <?php endif; ?>
                                    <?php if ($car['is_popular']): ?>
                                        <span class="badge badge-success">Popular</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="car-card__image">
                                <?php
                                // Obter o nome da imagem do banco
                                $image_filename = trim($car['image']);
                                $image_path = '';
                                $found = false;
                                
                                // Primeiro, tentar com o nome exato do banco
                                if (!empty($image_filename)) {
                                    $test_path = 'admin/uploads/' . $image_filename;
                                    if (file_exists($test_path)) {
                                        $image_path = $test_path;
                                        $found = true;
                                    }
                                }
                                
                                // Se não encontrou e o nome não tem extensão, tentar adicionar
                                if (!$found && !preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $image_filename)) {
                                    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    $base_name = $image_filename;
                                    
                                    foreach ($extensions as $ext) {
                                        $test_path = 'admin/uploads/' . $base_name . '.' . $ext;
                                        if (file_exists($test_path)) {
                                            $image_path = $test_path;
                                            $found = true;
                                            break;
                                        }
                                    }
                                }
                                
                                // Se não encontrou, buscar arquivo com nome similar
                                if (!$found && !empty($image_filename)) {
                                    $upload_dir = 'admin/uploads/';
                                    if (is_dir($upload_dir)) {
                                        $files = scandir($upload_dir);
                                        $base_name = pathinfo($image_filename, PATHINFO_FILENAME);
                                        
                                        foreach ($files as $file) {
                                            if (strpos($file, $base_name) !== false) {
                                                $image_path = $upload_dir . $file;
                                                $found = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                                
                                // Se ainda não encontrou, usar imagem padrão
                                if (!$found || empty($image_filename)) {
                                    $image_path = 'img/car-default.jpg';
                                    if (!file_exists($image_path)) {
                                        // Fallback final - placeholder externo
                                        $image_path = 'https://via.placeholder.com/300x200?text=Sem+Imagem';
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                     alt="<?php echo htmlspecialchars($car['model']); ?>"
                                     onerror="this.src='img/car-default.jpg'; this.onerror=function(){this.src='https://via.placeholder.com/300x200?text=Sem+Imagem'};">
                            </div>
                            
                            <div class="car-card__content">
                                <span class="car-card__year"><?php echo $car['year']; ?></span>
                                <h3 class="car-card__title"><?php echo htmlspecialchars($car['model']); ?></h3>
                                <ul class="car-card__specs">
                                    <li><i class="fas fa-road"></i> <span><?php echo number_format($car['mileage'], 0, ',', '.'); ?></span> km</li>
                                    <li><i class="fas fa-cog"></i> <span><?php echo $car['transmission']; ?></span></li>
                                    <li><i class="fas fa-calendar-alt"></i> Shaken: <span><?php echo $car['shaken_expires']; ?></span></li>
                                </ul>
                                <div class="car-card__price">
                                    <?php 
                                    // Determinar a cor do badge baseado no tipo de parcelamento
                                    $badge_color = 'var(--primary)'; // Padrão
                                    if ($car['installment_type'] === '120x') {
                                        $badge_color = 'var(--accent)'; // Vermelho para 120x
                                    } elseif ($car['installment_type'] === '84x') {
                                        $badge_color = 'var(--success)'; // Verde para 84x
                                    }
                                    ?>
                                    <span class="car-card__price-badge" style="background: <?php echo $badge_color; ?>;">
                                        Até <?php echo $car['installment_type']; ?>
                                    </span>
                                    <div class="car-card__price-amount">
                                        ¥<?php echo number_format($car['monthly_payment'], 0, ',', '.'); ?><small>/Mês</small>
                                    </div>
                                </div>
                                <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20tenho%20interesse%20no%20<?php echo urlencode($car['model']); ?>" 
                                   class="btn btn-primary btn-block car-card__cta">
                                    Tenho Interesse
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                    $count++;
                endforeach; 
                
                if (empty($cars)):
                ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <p>Nenhum carro disponível no momento.</p>
                            <p>Entre em contato conosco para ver opções disponíveis!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Navegação do Carrossel -->
    <div class="simple-slider-nav">
        <button class="arrow-nav prev-arrow" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="slider-indicators">
            <span class="indicator active" data-slide="1"></span>
        </div>
        
        <button class="arrow-nav next-arrow" disabled>
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>
