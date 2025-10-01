/**
 * DinoPack Price Table Widget Frontend JS
 */
(function($) {
    'use strict';

    var DinoPriceTable = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            var self = this;
            
            // Handle button hover effects
            $('.dinopack-price-table-button').on('mouseenter', function() {
                $(this).closest('.dinopack-price-table').addClass('button-hovered');
            }).on('mouseleave', function() {
                $(this).closest('.dinopack-price-table').removeClass('button-hovered');
            });
            
            // Handle card hover effects
            $('.dinopack-price-table').on('mouseenter', function() {
                $(this).addClass('is-hovered');
            }).on('mouseleave', function() {
                $(this).removeClass('is-hovered');
            });
            
            // Initialize on Elementor frontend init
            $(window).on('elementor/frontend/init', function() {
                elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-price-table.default', function($scope) {
                    // Widget ready - no animations needed
                });
            });
        }
    };
    
    $(document).ready(function() {
        DinoPriceTable.init();
    });
    
}(jQuery));
