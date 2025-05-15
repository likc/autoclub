/**
 * AutoClub - Script principal otimizado para index.php
 * Versão: 2.0
 * Este script combina todas as funcionalidades necessárias sem duplicação
 */

(function($) {
    'use strict';

    // Inicialização após documento pronto
    $(document).ready(function() {
        // Inicializar todas as funcionalidades
        initMobileMenu();
        initScrollEffects();
        initCarFilters();
        initCarCarousel();
        initGeneralFeatures();
    });

    // 1. Menu Mobile
    function initMobileMenu() {
        // Toggle do menu mobile
        $('.mobile-menu-toggle').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle das classes
            $(this).toggleClass('active');
            $('.mobile-menu').toggleClass('active');
            $('.mobile-menu-overlay').toggleClass('active');
            $('body').toggleClass('overflow-hidden');
        });
        
        // Fechar ao clicar no overlay
        $('.mobile-menu-overlay').off('click').on('click', function() {
            closeMobileMenu();
        });
        
        // Fechar ao clicar em um link do menu
        $('.mobile-menu a').off('click').on('click', function() {
            closeMobileMenu();
        });
        
        // Função para fechar o menu
        function closeMobileMenu() {
            $('.mobile-menu-toggle').removeClass('active');
            $('.mobile-menu').removeClass('active');
            $('.mobile-menu-overlay').removeClass('active');
            $('body').removeClass('overflow-hidden');
        }
    }

    // 2. Efeitos de Scroll
    function initScrollEffects() {
        $(window).scroll(function() {
            const scrollTop = $(this).scrollTop();
            
            // Header sticky
            if (scrollTop > 100) {
                $('.header').addClass('sticky');
            } else {
                $('.header').removeClass('sticky');
            }
            
            // Botão voltar ao topo
            if (scrollTop > 300) {
                $('.back-to-top').addClass('visible');
            } else {
                $('.back-to-top').removeClass('visible');
            }
        });
        
        // Ação do botão voltar ao topo
        $('.back-to-top').off('click').on('click', function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
        
        // Scroll suave para âncoras
        $('a[href*="#"]:not([href="#"])').off('click').on('click', function() {
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
    }

    // 3. Filtros de Carros
    function initCarFilters() {
        const $container = $('.simple-car-slider .slides-container');
        const $allItems = $('.car-item').clone(true);
        
        $('.car-filter-btn').off('click').on('click', function() {
            // Atualizar botões
            $('.car-filter-btn').removeClass('active');
            $(this).addClass('active');
            
            const filter = $(this).data('filter');
            
            // Filtrar itens
            let $itemsToShow;
            if (filter === 'all') {
                $itemsToShow = $allItems.clone();
            } else {
                $itemsToShow = $allItems.filter(`[data-category*="${filter}"]`).clone();
            }
            
            // Limpar container
            $container.empty();
            
            // Se não houver itens
            if ($itemsToShow.length === 0) {
                $container.html('<div class="no-cars-found text-center p-5"><p>Nenhum carro encontrado nesta categoria.</p></div>');
                // Desabilitar navegação
                $('.arrow-nav').prop('disabled', true);
                return;
            }
            
            // Reorganizar em grid
            const $newSlide = $('<div class="car-slide active" data-slide="1"></div>');
            let $currentGrid = $('<div class="car-grid row"></div>');
            let itemCount = 0;
            
            $itemsToShow.each(function(index) {
                $currentGrid.append($(this));
                itemCount++;
                
                if (itemCount === 4 && index < $itemsToShow.length - 1) {
                    $newSlide.append($currentGrid);
                    $currentGrid = $('<div class="car-grid row mt-4"></div>');
                    itemCount = 0;
                }
            });
            
            if (itemCount > 0) {
                $newSlide.append($currentGrid);
            }
            
            $container.html($newSlide);
            
            // Atualizar navegação
            if ($itemsToShow.length > 8) {
                $('.arrow-nav').prop('disabled', false);
            } else {
                $('.arrow-nav').prop('disabled', true);
            }
        });
    }

    // 4. Carrossel de Carros (simplificado)
    function initCarCarousel() {
        // Como temos apenas um slide neste momento, 
        // vamos apenas configurar os botões de navegação
        $('.arrow-nav').prop('disabled', true);
        
        // Se no futuro houver múltiplos slides, 
        // a lógica de navegação pode ser adicionada aqui
    }

    // 5. Funcionalidades Gerais
    function initGeneralFeatures() {
        // Hover em cards de carros
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
        
        // Remover preloader se existir
        if ($('#preloder').length > 0) {
            setTimeout(function() {
                $('#preloder').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 300);
        }
    }

})(jQuery);