/**
 * AutoClub - Script principal otimizado
 * Versão: 1.1
 * Este script contém todas as funcionalidades principais do site com correções de bugs
 */

(function($) {
    'use strict';

    // Inicialização após documento pronto
    $(document).ready(function() {
        // Remover preloader após carregamento completo
        setTimeout(function() {
            $('#preloder').fadeOut(500, function() {
                $(this).remove();
            });
        }, 300);

        // Funcionalidades gerais do site
        initGeneralFunctions();
        
        // Inicializar FAQ acordeão (se presente)
        initFaqAccordion();
        
        // Inicializar formulário de contato
        initContactForm();
        
        // Inicializar animações de elementos ao scroll
        initScrollAnimations();
        
        // Inicializar filtros de carros (se presentes na página)
        if ($('.car-filter-btn').length > 0) {
            initCarFilters();
        }
        
        // Inicializar carrossel simples (se presente na página)
        if ($('.simple-car-slider').length > 0) {
            initSimpleCarousel();
        }
        
        // Inicializar galerias de detalhes de carros (se presente na página)
        if ($('.car-detail-gallery').length > 0) {
            initCarDetailGallery();
        }
    });

    // Inicialização de funções gerais do site
    function initGeneralFunctions() {
        // Menu fixo ao scroll
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.header').addClass('sticky');
            } else {
                $('.header').removeClass('sticky');
            }
            
            // Botão Voltar ao Topo
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').addClass('visible');
            } else {
                $('.back-to-top').removeClass('visible');
            }
        });
        
        // Botão Voltar ao Topo - Ação
        $('.back-to-top').click(function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
        
        // Mobile Menu
        $('.mobile-menu-toggle').click(function() {
            $(this).toggleClass('active');
            $('.mobile-menu').toggleClass('active');
            $('.mobile-menu-overlay').toggleClass('active');
            $('body').toggleClass('overflow-hidden');
        });
        
        $('.mobile-menu-overlay').click(function() {
            $('.mobile-menu-toggle').removeClass('active');
            $('.mobile-menu').removeClass('active');
            $('.mobile-menu-overlay').removeClass('active');
            $('body').removeClass('overflow-hidden');
        });
        
        // Fechar menu ao clicar em um link
        $('.mobile-menu a').click(function() {
            $('.mobile-menu-toggle').removeClass('active');
            $('.mobile-menu').removeClass('active');
            $('.mobile-menu-overlay').removeClass('active');
            $('body').removeClass('overflow-hidden');
        });
        
        // Rolagem suave para links âncora
        $('a[href*="#"]:not([href="#"])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                    return false;
                }
            }
        });
        
        // Efeito hover para cards de carros
        setupHoverEffects();
    }
    
    // Configurar efeitos de hover
    function setupHoverEffects() {
        // Efeito hover para cards de carros
        $('.car-card').hover(
            function() {
                $(this).find('.car-card__overlay').css('opacity', '1');
                $(this).find('.car-card__image img').css('transform', 'scale(1.05)');
            },
            function() {
                $(this).find('.car-card__overlay').css('opacity', '0');
                $(this).find('.car-card__image img').css('transform', 'scale(1)');
            }
        );
        
        // Efeito hover para os botões dentro do overlay
        $('.car-card__overlay .btn').hover(
            function(e) {
                e.stopPropagation();
                $(this).css({
                    'background-color': '#d69c1e', 
                    'color': '#ffffff'
                });
            },
            function() {
                $(this).css({
                    'background-color': 'transparent',
                    'color': '#ffffff'
                });
            }
        );
    }
    
    // Inicializar o acordeão do FAQ
    function initFaqAccordion() {
        $('.faq-title, .faq-question').on('click', function() {
            const parent = $(this).parent();
            
            if (parent.hasClass('active')) {
                parent.removeClass('active');
            } else {
                $('.faq-item').removeClass('active');
                parent.addClass('active');
            }
        });
    }
    
    // Inicializar formulário de contato
    function initContactForm() {
        // Verificar se o formulário existe na página
        if ($('#contact-form').length === 0) return;
        
        $('#contact-form').submit(function(e) {
            e.preventDefault();
            
            let isValid = true;
            const form = $(this);
            
            // Validação básica de campos obrigatórios
            form.find('input, textarea').each(function() {
                if ($(this).prop('required') && $(this).val() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Se válido, enviar (simulado)
            if (isValid) {
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.text();
                
                submitBtn.prop('disabled', true).text('Enviando...');
                
                // Simulação de envio
                setTimeout(function() {
                    $('.form-status.success').fadeIn();
                    form[0].reset();
                    
                    setTimeout(function() {
                        $('.form-status.success').fadeOut();
                        submitBtn.prop('disabled', false).text(originalText);
                    }, 3000);
                }, 1500);
            } else {
                $('.form-status.error').fadeIn();
                
                setTimeout(function() {
                    $('.form-status.error').fadeOut();
                }, 3000);
            }
        });
    }
    
    // Inicializar animações de elementos ao scroll
    function initScrollAnimations() {
        function animateElements() {
            $('.animate-on-scroll').each(function() {
                const position = $(this).offset().top;
                const scroll = $(window).scrollTop();
                const windowHeight = $(window).height();
                
                if (scroll + windowHeight > position + 100) {
                    $(this).addClass('animated');
                }
            });
        }
        
        // Executar animações no carregamento e ao rolar
        animateElements();
        $(window).scroll(function() {
            animateElements();
        });
    }
    
    // Inicializar filtros de carros
    function initCarFilters() {
        $('.car-filter-btn').on('click', function() {
            // Remover classe active de todos os botões
            $('.car-filter-btn').removeClass('active');
            
            // Adicionar classe active ao botão clicado
            $(this).addClass('active');
            
            // Obter o filtro
            const filter = $(this).data('filter');
            
            // Aplicar o filtro baseado na estrutura da página
            if ($('.car-filter .mix').length > 0) {
                if (filter === '*' || filter === 'all') {
                    $('.car-filter .mix').show();
                } else {
                    $('.car-filter .mix').hide();
                    $('.car-filter .' + filter).show();
                }
            } else {
                if (filter === '*' || filter === 'all') {
                    $('.car-item').show();
                } else {
                    $('.car-item').hide();
                    $(`.car-item[data-category*="${filter}"]`).show();
                }
            }
        });
    }

    // Inicialização do carrossel simples
    function initSimpleCarousel() {
        // Variáveis globais do carrossel
        let currentSlide = 1;
        let totalSlides = $('.car-slide').length;
        
        // Configurar handlers de clique para navegação
        $('.prev-arrow').on('click', function() {
            if (currentSlide > 1) {
                goToSlide(currentSlide - 1);
            }
        });
        
        $('.next-arrow').on('click', function() {
            if (currentSlide < totalSlides) {
                goToSlide(currentSlide + 1);
            }
        });
        
        // Configurar indicadores
        $('.indicator').on('click', function() {
            const slideNum = $(this).data('slide');
            goToSlide(slideNum);
        });
        
        // Inicializar o estado do carrossel
        updateSliderState();
        
        // Função para navegar para um slide específico
        function goToSlide(slideNum) {
            // Não fazer nada se já estiver no slide solicitado
            if (slideNum === currentSlide) return;
            
            // Esconder todos os slides
            $('.car-slide').removeClass('active');
            
            // Mostrar o slide solicitado
            $(`.car-slide[data-slide="${slideNum}"]`).addClass('active');
            
            // Atualizar slide atual
            currentSlide = slideNum;
            
            // Atualizar UI
            updateSliderState();
            
            // Scroll suave para o topo da seção
            if ($('#carros').length > 0) {
                $('html, body').animate({
                    scrollTop: $('#carros').offset().top - 80
                }, 300);
            }
        }
        
        // Atualizar o estado visual do carrossel
        function updateSliderState() {
            // Atualizar botões de navegação
            $('.prev-arrow').prop('disabled', currentSlide === 1);
            $('.next-arrow').prop('disabled', currentSlide === totalSlides);
            
            // Atualizar indicadores
            $('.indicator').removeClass('active');
            $(`.indicator[data-slide="${currentSlide}"]`).addClass('active');
        }
    }

    // Inicialização da galeria de detalhes do carro
    function initCarDetailGallery() {
        if ($('.car-detail-slider').length > 0 && $.fn.slick) {
            // Slider principal
            $('.car-detail-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                asNavFor: '.car-detail-thumbs'
            });
            
            // Slider de miniaturas
            $('.car-detail-thumbs').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                asNavFor: '.car-detail-slider',
                dots: false,
                focusOnSelect: true,
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });
        } else {
            // Alternativa se o Slick não estiver disponível
            // Exibir a primeira imagem como padrão
            $('.car-detail-slide').first().addClass('active');
            
            // Clicar nas miniaturas alterna a imagem principal
            $('.car-thumb').click(function() {
                const index = $(this).index();
                $('.car-detail-slide').removeClass('active');
                $('.car-detail-slide').eq(index).addClass('active');
                $('.car-thumb').removeClass('active');
                $(this).addClass('active');
            });
        }
        
        // Inicializar tabs na página de detalhes
        $('.car-detail-tabs .tab-nav button').on('click', function() {
            const target = $(this).data('target');
            
            $('.car-detail-tabs .tab-nav button').removeClass('active');
            $(this).addClass('active');
            
            $('.car-detail-tabs .tab-content').removeClass('active');
            $(target).addClass('active');
        });
    }

    // Inicializar um carrossel simples
    $(window).on('load', function() {
        // Iniciar animações após carregamento
        setTimeout(function() {
            $('.hero__content h1').addClass('animate-fadeInUp');
            $('.hero__content p').addClass('animate-fadeInUp delay-1');
            $('.hero__buttons').addClass('animate-fadeInUp delay-2');
        }, 500);
    });
})(jQuery);