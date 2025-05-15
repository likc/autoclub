<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// Criar tabela se não existir
$create_table = "CREATE TABLE IF NOT EXISTS shaken_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($create_table);

// FAQs padrão
$default_faqs = [
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

// Verificar se existem FAQs, se não, inserir as padrão
$check_faqs = $conn->query("SELECT COUNT(*) as count FROM shaken_faqs");
$faq_count = $check_faqs->fetch_assoc()['count'];

if ($faq_count == 0) {
    $stmt = $conn->prepare("INSERT INTO shaken_faqs (question, answer, display_order) VALUES (?, ?, ?)");
    foreach ($default_faqs as $index => $faq) {
        $order = $index + 1;
        $stmt->bind_param("ssi", $faq['question'], $faq['answer'], $order);
        $stmt->execute();
    }
}

// Processar upload de imagem hero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hero') {
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] != UPLOAD_ERR_NO_FILE) {
        $upload_result = upload_image($_FILES['hero_image'], "../img/");
        
        if ($upload_result['success']) {
            // Remover imagem antiga
            $old_image = "../img/hero-bg.jpg";
            if (file_exists($old_image)) {
                unlink($old_image);
            }
            
            // Renomear nova imagem para hero-bg.jpg
            $new_path = "../img/hero-bg.jpg";
            rename("../img/" . $upload_result['filename'], $new_path);
            
            set_alert('success', 'Imagem hero atualizada com sucesso!');
        } else {
            set_alert('danger', 'Erro ao fazer upload da imagem: ' . $upload_result['message']);
        }
    }
    redirect('site_settings.php');
}

// Processar FAQs do Shaken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_faqs') {
    if (isset($_POST['faq_id']) && is_array($_POST['faq_id'])) {
        $stmt = $conn->prepare("UPDATE shaken_faqs SET question = ?, answer = ? WHERE id = ?");
        
        foreach ($_POST['faq_id'] as $index => $faq_id) {
            $question = $_POST['faq_question'][$index];
            $answer = $_POST['faq_answer'][$index];
            $stmt->bind_param("ssi", $question, $answer, $faq_id);
            $stmt->execute();
        }
        
        set_alert('success', 'FAQs do Shaken atualizadas com sucesso!');
    }
    redirect('site_settings.php');
}

// Obter FAQs existentes
$faqs_result = $conn->query("SELECT * FROM shaken_faqs ORDER BY display_order ASC");
$faqs = $faqs_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Configurações do Site</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Configurações do Site</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Imagem Hero -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Imagem Principal (Hero)</h3>
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_hero">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Imagem Atual</label>
                                <div class="mb-3">
                                    <img src="../img/hero-bg.jpg?v=<?php echo time(); ?>" alt="Hero atual" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="hero_image">Nova Imagem</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="hero_image" name="hero_image" accept="image/*" required>
                                        <label class="custom-file-label" for="hero_image">Escolher arquivo</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Recomendado: 1920x800px ou maior. Formatos: JPG, PNG.</small>
                            </div>
                            
                            <div class="image-preview mt-3" id="heroPreview" style="display: none;">
                                <label>Preview:</label><br>
                                <img src="" id="heroPreviewImg" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Atualizar Imagem</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- FAQs do Shaken -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">FAQs do Shaken</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_faqs">
                        <div class="card-body">
                            <?php foreach ($faqs as $faq): ?>
                                <div class="faq-item mb-4 p-3 border rounded">
                                    <input type="hidden" name="faq_id[]" value="<?php echo $faq['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label>Pergunta</label>
                                        <input type="text" class="form-control" name="faq_question[]" value="<?php echo htmlspecialchars($faq['question']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Resposta</label>
                                        <textarea class="form-control" name="faq_answer[]" rows="4" required><?php echo htmlspecialchars($faq['answer']); ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview da imagem hero
    const heroInput = document.getElementById('hero_image');
    const heroPreview = document.getElementById('heroPreview');
    const heroPreviewImg = document.getElementById('heroPreviewImg');
    
    heroInput.addEventListener('change', function() {
        const file = this.files[0];
        const fileLabel = document.querySelector('.custom-file-label');
        
        if (file) {
            fileLabel.textContent = file.name;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                heroPreviewImg.src = e.target.result;
                heroPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            fileLabel.textContent = 'Escolher arquivo';
            heroPreview.style.display = 'none';
        }
    });
});
</script>

<style>
.faq-item {
    background-color: #f8f9fa;
}
.faq-item:hover {
    background-color: #f0f0f0;
}
</style>

<?php include 'includes/footer.php'; ?>