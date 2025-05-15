/**
 * AutoClub - Correção do Filtro do Carrossel
 * Versão: 1.4
 * Esta versão corrige o problema de layout ao filtrar categorias
 */

(function($) {
    'use strict';

    // Variáveis globais
    let currentSlide = 1;
    let totalSlides = 0;
    let isCarouselInitialized = false;

    // Inicialização após carregamento do DOM
    $(document).ready(function() {
        // Inicializar componentes
        initCarouselAndFilters();
        initGeneralFunctions();
    });

    // Função principal de inicialização do carrossel e filtros
    function initCarouselAndFilters() {
        // Configurar filtros primeiro
        $('.car-filter-btn').on('click', function() {
            // Remover classe active de todos os botões
            $('.car-filter-btn').removeClass('active');
            
            // Adicionar classe active ao botão clicado
            $(this).addClass('active');
            
            // Obter o filtro
            const filter = $(this).data('filter');
            
            // Aplicar filtro
            applyFilter(filter);
        });

        // Depois inicializar o carrossel se necessário
        if ($('.simple-car-slider').length > 0) {
            initCarousel();
        }
    }

    // Aplicar filtro de forma simplificada
    function applyFilter(filter) {
        // Se for "all", mostrar todos
        if (filter === 'all' || filter === '*') {
            // Mostrar todos os carros
            $('.car-item').show();
        } else {
            // Ocultar todos primeiro
            $('.car-item').hide();
            
            // Mostrar apenas os que correspondem ao filtro
            $(`.car-item[data-category*="${filter}"]`).show();
        }
        
        // Reorganizar o grid após aplicar o filtro
        reorganizeCarGrid();
    }

    // Reorganizar o grid mantendo 4 colunas
    function reorganizeCarGrid() {
        // Verificar cada slide
        $('.car-slide').each(function() {
            const $slide = $(this);
            const $allItems = $slide.find('.car-item');
            const $visibleItems = $allItems.filter(':visible');
            
            // Se não houver itens visíveis, esconder o slide inteiro
            if ($visibleItems.length === 0) {
                $slide.hide();
                return;
            } else {
                $slide.show();
            }
            
            // Criar um novo container temporário
            const $tempContainer = $('<div>');
            
            // Mover todos os itens visíveis para o container temporário
            $visibleItems.each(function() {
                $tempContainer.append($(this).detach());
            });
            
            // Limpar todas as car-grids existentes
            $slide.find('.car-grid').empty();
            
            // Obter ou criar as car-grids necessárias
            let $grids = $slide.find('.car-grid');
            const itemsPerRow = 4;
            const rowsNeeded = Math.ceil($visibleItems.length / itemsPerRow);
            
            // Garantir que temos o número correto de grids
            if ($grids.length < rowsNeeded) {
                for (let i = $grids.length; i < rowsNeeded; i++) {
                    const $newGrid = $('<div class="car-grid row"></div>');
                    if (i > 0) {
                        $newGrid.addClass('mt-4');
                    }
                    $slide.append($newGrid);
                }
                $grids = $slide.find('.car-grid');
            }
            
            // Distribuir os itens nas grids
            let itemIndex = 0;
            $grids.each(function(gridIndex) {
                const $grid = $(this);
                
                // Adicionar até 4 itens nesta grid
                for (let i = 0; i < itemsPerRow && itemIndex < $visibleItems.length; i++) {
                    const $item = $visibleItems.eq(itemIndex);
                    $grid.append($item);
                    itemIndex++;
                }
                
                // Se a grid estiver vazia, removê-la
                if ($grid.children().length === 0) {
                    $grid.remove();
                }
            });
        });
        
        // Atualizar o estado do carrossel
        updateCarouselAfterFilter();
    }

    // Atualizar o carrossel após o filtro
    function updateCarouselAfterFilter() {
        // Contar slides visíveis
        const $visibleSlides = $('.car-slide:visible');
        totalSlides = $visibleSlides.length;
        
        // Se não houver slides visíveis
        if (totalSlides === 0) {
            // Criar mensagem de "nenhum carro encontrado"
            const $noResults = $('<div class="no-cars-found text-center w-100 my-5 p-4"><p>Nenhum carro encontrado nesta categoria.</p></div>');
            $('.slides-container').append($noResults);
            
            // Desabilitar navegação
            $('.prev-arrow, .next-arrow').prop('disabled', true);
            $('.slider-indicators').hide();
            return;
        }
        
        // Remover mensagens anteriores
        $('.no-cars-found').remove();
        $('.slider-indicators').show();
        
        // Se o slide atual não estiver visível, ir para o primeiro visível
        if (!$(`.car-slide[data-slide="${currentSlide}"]`).is(':visible')) {
            currentSlide = parseInt($visibleSlides.first().data('slide'));
        }
        
        // Atualizar indicadores
        updateSliderState();
    }

    // Inicializar carrossel
    function initCarousel() {
        if (isCarouselInitialized) return;
        
        totalSlides = $('.car-slide').length;
        
        if (totalSlides === 0) return;
        
        // Navegação
        $('.prev-arrow').on('click', function() {
            navigateToPreviousSlide();
        });
        
        $('.next-arrow').on('click', function() {
            navigateToNextSlide();
        });
        
        // Indicadores
        $('.indicator').on('click', function() {
            const slideNum = $(this).data('slide');
            goToSlide(slideNum);
        });
        
        updateSliderState();
        isCarouselInitialized = true;
    }

    // Navegar para o slide anterior visível
    function navigateToPreviousSlide() {
        const $visibleSlides = $('.car-slide:visible');
        const currentIndex = $visibleSlides.index($(`.car-slide[data-slide="${currentSlide}"]`));
        
        if (currentIndex > 0) {
            const $prevSlide = $visibleSlides.eq(currentIndex - 1);
            goToSlide(parseInt($prevSlide.data('slide')));
        }
    }

    // Navegar para o próximo slide visível
    function navigateToNextSlide() {
        const $visibleSlides = $('.car-slide:visible');
        const currentIndex = $visibleSlides.index($(`.car-slide[data-slide="${currentSlide}"]`));
        
        if (currentIndex < $visibleSlides.length - 1) {
            const $nextSlide = $visibleSlides.eq(currentIndex + 1);
            goToSlide(parseInt($nextSlide.data('slide')));
        }
    }

    // Ir para um slide específico
    function goToSlide(slideNum) {
        // Esconder todos os slides
        $('.car-slide').removeClass('active');
        
        // Mostrar o slide solicitado
        $(`.car-slide[data-slide="${slideNum}"]`).addClass('active');
        
        // Atualizar slide atual
        currentSlide = slideNum;
        
        // Atualizar UI
        updateSliderState();
    }

    // Atualizar estado do slider
    function updateSliderState() {
        const $visibleSlides = $('.car-slide:visible');
        const currentIndex = $visibleSlides.index($(`.car-slide[data-slide="${currentSlide}"]`));
        
        // Atualizar botões
        $('.prev-arrow').prop('disabled', currentIndex === 0);
        $('.next-arrow').prop('disabled', currentIndex === $visibleSlides.length - 1);
        
        // Atualizar indicadores
        $('.indicator').removeClass('active');
        $(`.indicator[data-slide="${currentSlide}"]`).addClass('active');
    }

    // Inicializar funcionalidades gerais
    function initGeneralFunctions() {
        // Menu sticky
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.header').addClass('sticky');
            } else {
                $('.header').removeClass('sticky');
            }
            
            // Botão voltar ao topo
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').addClass('visible');
            } else {
                $('.back-to-top').removeClass('visible');
            }
        });
        
        // Botão voltar ao topo
        $('.back-to-top').click(function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
        
        // Mobile menu
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
        
        // Hover effects
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
    }

})(jQuery);