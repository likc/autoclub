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
                                // Obter o nome do arquivo do banco de dados
                                $image_filename = $car['image'];
                                
                                // Construir o caminho correto da imagem
                                // Como o index.php está na raiz, precisamos apontar para admin/uploads/
                                $image_path = "admin/uploads/" . $image_filename;
                                
                                // Verificar se o arquivo existe
                                if (!file_exists($image_path) || empty($image_filename)) {
                                    // Caso a imagem não seja encontrada, usar imagem padrão
                                    $image_path = "img/car-default.jpg";
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($car['model']); ?>">
                              <!--  <div class="car-card__overlay">
                                    <a href="car-details.html?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-outline">Ver Detalhes</a>
                                </div>-->
                            </div>
                            
                            <div class="car-card__content">
                                <span class="car-card__year"><?php echo $car['year']; ?></span>
                                <h3 class="car-card__title"><?php echo htmlspecialchars($car['model']); ?></h3>
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
                            <p>Nenhum carro encontrado no banco de dados.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Mostrar a segunda linha se houver mais de 4 carros -->
            <?php if ($count > 4): ?>
                <!-- Segunda linha já foi criada no loop acima -->
            <?php endif; ?>
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
<!-- Adicione este código temporariamente no início do show_cars.php para debug -->
<?php
// Debug: Verificar estrutura dos dados
echo "<!-- DEBUG INFO -->";
echo "<!-- Current Directory: " . getcwd() . " -->";
echo "<!-- Upload Directory Exists: " . (file_exists('admin/uploads/') ? 'YES' : 'NO') . " -->";

// Se quiser ver os dados dos carros no código-fonte HTML
foreach ($cars as $index => $car) {
    echo "<!-- Car $index: ";
    echo "Image: " . $car['image'];
    echo ", Path: admin/uploads/" . $car['image'];
    echo ", Exists: " . (file_exists('admin/uploads/' . $car['image']) ? 'YES' : 'NO');
    echo " -->";
}
?>