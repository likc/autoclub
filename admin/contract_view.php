<?php
session_start();
require_once 'config.php';
check_login();
check_admin_permission(); // Permitido apenas para Admin

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_alert('danger', 'ID do contrato inválido!');
    redirect('contracts.php');
}

$contract_id = (int)$_GET['id'];
$conn = db_connect();

// Obter dados do contrato
$stmt = $conn->prepare("SELECT * FROM contracts WHERE id = ?");
$stmt->bind_param("i", $contract_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    set_alert('danger', 'Contrato não encontrado!');
    redirect('contracts.php');
}

$contract = $result->fetch_assoc();
$conn->close();

// Verificar se é para visualizar ou fazer download
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Caminho para a biblioteca FPDF (verifique se existe)
$fpdf_path = 'includes/fpdf/fpdf.php';
if (!file_exists($fpdf_path)) {
    set_alert('danger', 'Biblioteca FPDF não encontrada! Por favor, instale a biblioteca FPDF em includes/fpdf/.');
    redirect('contracts.php');
}

require_once $fpdf_path;

// Estender a classe FPDF para incluir recursos personalizados
class ContractPDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('img/logo.png', 10, 10, 30);
        
        // Título do contrato
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'CONTRATO DE LEASING', 0, 1, 'C');
        $this->Ln(5);
    }
    
    function Footer() {
        // Posicionar a 1.5 cm do fundo
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Função para criar a seção de informações
    function CreateSection($title, $border = 0) {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, $title, $border, 1, 'L');
        $this->SetFont('Arial', '', 10);
    }
    
    // Função para criar uma célula com campo e valor
    function CreateField($field, $value, $width1 = 60, $width2 = 0) {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($width1, 6, $field, 0, 0);
        $this->SetFont('Arial', '', 9);
        
        if ($width2 > 0) {
            $this->Cell($width2, 6, $value, 0, 1);
        } else {
            $this->Cell(0, 6, $value, 0, 1);
        }
    }
    
    // Função para criar uma tabela
    function CreateTable($headers, $data) {
        // Larguras de coluna
        $w = array(40, 60, 40, 50);
        
        // Cabeçalho
        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 9);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($w[$i], 7, $headers[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Dados
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(240, 240, 240);
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Linha de fechamento
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
    }
    
    // Função para criar as cláusulas do contrato
    function CreateClause($number, $text) {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 6, "CLÁUSULA $number:", 0, 0);
        $this->SetFont('Arial', '', 9);
        
        // Calcular quantas linhas o texto vai ocupar (estimativa simples)
        $lines = ceil(strlen($text) / 120); // Aproximadamente 120 caracteres por linha
        
        if ($lines <= 1) {
            // Se for uma linha só, usar Cell normal
            $this->Cell(0, 6, $text, 0, 1);
        } else {
            // Se for mais de uma linha, usar MultiCell
            $this->Ln(6);
            $this->MultiCell(0, 5, $text, 0, 'J');
            $this->Ln(1);
        }
    }
    
    // Função para criar campo de assinatura
    function CreateSignature($left_title, $right_title) {
        $this->Ln(10);
        
        // Linha de assinatura
        $this->SetFont('Arial', '', 10);
        $this->Cell(90, 6, str_repeat('_', 40), 0, 0, 'C');
        $this->Cell(10, 6, '', 0, 0);
        $this->Cell(90, 6, str_repeat('_', 40), 0, 1, 'C');
        
        // Títulos
        $this->Cell(90, 6, $left_title, 0, 0, 'C');
        $this->Cell(10, 6, '', 0, 0);
        $this->Cell(90, 6, $right_title, 0, 1, 'C');
        
        $this->Ln(10);
    }
    
    // Função para criar campo de data
    function CreateDateField($city = 'HAMAMATSU') {
        $this->Ln(5);
        $this->Cell(25, 6, $city . ',', 0, 0);
        $this->Cell(20, 6, '_______ DE', 0, 0);
        $this->Cell(45, 6, '____________________DE', 0, 0);
        $this->Cell(20, 6, '___________', 0, 1);
    }
}

// Criar o documento PDF
$pdf = new ContractPDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

// Seção de Informações do Cliente
$pdf->CreateSection('INFORMACOES DO CLIENTE', 0);
$pdf->Ln(2);

// Grid de 2 colunas para informações do cliente
$pdf->CreateField('NOME COMPLETO', mb_strtoupper($contract['client_name']));
$pdf->CreateField('ENDERECO', mb_strtoupper($contract['client_address']));
$pdf->CreateField('TELEFONE 1', $contract['client_phone1']);
$pdf->CreateField('TELEFONE 2', $contract['client_phone2']);

// Seção de Informações do Financiamento
$pdf->Ln(5);
$pdf->CreateSection('INFORMACOES DO FINANCIAMENTO', 0);
$pdf->Ln(2);

// Grid para informações do financiamento
$pdf->CreateField('ID DO CLIENTE', $contract['client_id']);
$pdf->CreateField('VENDEDOR', mb_strtoupper($contract['seller']));
$pdf->CreateField('DATA', date('d/m/Y', strtotime($contract['date'])));
$pdf->CreateField('FORMA DE PAGAMENTO', mb_strtoupper($contract['payment_method']));

// Seção de Informações do Veículo
$pdf->Ln(5);
$pdf->CreateSection('INFORMACOES DO VEICULO', 0);
$pdf->Ln(2);

// Grid para informações do veículo
$pdf->CreateField('NOME', mb_strtoupper($contract['vehicle_name']));
$pdf->CreateField('PLACA', mb_strtoupper($contract['vehicle_plate']));
$pdf->CreateField('ANO DE FABRICACAO', $contract['vehicle_year']);
$pdf->CreateField('CHASSI', mb_strtoupper($contract['vehicle_chassis']));
$pdf->CreateField('KATASHIKI', mb_strtoupper($contract['vehicle_katashiki']));
$pdf->CreateField('COR', mb_strtoupper($contract['vehicle_color']));
$pdf->CreateField('QUILOMETRAGEM', number_format($contract['vehicle_mileage'], 0, ',', '.'));

// Seção de Valores do Leasing
$pdf->Ln(5);
$pdf->CreateSection('VALORES DO LEASING', 0);
$pdf->Ln(2);

// Formatar números em ienes
function formatYen($value) {
    return '¥' . number_format($value, 0, ',', '.');
}

// Grid para valores do leasing
$pdf->CreateField('VALOR DO VEICULO', formatYen($contract['vehicle_value']));
$pdf->CreateField('IMPOSTO DE CONSUMO (10%)', formatYen($contract['consumption_tax']));
$pdf->CreateField('GPS', formatYen($contract['gps_value']));
$pdf->CreateField('SHAKEN', formatYen($contract['shaken_value']));
$pdf->CreateField('IMPOSTO ANUAL (ZEIKIN)', formatYen($contract['annual_tax']));
$pdf->CreateField('TRANSFERENCIA E ENTREGA', formatYen($contract['transfer_delivery']));
$pdf->CreateField('VALOR DO KAITORI', formatYen($contract['kaitori_value']));
$pdf->CreateField('TOTAL', formatYen($contract['total_value']));

// Adicionar as cláusulas contratuais
$pdf->Ln(10);
$pdf->CreateSection('CLAUSULAS CONTRATUAIS', 0);
$pdf->Ln(5);

// Cláusulas
$clausulas = [
    "Fica na responsabilidade do ARRENDATÁRIO(a) providenciar toda a documentação necessária para a transferência do veículo, no prazo máximo de 20 dias, o não cumprimento deste prazo será cobrado uma multa de ¥10.000.",
    
    "Os pagamentos via depósitos bancários devem ser realizados com o ID (número de identificação do arrendatário(a), caso haja dificuldade entre em contato com a arrendadora pelo número 080 9281 5155.",
    
    "O arrendatário pagará a quantia mensal discriminado no verso do contrato, caso não seja efetuada até a data que está estabelecida no contrato, a arrendadora cobrará ¥3.500 de multa pelo atraso e se a falta de pagamento ultrapassar 5 dias do contrato, a arrendadora poderá BLOQUEAR a partida do motor por controle remoto do terminal de GPS e reter o veículo, e a liberação somente será possível se o arrendatário(a) efetuar o pagamento da quantia mensal em atraso.",
    
    "A arrendadora dará garantia SOMENTE de motor e câmbio durante 2 meses ou 2 mil quilômetros (o que vencer primeiro), a partir da data que foi entregue o carro e se caso houver algum problema fica na responsabilidade do cliente trazê-lo até a arrendadora para realizar a checagem e tomar as devidas providências. NÃO DAREMOS GARANTIA A VEÍCULOS ESPORTIVOS, IMPORTADOS E VEÍCULOS COM MAIS DE DEZ ANOS DE USO. No caso de atraso nos pagamentos mensais, não será possível recorrer aos serviços de garantia (consertos, manutenções, etc) ficando a cargo do arrendatário(a), todas as responsabilidades de possíveis despesas.",
    
    "Em caso de rescisão do contrato por parte do arrendatário(a), este deverá pagar o valor de ¥100.000 (cem mil ienes) no ato da rescisão e ou devolução do carro, se houver quantias mensais atrasadas até o momento também deverão ser pagas.",
    
    "A troca do bem será aceita somente após a quitação do contrato.",
    
    "A arrendadora não se responsabiliza pelos danos causados por desgastes naturais devido ao tempo de uso, por estragos do bem por causa da negligência do arrendatário(a), ou devido às modificações das características originais do bem.",
    
    "Ao término de pagamento do contrato, o arrendatário passa a ser proprietário do bem adquirido.",
    
    "O arrendatário está ciente da instalação de um GPS de rastreio e bloqueio no veículo por uma empresa terceirizada contratada pela arrendadora.",
    
    "ANTES DA ASSINATURA DESTE CONTRATO, CABE AO ARRENDATÁRIO(A) TESTAR E AVALIAR AS CONDIÇÕES DO BEM ARRENDADO."
];

foreach ($clausulas as $index => $clausula) {
    $pdf->CreateClause($index + 1, $clausula);
    $pdf->Ln(3);
}

// Artigo 1 (tratado separadamente por ser formatado diferente)
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 6, "ARTIGO 1:", 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, "O arrendatário(a) não terá o reembolso do que foi pago (como entrada de veículo, dinheiro em espécie, quantias pagas em dinheiro, etc.) e se compromete a devolver o veículo nas mesmas condições de conservação no ato da aquisição, bem como os acessórios instalados por solicitação do arrendatário(a) e todos os gastos com as despesas (caso seja necessário a arrendatária ter que buscar o veículo).", 0, 'J');
$pdf->Ln(3);

// Campo de assinaturas
$pdf->CreateSignature('VENDEDOR', 'COMPRADOR');

// Campo de data
$pdf->CreateDateField();

// Segunda página para detalhes do leasing e pagamentos
$pdf->AddPage();

// Título da segunda página
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'DETALHES DO LEASING', 0, 1, 'C');
$pdf->Ln(5);

// Seção de Informações do Veículo (repetida)
$pdf->CreateSection('INFORMACOES DO VEICULO', 0);
$pdf->Ln(2);

// Grid para informações do veículo na segunda página
$pdf->CreateField('MODELO DO CARRO', mb_strtoupper($contract['vehicle_name']));
$pdf->CreateField('MARCA', getCarBrand($contract['vehicle_name']));
$pdf->CreateField('PLACA', mb_strtoupper($contract['vehicle_plate']));
$pdf->CreateField('ANO DE FABRICACAO', $contract['vehicle_year']);
$pdf->CreateField('CHASSI', mb_strtoupper($contract['vehicle_chassis']));
$pdf->CreateField('KATASHIKI', mb_strtoupper($contract['vehicle_katashiki']));
$pdf->CreateField('COR', mb_strtoupper($contract['vehicle_color']));
$pdf->CreateField('QUILOMETRAGEM', number_format($contract['vehicle_mileage'], 0, ',', '.'));

// Seção de Informações do Leasing
$pdf->Ln(5);
$pdf->CreateSection('INFORMACOES DO LEASING', 0);
$pdf->Ln(2);

// Grid para informações do leasing
$pdf->CreateField('DATA', date('d/m/Y', strtotime($contract['date'])));
$pdf->CreateField('VALOR TOTAL', formatYen($contract['total_value']));
$pdf->CreateField('VALOR DA ENTRADA', formatYen(0)); // Exemplo, ajuste conforme necessário
$pdf->CreateField('VALOR DO KAITORI', formatYen($contract['kaitori_value']));
$pdf->CreateField('VALOR FINANCIADO', formatYen($contract['total_value'])); // Exemplo, ajuste conforme necessário

// Seção Relação de Pagamentos
$pdf->Ln(10);
$pdf->CreateSection('RELACAO DE PAGAMENTOS', 0);
$pdf->Ln(5);

// Cabeçalho da tabela
$headers = array('N° DA PARCELA', 'VENCIMENTO', 'VALOR', '');

// Criar amostra de pagamentos (25 parcelas)
$payments = array();
$baseDate = new DateTime($contract['date']);
$monthlyValue = formatYen(20000); // Exemplo, ajuste conforme necessário

for ($i = 1; $i <= 25; $i++) {
    $date = clone $baseDate;
    $date->modify("+$i month");
    $formattedDate = $date->format('d/m/Y');
    
    $payments[] = array($i . "/25", $formattedDate, $monthlyValue, '');
}

// Criar tabela de pagamentos
$pdf->CreateTable($headers, $payments);

// Seção Dados para Depósito
$pdf->Ln(10);
$pdf->CreateSection('DADOS PARA DEPOSITO', 0);
$pdf->Ln(5);

// Informações bancárias
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '静岡銀行 (Shizuoka Ginko) 有田支店 (Aritama) 普通口座 (Futsu) 0408725', 0, 1, 'C');
$pdf->Cell(0, 6, 'RARAMOS PEREIRA ANDERSON (ラモス ペレイラ アンデルソン)', 0, 1, 'C');

// Campo de assinaturas
$pdf->Ln(20);
$pdf->CreateSignature('VENDEDOR', 'COMPRADOR');

// Campo de data
$pdf->CreateDateField();

// Gerar o PDF
if ($action == 'download') {
    // Gerar nome do arquivo
    $filename = 'contrato_' . $contract_id . '_' . date('Ymd') . '.pdf';
    
    // Atualizar o nome do arquivo no banco de dados se necessário
    if ($contract['filename'] != $filename) {
        $conn = db_connect();
        $stmt = $conn->prepare("UPDATE contracts SET filename = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $contract_id);
        $stmt->execute();
        $conn->close();
    }
    
    // Forçar download
    $pdf->Output('D', $filename);
} else {
    // Visualizar no navegador
    $pdf->Output();
}

// Função auxiliar para extrair a marca do carro pelo nome
function getCarBrand($carName) {
    $brands = array(
        'TOYOTA', 'HONDA', 'NISSAN', 'MAZDA', 'SUBARU', 'MITSUBISHI', 'SUZUKI', 'DAIHATSU',
        'LEXUS', 'INFINITY', 'ACURA', 'BMW', 'MERCEDES', 'AUDI', 'VOLKSWAGEN', 'FORD', 
        'CHEVROLET', 'HYUNDAI', 'KIA', 'FIAT', 'PEUGEOT', 'RENAULT', 'VOLVO', 'JAGUAR',
        'LAND ROVER', 'PORSCHE', 'FERRARI', 'LAMBORGHINI', 'MASERATI', 'BENTLEY', 'ROLLS ROYCE'
    );
    
    $carName = strtoupper($carName);
    foreach ($brands as $brand) {
        if (strpos($carName, $brand) !== false) {
            return $brand;
        }
    }
    
    // Se não encontrar a marca, retornar as primeiras palavras do nome
    $words = explode(' ', $carName);
    return $words[0] . (isset($words[1]) ? ' ' . $words[1] : '');
}