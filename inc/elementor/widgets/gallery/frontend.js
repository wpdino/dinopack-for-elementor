/**
 * DinoPack Gallery Widget Frontend JS
 */
(function($) {
    'use strict';

    // Gallery widget class
    var DinoPackGalleryHandler = function($scope) {
        var $gallery = $scope.find('.dinopack-gallery-container');
        
        // Initialize GLightbox for this gallery
        initGLightbox();
        
        // Initialize GLightbox
        function initGLightbox() {
            // Don't initialize lightbox in Elementor editor mode
            var isEditorMode = false;
            
            // Check various ways Elementor indicates editor mode
            if (typeof elementor !== 'undefined') {
                if (elementor.config && elementor.config.environmentMode && elementor.config.environmentMode.edit) {
                    isEditorMode = true;
                } else if (elementor.config && elementor.config.environmentMode && elementor.config.environmentMode.edit === true) {
                    isEditorMode = true;
                } else if (window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode()) {
                    isEditorMode = true;
                } else if (document.body.classList.contains('elementor-editor-active')) {
                    isEditorMode = true;
                }
            }
            
            if (isEditorMode) {
                console.log('Lightbox disabled in Elementor editor mode');
                return;
            }
            
            // Check if GLightbox is available
            if (typeof GLightbox !== 'undefined') {
                // Get lightbox settings from data attribute
                var lightboxSettings = {};
                var settingsData = $gallery.attr('data-lightbox-settings');
                
                if (settingsData) {
                    try {
                        lightboxSettings = JSON.parse(settingsData);
                    } catch (e) {
                        console.log('Error parsing lightbox settings:', e);
                    }
                }
                
                // Default settings
                var defaultSettings = {
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                    autoplayVideos: false,
                    closeButton: true,
                    closeOnOutsideClick: true,
                    escKey: true,
                    keyboardNavigation: true,
                    preload: true,
                    oneSlidePerClick: false,
                    touchFollowAxis: true,
                    skin: 'clean',
                    width: '90vw',
                    height: '90vh',
                    zoomable: false,
                    draggable: false,
                    slideEffect: 'slide',
                    openEffect: 'fade',
                    closeEffect: 'fade',
                    cssEfects: {
                        fade: { in: 'fadeIn', out: 'fadeOut' }
                    }
                };
                
                // Merge user settings with defaults
                var finalSettings = $.extend({}, defaultSettings, lightboxSettings);
                
                // Convert boolean strings to actual booleans
                ['loop', 'keyboardNavigation', 'touchNavigation', 'closeOnOutsideClick', 'zoomable', 'draggable', 'preload'].forEach(function(key) {
                    if (typeof finalSettings[key] === 'string') {
                        finalSettings[key] = finalSettings[key] === 'yes';
                    }
                });
                
                // Initialize GLightbox with merged settings
                const lightbox = GLightbox(finalSettings);

                console.log('GLightbox initialized for gallery with settings:', finalSettings);
            } else {
                console.log('GLightbox library not found');
            }
        }
    };

    // Initialize on Elementor frontend
    $(window).on('elementor/frontend/init', function() {
        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/dinopack-gallery.default', DinoPackGalleryHandler);
        }
    });
    
    // Fallback initialization for non-Elementor pages
    $(document).ready(function() {
        $('.elementor-widget-dinopack-gallery').each(function() {
            DinoPackGalleryHandler($(this));
        });
    });
    
}(jQuery));