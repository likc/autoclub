<?php
// Carregar FAQs do banco de dados
$conn = new mysqli('localhost', 'minec761_likc', 'rw23xrd807ox', 'minec761_autoclub');
$conn->set_charset("utf8mb4");

$faqs_result = $conn->query("SELECT * FROM shaken_faqs ORDER BY display_order ASC");
$faqs = [];

if ($faqs_result && $faqs_result->num_rows > 0) {
    $faqs = $faqs_result->fetch_all(MYSQLI_ASSOC);
} else {
    // FAQs padrão caso não haja nenhuma no banco
    $faqs = [
        [
            'question' => 'Quanto tempo leva para fazer o Shaken?',
            'answer' => 'Em média, o processo completo leva de 2 a 3 dias úteis, dependendo das condições do veículo e dos reparos necessários. Em alguns casos, quando são necessários reparos mais complexos, o prazo pode ser maior.'
        ],
        [
            'question' => 'Posso usar um carro com Shaken vencido?',
            'answer' => 'Não. Dirigir com Shaken vencido é ilegal no Japão e pode resultar em multas severas (até ¥300.000), perda de pontos na carteira e problemas com o seguro. O veículo só pode circular para ir diretamente ao local de inspeção.'
        ],
        [
            'question' => 'O que está incluído no preço do Shaken?',
            'answer' => 'Nosso serviço inclui a pré-inspeção, documentação, taxas governamentais, seguro compulsório (Jibaiseki) e a inspeção oficial. Quaisquer reparos necessários não estão incluídos no preço base, mas podemos recomendar oficinas parceiras para realizar os serviços.'
        ],
        [
            'question' => 'Posso transferir o Shaken para outro veículo?',
            'answer' => 'Não, o certificado de Shaken é vinculado exclusivamente ao veículo inspecionado e não pode ser transferido. Ao vender um veículo, o Shaken permanece válido para o novo proprietário pelo tempo restante.'
        ],
        [
            'question' => 'Preciso deixar meu carro na oficina durante todo o processo?',
            'answer' => 'Sim, geralmente o veículo fica conosco durante o processo de inspeção e documentação. No entanto, entendemos que você pode precisar do carro, então em alguns casos podemos fazer arranjos especiais. Converse conosco sobre suas necessidades.'
        ],
        [
            'question' => 'Carros modificados podem passar no Shaken?',
            'answer' => 'Veículos com modificações precisam atender a regulamentos específicos. Algumas modificações são permitidas se forem homologadas e registradas corretamente. Outras podem causar reprovação. Recomendamos consulta prévia para avaliarmos seu caso específico.'
        ]
    ];
}

// Dividir FAQs em duas colunas
$half = ceil(count($faqs) / 2);
$faqs_column1 = array_slice($faqs, 0, $half);
$faqs_column2 = array_slice($faqs, $half);

$conn->close();
?>

<section class="section-padding">
    <div class="container">
        <div class="section-title">
            <span>Perguntas Frequentes</span>
            <h2>Dúvidas sobre Shaken</h2>
            <p>Respostas para as perguntas mais comuns</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <?php foreach ($faqs_column1 as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-title">
                            <h5><?php echo htmlspecialchars($faq['question']); ?></h5>
                        </div>
                        <div class="faq-content">
                            <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-lg-6">
                <?php foreach ($faqs_column2 as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-title">
                            <h5><?php echo htmlspecialchars($faq['question']); ?></h5>
                        </div>
                        <div class="faq-content">
                            <p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>