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

// Obter informações do carro para excluir a imagem
$stmt = $conn->prepare("SELECT image FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    set_alert('danger', 'Veículo não encontrado!');
    redirect('cars.php');
}

$car = $result->fetch_assoc();
$image_path = "uploads/" . $car['image'];

// Iniciar transação
$conn->begin_transaction();

try {
    // Excluir relações com categorias
    $stmt = $conn->prepare("DELETE FROM car_category_relations WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    
    // Excluir o carro
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    
    // Confirmar transação
    $conn->commit();
    
    // Excluir a imagem
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    
    set_alert('success', 'Veículo excluído com sucesso!');
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    set_alert('danger', 'Erro ao excluir o veículo: ' . $e->getMessage());
}

$conn->close();
redirect('cars.php');
?>