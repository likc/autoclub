/**
 * AutoClub - Script principal para página Shaken
 * Versão: 1.0
 * Otimizado especificamente para a página de Shaken
 */

(function($) {
    'use strict';

    // Inicialização após documento pronto
    $(document).ready(function() {
        // Inicializar todas as funcionalidades
        initMobileMenu();
        initScrollEffects();
        initFaqAccordion();
        initAnimations();
    });

    // 1. Menu Mobile
    function initMobileMenu() {
        $('.mobile-menu-toggle').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $(this).toggleClass('active');
            $('.mobile-menu').toggleClass('active');
            $('.mobile-menu-overlay').toggleClass('active');
            $('body').toggleClass('overflow-hidden');
        });
        
        $('.mobile-menu-overlay').off('click').on('click', function() {
            closeMobileMenu();
        });
        
        $('.mobile-menu a').off('click').on('click', function() {
            closeMobileMenu();
        });
        
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

    // 3. FAQ Accordion
    function initFaqAccordion() {
        $('.faq-title').off('click').on('click', function() {
            const $parent = $(this).parent();
            
            if ($parent.hasClass('active')) {
                $parent.removeClass('active');
                $parent.find('.faq-content').slideUp(300);
            } else {
                // Fechar outras FAQs
                $('.faq-item.active').removeClass('active');
                $('.faq-item .faq-content').slideUp(300);
                
                // Abrir a FAQ clicada
                $parent.addClass('active');
                $parent.find('.faq-content').slideDown(300);
            }
        });
    }

    // 4. Animações
    function initAnimations() {
        // Animações ao scroll
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
        
        // Executar no carregamento
        animateElements();
        
        // Executar ao rolar
        $(window).scroll(function() {
            animateElements();
        });
    }

})(jQuery);