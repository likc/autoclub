/**
 * AutoClub - Carrossel Unificado Otimizado
 * Versão: 1.2
 * Este script resolve conflitos entre os arquivos originais e melhora o desempenho
 * Atualizado: Correção para problema de filtro do carrossel
 */

(function($) {
    'use strict';

    // Variáveis globais
    let currentSlide = 1;
    let totalSlides = 0;
    let isCarouselInitialized = false;

    // Inicialização após carregamento do DOM
    $(document).ready(function() {
        // Remover preloader com animação suave
        fadeOutPreloader();
        
        // Inicializar funções gerais do site
        initGeneralFunctions();
        
        // Inicializar componentes específicos se existirem na página
        if ($('.simple-car-slider').length > 0) {
            initSimpleCarousel();
        }
        
        if ($('.car-filter-btn').length > 0) {
            initCarFilters();
        }
        
        if ($('.car-detail-gallery').length > 0) {
            initCarDetailGallery();
        }
        
        if ($('.faq-question, .faq-title').length > 0) {
            initFaqAccordion();
        }
        
        if ($('#contact-form').length > 0) {
            initContactForm();
        }
        
        // Inicializar animações de scroll
        initScrollAnimations();
    });

    // Fade out preloader com animação suave
    function fadeOutPreloader() {
        setTimeout(function() {
            $('#preloder').fadeOut(500, function() {
                $(this).remove();
            });
        }, 300);
    }

    // Inicialização de funções gerais do site
    function initGeneralFunctions() {
        // Menu fixo ao scroll
        $(window).scroll(function() {
            // Header fixo
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
            
            // Animar elementos ao scroll
            animateElementsOnScroll();
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
            
            // Acessibilidade - atualizar aria-expanded
            const isExpanded = $(this).hasClass('active');
            $(this).attr('aria-expanded', isExpanded);
        });
        
        $('.mobile-menu-overlay').click(function() {
            $('.mobile-menu-toggle').removeClass('active').attr('aria-expanded', false);
            $('.mobile-menu').removeClass('active');
            $('.mobile-menu-overlay').removeClass('active');
            $('body').removeClass('overflow-hidden');
        });
        
        // Fechar menu ao clicar em um link
        $('.mobile-menu a').click(function() {
            $('.mobile-menu-toggle').removeClass('active').attr('aria-expanded', false);
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
        
        // Inicializar efeitos de hover
        setupHoverEffects();
    }
    
    // Configurar efeitos de hover para melhor UX
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

    // Inicialização do carrossel simples - corrigido para evitar duplicação
    function initSimpleCarousel() {
        // Prevenir inicialização duplicada
        if (isCarouselInitialized) return;
        
        // Obter o número total de slides
        totalSlides = $('.car-slide').length;
        
        // Se não houver slides, não inicializar
        if (totalSlides === 0) return;
        
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
        
        // Marcar como inicializado
        isCarouselInitialized = true;
        
        console.log('Carrossel inicializado com sucesso: ' + totalSlides + ' slides');
    }

    // Navegação para um slide específico
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
        
        // Scroll suave para o topo da seção se necessário
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

    // Inicialização dos filtros de carros
    function initCarFilters() {
        $('.car-filter-btn').on('click', function() {
            // Remover classe active de todos os botões
            $('.car-filter-btn').removeClass('active');
            
            // Adicionar classe active ao botão clicado
            $(this).addClass('active');
            
            // Obter o filtro
            const filter = $(this).data('filter');
            
            // Aplicar o filtro apropriado baseado na estrutura da página
            filterCars(filter);
        });
    }

    // Filtrar carros baseado na categoria - versão melhorada
    function filterCars(filter) {
        // Se estiver em um carrossel, resetar para o slide 1
        if ($('.car-slide').length > 0) {
            goToSlide(1);
        }
        
        // Remover mensagens anteriores de "nenhum carro encontrado"
        $('.no-cars-found').remove();
        
        // Se for "all" ou "*", mostrar todos os carros
        if (filter === 'all' || filter === '*') {
            $('.car-item').show();
            
            // Garantir que todas as linhas do grid estejam visíveis
            $('.car-grid').show();
            return;
        }
        
        // Ocultar todos os carros primeiro
        $('.car-item').hide();
        
        // Mostrar apenas os da categoria selecionada
        const $filteredItems = $(`.car-item[data-category*="${filter}"]`).show();
        
        // Verificar se há linhas do grid completamente vazias após filtragem
        $('.car-grid').each(function() {
            const $grid = $(this);
            const hasVisibleItems = $grid.find('.car-item:visible').length > 0;
            
            if (hasVisibleItems) {
                $grid.show();
            } else {
                $grid.hide();
            }
        });
        
        // Se nenhum carro for encontrado, mostrar mensagem
        if ($filteredItems.length === 0) {
            let $noItemsMsg = $('<div class="no-cars-found text-center w-100 mt-4"><p>Nenhum carro encontrado nesta categoria.</p></div>');
            $('.car-slide.active').append($noItemsMsg);
        }
    }
    
    // Inicialização da galeria de detalhes do carro
    function initCarDetailGallery() {
        // Versão com Slick (se disponível)
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
        } 
        // Versão fallback sem Slick
        else {
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
    
    // Inicializar o acordeão do FAQ
    function initFaqAccordion() {
        $('.faq-title, .faq-question').on('click', function() {
            const parent = $(this).parent();
            
            if (parent.hasClass('active')) {
                parent.removeClass('active');
            } else {
                // Fechar outros itens abertos
                $('.faq-item').removeClass('active');
                parent.addClass('active');
            }
        });
        
        // Busca no FAQ
        $('#faq-search-input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            if (searchTerm.length > 2) {
                // Esconder todas as categorias e mostrar todos os items
                $('.faq-category-content').addClass('active');
                $('.faq-category').removeClass('active');
                
                // Filtrar os items
                $('.faq-item').each(function() {
                    const questionText = $(this).find('.faq-question span, .faq-title h5').text().toLowerCase();
                    const answerText = $(this).find('.faq-answer, .faq-content').text().toLowerCase();
                    
                    if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else if (searchTerm.length === 0) {
                // Restaurar visualização normal - achar a categoria padrão
                $('.faq-category-content').removeClass('active');
                
                // Tentar encontrar categoria geral, ou pegar a primeira
                const defaultCategory = $('.faq-category[data-category="geral"]').length > 0 ? 
                    'geral' : $('.faq-category').first().data('category');
                
                $(`.faq-category[data-category="${defaultCategory}"]`).addClass('active');
                $(`#${defaultCategory}`).addClass('active');
                
                // Mostrar todos os itens
                $('.faq-item').show();
            }
        });
        
        // Navegação entre categorias
        $('.faq-category').click(function() {
            const category = $(this).data('category');
            
            // Mudar categoria ativa
            $('.faq-category').removeClass('active');
            $(this).addClass('active');
            
            // Mostrar conteúdo da categoria
            $('.faq-category-content').removeClass('active');
            $('#' + category).addClass('active');
            
            // Limpar busca
            $('#faq-search-input').val('');
            
            // Mostrar todos os itens da categoria
            $('.faq-item').show();
        });
    }
    
    // Inicializar formulário de contato
    function initContactForm() {
        $('#contact-form').submit(function(e) {
            e.preventDefault();
            
            let isValid = true;
            const form = $(this);
            
            // Validação básica de campos obrigatórios
            form.find('input, textarea, select').each(function() {
                if ($(this).prop('required') && $(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                    
                    // Adicionar mensagem de erro se não existir
                    if ($(this).next('.error-message').length === 0) {
                        $(this).after('<div class="error-message">Este campo é obrigatório</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                }
            });
            
            // Validação de email
            const emailField = form.find('input[type="email"]');
            if (emailField.length > 0 && emailField.val().trim() !== '') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.val())) {
                    isValid = false;
                    emailField.addClass('is-invalid');
                    
                    if (emailField.next('.error-message').length === 0) {
                        emailField.after('<div class="error-message">Por favor, insira um email válido</div>');
                    }
                }
            }
            
            // Se válido, enviar (simulado)
            if (isValid) {
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.text();
                
                submitBtn.prop('disabled', true).text('Enviando...');
                
                // Esconder mensagens de status anteriores
                $('.form-status').hide();
                
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
        
        // Remover mensagens de erro ao digitar
        $('#contact-form input, #contact-form textarea, #contact-form select').on('input change', function() {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            }
        });
    }
    
    // Inicializar animações de elementos ao scroll
    function initScrollAnimations() {
        // Executar no carregamento
        setTimeout(function() {
            animateElementsOnScroll();
            
            // Animar elementos do hero se existirem
            if ($('.hero__content').length > 0) {
                $('.hero__content h1').addClass('animate-fadeInUp');
                $('.hero__content p').addClass('animate-fadeInUp delay-1');
                $('.hero__buttons').addClass('animate-fadeInUp delay-2');
            }
        }, 500);
    }
    
    // Função para animar elementos durante o scroll
    function animateElementsOnScroll() {
        $('.animate-on-scroll').each(function() {
            const position = $(this).offset().top;
            const scroll = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            if (scroll + windowHeight > position + 100) {
                $(this).addClass('animated');
            }
        });
    }

    // Carregar iniciações após o load completo
    $(window).on('load', function() {
        // Garantir que todas as funções sejam inicializadas corretamente
        if ($('.simple-car-slider').length > 0 && !isCarouselInitialized) {
            initSimpleCarousel();
        }
        
        // Executar animações após carregamento completo
        animateElementsOnScroll();
    });

})(jQuery);