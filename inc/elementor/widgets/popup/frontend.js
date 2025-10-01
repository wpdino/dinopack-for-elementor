/**
 * DinoPack Popup Widget Frontend JS
 */
(function($) {
    'use strict';

    // Popup widget class
    var DinoPackPopupHandler = function($scope) {
        console.log('DinoPackPopupHandler initialized for:', $scope);
        
        var $wrapper = $scope.find('.dinopack-popup-wrapper'),
            $popup = $scope.find('.dinopack-popup'),
            $trigger = $scope.find('.dinopack-popup-trigger'),
            $closeBtn = $scope.find('.dinopack-popup-close'),
            $overlay = $scope.find('.dinopack-popup-overlay'),
            popupSettings = $popup.data('settings') || {},
            popupId = $trigger.data('popup-id');
        
        console.log('Popup elements found:', {
            wrapper: $wrapper.length,
            popup: $popup.length,
            trigger: $trigger.length,
            closeBtn: $closeBtn.length,
            overlay: $overlay.length,
            settings: popupSettings,
            popupId: popupId
        });
        
        // Auto open popup
        if (popupSettings.autoOpen) {
            setTimeout(function() {
                openPopup();
            }, popupSettings.delay);
        }
        
        // Open popup on trigger click
        $trigger.on('click', function(e) {
            console.log('Trigger clicked!');
            e.preventDefault();
            openPopup();
        });
        
        // Close popup on close button click
        if (popupSettings.closeButton) {
            $closeBtn.on('click', function() {
                closePopup();
            });
        }
        
        // Close popup on overlay click
        if (popupSettings.closeOnOverlayClick) {
            $overlay.on('click', function() {
                closePopup();
            });
        }
        
        // Close popup on ESC key press
        if (popupSettings.closeOnEsc) {
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && $popup.hasClass('is-open')) {
                    closePopup();
                }
            });
        }
        
        // Open popup function
        function openPopup() {
            console.log('Opening popup...');
            $popup.addClass('is-open');
            $('body').addClass('dinopack-popup-open');
            console.log('Popup classes added. Popup element:', $popup);
            
            // Trigger animations
            var $content = $popup.find('.dinopack-popup-content');
            var $overlay = $popup.find('.dinopack-popup-overlay');
            
            // Reset animations
            $content.removeClass('dinopack-animation-' + popupSettings.animation);
            $overlay.removeClass('dinopack-animation-' + popupSettings.overlayAnimation);
            
            // Trigger animations with a small delay to ensure DOM is ready
            setTimeout(function() {
                if (popupSettings.animation && popupSettings.animation !== 'none') {
                    $content.addClass('dinopack-animation-' + popupSettings.animation);
                }
                if (popupSettings.overlayAnimation && popupSettings.overlayAnimation !== 'none') {
                    $overlay.addClass('dinopack-animation-' + popupSettings.overlayAnimation);
                }
            }, 10);
            
            // Trigger event when popup is opened
            $(document).trigger('dinopackPopupOpen', [popupId]);
        }
        
        // Close popup function
        function closePopup() {
            $popup.removeClass('is-open');
            $('body').removeClass('dinopack-popup-open');
            
            // Trigger event when popup is closed
            $(document).trigger('dinopackPopupClose', [popupId]);
        }
    };

    // Make sure we run this code under Elementor
    $(window).on('elementor/frontend/init', function() {
        console.log('Elementor frontend initialized, registering popup handler...');
        elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-popup.default', DinoPackPopupHandler);
        console.log('Popup handler registered for: frontend/element_ready/dinopack-popup.default');
    });
    
    // Fallback initialization for when Elementor is not available
    $(document).ready(function() {
        console.log('Document ready, checking for popup widgets...');
        $('.elementor-widget-dinopack-popup').each(function() {
            console.log('Found popup widget, initializing...');
            DinoPackPopupHandler($(this));
        });
    });
    
}(jQuery)); 