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

// Caminho para a biblioteca FPDF
$fpdf_path = 'includes/fpdf/fpdf.php';
if (!file_exists($fpdf_path)) {
    set_alert('danger', 'Biblioteca FPDF não encontrada! Por favor, instale a biblioteca FPDF em includes/fpdf/.');
    redirect('contracts.php');
}

require_once $fpdf_path;

// Verifica se existe a extensão para japonês do FPDF
$japanese_font = false;
if (file_exists('includes/fpdf/makefont/makefont.php') && file_exists('includes/fpdf/font/sjis.php')) {
    $japanese_font = true;
    require_once('includes/fpdf/font/sjis.php');
}

class ContractPDF extends FPDF {
    // Margem ajustada para cláusulas
    protected $clause_left_margin = 30;
    protected $using_japanese = false;
    
    // Construtor personalizado para usar fonte japonesa se disponível
    function __construct($using_japanese = false) {
        parent::__construct();
        $this->using_japanese = $using_japanese;
        
        if ($this->using_japanese) {
            $this->AddSJISFont();
        }
    }
    
    // Adiciona suporte para fonte SJIS - só será executado se as extensões estiverem disponíveis
    function AddSJISFont() {
        if (method_exists($this, 'AddSJISFont')) {
            // Esta função existe na extensão SJIS do FPDF
            parent::AddSJISFont();
        }
    }
    
    function Header() {
        // Logo (verificar se existe)
        if (file_exists('img/logo.png')) {
            $this->Image('img/logo.png', 10, 10, 30);
        }
        
        // Título do contrato
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, $this->fixText('CONTRATO DE LEASING'), 0, 1, 'C');
        $this->Ln(5);
    }
    
    function Footer() {
        // Posicionar a 1.5 cm do fundo
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, $this->fixText('Página ') . $this->PageNo() . '/' . $this->AliasNbPages(), 0, 0, 'C');
    }
    
    // Versão melhorada da normalização de texto para suportar japonês
    function fixText($text) {
        // Se usando fonte japonesa, converter para SJIS
        if ($this->using_japanese) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($text, 'SJIS', 'UTF-8');
            }
        }
        
        // Caso contrário, usar conversão padrão para ISO-8859-1
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        } else if (function_exists('iconv')) {
            return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        } else {
            return utf8_decode($text);
        }
    }
    
    // Função para verificar se há espaço suficiente
    function NeedNewPage($height) {
        return ($this->GetY() + $height > $this->PageBreakTrigger);
    }
    
    // Função para criar a seção de informações
    function CreateSection($title, $border = 0) {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, $this->fixText($title), $border, 1, 'L');
        $this->SetFont('Arial', '', 10);
    }
    
    // Função para criar uma célula com campo e valor
    function CreateField($field, $value, $width1 = 60, $width2 = 0) {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($width1, 6, $this->fixText($field), 0, 0);
        $this->SetFont('Arial', '', 9);
        
        if ($width2 > 0) {
            $this->Cell($width2, 6, $this->fixText($value), 0, 1);
        } else {
            $this->Cell(0, 6, $this->fixText($value), 0, 1);
        }
    }
    
    // Função para criar uma tabela tradicional (mantida para compatibilidade)
    function CreateTable($headers, $data) {
        // Larguras de coluna
        $w = array(40, 60, 40, 50);
        
        // Cabeçalho
        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 9);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($w[$i], 7, $this->fixText($headers[$i]), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Dados
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(240, 240, 240);
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $this->fixText($row[0]), 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $this->fixText($row[1]), 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $this->fixText($row[2]), 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $this->fixText($row[3]), 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Linha de fechamento
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln(5);
    }
    
    // Nova versão da tabela de pagamentos - formato de grade com 5 colunas
    function CreatePaymentGrid($contract) {
        $this->AddPage(); // Começar uma nova página dedicada para pagamentos
        
        // Título da página de pagamentos
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, $this->fixText('CONTRATO DE LEASING'), 0, 1, 'C');
        $this->Ln(5);
        
        // Cabeçalho da relação de pagamentos
        $this->CreateSection('RELAÇÃO DE PAGAMENTOS', 0);
        $this->Ln(5);
        
        $baseDate = new DateTime($contract['date']);
        $monthlyValue = formatYen(20000); // Exemplo, ajuste conforme necessário
        
        // Configuração da tabela
        // 5 colunas divididas em 5 conjuntos completos (parcela, vencimento, valor)
        $colCount = 5;
        $parcelasTotal = 25;
        $parcelasPorColuna = 5; // 5 linhas de parcelas
        
        // Larguras das colunas
        $colWidth = array(8, 25, 18); // Nº, Vencimento, Valor
        $spacing = 2; // Espaçamento entre conjuntos de colunas
        
        // Espaço total disponível na página
        $pageWidth = $this->GetPageWidth() - 20; // 10mm de margem de cada lado
        
        // Ajustar as larguras para caber na página
        $totalWidth = ($colWidth[0] + $colWidth[1] + $colWidth[2]) * $colCount + ($spacing * ($colCount - 1));
        $ratio = $pageWidth / $totalWidth;
        
        if ($ratio < 1) {
            // Se não couber, redimensionar proporcionalmente
            for ($i = 0; $i < count($colWidth); $i++) {
                $colWidth[$i] = $colWidth[$i] * $ratio;
            }
        }
        
        // Cabeçalho da tabela
        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 8);
        
        // Desenhar o cabeçalho para cada coluna
        $currentX = 10; // Começar na margem esquerda
        
        for ($col = 0; $col < $colCount; $col++) {
            $this->SetX($currentX);
            $this->Cell($colWidth[0], 6, $this->fixText('Nº'), 1, 0, 'C', true);
            $this->Cell($colWidth[1], 6, $this->fixText('VENCIMENTO'), 1, 0, 'C', true);
            $this->Cell($colWidth[2], 6, $this->fixText('VALOR'), 1, 0, 'C', true);
            $currentX += $colWidth[0] + $colWidth[1] + $colWidth[2] + $spacing;
        }
        $this->Ln();
        
        // Dados da tabela
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(240, 240, 240);
        $fill = false;
        
        // Desenhar as linhas de parcelas
        for ($linha = 0; $linha < $parcelasPorColuna; $linha++) {
            $currentX = 10;
            
            for ($col = 0; $col < $colCount; $col++) {
                // Calcular o número da parcela
                $parcela = $linha + ($col * $parcelasPorColuna) + 1;
                
                if ($parcela <= $parcelasTotal) {
                    // Calcular a data de vencimento
                    $date = clone $baseDate;
                    $date->modify("+$parcela month");
                    $formattedDate = $date->format('d/m/Y');
                    
                    // Desenhar a célula
                    $this->SetX($currentX);
                    $this->Cell($colWidth[0], 6, $parcela . '/25', 1, 0, 'C', $fill);
                    $this->Cell($colWidth[1], 6, $formattedDate, 1, 0, 'C', $fill);
                    $this->Cell($colWidth[2], 6, $monthlyValue, 1, 0, 'C', $fill);
                }
                
                $currentX += $colWidth[0] + $colWidth[1] + $colWidth[2] + $spacing;
            }
            
            $this->Ln();
            $fill = !$fill;
        }
        
        $this->Ln(10);
        
        // Seção Dados para Depósito
        $this->CreateBankData();
        
        // Campo de assinaturas
        $this->Ln(10);
        $this->CreateSignature('VENDEDOR', 'COMPRADOR');
        
        // Campo de data
        $this->CreateDateField();
    }
    
    // Versão melhorada para criar todas as cláusulas em uma página
    function CreateAllClauses($clausulas) {
        // Adicionar uma nova página dedicada para as cláusulas
        $this->AddPage();
        
        $this->CreateSection('CLÁUSULAS CONTRATUAIS', 0);
        $this->Ln(5);
        
        // Layout melhorado - alinhamento tabular para cláusulas
        $leftMargin = $this->GetX();
        
        foreach ($clausulas as $index => $clausula) {
            $clauseNum = $index + 1;
            
            // Início da linha para alinhamento tabular
            $this->SetX($leftMargin);
            
            // Número da cláusula em negrito
            $this->SetFont('Arial', 'B', 9);
            $clauseHeader = "CLÁUSULA $clauseNum:";
            $this->Cell($this->clause_left_margin, 5, $this->fixText($clauseHeader), 0, 0);
            
            // Para cláusulas longas, usamos um sistema tabular
            $this->SetFont('Arial', '', 9);
            
            // Calcular espaço disponível para o texto
            $availableWidth = $this->GetPageWidth() - $this->clause_left_margin - $this->rMargin - $leftMargin;
            
            // Salvar posição X após imprimir o título da cláusula
            $currentX = $this->GetX();
            $currentY = $this->GetY();
            
            // Mover para posição de iniciar o MultiCell
            $this->SetXY($currentX, $currentY);
            
            // Imprimir o texto da cláusula com MultiCell
            $this->MultiCell($availableWidth, 5, $this->fixText($clausula), 0, 'J');
            
            // Adicionar espaço entre cláusulas
            $this->Ln(2);
        }
        
        $this->Ln(2);
    }
    
    // Função para artigo especial com layout tabular
    function CreateSpecialArticle($number, $text) {
        $leftMargin = $this->GetX();
        
        // Início da linha para alinhamento tabular
        $this->SetX($leftMargin);
        
        // Número do artigo em negrito
        $this->SetFont('Arial', 'B', 9);
        $articleHeader = "ARTIGO $number:";
        $this->Cell($this->clause_left_margin, 5, $this->fixText($articleHeader), 0, 0);
        
        // Calcular espaço disponível para o texto
        $availableWidth = $this->GetPageWidth() - $this->clause_left_margin - $this->rMargin - $leftMargin;
        
        // Salvar posição atual
        $currentX = $this->GetX();
        $currentY = $this->GetY();
        
        // Mover para posição de iniciar o MultiCell
        $this->SetXY($currentX, $currentY);
        
        // Imprimir o texto do artigo
        $this->SetFont('Arial', '', 9);
        $this->MultiCell($availableWidth, 5, $this->fixText($text), 0, 'J');
        
        $this->Ln(3);
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
        $this->Cell(90, 6, $this->fixText($left_title), 0, 0, 'C');
        $this->Cell(10, 6, '', 0, 0);
        $this->Cell(90, 6, $this->fixText($right_title), 0, 1, 'C');
        
        $this->Ln(5);
    }
    
    // Função para criar campo de data
    function CreateDateField($city = 'HAMAMATSU') {
        $this->Ln(5);
        $this->Cell(25, 6, $this->fixText($city . ','), 0, 0);
        $this->Cell(20, 6, $this->fixText('_______ DE'), 0, 0);
        $this->Cell(45, 6, $this->fixText('____________________DE'), 0, 0);
        $this->Cell(20, 6, $this->fixText('___________'), 0, 1);
    }
    
    // Esta função manipula diretamente as coordenadas para os dados bancários
    function CreateBankData() {
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, $this->fixText('DADOS PARA DEPÓSITO'), 0, 1, 'L');
        $this->Ln(5);
        
        $this->SetFont('Arial', '', 10);
        
        // Imagem com os dados do banco
        if (file_exists('img/banco_dados.png')) {
            // Se existir uma imagem com os dados, usa ela em vez do texto
            $this->Image('img/banco_dados.png', null, null, 180);
        } else {
            // Modo alternativo - usa imagem de texto
            $bank_text = '静岡銀行 (Shizuoka Ginko) - 有玉支店 （Aritama） - 普通口座 (Futsu) 0408725';
            $name_text = 'RAMOS PEREIRA ANDERSON';
            
            $this->Cell(0, 6, $this->fixText($bank_text), 0, 1, 'C');
            $this->Cell(0, 6, $this->fixText($name_text), 0, 1, 'C');
        }
    }
}

// Formatar números em ienes
function formatYen($value) {
    return '¥' . number_format($value, 0, ',', '.');
}

// Criar imagem com texto em japonês
function createBankInfoImage() {
    // Verificar se a imagem já existe
    if (file_exists('img/banco_dados.png')) {
        return true;
    }
    
    // Verificar se temos a extensão GD
    if (!function_exists('imagecreate')) {
        return false;
    }
    
    // Criar uma imagem
    $width = 800;
    $height = 80;
    $image = imagecreate($width, $height);
    
    // Cores
    $bg = imagecolorallocate($image, 255, 255, 255);
    $textcolor = imagecolorallocate($image, 0, 0, 0);
    
    // Texto - incluindo caracteres japoneses
    $line1 = '静岡銀行 (Shizuoka Ginko) - 有玉支店 （Aritama） - 普通口座 (Futsu) 0408725';
    $line2 = 'RARAMOS PEREIRA ANDERSON';
    
    // Adicionar texto
    $font = 5; // Fonte built-in do GD
    $y_pos1 = 30;
    $y_pos2 = 50;
    
    // Centralizar texto
    $x_pos1 = ($width - imagefontwidth($font) * strlen($line1)) / 2;
    $x_pos2 = ($width - imagefontwidth($font) * strlen($line2)) / 2;
    
    imagestring($image, $font, $x_pos1, $y_pos1, $line1, $textcolor);
    imagestring($image, $font, $x_pos2, $y_pos2, $line2, $textcolor);
    
    // Salvar imagem
    if (!is_dir('img')) {
        mkdir('img', 0755, true);
    }
    
    imagepng($image, 'img/banco_dados.png');
    imagedestroy($image);
    
    return true;
}

// Tentar criar a imagem com as informações bancárias
createBankInfoImage();

// Criar o documento PDF
$pdf = new ContractPDF($japanese_font);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 25);

// Seção de Informações do Cliente
$pdf->CreateSection('INFORMAÇÕES DO CLIENTE', 0);
$pdf->Ln(2);

// Grid de 2 colunas para informações do cliente
$pdf->CreateField('NOME COMPLETO', mb_strtoupper($contract['client_name']));
$pdf->CreateField('ENDEREÇO', mb_strtoupper($contract['client_address']));
$pdf->CreateField('TELEFONE 1', $contract['client_phone1']);
$pdf->CreateField('TELEFONE 2', $contract['client_phone2']);

// Seção de Informações do Financiamento
$pdf->Ln(5);
$pdf->CreateSection('INFORMAÇÕES DO FINANCIAMENTO', 0);
$pdf->Ln(2);

// Grid para informações do financiamento
$pdf->CreateField('ID DO CLIENTE', $contract['client_id']);
$pdf->CreateField('VENDEDOR', mb_strtoupper($contract['seller']));
$pdf->CreateField('DATA', date('d/m/Y', strtotime($contract['date'])));
$pdf->CreateField('FORMA DE PAGAMENTO', mb_strtoupper($contract['payment_method']));

// Seção de Informações do Veículo
$pdf->Ln(5);
$pdf->CreateSection('INFORMAÇÕES DO VEÍCULO', 0);
$pdf->Ln(2);

// Grid para informações do veículo
$pdf->CreateField('NOME', mb_strtoupper($contract['vehicle_name']));
$pdf->CreateField('PLACA', mb_strtoupper($contract['vehicle_plate']));
$pdf->CreateField('ANO DE FABRICAÇÃO', $contract['vehicle_year']);
$pdf->CreateField('CHASSI', mb_strtoupper($contract['vehicle_chassis']));
$pdf->CreateField('KATASHIKI', mb_strtoupper($contract['vehicle_katashiki']));
$pdf->CreateField('COR', mb_strtoupper($contract['vehicle_color']));
$pdf->CreateField('QUILOMETRAGEM', number_format($contract['vehicle_mileage'], 0, ',', '.'));

// Seção de Valores do Leasing
$pdf->Ln(5);
$pdf->CreateSection('VALORES DO LEASING', 0);
$pdf->Ln(2);

// Grid para valores do leasing
$pdf->CreateField('VALOR DO VEÍCULO', formatYen($contract['vehicle_value']));
$pdf->CreateField('IMPOSTO DE CONSUMO (10%)', formatYen($contract['consumption_tax']));
$pdf->CreateField('GPS', formatYen($contract['gps_value']));
$pdf->CreateField('SHAKEN', formatYen($contract['shaken_value']));
$pdf->CreateField('IMPOSTO ANUAL (ZEIKIN)', formatYen($contract['annual_tax']));
$pdf->CreateField('TRANSFERÊNCIA E ENTREGA', formatYen($contract['transfer_delivery']));
$pdf->CreateField('VALOR DO KAITORI', formatYen($contract['kaitori_value']));
$pdf->CreateField('TOTAL', formatYen($contract['total_value']));

// Cláusulas contratuais
$clausulas = [
    "Fica na responsabilidade do ARRENDATÁRIO(a) providenciar toda a documentação necessária para a transferência do veículo, no prazo máximo de 20 dias, o não cumprimento deste prazo será cobrado uma multa de ¥10.000.",
    
    "Os pagamentos via depósitos bancários devem ser realizados com o ID (número de identificação do arrendatário(a)), caso haja dificuldade entre em contato com a arrendadora pelo número 080 9281 5155.",
    
    "O arrendatário pagará a quantia mensal discriminado no verso do contrato, caso não seja efetuada até a data que está estabelecida no contrato, a arrendadora cobrará ¥3.500 de multa pelo atraso e se a falta de pagamento ultrapassar 5 dias do contrato, a arrendadora poderá BLOQUEAR a partida do motor por controle remoto do terminal de GPS e reter o veículo, e a liberação somente será possível se o arrendatário(a) efetuar o pagamento da quantia mensal em atraso.",
    
    "A arrendadora dará garantia SOMENTE de motor e câmbio durante 2 meses ou 2 mil quilômetros (o que vencer primeiro), a partir da data que foi entregue o carro e se caso houver algum problema fica na responsabilidade do cliente trazê-lo até a arrendadora para realizar a checagem e tomar as devidas providências. NÃO DAREMOS GARANTIA A VEÍCULOS ESPORTIVOS, IMPORTADOS E VEÍCULOS COM MAIS DE DEZ ANOS DE USO. No caso de atraso nos pagamentos mensais, não será possível recorrer aos serviços de garantia (consertos, manutenções, etc) ficando a cargo do arrendatário(a), todas as responsabilidades de possíveis despesas.",
    
    "Em caso de rescisão do contrato por parte do arrendatário(a), este deverá pagar o valor de ¥100.000 (cem mil ienes) no ato da rescisão e ou devolução do carro, se houver quantias mensais atrasadas até o momento também deverão ser pagas.",
    
    "A troca do bem será aceita somente após a quitação do contrato.",
    
    "A arrendadora não se responsabiliza pelos danos causados por desgastes naturais devido ao tempo de uso, por estragos do bem por causa da negligência do arrendatário(a), ou devido às modificações das características originais do bem.",
    
    "Ao término de pagamento do contrato, o arrendatário passa a ser proprietário do bem adquirido.",
    
    "O arrendatário está ciente da instalação de um GPS de rastreio e bloqueio no veículo por uma empresa terceirizada contratada pela arrendadora.",
    
    "ANTES DA ASSINATURA DESTE CONTRATO, CABE AO ARRENDATÁRIO(A) TESTAR E AVALIAR AS CONDIÇÕES DO BEM ARRENDADO."
];

// Adicionar todas as cláusulas em uma única página
$pdf->CreateAllClauses($clausulas);

// Artigo 1
$artigo1 = "O arrendatário(a) não terá o reembolso do que foi pago (como entrada de veículo, dinheiro em espécie, quantias pagas em dinheiro, etc.) e se compromete a devolver o veículo nas mesmas condições de conservação no ato da aquisição, bem como os acessórios instalados por solicitação do arrendatário(a) e todos os gastos com as despesas (caso seja necessário a arrendatária ter que buscar o veículo).";
$pdf->CreateSpecialArticle(1, $artigo1);

// Campo de assinaturas
$pdf->CreateSignature('VENDEDOR', 'COMPRADOR');

// Campo de data
$pdf->CreateDateField();

// Usar o método de grade de pagamentos em uma página separada
// com o mesmo layout visto na imagem de exemplo
$pdf->CreatePaymentGrid($contract);

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