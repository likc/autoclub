<?php
// Iniciar o script PHP
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>

<style>
/* Estilos de emergência para o carrossel */
.car-grid {
  display: flex;
  flex-wrap: wrap;
  margin-bottom: 30px !important;
}

.car-item {
  transition: all 0.3s ease;
}

/* Melhorar a aparência da mensagem "nenhum carro encontrado" */
.no-cars-found {
  background: rgba(34, 34, 34, 0.8);
  border-radius: 8px;
  border: 1px solid #d69c1e;
  padding: 20px;
  text-align: center;
  width: 100%;
  margin: 30px 0;
}

.no-cars-found p {
  color: #fff;
  font-size: 18px;
  margin: 0;
}

/* Garantir que as linhas do grid tenham altura adequada quando vazias */
.car-grid:empty {
  display: none;
}

/* Correção para os botões de filtro */
.car-filter-btn.active {
  background-color: #d69c1e !important;
  color: #fff !important;
}
</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoClub - Seu sonho automotivo realizado no Japão</title>
    
    <!-- Meta informações -->
    <meta name="description" content="AutoClub - Compre seu carro no Japão com financiamento facilitado. Acesso a leilões exclusivos e aprovação rápida, mesmo sem visto permanente.">
    <meta name="keywords" content="carros Japão, financiamento carros Japão, brasileiros Japão, carros importados, AutoClub, shaken">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="img/favicon/favicon.svg" />
    <link rel="shortcut icon" href="img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="AutoClub" />
    <link rel="manifest" href="img/favicon/site.webmanifest" />
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts - Poppins + Lato -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="css/css-variables.css">
    <link rel="stylesheet" href="css/optimized.css">
    
    <!-- Slick Slider CSS (para páginas com slider) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
</head>
<body>
    <!-- Whatsapp Flutuante -->
    <div class="whatsapp-float">
        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais">
            <i class="fab fa-whatsapp"></i> Falar Agora
        </a>
    </div>
    
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
                        <li class="active"><a href="index.php">Início</a></li>
                        <li><a href="#carros">Carros</a></li>
                        <li><a href="#financiamento">Financiamento</a></li>
                        <li><a href="shaken.html">Shaken</a></li>
                       <!-- <li><a href="sobre.html">Sobre Nós</a></li> 
                        <li><a href="contato.html">Contato</a></li>-->
                    </ul>
                </nav>
                <div class="header__cta">
                    <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20agendar%20uma%20visita" class="btn btn-primary">Agendar Visita</a>
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
            <li><a href="shaken.html">Shaken</a></li>
           <!-- <li><a href="sobre.html">Sobre Nós</a></li>
            <li><a href="contato.html">Contato</a></li> -->
            <li><a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20agendar%20uma%20visita" class="btn btn-primary">Agendar Visita</a></li>
        </ul>
    </div>
    <div class="mobile-menu-overlay"></div>

    <!-- Hero Section -->
    <section class="hero" style="background-image: url('img/hero-bg.jpg');">
        <div class="container">
            <div class="hero__content">
                <h1>Realize seu sonho automotivo no Japão</h1>
                <p>Financiamento facilitado com ou sem visto permanente, acesso exclusivo aos melhores leilões do Japão.</p>
                <div class="hero__buttons">
                    <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20fazer%20um%20orçamento" class="btn btn-primary btn-with-icon">
                        <i class="fas fa-car"></i> Solicitar Orçamento
                    </a>
                    <a href="#financiamento" class="btn btn-outline">Saiba Mais</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-title">
                <span>Nossos Serviços</span>
                <h2>AutoClub oferece:</h2>
                <p>Soluções completas para brasileiros que desejam adquirir veículos no Japão</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/auction.png" alt="Financiamento Japonês" class="service-card__icon">
                        <h3>Financiamento Japonês</h3>
                        <p>Financiamento com visto permanente ou um Hoshonim com visto permanente em até 120x sem entrada. Financiamento sem visto permanente em até 84x sem entrada.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20financiamento%20japonês" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/services-2.png" alt="Financiamento Próprio" class="service-card__icon">
                        <h3>Financiamento Próprio</h3>
                        <p>Nunca foi tão fácil realizar o seu sonho! Aqui temos financiamento próprio e sem burocracias. Não há necessidade de ter um hoshonin e também não é necessário ter visto permanente.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20financiamento%20próprio" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/auction.png" alt="Acesso aos Leilões" class="service-card__icon">
                        <h3>Acesso aos Leilões</h3>
                        <p>Temos acesso a todos os leilões do Japão, com isso é possível achar o carro do jeito que você sempre quis e com um ótimo preço. Mais de 5.000 veículos disponíveis semanalmente.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20o%20acesso%20aos%20leilões" class="btn-link">
                            Saiba mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                    <div class="service-card">
                        <img src="img/services/guincho.png" alt="Guincho 24/7" class="service-card__icon">
                        <h3>Guincho 24/7</h3>
                        <p>Temos serviço de guincho disponível 24 horas por dia, 7 dias por semana. Assistência completa em qualquer emergência, em qualquer lugar do Japão.</p>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20preciso%20solicitar%20um%20guincho" class="btn-link">
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
            <h2>Veículos Disponíveis</h2>
            <p>Uma seleção premium de veículos para todos os gostos e necessidades</p>
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
                        <h2>Não achou o carro dos seus sonhos?</h2>
                        <p>Entre em contato e conte para nós o que você está procurando.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 text-right">
                    <div class="not-found-buttons">
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20estou%20procurando%20um%20carro%20específico" class="btn btn-primary">Fale conosco!</a>
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
                        <p>Na AutoClub, entendemos as dificuldades que os brasileiros enfrentam para adquirir um veículo no Japão. Por isso, desenvolvemos opções de financiamento flexíveis que se adaptam à sua situação:</p>
                        
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> <strong>Com visto permanente:</strong> Financiamento em até 120x sem entrada</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Com hoshonim:</strong> Aprovação rápida para quem tem um fiador com visto permanente</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Sem visto permanente:</strong> Financiamento em até 84x sem entrada</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Financiamento próprio:</strong> Opções exclusivas sem necessidade de hoshonim ou visto permanente</li>
                            <li><i class="fas fa-check-circle"></i> <strong>Aprovação rápida:</strong> Resposta em até 48 horas úteis</li>
                        </ul>
                        
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20as%20opções%20de%20financiamento" class="btn btn-primary">Consultar condições de financiamento</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="financing-image">
                        <img src="img/financing.jpg" alt="Financiamento de veículos" class="img-fluid rounded">
                        <div class="financing-quick-info">
                            <div class="financing-info-item">
                                <h4>Até 120x</h4>
                                <p>para pagamento</p>
                            </div>
                            <div class="financing-info-item">
                                <h4>Sem entrada</h4>
                                <p>para iniciar</p>
                            </div>
                            <div class="financing-info-item">
                                <h4>48h</h4>
                                <p>para aprovação</p>
                            </div>
                        </div>
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
                            <p>Na AutoClub, oferecemos mais do que apenas carros, oferecemos uma experiência completa</p>
                        </div>
                        <ul class="about-list">
                            <li><i class="fas fa-check-circle"></i> Acesso exclusivo aos maiores leilões de veículos do Japão</li>
                            <li><i class="fas fa-check-circle"></i> Financiamento personalizado, adaptado às suas necessidades e situação de visto</li>
                            <li><i class="fas fa-check-circle"></i> Equipe bilíngue que entende as necessidades dos brasileiros no Japão</li>
                            <li><i class="fas fa-check-circle"></i> Assistência completa: desde a compra até a manutenção do seu veículo</li>
                            <li><i class="fas fa-check-circle"></i> Atendimento 24 horas via WhatsApp para emergências veiculares</li>
                        </ul>
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20saber%20mais%20sobre%20as%20vantagens%20de%20ser%20um%20cliente%20premium" class="btn btn-primary">Torne-se um cliente Premium</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-video">
                        <img src="img/about-img.jpg" alt="Vídeo sobre nós">
                        <a href="#" class="play-btn">
                            <i class="fas fa-play"></i>
                        </a>
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
                        <h2>Pronto para realizar seu sonho automotivo?</h2>
                        <p>Entre em contato agora mesmo e descubra as melhores opções para você.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 text-right">
                    <div class="cta-button">
                        <a href="https://wa.me/+818092815155?text=Olá,%20vim%20pelo%20site%20e%20gostaria%20de%20fazer%20um%20orçamento" class="btn btn-white">Entrar em Contato</a>
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
                            <li><a href="./index.php">Início</a></li>
                            <li><a href="#carros">Carros</a></li>
                            <li><a href="#financiamento">Financiamento</a></li>
                            <li><a href="shaken.html">Shaken</a></li>
                           <!-- <li><a href="sobre.html">Sobre Nós</a></li> 
                            <li><a href="contato.html">Contato</a></li>-->
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
                            <li><a href="shaken.html">Serviço de Shaken</a></li>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script src="js/main-updated.js"></script>
<script src="js/unified-carousel.js"></script>

<script>
// SOLUÇÃO DE EMERGÊNCIA - REESCRITA COMPLETA DO CARROSSEL
document.addEventListener('DOMContentLoaded', function() {
  if (typeof jQuery !== 'undefined') {
    jQuery(function($) {
      // Primeiro, vamos desabilitar todos os handlers de eventos existentes relacionados ao carrossel
      $('.car-filter-btn').off();
      $('.indicator').off();
      $('.prev-arrow').off();
      $('.next-arrow').off();
      
      // Variáveis globais para controle do carrossel
      let currentSlide = 1;
      const totalSlides = $('.car-slide').length;
      
      // Função para navegar para um slide específico
      function navegarParaSlide(numero) {
        // Limitar o número para não ultrapassar o total de slides
        if (numero < 1) numero = 1;
        if (numero > totalSlides) numero = totalSlides;
        
        // Atualizar slide atual
        currentSlide = numero;
        
        // Esconder todos os slides
        $('.car-slide').removeClass('active');
        
        // Mostrar o slide solicitado
        $(`.car-slide[data-slide="${numero}"]`).addClass('active');
        
        // Atualizar indicadores
        $('.indicator').removeClass('active');
        $(`.indicator[data-slide="${numero}"]`).addClass('active');
        
        // Atualizar estado dos botões de navegação
        $('.prev-arrow').prop('disabled', numero === 1);
        $('.next-arrow').prop('disabled', numero === totalSlides);
      }
      
      // Configurar navegação por botões
      $('.prev-arrow').on('click', function() {
        navegarParaSlide(currentSlide - 1);
      });
      
      $('.next-arrow').on('click', function() {
        navegarParaSlide(currentSlide + 1);
      });
      
      // Configurar indicadores
      $('.indicator').on('click', function() {
        const slideNum = $(this).data('slide');
        navegarParaSlide(slideNum);
      });
      
      // Limpar quaisquer filtros anteriores e garantir que tudo esteja visível inicialmente
      $('.car-item').css({
        'display': 'block',
        'visibility': 'visible',
        'opacity': '1'
      });
      
      // Configurar filtragem por botões - abordagem completamente nova
      $('.car-filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        
        // Atualizar estado dos botões
        $('.car-filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Voltar para o primeiro slide
        navegarParaSlide(1);
        
        // Remover mensagens anteriores
        $('.no-cars-found').remove();
        
        // ABORDAGEM FIXA: Forçar exibição de todos os itens e linhas primeiro
        $('.car-grid').show();
        
        // Se for "all", mostrar todos os carros
        if (filter === 'all' || filter === '*') {
          $('.car-item').show();
          return;
        }
        
        // Esconder todos os carros primeiro
        $('.car-item').hide();
        
        // Mostrar apenas os carros com a categoria selecionada
        $(`.car-item[data-category*="${filter}"]`).show();
        
        // IMPORTANTE: Em vez de tentar manter o layout existente, vamos garantir que cada linha
        // tenha pelo menos um item visível, caso contrário, escondemos a linha inteira
        $('.car-grid').each(function() {
          const grid = $(this);
          const visibleItems = grid.find('.car-item:visible').length;
          
          if (visibleItems === 0) {
            grid.hide();
          } else {
            grid.show();
          }
        });
        
        // Verificar se há algum carro visível em todo o slide
        const slidesWithVisibleCars = $('.car-slide').filter(function() {
          return $(this).find('.car-item:visible').length > 0;
        }).length;
        
        if (slidesWithVisibleCars === 0) {
          // Nenhum carro encontrado em nenhum slide
          const noItemsMsg = $('<div class="no-cars-found text-center w-100 my-5 p-4"><p>Nenhum carro encontrado nesta categoria.</p></div>');
          $('.car-slide.active').append(noItemsMsg);
        }
      });
      
      // Inicializar o carrossel
      navegarParaSlide(1);
      
      console.log('Carrossel reescrito com sucesso - solução de emergência aplicada');
    });
  }
});
</script>
</body>
</html>