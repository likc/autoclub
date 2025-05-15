<?php
session_start();
require_once 'config.php';
check_login();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_alert('danger', 'ID do veículo inválido!');
    redirect('cars.php');
}

$car_id = (int)$_GET['id'];
$conn = db_connect();

// Obter dados do carro
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    set_alert('danger', 'Veículo não encontrado!');
    redirect('cars.php');
}

$car = $result->fetch_assoc();

// Obter categorias do carro
$stmt = $conn->prepare("
    SELECT category_id FROM car_category_relations 
    WHERE car_id = ?
");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car_categories_result = $stmt->get_result();
$car_categories = [];

while ($row = $car_categories_result->fetch_assoc()) {
    $car_categories[] = $row['category_id'];
}

// Obter todas as categorias
$categories_query = "SELECT * FROM car_categories WHERE slug != 'all' ORDER BY name ASC";
$categories = $conn->query($categories_query)->fetch_all(MYSQLI_ASSOC);

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar e sanitizar os dados
    $model = sanitize($_POST['model']);
    $year = (int)$_POST['year'];
    $mileage = (int)$_POST['mileage'];
    $transmission = sanitize($_POST['transmission']);
    $shaken_expires = sanitize($_POST['shaken_expires']);
    $price = (float)$_POST['price'];
    $installment_type = sanitize($_POST['installment_type']);
    $monthly_payment = (float)$_POST['monthly_payment'];
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $selected_categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    
    // Validação básica
    $errors = [];
    
    if (empty($model)) {
        $errors[] = "O modelo do veículo é obrigatório.";
    }
    
    if ($year < 1980 || $year > date('Y') + 1) {
        $errors[] = "O ano do veículo deve estar entre 1980 e " . (date('Y') + 1) . ".";
    }
    
    if ($mileage < 0) {
        $errors[] = "A quilometragem não pode ser negativa.";
    }
    
    if (empty($transmission)) {
        $errors[] = "A transmissão é obrigatória.";
    }
    
    if (empty($shaken_expires)) {
        $errors[] = "A data de validade do Shaken é obrigatória.";
    }
    
    if ($price <= 0) {
        $errors[] = "O preço deve ser maior que zero.";
    }
    
    if (empty($installment_type)) {
        $errors[] = "O tipo de parcelamento é obrigatório.";
    }
    
    if ($monthly_payment <= 0) {
        $errors[] = "O valor da parcela mensal deve ser maior que zero.";
    }
    
    if (empty($selected_categories)) {
        $errors[] = "Selecione pelo menos uma categoria.";
    }
    
    // Se não houver erros, atualizar os dados
    if (empty($errors)) {
        // Iniciar transação
        $conn->begin_transaction();
        
        try {
            // Verificar se uma nova imagem foi enviada
            $image_filename = $car['image']; // Manter a imagem atual por padrão
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
                $upload_result = upload_image($_FILES['image'], "uploads/");
                
                if ($upload_result['success']) {
                    $image_filename = $upload_result['filename'];
                    
                    // Remover a imagem antiga
                    if (file_exists("uploads/" . $car['image']) && $car['image'] != $image_filename) {
                        unlink("uploads/" . $car['image']);
                    }
                } else {
                    $errors[] = "Erro ao fazer upload da imagem: " . $upload_result['message'];
                    throw new Exception($upload_result['message']);
                }
            }
            
            // Atualizar dados do carro
            $stmt = $conn->prepare("
                UPDATE cars 
                SET model = ?, year = ?, mileage = ?, transmission = ?, shaken_expires = ?, 
                    price = ?, installment_type = ?, monthly_payment = ?, image = ?, 
                    is_new = ?, is_popular = ? 
                WHERE id = ?
            ");
            
            // CORRETO: 12 tipos para 12 variáveis - string, int, int, string, string, double, string, double, string, int, int, int
            $stmt->bind_param("siissdsdsiii", $model, $year, $mileage, $transmission, $shaken_expires, 
                                         $price, $installment_type, $monthly_payment, $image_filename, 
                                         $is_new, $is_popular, $car_id);
            
            $stmt->execute();
            
            // Excluir as relações de categoria antigas
            $stmt = $conn->prepare("DELETE FROM car_category_relations WHERE car_id = ?");
            $stmt->bind_param("i", $car_id);
            $stmt->execute();
            
            // Inserir novas relações de categoria
            foreach ($selected_categories as $category_id) {
                $stmt = $conn->prepare("
                    INSERT INTO car_category_relations (car_id, category_id)
                    VALUES (?, ?)
                ");
                
                $stmt->bind_param("ii", $car_id, $category_id);
                $stmt->execute();
            }
            
            // Confirmar transação
            $conn->commit();
            
            set_alert('success', 'Veículo atualizado com sucesso!');
            redirect('cars.php');
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            $conn->rollback();
            $errors[] = "Erro ao atualizar o veículo: " . $e->getMessage();
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
                <h1 class="m-0">Editar Veículo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="cars.php">Veículos</a></li>
                    <li class="breadcrumb-item active">Editar Veículo</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações do Veículo</h3>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger m-3">
                            <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="model">Modelo do Veículo*</label>
                                        <input type="text" class="form-control" id="model" name="model" placeholder="Ex: Toyota Camry ASV50L" required value="<?php echo htmlspecialchars($car['model']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="year">Ano*</label>
                                        <input type="number" class="form-control" id="year" name="year" placeholder="Ex: 2020" required min="1980" max="<?php echo date('Y') + 1; ?>" value="<?php echo htmlspecialchars($car['year']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="mileage">Quilometragem (km)*</label>
                                        <input type="number" class="form-control" id="mileage" name="mileage" placeholder="Ex: 35000" required min="0" value="<?php echo htmlspecialchars($car['mileage']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="transmission">Transmissão*</label>
                                        <select class="form-control" id="transmission" name="transmission" required>
                                            <option value="">Selecione</option>
                                            <option value="AT" <?php echo ($car['transmission'] == 'AT') ? 'selected' : ''; ?>>AT (Automático)</option>
                                            <option value="MT" <?php echo ($car['transmission'] == 'MT') ? 'selected' : ''; ?>>MT (Manual)</option>
                                            <option value="CVT" <?php echo ($car['transmission'] == 'CVT') ? 'selected' : ''; ?>>CVT</option>
                                            <option value="DCT" <?php echo ($car['transmission'] == 'DCT') ? 'selected' : ''; ?>>DCT (Dupla Embreagem)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="shaken_expires">Validade do Shaken*</label>
                                        <input type="text" class="form-control" id="shaken_expires" name="shaken_expires" placeholder="Ex: 12/2025" required value="<?php echo htmlspecialchars($car['shaken_expires']); ?>">
                                        <small class="form-text text-muted">Formato: MM/AAAA</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Preço Total (¥)*</label>
                                        <input type="number" class="form-control" id="price" name="price" placeholder="Preço total em ienes" required min="0" value="<?php echo htmlspecialchars($car['price']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="installment_type">Tipo de Parcelamento*</label>
                                        <select class="form-control" id="installment_type" name="installment_type" required>
                                            <option value="">Selecione</option>
                                            <option value="84x" <?php echo ($car['installment_type'] == '84x') ? 'selected' : ''; ?>>Até 84x</option>
                                            <option value="120x" <?php echo ($car['installment_type'] == '120x') ? 'selected' : ''; ?>>Até 120x</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="monthly_payment">Valor da Parcela Mensal (¥)*</label>
                                        <input type="number" class="form-control" id="monthly_payment" name="monthly_payment" placeholder="Valor mensal em ienes" required min="0" value="<?php echo htmlspecialchars($car['monthly_payment']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Categorias*</label>
                                        <div class="select-all mb-2">
                                            <div class="icheck-primary">
                                                <input type="checkbox" id="select_all_categories">
                                                <label for="select_all_categories">Selecionar todas</label>
                                            </div>
                                        </div>
                                        <?php foreach ($categories as $category): ?>
                                            <div class="icheck-primary">
                                                <input type="checkbox" name="categories[]" id="category_<?php echo $category['id']; ?>" value="<?php echo $category['id']; ?>" class="category-checkbox" <?php echo (in_array($category['id'], $car_categories)) ? 'checked' : ''; ?>>
                                                <label for="category_<?php echo $category['id']; ?>"><?php echo $category['name']; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Destaque</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" name="is_new" id="is_new" <?php echo ($car['is_new'] == 1) ? 'checked' : ''; ?>>
                                                    <label for="is_new">Marcar como Novo</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" name="is_popular" id="is_popular" <?php echo ($car['is_popular'] == 1) ? 'checked' : ''; ?>>
                                                    <label for="is_popular">Marcar como Popular</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Imagem do Veículo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                        <label class="custom-file-label" for="image">Escolher nova imagem (opcional)</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Tamanho máximo: 5MB. Formatos aceitos: JPG, JPEG, PNG, GIF. Deixe em branco para manter a imagem atual.</small>
                            </div>
                            
                            <div class="image-preview mt-3">
                                <label>Imagem Atual:</label><br>
                                <img src="uploads/<?php echo $car['image']; ?>" id="current_image" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </div>
                            
                            <div class="image-preview mt-3" id="imagePreview" style="display: none;">
                                <label>Nova Imagem:</label><br>
                                <img src="" id="preview" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="cars.php" class="btn btn-default">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview da imagem
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const preview = document.getElementById('preview');
        
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            const fileLabel = document.querySelector('.custom-file-label');
            
            if (file) {
                fileLabel.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                fileLabel.textContent = 'Escolher nova imagem (opcional)';
                imagePreview.style.display = 'none';
            }
        });
        
        // Selecionar/desmarcar todas as categorias
        const selectAllCheckbox = document.getElementById('select_all_categories');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Verificar se todas as categorias estão selecionadas
        function checkAllSelected() {
            const allChecked = Array.from(categoryCheckboxes).every(checkbox => checkbox.checked);
            selectAllCheckbox.checked = allChecked;
        }
        
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', checkAllSelected);
        });
        
        // Verificar inicialmente
        checkAllSelected();
        
        // Calcular valor mensal automaticamente quando o preço ou tipo de parcelamento mudar
        const priceInput = document.getElementById('price');
        const installmentTypeSelect = document.getElementById('installment_type');
        const monthlyPaymentInput = document.getElementById('monthly_payment');
        
        function calculateMonthlyPayment() {
            const price = parseFloat(priceInput.value) || 0;
            const installmentType = installmentTypeSelect.value;
            
            if (price > 0 && installmentType) {
                const months = installmentType === '84x' ? 84 : 120;
                const monthlyPayment = Math.ceil(price / months);
                monthlyPaymentInput.value = monthlyPayment;
            }
        }
        
        priceInput.addEventListener('input', calculateMonthlyPayment);
        installmentTypeSelect.addEventListener('change', calculateMonthlyPayment);
    });
</script>

<?php include 'includes/footer.php';