/**
 * DinoPack Icon Box Widget Frontend JS
 */
(function($) {
    'use strict';

    // Icon Box widget class
    var DinoPackIconBoxHandler = function($scope) {
        var $iconBox = $scope.find('.dinopack-icon-box');
        
        // Add hover effects or additional functionality here
        $iconBox.on('mouseenter', function() {
            $(this).addClass('dinopack-icon-box-hover');
        }).on('mouseleave', function() {
            $(this).removeClass('dinopack-icon-box-hover');
        });
        
        // Initialize any additional functionality
    };

    // Make sure we run this code under Elementor
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-icon-box.default', DinoPackIconBoxHandler);
    });
    
}(jQuery)); 