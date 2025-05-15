<?php
session_start();
require_once 'config.php';
check_login();

$conn = db_connect();

// ===============================
// FUNÇÕES DE CONFIGURAÇÃO DO SITE
// ===============================

// Criar tabela de configurações se não existir
$create_config_table = "CREATE TABLE IF NOT EXISTS site_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NOT NULL,
    config_type ENUM('text', 'longtext', 'email', 'phone', 'url') DEFAULT 'text',
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($create_config_table);

// Função para obter uma configuração
function get_site_config($key, $default = '') {
    global $conn;
    $stmt = $conn->prepare("SELECT config_value FROM site_config WHERE config_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['config_value'];
    }
    
    return $default;
}

// Função para salvar uma configuração
function save_site_config($key, $value, $type = 'text', $description = '') {
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO site_config (config_key, config_value, config_type, description) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            config_value = VALUES(config_value),
            config_type = VALUES(config_type),
            description = VALUES(description)
    ");
    $stmt->bind_param("ssss", $key, $value, $type, $description);
    return $stmt->execute();
}

// ===============================
// PROCESSAR FORMULÁRIOS
// ===============================

// Processar formulário de SEO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_seo') {
    $configs = [
        'site_title' => $_POST['site_title'],
        'site_description' => $_POST['site_description'],
        'site_keywords' => $_POST['site_keywords']
    ];
    
    $success = true;
    foreach ($configs as $key => $value) {
        $type = ($key === 'site_description') ? 'longtext' : 'text';
        if (!save_site_config($key, $value, $type)) {
            $success = false;
        }
    }
    
    if ($success) {
        set_alert('success', 'Configurações de SEO atualizadas com sucesso!');
    } else {
        set_alert('danger', 'Erro ao atualizar SEO.');
    }
    redirect('site_settings.php');
}

// Processar formulário de configurações gerais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_general') {
    $configs = [
        'site_name' => $_POST['site_name'],
        'site_email' => $_POST['site_email'],
        'site_phone' => $_POST['site_phone'],
        'facebook_url' => $_POST['facebook_url'],
        'instagram_url' => $_POST['instagram_url']
    ];
    
    $success = true;
    foreach ($configs as $key => $value) {
        $type = ($key === 'site_email') ? 'email' : (($key === 'site_phone') ? 'phone' : 'text');
        if (!save_site_config($key, $value, $type)) {
            $success = false;
        }
    }
    
    if ($success) {
        set_alert('success', 'Configurações gerais atualizadas com sucesso!');
    } else {
        set_alert('danger', 'Erro ao atualizar algumas configurações.');
    }
    redirect('site_settings.php');
}

// Processar formulário de textos da página inicial
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_homepage') {
    $configs = [
        'hero_title' => $_POST['hero_title'],
        'hero_subtitle' => $_POST['hero_subtitle'],
        'hero_button_1_text' => $_POST['hero_button_1_text'],
        'hero_button_2_text' => $_POST['hero_button_2_text'],
        'services_title' => $_POST['services_title'],
        'services_subtitle' => $_POST['services_subtitle'],
        'cars_section_title' => $_POST['cars_section_title'],
        'cars_section_subtitle' => $_POST['cars_section_subtitle'],
        'cars_not_found_title' => $_POST['cars_not_found_title'],
        'cars_not_found_text' => $_POST['cars_not_found_text'],
        'cta_title' => $_POST['cta_title'],
        'cta_subtitle' => $_POST['cta_subtitle']
    ];
    
    $success = true;
    foreach ($configs as $key => $value) {
        $type = (strpos($key, 'subtitle') !== false || strpos($key, 'text') !== false) ? 'longtext' : 'text';
        if (!save_site_config($key, $value, $type)) {
            $success = false;
        }
    }
    
    if ($success) {
        set_alert('success', 'Textos da página inicial atualizados com sucesso!');
    } else {
        set_alert('danger', 'Erro ao atualizar alguns textos.');
    }
    redirect('site_settings.php');
}

// Processar formulário de mensagens do WhatsApp
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_whatsapp') {
    $configs = [
        'whatsapp_budget_message' => $_POST['whatsapp_budget_message'],
        'whatsapp_visit_message' => $_POST['whatsapp_visit_message']
    ];
    
    $success = true;
    foreach ($configs as $key => $value) {
        if (!save_site_config($key, $value, 'text')) {
            $success = false;
        }
    }
    
    if ($success) {
        set_alert('success', 'Mensagens do WhatsApp atualizadas com sucesso!');
    } else {
        set_alert('danger', 'Erro ao atualizar mensagens.');
    }
    redirect('site_settings.php');
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

// FAQs do Shaken
$create_faqs_table = "CREATE TABLE IF NOT EXISTS shaken_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($create_faqs_table);

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

// Obter valores atuais das configurações
$site_title = get_site_config('site_title', 'AutoClub - Seu sonho automotivo realizado no Japão');
$site_description = get_site_config('site_description', 'AutoClub - Compre seu carro no Japão com financiamento facilitado. Acesso a leilões exclusivos e aprovação rápida, mesmo sem visto permanente.');
$site_keywords = get_site_config('site_keywords', 'carros Japão, financiamento carros Japão, brasileiros Japão, carros importados, AutoClub, shaken');

$site_name = get_site_config('site_name', 'AutoClub');
$site_email = get_site_config('site_email', 'contato@autoclub.jp');
$site_phone = get_site_config('site_phone', '+818092815155');
$facebook_url = get_site_config('facebook_url', 'https://www.facebook.com/anderson.premium.motors.japao/');
$instagram_url = get_site_config('instagram_url', 'https://www.instagram.com/AutoClub_anderson/');

$hero_title = get_site_config('hero_title', 'Realize seu sonho automotivo no Japão');
$hero_subtitle = get_site_config('hero_subtitle', 'Financiamento facilitado com ou sem visto permanente, acesso exclusivo aos melhores leilões do Japão.');
$hero_button_1_text = get_site_config('hero_button_1_text', 'Solicitar Orçamento');
$hero_button_2_text = get_site_config('hero_button_2_text', 'Saiba Mais');

$services_title = get_site_config('services_title', 'AutoClub oferece:');
$services_subtitle = get_site_config('services_subtitle', 'Soluções completas para brasileiros que desejam adquirir veículos no Japão');

$cars_section_title = get_site_config('cars_section_title', 'Veículos Disponíveis');
$cars_section_subtitle = get_site_config('cars_section_subtitle', 'Uma seleção premium de veículos para todos os gostos e necessidades');
$cars_not_found_title = get_site_config('cars_not_found_title', 'Não achou o carro dos seus sonhos?');
$cars_not_found_text = get_site_config('cars_not_found_text', 'Entre em contato e conte para nós o que você está procurando.');

$cta_title = get_site_config('cta_title', 'Pronto para realizar seu sonho automotivo?');
$cta_subtitle = get_site_config('cta_subtitle', 'Entre em contato agora mesmo e descubra as melhores opções para você.');

$whatsapp_budget_message = get_site_config('whatsapp_budget_message', 'Olá, vim pelo site e gostaria de fazer um orçamento');
$whatsapp_visit_message = get_site_config('whatsapp_visit_message', 'Olá, vim pelo site e gostaria de agendar uma visita');

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
        <!-- SEO e Configurações Gerais -->
        <div class="row">
            <div class="col-md-6">
                <!-- SEO e Meta Tags -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">SEO e Meta Tags</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_seo">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="site_title">Título do Site (SEO)</label>
                                <input type="text" class="form-control" id="site_title" name="site_title" value="<?php echo htmlspecialchars($site_title); ?>" required>
                                <small class="form-text text-muted">Aparece na aba do navegador</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description">Descrição do Site (SEO)</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3" required><?php echo htmlspecialchars($site_description); ?></textarea>
                                <small class="form-text text-muted">Aparece nos resultados de busca do Google</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_keywords">Palavras-chave (SEO)</label>
                                <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="<?php echo htmlspecialchars($site_keywords); ?>" required>
                                <small class="form-text text-muted">Separe por vírgulas. Ex: carros japão, financiamento, shaken</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar SEO</button>
                        </div>
                    </form>
                </div>
            
                <!-- Informações Gerais -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informações Gerais</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_general">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="site_name">Nome do Site</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_email">Email de Contato</label>
                                <input type="email" class="form-control" id="site_email" name="site_email" value="<?php echo htmlspecialchars($site_email); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_phone">WhatsApp/Telefone</label>
                                <input type="text" class="form-control" id="site_phone" name="site_phone" value="<?php echo htmlspecialchars($site_phone); ?>" required>
                                <small class="form-text text-muted">Formato: código do país + número (ex: +818092815155)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="facebook_url">URL do Facebook</label>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($facebook_url); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="instagram_url">URL do Instagram</label>
                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($instagram_url); ?>">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                        </div>
                    </form>
                </div>
                
                <!-- Mensagens do WhatsApp -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Mensagens Padrão do WhatsApp</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_whatsapp">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="whatsapp_budget_message">Mensagem de Orçamento</label>
                                <textarea class="form-control" id="whatsapp_budget_message" name="whatsapp_budget_message" rows="3" required><?php echo htmlspecialchars($whatsapp_budget_message); ?></textarea>
                                <small class="form-text text-muted">Mensagem enviada quando alguém clica em "Solicitar Orçamento"</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="whatsapp_visit_message">Mensagem de Visita</label>
                                <textarea class="form-control" id="whatsapp_visit_message" name="whatsapp_visit_message" rows="3" required><?php echo htmlspecialchars($whatsapp_visit_message); ?></textarea>
                                <small class="form-text text-muted">Mensagem enviada quando alguém clica em "Agendar Visita"</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Mensagens</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Textos da Página Inicial -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Textos da Página Inicial</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_homepage">
                        <div class="card-body">
                            <h4>Hero Section</h4>
                            <div class="form-group">
                                <label for="hero_title">Título Principal</label>
                                <input type="text" class="form-control" id="hero_title" name="hero_title" value="<?php echo htmlspecialchars($hero_title); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hero_subtitle">Subtítulo</label>
                                <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" rows="3" required><?php echo htmlspecialchars($hero_subtitle); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hero_button_1_text">Texto Botão 1</label>
                                        <input type="text" class="form-control" id="hero_button_1_text" name="hero_button_1_text" value="<?php echo htmlspecialchars($hero_button_1_text); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hero_button_2_text">Texto Botão 2</label>
                                        <input type="text" class="form-control" id="hero_button_2_text" name="hero_button_2_text" value="<?php echo htmlspecialchars($hero_button_2_text); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h4>Seção de Serviços</h4>
                            <div class="form-group">
                                <label for="services_title">Título</label>
                                <input type="text" class="form-control" id="services_title" name="services_title" value="<?php echo htmlspecialchars($services_title); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="services_subtitle">Subtítulo</label>
                                <input type="text" class="form-control" id="services_subtitle" name="services_subtitle" value="<?php echo htmlspecialchars($services_subtitle); ?>" required>
                            </div>
                            
                            <hr>
                            <h4>Seção de Carros</h4>
                            <div class="form-group">
                                <label for="cars_section_title">Título</label>
                                <input type="text" class="form-control" id="cars_section_title" name="cars_section_title" value="<?php echo htmlspecialchars($cars_section_title); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cars_section_subtitle">Subtítulo</label>
                                <input type="text" class="form-control" id="cars_section_subtitle" name="cars_section_subtitle" value="<?php echo htmlspecialchars($cars_section_subtitle); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cars_not_found_title">Título "Não Achou o Carro"</label>
                                <input type="text" class="form-control" id="cars_not_found_title" name="cars_not_found_title" value="<?php echo htmlspecialchars($cars_not_found_title); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cars_not_found_text">Texto "Não Achou o Carro"</label>
                                <input type="text" class="form-control" id="cars_not_found_text" name="cars_not_found_text" value="<?php echo htmlspecialchars($cars_not_found_text); ?>" required>
                            </div>
                            
                            <hr>
                            <h4>Seção CTA</h4>
                            <div class="form-group">
                                <label for="cta_title">Título</label>
                                <input type="text" class="form-control" id="cta_title" name="cta_title" value="<?php echo htmlspecialchars($cta_title); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cta_subtitle">Subtítulo</label>
                                <input type="text" class="form-control" id="cta_subtitle" name="cta_subtitle" value="<?php echo htmlspecialchars($cta_subtitle); ?>" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Salvar Textos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Imagem Hero e FAQs do Shaken -->
        <div class="row">
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
            
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">FAQs do Shaken</h3>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_faqs">
                        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
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