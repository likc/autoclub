<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

$conn = db_connect();

// Verificar se é edição ou criação
$is_edit = isset($_GET['id']) && is_numeric($_GET['id']);
$contract_id = $is_edit ? (int)$_GET['id'] : 0;
$contract = [];

// Se for edição, obter os dados do contrato
if ($is_edit) {
    $stmt = $conn->prepare("SELECT * FROM contracts WHERE id = ?");
    $stmt->bind_param("i", $contract_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        set_alert('danger', 'Contrato não encontrado!');
        redirect('contracts.php');
    }
    
    $contract = $result->fetch_assoc();
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter e sanitizar dados do formulário
    $client_name = sanitize($_POST['client_name']);
    $client_id = sanitize($_POST['client_id']);
    $client_address = sanitize($_POST['client_address']);
    $client_phone1 = sanitize($_POST['client_phone1']);
    $client_phone2 = sanitize($_POST['client_phone2']);
    $payment_method = sanitize($_POST['payment_method']);
    $seller = sanitize($_POST['seller']);
    $date = sanitize($_POST['date']);
    
    $vehicle_name = sanitize($_POST['vehicle_name']);
    $vehicle_plate = sanitize($_POST['vehicle_plate']);
    $vehicle_year = sanitize($_POST['vehicle_year']);
    $vehicle_chassis = sanitize($_POST['vehicle_chassis']);
    $vehicle_katashiki = sanitize($_POST['vehicle_katashiki']);
    $vehicle_color = sanitize($_POST['vehicle_color']);
    $vehicle_mileage = (int)$_POST['vehicle_mileage'];
    
    $vehicle_value = (float)$_POST['vehicle_value'];
    $consumption_tax = (float)$_POST['consumption_tax'];
    $gps_value = (float)$_POST['gps_value'];
    $shaken_value = (float)$_POST['shaken_value'];
    $annual_tax = (float)$_POST['annual_tax'];
    $transfer_delivery = (float)$_POST['transfer_delivery'];
    $kaitori_value = (float)$_POST['kaitori_value'];
    $total_value = (float)$_POST['total_value'];
    
    // Validação básica
    $errors = [];
    
    if (empty($client_name)) {
        $errors[] = "Nome do cliente é obrigatório.";
    }
    
    if (empty($date)) {
        $errors[] = "Data é obrigatória.";
    }
    
    if (empty($vehicle_name)) {
        $errors[] = "Nome do veículo é obrigatório.";
    }
    
    // Se não houver erros, salvar o contrato
    if (empty($errors)) {
        $admin_id = $_SESSION['admin_id'];
        
        if ($is_edit) {
            // Atualizar contrato existente
            $stmt = $conn->prepare("
                UPDATE contracts SET 
                    client_name = ?, client_id = ?, client_address = ?, client_phone1 = ?, client_phone2 = ?,
                    payment_method = ?, seller = ?, date = ?, vehicle_name = ?, vehicle_plate = ?,
                    vehicle_year = ?, vehicle_chassis = ?, vehicle_katashiki = ?, vehicle_color = ?,
                    vehicle_mileage = ?, vehicle_value = ?, consumption_tax = ?, gps_value = ?,
                    shaken_value = ?, annual_tax = ?, transfer_delivery = ?, kaitori_value = ?,
                    total_value = ?
                WHERE id = ?
            ");
            
            $stmt->bind_param(
                "ssssssssssssssiiddddddi",
                $client_name, $client_id, $client_address, $client_phone1, $client_phone2,
                $payment_method, $seller, $date, $vehicle_name, $vehicle_plate,
                $vehicle_year, $vehicle_chassis, $vehicle_katashiki, $vehicle_color,
                $vehicle_mileage, $vehicle_value, $consumption_tax, $gps_value,
                $shaken_value, $annual_tax, $transfer_delivery, $kaitori_value,
                $total_value, $contract_id
            );
            
            if ($stmt->execute()) {
                // Registrar atividade
                log_admin_activity("Editou o contrato #$contract_id para o cliente: $client_name", "edit", $contract_id, "contract");
                
                // Redirecionar para a visualização do PDF
                redirect('contract_view.php?id=' . $contract_id);
            } else {
                $errors[] = "Erro ao atualizar contrato: " . $conn->error;
            }
        } else {
            // Inserir novo contrato
            $stmt = $conn->prepare("
                INSERT INTO contracts (
                    client_name, client_id, client_address, client_phone1, client_phone2,
                    payment_method, seller, date, vehicle_name, vehicle_plate,
                    vehicle_year, vehicle_chassis, vehicle_katashiki, vehicle_color,
                    vehicle_mileage, vehicle_value, consumption_tax, gps_value,
                    shaken_value, annual_tax, transfer_delivery, kaitori_value,
                    total_value, created_by
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");
            
            $stmt->bind_param(
                "ssssssssssssssiiddddddi",
                $client_name, $client_id, $client_address, $client_phone1, $client_phone2,
                $payment_method, $seller, $date, $vehicle_name, $vehicle_plate,
                $vehicle_year, $vehicle_chassis, $vehicle_katashiki, $vehicle_color,
                $vehicle_mileage, $vehicle_value, $consumption_tax, $gps_value,
                $shaken_value, $annual_tax, $transfer_delivery, $kaitori_value,
                $total_value, $admin_id
            );
            
            if ($stmt->execute()) {
                $contract_id = $conn->insert_id;
                
                // Registrar atividade
                log_admin_activity("Criou novo contrato #$contract_id para o cliente: $client_name", "add", $contract_id, "contract");
                
                // Redirecionar para a visualização do PDF
                redirect('contract_view.php?id=' . $contract_id);
            } else {
                $errors[] = "Erro ao criar contrato: " . $conn->error;
            }
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
                <h1 class="m-0"><?php echo $is_edit ? 'Editar' : 'Novo'; ?> Contrato</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="contracts.php">Contratos</a></li>
                    <li class="breadcrumb-item active"><?php echo $is_edit ? 'Editar' : 'Novo'; ?> Contrato</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Ocorreram erros:</h5>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" id="contractForm">
            <div class="row">
                <!-- Informações do Cliente -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informações do Cliente</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="client_name">Nome Completo*</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required
                                    value="<?php echo isset($contract['client_name']) ? htmlspecialchars($contract['client_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="client_id">ID do Cliente</label>
                                <input type="text" class="form-control" id="client_id" name="client_id"
                                    value="<?php echo isset($contract['client_id']) ? htmlspecialchars($contract['client_id']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="client_address">Endereço</label>
                                <textarea class="form-control" id="client_address" name="client_address" rows="3"><?php echo isset($contract['client_address']) ? htmlspecialchars($contract['client_address']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="client_phone1">Telefone 1</label>
                                <input type="text" class="form-control" id="client_phone1" name="client_phone1"
                                    value="<?php echo isset($contract['client_phone1']) ? htmlspecialchars($contract['client_phone1']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="client_phone2">Telefone 2</label>
                                <input type="text" class="form-control" id="client_phone2" name="client_phone2"
                                    value="<?php echo isset($contract['client_phone2']) ? htmlspecialchars($contract['client_phone2']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informações do Financiamento -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informações do Financiamento</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="seller">Vendedor</label>
                                <input type="text" class="form-control" id="seller" name="seller"
                                    value="<?php echo isset($contract['seller']) ? htmlspecialchars($contract['seller']) : 'ANDERSON RAMOS'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="date">Data*</label>
                                <input type="date" class="form-control" id="date" name="date" required
                                    value="<?php echo isset($contract['date']) ? htmlspecialchars($contract['date']) : date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Forma de Pagamento</label>
                                <input type="text" class="form-control" id="payment_method" name="payment_method"
                                    value="<?php echo isset($contract['payment_method']) ? htmlspecialchars($contract['payment_method']) : 'FINANCIAMENTO AUTO CLUB'; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informações do Veículo -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informações do Veículo</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vehicle_name">Nome*</label>
                                <input type="text" class="form-control" id="vehicle_name" name="vehicle_name" required
                                    value="<?php echo isset($contract['vehicle_name']) ? htmlspecialchars($contract['vehicle_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_plate">Placa</label>
                                <input type="text" class="form-control" id="vehicle_plate" name="vehicle_plate"
                                    value="<?php echo isset($contract['vehicle_plate']) ? htmlspecialchars($contract['vehicle_plate']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_year">Ano de Fabricação</label>
                                <input type="text" class="form-control" id="vehicle_year" name="vehicle_year"
                                    value="<?php echo isset($contract['vehicle_year']) ? htmlspecialchars($contract['vehicle_year']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_chassis">Chassi</label>
                                <input type="text" class="form-control" id="vehicle_chassis" name="vehicle_chassis"
                                    value="<?php echo isset($contract['vehicle_chassis']) ? htmlspecialchars($contract['vehicle_chassis']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_katashiki">Katashiki</label>
                                <input type="text" class="form-control" id="vehicle_katashiki" name="vehicle_katashiki"
                                    value="<?php echo isset($contract['vehicle_katashiki']) ? htmlspecialchars($contract['vehicle_katashiki']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_color">Cor</label>
                                <input type="text" class="form-control" id="vehicle_color" name="vehicle_color"
                                    value="<?php echo isset($contract['vehicle_color']) ? htmlspecialchars($contract['vehicle_color']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_mileage">Quilometragem</label>
                                <input type="number" class="form-control" id="vehicle_mileage" name="vehicle_mileage"
                                    value="<?php echo isset($contract['vehicle_mileage']) ? htmlspecialchars($contract['vehicle_mileage']) : '0'; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Valores do Leasing -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Valores do Leasing</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="vehicle_value">Valor do Veículo (¥)</label>
                                <input type="number" class="form-control calc-field" id="vehicle_value" name="vehicle_value" step="1"
                                    value="<?php echo isset($contract['vehicle_value']) ? htmlspecialchars($contract['vehicle_value']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="consumption_tax">Imposto de Consumo (10%) (¥)</label>
                                <input type="number" class="form-control calc-field" id="consumption_tax" name="consumption_tax" step="1"
                                    value="<?php echo isset($contract['consumption_tax']) ? htmlspecialchars($contract['consumption_tax']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="gps_value">GPS (¥)</label>
                                <input type="number" class="form-control calc-field" id="gps_value" name="gps_value" step="1"
                                    value="<?php echo isset($contract['gps_value']) ? htmlspecialchars($contract['gps_value']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="shaken_value">Shaken (¥)</label>
                                <input type="number" class="form-control calc-field" id="shaken_value" name="shaken_value" step="1"
                                    value="<?php echo isset($contract['shaken_value']) ? htmlspecialchars($contract['shaken_value']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="annual_tax">Imposto Anual (Zeikin) (¥)</label>
                                <input type="number" class="form-control calc-field" id="annual_tax" name="annual_tax" step="1"
                                    value="<?php echo isset($contract['annual_tax']) ? htmlspecialchars($contract['annual_tax']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="transfer_delivery">Transferência e Entrega (¥)</label>
                                <input type="number" class="form-control calc-field" id="transfer_delivery" name="transfer_delivery" step="1"
                                    value="<?php echo isset($contract['transfer_delivery']) ? htmlspecialchars($contract['transfer_delivery']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="kaitori_value">Valor do Kaitori (¥)</label>
                                <input type="number" class="form-control calc-field" id="kaitori_value" name="kaitori_value" step="1"
                                    value="<?php echo isset($contract['kaitori_value']) ? htmlspecialchars($contract['kaitori_value']) : '0'; ?>">
                            </div>
                            <div class="form-group">
                                <label for="total_value">TOTAL (¥)</label>
                                <input type="number" class="form-control" id="total_value" name="total_value" readonly
                                    value="<?php echo isset($contract['total_value']) ? htmlspecialchars($contract['total_value']) : '0'; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i> Salvar Contrato
                            </button>
                            <a href="contracts.php" class="btn btn-default btn-lg ml-2">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcular consumo de imposto automaticamente (10%)
        document.getElementById('vehicle_value').addEventListener('input', function() {
            const vehicleValue = parseFloat(this.value) || 0;
            const consumptionTax = Math.round(vehicleValue * 0.1);
            document.getElementById('consumption_tax').value = consumptionTax;
            calculateTotal();
        });
        
        // Calcular total automaticamente
        const calcFields = document.querySelectorAll('.calc-field');
        calcFields.forEach(function(field) {
            field.addEventListener('input', calculateTotal);
        });
        
        function calculateTotal() {
            const vehicleValue = parseFloat(document.getElementById('vehicle_value').value) || 0;
            const consumptionTax = parseFloat(document.getElementById('consumption_tax').value) || 0;
            const gpsValue = parseFloat(document.getElementById('gps_value').value) || 0;
            const shakenValue = parseFloat(document.getElementById('shaken_value').value) || 0;
            const annualTax = parseFloat(document.getElementById('annual_tax').value) || 0;
            const transferDelivery = parseFloat(document.getElementById('transfer_delivery').value) || 0;
            const kaitoriValue = parseFloat(document.getElementById('kaitori_value').value) || 0;
            
            const total = vehicleValue + consumptionTax + gpsValue + shakenValue + 
                          annualTax + transferDelivery + kaitoriValue;
            
            document.getElementById('total_value').value = Math.round(total);
        }
        
        // Calcular total na carga da página
        calculateTotal();
    });
</script>

<?php include 'includes/footer.php'; ?>