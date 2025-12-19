/**
 * AI Product Image Widget Editor Script
 * Handles image generation and updates the media control
 */
(function($) {
    'use strict';

    var currentWidgetModel = null;

    /**
     * Get current widget model
     */
    function getCurrentWidgetModel() {
        if (elementor.panels && elementor.panels.currentView) {
            var currentView = elementor.panels.currentView;
            if (currentView.getCurrentModel) {
                var model = currentView.getCurrentModel();
                if (model) {
                    currentWidgetModel = model;
                    return model;
                }
            }
        }
        
        if (elementor.getCurrentElement) {
            var currentElement = elementor.getCurrentElement();
            if (currentElement) {
                var model = currentElement.getEditModel();
                if (model) {
                    currentWidgetModel = model;
                    return model;
                }
            }
        }
        
        return currentWidgetModel;
    }

    // Store the current widget model when panel opens
    elementor.hooks.addAction('panel/open_editor/widget/dinopack-ai-product-image', function(panel, model, view) {
        currentWidgetModel = model;
    });

    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        if (model && model.get('widgetType') === 'dinopack-ai-product-image') {
            currentWidgetModel = model;
        }
    });

    // Wait for Elementor to be ready
    $(window).on('elementor:init', function() {
        // Handle generate button click in the panel
        $(document).on('click', '.dinopack-ai-generate-image-btn', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $status = $button.siblings('.dinopack-ai-generate-status');
            
            var widgetModel = getCurrentWidgetModel();
            
            if (!widgetModel) {
                $status.html('<span style="color: red;">Error: Could not find widget model. Please try again.</span>').show();
                setTimeout(function() {
                    $status.fadeOut();
                }, 5000);
                return;
            }
            
            var settings = widgetModel.get('settings').toJSON();
            var productId = settings.product_id || '';
            var imageStyle = settings.image_style || 'photorealistic';
            var imageSize = settings.image_size || '1024x1024';
            var customPrompt = settings.custom_prompt || '';
            var setAsFeatured = settings.set_as_featured || 'no';
            
            if (!productId) {
                $status.html('<span style="color: red;">Please select a product first.</span>').show();
                setTimeout(function() {
                    $status.fadeOut();
                }, 3000);
                return;
            }
            
            // Show loading state
            $button.prop('disabled', true);
            $button.find('.eicon-image').replaceWith('<span class="eicon-loading eicon-animation-spin"></span>');
            $status.html('<span style="color: #0073aa;">Generating image... This may take 10-30 seconds.</span>').show();
            
            // Set loading state in widget
            var settingsModel = widgetModel.get('settings');
            settingsModel.set({
                _is_generating: true
            });
            
            // Check if AJAX data is available
            if (typeof dinopackAjax === 'undefined' || !dinopackAjax.ajaxurl) {
                $status.html('<span style="color: red;">AJAX configuration not loaded. Please refresh the page.</span>').show();
                $button.prop('disabled', false);
                $button.find('.eicon-loading').replaceWith('<span class="eicon-image"></span>');
                setTimeout(function() {
                    $status.fadeOut();
                }, 5000);
                return;
            }
            
            // Make AJAX request
            $.ajax({
                url: dinopackAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'dinopack_generate_product_image',
                    nonce: dinopackAjax.nonce,
                    product_id: productId,
                    image_style: imageStyle,
                    image_size: imageSize,
                    custom_prompt: customPrompt,
                    set_as_featured: setAsFeatured,
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-image"></span>');
                    
                    if (response.success && response.data && response.data.image_url) {
                        var imageUrl = response.data.image_url;
                        var attachmentId = response.data.attachment_id || '';
                        
                        // Update settings with generated image
                        var settings = widgetModel.get('settings');
                        
                        if (!settings) {
                            console.error('Settings object is null!');
                            return;
                        }
                        
                        var imageData = {
                            url: imageUrl,
                            id: attachmentId
                        };
                        
                        var updates = {
                            generated_image: imageData,
                            _is_generating: false
                        };
                        
                        // Apply updates
                        settings.set(updates);
                        widgetModel.setSetting('generated_image', imageData);
                        widgetModel.setSetting('_is_generating', false);
                        widgetModel.trigger('change:settings');
                        
                        // Update media control view
                        setTimeout(function() {
                            try {
                                var controlView = null;
                                if (elementor && elementor.panels && elementor.panels.currentView) {
                                    var panelView = elementor.panels.currentView;
                                    if (typeof panelView.getControlView === 'function') {
                                        controlView = panelView.getControlView('generated_image');
                                    }
                                }
                                
                                if (controlView) {
                                    // Update control view value
                                    if (controlView.setValue) {
                                        controlView.setValue(imageData);
                                    }
                                    
                                    // For media controls, we may need to trigger specific methods
                                    if (controlView.onSelectedImage) {
                                        controlView.onSelectedImage(imageData);
                                    }
                                    
                                    // Trigger change event
                                    if (controlView.trigger) {
                                        controlView.trigger('change');
                                    }
                                } else {
                                    // Try alternative method to get control view
                                    if (elementor && elementor.panels) {
                                        var currentPageView = elementor.panels.currentView;
                                        if (currentPageView && currentPageView.children) {
                                            currentPageView.children.each(function(child) {
                                                if (child.model && child.model.get('name') === 'generated_image') {
                                                    controlView = child;
                                                    if (controlView.setValue) {
                                                        controlView.setValue(imageData);
                                                    }
                                                    if (controlView.onSelectedImage) {
                                                        controlView.onSelectedImage(imageData);
                                                    }
                                                }
                                            });
                                        }
                                    }
                                    
                                    // Fallback: Update media control directly via DOM
                                    if (!controlView) {
                                        var $field = $('[data-setting="generated_image"]');
                                        if ($field.length) {
                                            // Try to find and update the media control
                                            var $input = $field.find('input[type="hidden"]');
                                            if ($input.length && $input.data('attachment')) {
                                                $input.data('attachment', imageData);
                                            }
                                            
                                            // Trigger change event
                                            $field.trigger('change');
                                        }
                                    }
                                }
                            } catch(e) {
                                console.error('Error updating control view:', e);
                            }
                        }, 100);
                        
                        // Force preview refresh by re-rendering the element view
                        var elementId = widgetModel.get('id');
                        
                        setTimeout(function() {
                            try {
                                // Method 1: Use Elementor's editor.elements to find and re-render
                                if (elementor && elementor.modules && elementor.modules.editor) {
                                    var editor = elementor.modules.editor;
                                    
                                    if (editor.elements && editor.elements.models) {
                                        editor.elements.models.each(function(model) {
                                            if (model.get('id') === elementId) {
                                                var view = editor.elements.getView(model);
                                                if (view) {
                                                    if (typeof view.render === 'function') {
                                                        view.render();
                                                    } else if (view.renderContent) {
                                                        view.renderContent();
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                                
                                // Method 2: Find element in preview iframe and update it
                                var $iframe = $('#elementor-preview-iframe');
                                if ($iframe.length) {
                                    try {
                                        var iframeDoc = $iframe[0].contentDocument || $iframe[0].contentWindow.document;
                                        if (iframeDoc) {
                                            var $iframeBody = $(iframeDoc.body || iframeDoc);
                                            var $element = $iframeBody.find('[data-id="' + elementId + '"]');
                                            if ($element.length) {
                                                // Try to get element view from Elementor's frontend
                                                var elementView = null;
                                                
                                                // Get from elementorFrontend in iframe
                                                if (iframeDoc.defaultView && iframeDoc.defaultView.elementorFrontend) {
                                                    var elementorFrontend = iframeDoc.defaultView.elementorFrontend;
                                                    if (elementorFrontend.elementsHandler && elementorFrontend.elementsHandler.views) {
                                                        var views = elementorFrontend.elementsHandler.views;
                                                        if (views[elementId]) {
                                                            elementView = views[elementId];
                                                        }
                                                    }
                                                }
                                                
                                                // Get from element data
                                                if (!elementView) {
                                                    elementView = $element.data('elementView') || $element.data('view') || $element.data('elementViewInstance');
                                                }
                                                
                                                // Get from widget model's view
                                                if (!elementView && widgetModel.view) {
                                                    elementView = widgetModel.view;
                                                }
                                                
                                                if (elementView) {
                                                    if (typeof elementView.render === 'function') {
                                                        elementView.render();
                                                    } else if (typeof elementView.renderContent === 'function') {
                                                        elementView.renderContent();
                                                    }
                                                } else {
                                                    // Fallback: Manually update the element's HTML
                                                    var currentImage = settings.get('generated_image');
                                                    if (currentImage && currentImage.url) {
                                                        var $imageContainer = $element.find('.dinopack-ai-image-container');
                                                        if ($imageContainer.length) {
                                                            var $img = $imageContainer.find('img');
                                                            if ($img.length) {
                                                                $img.attr('src', currentImage.url);
                                                            } else {
                                                                $imageContainer.html('<img src="' + currentImage.url + '" alt="" />');
                                                            }
                                                        } else {
                                                            // Create image container if it doesn't exist
                                                            var $placeholder = $element.find('.dinopack-ai-image-placeholder');
                                                            if ($placeholder.length) {
                                                                $placeholder.replaceWith('<div class="dinopack-ai-image-container"><img src="' + currentImage.url + '" alt="" /></div>');
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Also trigger model changes
                                                    widgetModel.trigger('change');
                                                    widgetModel.trigger('change:settings');
                                                }
                                            }
                                        }
                                    } catch(e) {
                                        // Cross-origin or other iframe access issues - silently fail
                                    }
                                }
                                
                                // Method 3: Trigger Elementor's change events
                                if (elementor && elementor.channels && elementor.channels.editor) {
                                    elementor.channels.editor.trigger('element:settings:changed', {
                                        model: widgetModel
                                    });
                                }
                            } catch(e) {
                                // Silently fail - manual HTML update should handle it
                            }
                        }, 200);
                        
                        // Show success message
                        $status.html('<span style="color: green;">✓ Image generated successfully!</span>').show();
                        
                        setTimeout(function() {
                            var $imageControl = $button.closest('.elementor-control-section').find('[data-setting="generated_image"]');
                            if ($imageControl.length) {
                                $('html, body').animate({
                                    scrollTop: $imageControl.offset().top - 100
                                }, 500);
                            }
                        }, 500);
                        
                        setTimeout(function() {
                            $status.fadeOut();
                        }, 5000);
                    } else {
                        settingsModel.set({
                            _is_generating: false
                        });
                        
                        var errorMessage = 'Failed to generate image';
                        if (response.data && response.data.message) {
                            errorMessage = response.data.message;
                        } else if (response.data && typeof response.data === 'string') {
                            errorMessage = response.data;
                        }
                        
                        $status.html('<span style="color: red;">Error: ' + errorMessage + '</span>').show();
                        setTimeout(function() {
                            $status.fadeOut();
                        }, 8000); // Show longer for important error messages
                    }
                },
                error: function(xhr, status, error) {
                    var settings = widgetModel.get('settings');
                    settings.set({
                        _is_generating: false
                    });
                    
                    $button.prop('disabled', false);
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-image"></span>');
                    $status.html('<span style="color: red;">Network error. Please try again.</span>').show();
                    setTimeout(function() {
                        $status.fadeOut();
                    }, 5000);
                }
            });
        });
    });

})(jQuery);

