<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_alert('danger', 'ID do contrato inválido!');
    redirect('contracts.php');
}

$contract_id = (int)$_GET['id'];
$conn = db_connect();

// Obter informações do contrato para registro de atividade
$stmt = $conn->prepare("SELECT client_name, filename FROM contracts WHERE id = ?");
$stmt->bind_param("i", $contract_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    set_alert('danger', 'Contrato não encontrado!');
    redirect('contracts.php');
}

$contract = $result->fetch_assoc();
$client_name = $contract['client_name'];
$filename = $contract['filename'];

// Iniciar transação
$conn->begin_transaction();

try {
    // Excluir o contrato
    $stmt = $conn->prepare("DELETE FROM contracts WHERE id = ?");
    $stmt->bind_param("i", $contract_id);
    $stmt->execute();
    
    // Confirmar transação
    $conn->commit();
    
    // Excluir o arquivo PDF se existir
    if (!empty($filename) && file_exists('uploads/contracts/' . $filename)) {
        unlink('uploads/contracts/' . $filename);
    }
    
    // Registrar atividade
    log_admin_activity("Excluiu o contrato #$contract_id para o cliente: $client_name", "delete", $contract_id, "contract");
    
    set_alert('success', 'Contrato excluído com sucesso!');
} catch (Exception $e) {
    // Reverter transação em caso de erro
    $conn->rollback();
    set_alert('danger', 'Erro ao excluir o contrato: ' . $e->getMessage());
}

$conn->close();
redirect('contracts.php');
?>