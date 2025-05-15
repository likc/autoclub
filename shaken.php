<?php
// Script PHP para página Shaken
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço de Shaken - AutoClub</title>
    
    <!-- Meta informações -->
    <meta name="description" content="AutoClub - Serviço completo de Shaken para seu veículo no Japão. Inspeção veicular e renovação feita por especialistas, com simplicidade e preço justo.">
    <meta name="keywords" content="shaken Japão, inspeção veicular, renovação shaken, carros Japão, brasileiros Japão, AutoClub">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="AutoClub" />
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts - Poppins + Lato -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="css/css-variables.css">
    <link rel="stylesheet" href="css/optimized.css">
    <link rel="stylesheet" href="css/fix.css">
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
                        <li><i class="fas fa-envelope"></i> <a href="mailto:contato@autoclub.jp">contato@autoclub.jp</a></li>
                    </ul>
                    <div class="header__top__social">
                        <a href="tel:+818092815155"><i class="fas fa-phone-alt"></i> (080) 9281-5155</a>
                        <a href="https://www.facebook.com/anderson.premium.motors.japao/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/AutoClub_anderson/" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="header__nav">
                <div class="header__logo">
                    <a href="index.php">
                        <img src="img/logo.png" alt="AutoClub Logo">
                    </a>
                </div>
                <nav class="header__menu">
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="index.php#carros">Carros</a></li>
                        <li><a href="index.php#financiamento">Financiamento</a></li>
                        <li class="active"><a href="shaken.php">Shaken</a></li>
                    </ul>
                </nav>
                <div class="header__cta">
                    <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20solicitar%20um%20Shaken" class="btn btn-primary">Solicitar Shaken</a>
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
            <li><a href="index.php">Início</a></li>
            <li><a href="index.php#carros">Carros</a></li>
            <li><a href="index.php#financiamento">Financiamento</a></li>
            <li class="active"><a href="shaken.php">Shaken</a></li>
            <li><a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20solicitar%20um%20Shaken" class="btn btn-primary">Solicitar Shaken</a></li>
        </ul>
    </div>
    <div class="mobile-menu-overlay"></div>

    <!-- Page Header -->
    <section class="page-header" style="background-image: url('img/shaken-bg.jpg');">
        <div class="container">
            <h1>Serviço de Shaken</h1>
            <p>Renovação e inspeção veicular feita por especialistas, com simplicidade e preço justo</p>
            <ul class="breadcrumb">
                <li><a href="index.php">Início</a></li>
                <li>Shaken</li>
            </ul>
        </div>
    </section>

    <!-- Shaken Intro Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="about-image animate-on-scroll">
                        <img src="img/services/shaken-intro.jpg" alt="Serviço de Shaken">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-title text-left animate-on-scroll">
                        <span>O que é Shaken?</span>
                        <h2>A inspeção veicular japonesa</h2>
                    </div>
                    <div class="about-content animate-on-scroll">
                        <p>O <strong>Shaken (車検)</strong> é a inspeção técnica veicular obrigatória no Japão, que verifica se o veículo está em conformidade com os padrões de segurança e emissão de poluentes estabelecidos pelo governo japonês.</p>
                        
                        <p>Todo veículo no Japão precisa passar por essa inspeção periodicamente para poder circular legalmente nas ruas. O prazo de validade do Shaken varia:</p>
                        
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> <strong>Carros novos:</strong> Válido por 3 anos</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Carros usados:</strong> Renovação a cada 2 anos</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Veículos comerciais e motos:</strong> Prazos específicos</li>
                        </ul>
                        
                        <p>A falta do Shaken válido pode resultar em multas severas, problemas com o seguro e impossibilidade de circular com o veículo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shaken Services Section -->
    <section class="section-padding" style="background-color: var(--dark-medium);">
        <div class="container">
            <div class="section-title">
                <span>Nossos Serviços</span>
                <h2>Serviços de Shaken na AutoClub</h2>
                <p>Facilidade e transparência em todo o processo</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card animate-on-scroll">
                        <div class="service-card__icon fa-icon-wrapper">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        <h3>Inspeção Completa</h3>
                        <p>Realizamos uma inspeção detalhada para identificar qualquer problema que possa causar reprovação no Shaken. Avaliamos mais de 100 itens do veículo, incluindo sistema de freios, suspensão, emissão de gases, iluminação e muito mais.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20a%20inspeção%20para%20Shaken" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card animate-on-scroll">
                        <div class="service-card__icon fa-icon-wrapper">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3>Consultoria Técnica</h3>
                        <p>Identificamos todos os itens que precisam estar em conformidade para aprovação no Shaken. Fornecemos um relatório detalhado e recomendamos oficinas parceiras caso reparos sejam necessários, sempre com transparência e os melhores preços.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20consultoria%20técnica%20para%20Shaken" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card animate-on-scroll">
                        <div class="service-card__icon fa-icon-wrapper">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3>Documentação Completa</h3>
                        <p>Cuidamos de toda a burocracia do processo de Shaken. Preparamos e submetemos todos os documentos necessários, pagamos as taxas governamentais e impostos relacionados, e garantimos que tudo esteja em conformidade com as exigências japonesas.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20a%20documentação%20do%20Shaken" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shaken Process Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title">
                <span>Como Funciona</span>
                <h2>O Processo de Shaken na AutoClub</h2>
                <p>Simplicidade e transparência em cada etapa</p>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-card animate-on-scroll">
                        <div class="process-card__number">1</div>
                        <h3>Agendamento</h3>
                        <p>Entre em contato conosco por telefone, WhatsApp ou presencialmente para agendar o serviço de Shaken. Recomendamos agendar com pelo menos 1 mês de antecedência da data de vencimento.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-card animate-on-scroll">
                        <div class="process-card__number">2</div>
                        <h3>Pré-inspeção</h3>
                        <p>Realizamos uma avaliação detalhada do veículo para identificar qualquer problema que possa causar reprovação. Apresentamos um relatório claro de todos os itens que precisam ser corrigidos.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-card animate-on-scroll">
                        <div class="process-card__number">3</div>
                        <h3>Consultoria</h3>
                        <p>Fornecemos uma lista de oficinas parceiras recomendadas para os reparos necessários. Você escolhe a oficina de sua preferência para realizar os serviços identificados.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-card animate-on-scroll">
                        <div class="process-card__number">4</div>
                        <h3>Inspeção Oficial</h3>
                        <p>Levamos seu veículo para a inspeção oficial, cuidamos de toda a documentação e, após aprovação, entregamos o veículo com o novo certificado de Shaken válido.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20agendar%20um%20Shaken" class="btn btn-primary">Agendar Meu Shaken</a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section-padding" style="background-color: var(--dark-medium);">
        <div class="container">
            <div class="section-title">
                <span>Custos Transparentes</span>
                <h2>Preços do Serviço de Shaken</h2>
                <p>Valores honestos e sem surpresas</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12 mb-4">
                    <div class="pricing-card-premium animate-on-scroll">
                        <h3 class="price-title">Nossos Serviços de Shaken</h3>
                        <p class="price-subtitle">Preços acessíveis para todos os tipos de veículos</p>
                        
                        <div class="price-table">
                            <div class="price-option">
                                <div class="price-option-header">
                                    <h4>Carros Compactos</h4>
                                    <p>Até 660cc (Carros Kei)</p>
                                </div>
                                <div class="price-amount">
                                    <span>A partir de</span>
                                    <h4>¥38.900</h4>
                                </div>
                            </div>
                            
                            <div class="price-option">
                                <div class="price-option-header">
                                    <h4>Outros Veículos</h4>
                                    <p>Acima de 661cc e SUVs</p>
                                </div>
                                <div class="price-amount">
                                    <span>Orçamento personalizado</span>
                                    <h4>Consulte-nos</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="included-services">
                            <h4>O que está incluído:</h4>
                            <div class="services-grid">
                                <ul>
                                    <li><i class="fas fa-check-circle"></i> Inspeção completa</li>
                                    <li><i class="fas fa-check-circle"></i> Vistoria detalhada</li>
                                    <li><i class="fas fa-check-circle"></i> Verificação de sistemas</li>
                                    <li><i class="fas fa-check-circle"></i> Documentação completa</li>
                                    <li><i class="fas fa-check-circle"></i> Taxas governamentais inclusas</li>
                                    <li><i class="fas fa-check-circle"></i> Seguro compulsório (Jibaiseki)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="price-cta">
                            <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20um%20orçamento%20para%20Shaken" class="btn btn-primary">Solicitar Orçamento</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pricing-note text-center mt-4">
                <p>* Os valores podem variar dependendo das condições do veículo e reparos necessários. Um orçamento detalhado será fornecido após a pré-inspeção.</p>
                <p>* Taxas governamentais incluem: taxa de inspeção, peso do veículo, taxa de registro e seguro compulsório (Jibaiseki).</p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <?php include 'shaken_faqs_section.php'; ?>

    <!-- CTA Section -->
    <section class="section-padding cta-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8">
                    <div class="cta-content">
                        <h2>Pronto para renovar seu Shaken?</h2>
                        <p>Entre em contato agora mesmo e garanta um processo tranquilo e sem preocupações.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 text-right">
                    <div class="cta-button">
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20agendar%20um%20Shaken" class="btn btn-white">Agendar Shaken</a>
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
                            <a href="tel:+818092815155" class="footer-contact-btn">
                                <i class="fas fa-phone-alt"></i> (080) 9281-5155
                            </a>
                            <a href="mailto:contato@autoclub.jp" class="footer-contact-btn">
                                <i class="fas fa-envelope"></i> contato@autoclub.jp
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
                            <li><a href="index.php">Início</a></li>
                            <li><a href="index.php#carros">Carros</a></li>
                            <li><a href="index.php#financiamento">Financiamento</a></li>
                            <li><a href="shaken.php">Shaken</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Serviços</h3>
                        <ul>
                            <li><a href="index.php#financiamento">Financiamento Japonês</a></li>
                            <li><a href="index.php#financiamento">Financiamento Próprio</a></li>
                            <li><a href="index.php#carros">Leilões de Veículos</a></li>
                            <li><a href="shaken.php">Serviço de Shaken</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Contato</h3>
                        <ul class="contact-info">
                            <li><i class="fas fa-map-marker-alt"></i> <a href="https://maps.app.goo.gl/gwVWEN16h6L25yjk6" target="_blank">Ver no mapa</a></li>
                            <li><i class="fas fa-phone-alt"></i> <a href="tel:+818092815155">(080) 9281-5155</a></li>
                            <li><i class="fas fa-envelope"></i> <a href="mailto:contato@autoclub.jp">contato@autoclub.jp</a></li>
                            <li><i class="fas fa-clock"></i> Segunda a Sábado: 9h às 18h</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h3>Siga-nos</h3>
                        <div class="footer-social">
                            <a href="https://www.facebook.com/anderson.premium.motors.japao/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/AutoClub_anderson/" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://wa.me/+818092815155" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>AutoClub 結 | Copyright &copy;<?php echo date('Y'); ?> - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/shaken-main.js"></script>
    
    <!-- CSS para os ícones Font Awesome -->
    <style>
    .service-card__icon.fa-icon-wrapper {
        width: 80px;
        height: 80px;
        background: rgba(214, 156, 30, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        transition: all 0.3s ease;
        border: 2px solid rgba(214, 156, 30, 0.2);
    }

    .service-card__icon.fa-icon-wrapper i {
        font-size: 36px;
        color: var(--primary);
        transition: all 0.3s ease;
    }

    .service-card:hover .service-card__icon.fa-icon-wrapper {
        background: var(--primary);
        transform: scale(1.1) rotate(5deg);
        border-color: var(--primary);
        box-shadow: 0 10px 20px rgba(214, 156, 30, 0.3);
    }

    .service-card:hover .service-card__icon.fa-icon-wrapper i {
        color: var(--light);
    }

    /* Animação extra para os ícones */
    @keyframes iconPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .service-card:hover .service-card__icon.fa-icon-wrapper {
        animation: iconPulse 1s ease-in-out infinite;
    }
    </style>
</body>
</html>