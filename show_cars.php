<?php
/**
 * Show Cars - Script para exibir os carros na página principal
 * 
 * Salve este arquivo na raiz do site (mesmo nível do index.php)
 * E então inclua/substitua a seção de carros no seu HTML principal
 */

// Configuração do banco de dados (ajuste conforme suas configurações)
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
    <!-- Slide Container -->
    <div class="slides-container">
        <!-- Slide 1 (inicialmente visível) -->
        <div class="car-slide active" data-slide="1">
            <!-- Primeira linha de carros (andar 1) -->
            <div class="car-grid row">
                <?php
                // Contador para controlar quando criar uma nova linha
                $count = 0;
                
                // Loop pelos carros
                foreach ($cars as $car):
                    // Criar uma nova linha após 4 carros
                    if ($count === 4):
                        echo '</div><div class="car-grid row mt-4">';
                    endif;
                    
                    // Criar a string de categoria para o atributo data-category
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
                                // Correção para garantir que o caminho completo da imagem seja usado
                                $image_filename = $car['image']; // Nome do arquivo no banco de dados
                                
                                // Verificar se o arquivo existe
                                $image_path = "admin/uploads/{$image_filename}";
                                
                                if (!file_exists($image_path)) {
                                    // Caso a imagem não seja encontrada, usar imagem padrão
                                    $image_path = "img/car-default.jpg";
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $car['model']; ?>">
                              <!--  <div class="car-card__overlay">
                                    <a href="car-details.html?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-outline">Ver Detalhes</a>
                                </div>-->
                            </div>
                            
                            <div class="car-card__content">
                                <span class="car-card__year"><?php echo $car['year']; ?></span>
                                <h3 class="car-card__title"><?php echo $car['model']; ?></h3>
                                <ul class="car-card__specs">
                                    <li><i class="fas fa-road"></i> <span><?php echo number_format($car['mileage'], 0, ',', '.'); ?></span> km</li>
                                    <li><i class="fas fa-cog"></i> <span><?php echo $car['transmission']; ?></span> Automático</li>
                                    <li><i class="fas fa-calendar-alt"></i> Shaken: <span><?php echo $car['shaken_expires']; ?></span></li>
                                </ul>
                                <div class="car-card__price">
                                    <span class="car-card__price-badge" style="background: <?php echo $car['installment_type'] === '120x' ? 'var(--accent)' : 'var(--primary)'; ?>;">Até <?php echo $car['installment_type']; ?></span>
                                    <div class="car-card__price-amount">¥<?php echo number_format($car['monthly_payment'], 0, ',', '.'); ?><small>/Mês</small></div>
                                </div>
                                <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20tenho%20interesse%20no%20<?php echo urlencode($car['model']); ?>" class="btn btn-primary btn-block car-card__cta">Tenho Interesse</a>
                            </div>
                        </div>
                    </div>
                <?php 
                    $count++;
                endforeach; 
                
                // Se não houver carros, mostrar mensagem
                if (empty($cars)):
                ?>
                    <div class="col-12">
                        <div class="alert text-center">
                            <p>Nenhum carro encontrado.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Navegação do Carrossel (mantido apenas por compatibilidade, já que agora só temos 1 slide) -->
    <div class="simple-slider-nav">
        <!-- Botão Anterior -->
        <button class="arrow-nav prev-arrow" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <!-- Indicadores -->
        <div class="slider-indicators">
            <span class="indicator active" data-slide="1"></span>
        </div>
        
        <!-- Botão Próximo -->
        <button class="arrow-nav next-arrow" disabled>
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>