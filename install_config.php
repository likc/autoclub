<?php
/**
 * Script de instalação/atualização do sistema de configurações
 * Execute este arquivo uma vez para configurar o banco de dados
 */

// Configuração do banco de dados
$db_host = 'localhost';
$db_user = 'minec761_likc';
$db_pass = 'rw23xrd807ox';
$db_name = 'minec761_autoclub';

// Conectar ao banco de dados
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir charset
$conn->set_charset("utf8mb4");

echo "<h1>Instalação do Sistema de Configurações</h1>";
echo "<pre>";

// 1. Criar tabela de configurações
$sql = "CREATE TABLE IF NOT EXISTS site_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NOT NULL,
    config_type ENUM('text', 'longtext', 'email', 'phone', 'url') DEFAULT 'text',
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabela site_config criada com sucesso.\n";
} else {
    echo "✗ Erro ao criar tabela: " . $conn->error . "\n";
}

// 2. Verificar se a tabela está vazia
$check_empty = $conn->query("SELECT COUNT(*) as count FROM site_config");
$is_empty = $check_empty->fetch_assoc()['count'] == 0;

if ($is_empty) {
    echo "\n✓ Tabela está vazia. Inserindo configurações padrão...\n\n";
    
    // Configurações padrão
    $default_configs = [
        // SEO
        ['site_title', 'AutoClub - Seu sonho automotivo realizado no Japão', 'text', 'Título do site (SEO)'],
        ['site_description', 'AutoClub - Compre seu carro no Japão com financiamento facilitado. Acesso a leilões exclusivos e aprovação rápida, mesmo sem visto permanente.', 'longtext', 'Descrição do site (SEO)'],
        ['site_keywords', 'carros Japão, financiamento carros Japão, brasileiros Japão, carros importados, AutoClub, shaken', 'text', 'Palavras-chave do site (SEO)'],
        
        // Informações gerais
        ['site_name', 'AutoClub', 'text', 'Nome do site'],
        ['site_email', 'contato@autoclub.jp', 'email', 'Email de contato do site'],
        ['site_phone', '+818092815155', 'phone', 'Telefone/WhatsApp de contato'],
        ['site_address', 'Aichi, Japão', 'text', 'Endereço físico'],
        ['google_maps_link', 'https://maps.app.goo.gl/gwVWEN16h6L25yjk6', 'url', 'Link do Google Maps'],
        
        // Textos da Hero Section
        ['hero_title', 'Realize seu sonho automotivo no Japão', 'text', 'Título principal da página inicial'],
        ['hero_subtitle', 'Financiamento facilitado com ou sem visto permanente, acesso exclusivo aos melhores leilões do Japão.', 'longtext', 'Subtítulo da página inicial'],
        ['hero_button_1_text', 'Solicitar Orçamento', 'text', 'Texto do primeiro botão da hero'],
        ['hero_button_2_text', 'Saiba Mais', 'text', 'Texto do segundo botão da hero'],
        
        // Seção de Serviços
        ['services_title', 'AutoClub oferece:', 'text', 'Título da seção de serviços'],
        ['services_subtitle', 'Soluções completas para brasileiros que desejam adquirir veículos no Japão', 'text', 'Subtítulo da seção de serviços'],
        
        // Seção de Carros
        ['cars_section_title', 'Veículos Disponíveis', 'text', 'Título da seção de carros'],
        ['cars_section_subtitle', 'Uma seleção premium de veículos para todos os gostos e necessidades', 'text', 'Subtítulo da seção de carros'],
        ['cars_not_found_title', 'Não achou o carro dos seus sonhos?', 'text', 'Título da seção "não encontrou o carro"'],
        ['cars_not_found_text', 'Entre em contato e conte para nós o que você está procurando.', 'text', 'Texto da seção "não encontrou o carro"'],
        
        // Seção de Financiamento
        ['financing_title', 'Facilidade e agilidade para realizar seu sonho', 'text', 'Título da seção de financiamento'],
        ['financing_subtitle', 'Oferecemos as melhores condições de financiamento para brasileiros no Japão', 'text', 'Subtítulo da seção de financiamento'],
        
        // Seção Sobre/Vantagens
        ['about_title', 'Vantagens de ser um cliente PREMIUM', 'text', 'Título da seção sobre/vantagens'],
        ['about_subtitle', 'Na AutoClub, oferecemos mais do que apenas carros, oferecemos uma experiência completa', 'text', 'Subtítulo da seção sobre/vantagens'],
        
        // CTA Section
        ['cta_title', 'Pronto para realizar seu sonho automotivo?', 'text', 'Título da seção CTA'],
        ['cta_subtitle', 'Entre em contato agora mesmo e descubra as melhores opções para você.', 'text', 'Subtítulo da seção CTA'],
        ['cta_button_text', 'Entrar em Contato', 'text', 'Texto do botão CTA'],
        
        // Footer
        ['footer_title', 'Seja você também um cliente <span style="color: var(--primary);">PREMIUM</span>!', 'text', 'Título do rodapé'],
        
        // WhatsApp Messages
        ['whatsapp_default_message', 'Olá, vim pelo site e gostaria de mais informações', 'text', 'Mensagem padrão do WhatsApp'],
        ['whatsapp_budget_message', 'Olá, vim pelo site e gostaria de fazer um orçamento', 'text', 'Mensagem de orçamento do WhatsApp'],
        ['whatsapp_visit_message', 'Olá, vim pelo site e gostaria de agendar uma visita', 'text', 'Mensagem de visita do WhatsApp'],
        
        // Horário de funcionamento
        ['business_hours', 'Segunda a Sábado: 9h às 18h', 'text', 'Horário de funcionamento'],
        
        // Redes sociais
        ['facebook_url', 'https://www.facebook.com/anderson.premium.motors.japao/', 'url', 'URL do Facebook'],
        ['instagram_url', 'https://www.instagram.com/AutoClub_anderson/', 'url', 'URL do Instagram']
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO site_config (config_key, config_value, config_type, description) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)
    ");
    
    foreach ($default_configs as $config) {
        $stmt->bind_param("ssss", $config[0], $config[1], $config[2], $config[3]);
        if ($stmt->execute()) {
            echo "✓ Configuração inserida: {$config[0]}\n";
        } else {
            echo "✗ Erro ao inserir {$config[0]}: " . $conn->error . "\n";
        }
    }
    
    echo "\n✓ Todas as configurações padrão foram inseridas com sucesso!\n";
} else {
    echo "\n• A tabela já contém configurações. Nenhuma ação necessária.\n";
}

// 3. Listar todas as configurações atuais
echo "\n<strong>Configurações atuais:</strong>\n";
$result = $conn->query("SELECT * FROM site_config ORDER BY id");

while ($row = $result->fetch_assoc()) {
    echo "{$row['config_key']}: {$row['config_value']}\n";
}

echo "</pre>";

echo "<h2>✓ Instalação concluída!</h2>";
echo "<p>Você pode acessar as configurações do site em: <a href='admin/site_settings.php'>admin/site_settings.php</a></p>";

$conn->close();
?>