<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

// Verificar se o ID do carro foi fornecido
if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    set_alert('danger', 'ID do carro inválido!');
    redirect('cars.php');
}

$car_id = (int)$_GET['car_id'];
$conn = db_connect();

// Obter dados do carro
$stmt = $conn->prepare("
    SELECT c.*, cc.name as category_name
    FROM cars c
    LEFT JOIN car_category_relations ccr ON c.id = ccr.car_id
    LEFT JOIN car_categories cc ON ccr.category_id = cc.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    set_alert('danger', 'Carro não encontrado!');
    redirect('cars.php');
}

$car = $result->fetch_assoc();

// Redirecionar para o formulário de contrato com valores pré-preenchidos
$redirect_url = 'contract_edit.php?' . http_build_query([
    'pre_fill' => '1',
    'vehicle_name' => $car['model'],
    'vehicle_plate' => '', // Preencher manualmente
    'vehicle_year' => $car['year'],
    'vehicle_chassis' => '', // Preencher manualmente
    'vehicle_katashiki' => '', // Preencher manualmente
    'vehicle_color' => '', // Preencher manualmente
    'vehicle_mileage' => $car['mileage'],
    'vehicle_value' => $car['price'],
    'consumption_tax' => round($car['price'] * 0.1), // 10% do valor do carro
    'gps_value' => 76000, // Valor padrão, ajustar conforme necessário
    'shaken_value' => 0, // Adicionar campo para o valor do Shaken
    'annual_tax' => 0, // Adicionar campo para o imposto anual
    'transfer_delivery' => 30000, // Valor padrão, ajustar conforme necessário
    'kaitori_value' => 0, // Adicionar campo para o valor do Kaitori
    'total_value' => $car['price'] + round($car['price'] * 0.1) + 76000 + 30000 // Soma inicial
]);

// Registrar atividade
log_admin_activity("Iniciou criação de contrato a partir do carro: " . $car['model'], "other", $car_id, "car");

$conn->close();
redirect($redirect_url);
?>