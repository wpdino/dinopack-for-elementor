/**
 * DinoPack Button Widget Frontend JS
 */
(function($) {
    'use strict';

    // Button widget class
    var DinoPackButtonHandler = function($scope) {
        var $button = $scope.find('.dinopack-button');
        
        // Add click animation
        $button.on('click', function() {
            var $this = $(this);
            
            // Add a small scale animation
            $this.css('transform', 'scale(0.95)');
            
            setTimeout(function() {
                $this.css('transform', 'scale(1)');
            }, 200);
        });
        
        // Initialize any additional functionality here
    };

    // Make sure we run this code under Elementor
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-button.default', DinoPackButtonHandler);
    });
    
}(jQuery)); 