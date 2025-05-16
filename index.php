<?php
// Carregar configurações do site
require_once 'load_site_config.php';

// Função para formatar número de telefone corretamente
function format_phone_display($phone) {
    $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
    
    if (strpos($clean_phone, '+81') === 0) {
        $local_number = '0' . substr($clean_phone, 3);
        
        if (strlen($local_number) == 11) {
            $part1 = substr($local_number, 0, 3);
            $part2 = substr($local_number, 3, 4);
            $part3 = substr($local_number, 7, 4);
            
            return "({$part1}) {$part2}-{$part3}";
        }
    }
    
    if (strpos($clean_phone, '0') === 0 && strlen($clean_phone) == 11) {
        $part1 = substr($clean_phone, 0, 3);
        $part2 = substr($clean_phone, 3, 4);
        $part3 = substr($clean_phone, 7, 4);
        
        return "({$part1}) {$part2}-{$part3}";
    }
    
    return $phone;
}

// Formatar o número para exibição
$phone_display = format_phone_display($site_configs['site_phone']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_configs['site_title']; ?></title>
    
    <!-- Meta informações -->
    <meta name="description" content="<?php echo $site_configs['site_description']; ?>">
    <meta name="keywords" content="<?php echo $site_configs['site_keywords']; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="<?php echo $site_configs['site_name']; ?>" />
    <link rel="manifest" href="img/favicon/site.webmanifest" />
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts - Poppins + Lato -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="css/css-variables.css">
    <link rel="stylesheet" href="css/optimized.css">
    <link rel="stylesheet" href="css/car-filters.css">
    <link rel="stylesheet" href="css/fix-carousel-layout.css">
</head>
<body>
    
    <!-- Botão Voltar ao Topo -->
    <div class="back-to-top">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Header Section -->
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="header__top__info">
                        <li><i class="fas fa-map-marker-alt"></i> Venha nos fazer uma visita! <a href="https://maps.app.goo.gl/gwVWEN16h6L25yjk6" target="_blank">abrir no mapa</a></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $site_configs['site_email']; ?>"><?php echo $site_configs['site_email']; ?></a></li>
                    </ul>
                    <div class="header__top__social">
                        <a href="tel:<?php echo $site_configs['site_phone']; ?>"><i class="fas fa-phone-alt"></i> <?php echo $phone_display; ?></a>
                        <a href="<?php echo $site_configs['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo $site_configs['instagram_url']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="header__nav">
                <div class="header__logo">
                    <a href="index.php">
                        <img src="img/logo.png" alt="<?php echo $site_configs['site_name']; ?> Logo">
                    </a>
                </div>
                <nav class="header__menu">
                    <ul>
                        <li class="active"><a href="index.php">Início</a></li>
                        <li><a href="#carros">Carros</a></li>
                        <li><a href="#financiamento">Financiamento</a></li>
                        <li><a href="shaken.php">Shaken</a></li>
                    </ul>
                </nav>
                <div class="header__cta">
                    <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=<?php echo urlencode($site_configs['whatsapp_visit_message']); ?>" class="btn btn-primary">Agendar Visita</a>
                </div>
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <ul>
            <li class="active"><a href="index.php">Início</a></li>
            <li><a href="#carros">Carros</a></li>
            <li><a href="#financiamento">Financiamento</a></li>
            <li><a href="shaken.php">Shaken</a></li>
            <li><a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=<?php echo urlencode($site_configs['whatsapp_visit_message']); ?>" class="btn btn-primary">Agendar Visita</a></li>
        </ul>
    </div>
    <div class="mobile-menu-overlay"></div>

    <!-- Hero Section -->
    <section class="hero" style="background-image: url('img/hero-bg.jpg');">
        <div class="container">
            <div class="hero__content">
                <h1><?php echo $site_configs['hero_title']; ?></h1>
                <p><?php echo $site_configs['hero_subtitle']; ?></p>
                <div class="hero__buttons">
                    <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=<?php echo urlencode($site_configs['whatsapp_budget_message']); ?>" class="btn btn-primary btn-with-icon">
                        <i class="fas fa-car"></i> <?php echo $site_configs['hero_button_1_text']; ?>
                    </a>
                    <a href="#financiamento" class="btn btn-outline"><?php echo $site_configs['hero_button_2_text']; ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title">
                <span>Nossos Serviços</span>
                <h2><?php echo $site_configs['services_title']; ?></h2>
                <p><?php echo $site_configs['services_subtitle']; ?></p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/1.png" alt="Financiamento Japonês" class="service-card__icon">
                        <h3>Financiamento Japonês</h3>
                        <p>Financiamento com visto permanente ou um Hoshonim com visto permanente em até 120x sem entrada. Financiamento sem visto permanente em até 84x sem entrada.</p>
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20financiamento%20japonês" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/services-2.png" alt="Financiamento Próprio" class="service-card__icon">
                        <h3>Financiamento Próprio</h3>
                        <p>Nunca foi tão fácil realizar o seu sonho! Aqui temos financiamento próprio e sem burocracias. Não há necessidade de ter um hoshonin e também não é necessário ter visto permanente.</p>
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20financiamento%20próprio" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/auction.png" alt="Acesso aos Leilões" class="service-card__icon">
                        <h3>Acesso aos Leilões</h3>
                        <p>Temos acesso a todos os leilões do Japão, com isso é possível achar o carro do jeito que você sempre quis e com um ótimo preço. Mais de 5.000 veículos disponíveis semanalmente.</p>
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20acesso%20aos%20leilões" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/guincho.png" alt="Guincho 24/7" class="service-card__icon">
                        <h3>Guincho 24/7</h3>
                        <p>Temos serviço de guincho disponível 24 horas por dia, 7 dias por semana. Assistência completa em qualquer emergência, em qualquer lugar do Japão.</p>
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20preciso%20solicitar%20um%20guincho" class="btn-link">
                            Solicitar <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Car Section (Layout de 2 andares) -->
    <section id="carros" class="section-padding">
        <div class="container">
            <div class="section-title">
                <span>NOSSOS CARROS</span>
                <h2><?php echo $site_configs['cars_section_title']; ?></h2>
                <p><?php echo $site_configs['cars_section_subtitle']; ?></p>
            </div>
            
            <!-- Filtros de Carros -->
            <div class="car-filters">
                <button class="car-filter-btn active" data-filter="all">Todos</button>
                <button class="car-filter-btn" data-filter="placa-branca">Placa Branca</button>
                <button class="car-filter-btn" data-filter="kei">Kei</button>
            </div>
            
            <?php include 'show_cars.php'; ?>

            <!-- Seção Não Achou o Carro -->
            <div class="not-found-car-section mt-5">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-8">
                        <div class="not-found-content">
                            <h2><?php echo $site_configs['cars_not_found_title']; ?></h2>
                            <p><?php echo $site_configs['cars_not_found_text']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 text-right">
                        <div class="not-found-buttons">
                            <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20estou%20procurando%20um%20carro%20específico" class="btn btn-primary">Fale conosco!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Financiamento -->
    <section id="financiamento" class="section-padding" style="background-color: var(--dark-medium);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="section-title text-left">
                        <span>Financiamento</span>
                        <h2>Facilidade e agilidade para realizar seu sonho</h2>
                        <p>Oferecemos as melhores condições de financiamento para brasileiros no Japão</p>
                    </div>
                    <div class="about-content mb-4">
                        <p>Na <?php echo $site_configs['site_name']; ?>, entendemos as dificuldades que os brasileiros enfrentam para adquirir um veículo no Japão. <br>Por isso, desenvolvemos opções de financiamento flexíveis que se adaptam à sua situação:</p>
                        <br>
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> <strong>Com visto permanente:</strong> Financiamento em até 120x sem entrada</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Com hoshonim:</strong> Aprovação rápida para quem tem um fiador com visto permanente</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Sem visto permanente:</strong> Financiamento em até 84x sem entrada</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Financiamento próprio:</strong> Opções exclusivas sem necessidade de hoshonim ou visto permanente</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Aprovação rápida:</strong> Resposta em até 48 horas úteis</li>
                        </ul>
                        
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20as%20opções%20de%20financiamento" class="btn btn-primary">Consultar condições de financiamento</a>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="sobre" class="section-padding about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-content">
                        <div class="section-title text-left">
                            <span>Por que nos escolher</span>
                            <h2>Vantagens de ser um cliente PREMIUM</h2>
                            <p>Na <?php echo $site_configs['site_name']; ?>, oferecemos mais do que apenas carros, oferecemos uma experiência completa</p>
                        </div>
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> Acesso exclusivo aos maiores leilões de veículos do Japão</li>
                            <li><i class="fas fa-check-circle"></i> Financiamento personalizado, adaptado às suas necessidades e situação de visto</li>
                            <li><i class="fas fa-check-circle"></i> Equipe bilíngue que entende as necessidades dos brasileiros no Japão</li>
                            <li><i class="fas fa-check-circle"></i> Assistência completa: desde a compra até a manutenção do seu veículo</li>
                            <li><i class="fas fa-check-circle"></i> Atendimento 24 horas via WhatsApp para emergências veiculares</li>
                        </ul>
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20as%20vantagens%20de%20ser%20um%20cliente%20premium" class="btn btn-primary">Torne-se um cliente Premium</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding cta-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8">
                    <div class="cta-content">
                        <h2><?php echo $site_configs['cta_title']; ?></h2>
                        <p><?php echo $site_configs['cta_subtitle']; ?></p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 text-right">
                    <div class="cta-button">
                        <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>?text=<?php echo urlencode($site_configs['whatsapp_budget_message']); ?>" class="btn btn-white">Entrar em Contato</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="footer-contact">
                            <h2>Seja você também um cliente <span style="color: var(--primary);">PREMIUM</span>!</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 text-right">
                        <div class="footer-contact-buttons">
                            <a href="tel:<?php echo $site_configs['site_phone']; ?>" class="footer-contact-btn">
                                <i class="fas fa-phone-alt"></i> <?php echo $phone_display; ?>
                            </a>
                            <a href="mailto:<?php echo $site_configs['site_email']; ?>" class="footer-contact-btn">
                                <i class="fas fa-envelope"></i> <?php echo $site_configs['site_email']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Navegação</h3>
                        <ul>
                            <li><a href="./index.php">Início</a></li>
                            <li><a href="#carros">Carros</a></li>
                            <li><a href="#financiamento">Financiamento</a></li>
                            <li><a href="shaken.php">Shaken</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Serviços</h3>
                        <ul>
                            <li><a href="#financiamento">Financiamento Japonês</a></li>
                            <li><a href="#financiamento">Financiamento Próprio</a></li>
                            <li><a href="#carros">Leilões de Veículos</a></li>
                            <li><a href="shaken.php">Serviço de Shaken</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Contato</h3>
                        <ul class="contact-info">
                            <li><i class="fas fa-map-marker-alt"></i> <a href="https://maps.app.goo.gl/gwVWEN16h6L25yjk6" target="_blank">Ver no mapa</a></li>
                            <li><i class="fas fa-phone-alt"></i> <a href="tel:<?php echo $site_configs['site_phone']; ?>"><?php echo $phone_display; ?></a></li>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $site_configs['site_email']; ?>"><?php echo $site_configs['site_email']; ?></a></li>
                            <li><i class="fas fa-clock"></i> Segunda a Sábado: 9h às 18h</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Siga-nos</h3>
                        <div class="footer-social">
                            <a href="<?php echo $site_configs['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?php echo $site_configs['instagram_url']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/<?php echo $site_configs['site_phone']; ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p><?php echo $site_configs['site_name']; ?> 結 | Copyright &copy;<?php echo date('Y'); ?> - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/index-main.js"></script>
</body>
</html>