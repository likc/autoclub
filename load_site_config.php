<?php
/**
 * Site Configuration Loader
 * Carrega as configurações do site do banco de dados
 */

// Conexão com o banco de dados
$db_host = 'localhost';
$db_user = 'minec761_likc';
$db_pass = 'rw23xrd807ox';
$db_name = 'minec761_autoclub';

// Conectar ao banco de dados
$config_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$config_conn->set_charset("utf8mb4");

// Função para obter configuração do site
function get_config($key, $default = '') {
    global $config_conn;
    
    // Verificar se a tabela existe
    $table_check = $config_conn->query("SHOW TABLES LIKE 'site_config'");
    if ($table_check->num_rows == 0) {
        return $default;
    }
    
    $stmt = $config_conn->prepare("SELECT config_value FROM site_config WHERE config_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['config_value'];
    }
    
    return $default;
}

// Carregar configurações do site
$site_configs = [
    // SEO
    'site_title' => get_config('site_title', 'AutoClub - Seu sonho automotivo realizado no Japão'),
    'site_description' => get_config('site_description', 'AutoClub - Compre seu carro no Japão com financiamento facilitado. Acesso a leilões exclusivos e aprovação rápida, mesmo sem visto permanente.'),
    'site_keywords' => get_config('site_keywords', 'carros Japão, financiamento carros Japão, brasileiros Japão, carros importados, AutoClub, shaken'),
    
    // Informações gerais
    'site_name' => get_config('site_name', 'AutoClub'),
    'site_email' => get_config('site_email', 'contato@autoclub.jp'),
    'site_phone' => get_config('site_phone', '+818092815155'),
    
    // Hero Section
    'hero_title' => get_config('hero_title', 'Realize seu sonho automotivo no Japão'),
    'hero_subtitle' => get_config('hero_subtitle', 'Financiamento facilitado com ou sem visto permanente, acesso exclusivo aos melhores leilões do Japão.'),
    'hero_button_1_text' => get_config('hero_button_1_text', 'Solicitar Orçamento'),
    'hero_button_2_text' => get_config('hero_button_2_text', 'Saiba Mais'),
    
    // Seções
    'services_title' => get_config('services_title', 'AutoClub oferece:'),
    'services_subtitle' => get_config('services_subtitle', 'Soluções completas para brasileiros que desejam adquirir veículos no Japão'),
    'cars_section_title' => get_config('cars_section_title', 'Veículos Disponíveis'),
    'cars_section_subtitle' => get_config('cars_section_subtitle', 'Uma seleção premium de veículos para todos os gostos e necessidades'),
    'cars_not_found_title' => get_config('cars_not_found_title', 'Não achou o carro dos seus sonhos?'),
    'cars_not_found_text' => get_config('cars_not_found_text', 'Entre em contato e conte para nós o que você está procurando.'),
    'cta_title' => get_config('cta_title', 'Pronto para realizar seu sonho automotivo?'),
    'cta_subtitle' => get_config('cta_subtitle', 'Entre em contato agora mesmo e descubra as melhores opções para você.'),
    
    // WhatsApp
    'whatsapp_budget_message' => get_config('whatsapp_budget_message', 'Olá, vim pelo site e gostaria de fazer um orçamento'),
    'whatsapp_visit_message' => get_config('whatsapp_visit_message', 'Olá, vim pelo site e gostaria de agendar uma visita'),
    
    // Redes sociais
    'facebook_url' => get_config('facebook_url', 'https://www.facebook.com/anderson.premium.motors.japao/'),
    'instagram_url' => get_config('instagram_url', 'https://www.instagram.com/AutoClub_anderson/')
];

// Fechar conexão
$config_conn->close();
?>