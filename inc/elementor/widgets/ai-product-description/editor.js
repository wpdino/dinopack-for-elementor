/**
 * AI Product Description Widget Editor Script
 * Handles content generation and updates the WYSIWYG control
 */
(function($) {
    'use strict';

    var currentWidgetModel = null;

    /**
     * Helper function to update Elementor control and refresh preview
     */
    function updateElementorControl(controlName, value, widgetModel) {
        widgetModel = widgetModel || currentWidgetModel;
        
        if (!widgetModel) {
            console.warn('Widget model not available for control update');
            return;
        }
        
        try {
            var settings = widgetModel.get('settings');
            var updates = {};
            updates[controlName] = value;
            settings.set(updates);
        } catch(e) {
            console.log('Error updating model:', e);
        }
        
        // Update TinyMCE if available
        setTimeout(function() {
            try {
                var $field = $('[data-setting="' + controlName + '"]');
                var $input = $field.find('textarea, input[type="text"]');
                
                if ($input.length) {
                    var editorId = $input.attr('id');
                    
                    if (typeof tinymce !== 'undefined' && editorId) {
                        var editor = tinymce.get(editorId);
                        if (editor) {
                            editor.setContent(value, { format: 'raw' });
                            editor.save();
                            editor.fire('change');
                        }
                    }
                    
                    $input.val(value);
                    $input.trigger('input').trigger('change');
                }
            } catch(e) {
                console.log('Error updating control field:', e);
            }
        }, 50);
        
        // Trigger preview refresh
        setTimeout(function() {
            try {
                if (elementor && elementor.channels && elementor.channels.editor) {
                    elementor.channels.editor.trigger('element:settings:changed');
                }
            } catch(e) {
                // Silently fail
            }
        }, 100);
    }

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
    elementor.hooks.addAction('panel/open_editor/widget/dinopack-ai-product-description', function(panel, model, view) {
        currentWidgetModel = model;
    });

    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        if (model && model.get('widgetType') === 'dinopack-ai-product-description') {
            currentWidgetModel = model;
        }
    });

    // Wait for Elementor to be ready
    $(window).on('elementor:init', function() {
        // Handle generate button click in the panel
        $(document).on('click', '.dinopack-ai-generate-description-btn', function(e) {
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
            var descriptionType = settings.description_type || 'full';
            var tone = settings.tone || 'professional';
            var customPrompt = settings.custom_prompt || '';
            
            if (!productId) {
                $status.html('<span style="color: red;">Please select a product first.</span>').show();
                setTimeout(function() {
                    $status.fadeOut();
                }, 3000);
                return;
            }
            
            // Show loading state
            $button.prop('disabled', true);
            $button.find('.eicon-edit').replaceWith('<span class="eicon-loading eicon-animation-spin"></span>');
            $status.html('<span style="color: #0073aa;">Generating description...</span>').show();
            
            // Set loading state in widget
            var settingsModel = widgetModel.get('settings');
            settingsModel.set({
                _is_generating: true
            });
            
            // Check if AJAX data is available
            if (typeof dinopackAjax === 'undefined' || !dinopackAjax.ajaxurl) {
                $status.html('<span style="color: red;">AJAX configuration not loaded. Please refresh the page.</span>').show();
                $button.prop('disabled', false);
                $button.find('.eicon-loading').replaceWith('<span class="eicon-edit"></span>');
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
                    action: 'dinopack_generate_product_description',
                    nonce: dinopackAjax.nonce,
                    product_id: productId,
                    description_type: descriptionType,
                    tone: tone,
                    custom_prompt: customPrompt,
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-edit"></span>');
                    
                    if (response.success && response.data && response.data.content) {
                        var content = response.data.content;
                        
                        // Clean up content
                        content = content.replace(/&nbsp;/g, ' ');
                        content = content.replace(/\s+/g, ' ');
                        content = content.replace(/>\s+</g, '><');
                        
                        // Update settings using set() method
                        var settings = widgetModel.get('settings');
                        
                        if (!settings) {
                            console.error('Settings object is null!');
                            return;
                        }
                        
                        var updates = {
                            generated_content: content,
                            _is_generating: false
                        };
                        
                        // Apply updates - this automatically triggers Elementor's change detection and preview refresh
                        settings.set(updates);
                        
                        // Also update via setSetting to ensure it's saved
                        widgetModel.setSetting('generated_content', content);
                        widgetModel.setSetting('_is_generating', false);
                        
                        // Trigger change:settings to ensure preview refreshes
                        widgetModel.trigger('change:settings');
                        
                        // For WYSIWYG controls, we also need to update the control view so TinyMCE shows the content
                        // This must happen AFTER settings.set() so the control view picks up the new value
                        setTimeout(function() {
                            try {
                                // Get control view
                                var controlView = null;
                                if (elementor && elementor.panels && elementor.panels.currentView) {
                                    var panelView = elementor.panels.currentView;
                                    if (typeof panelView.getControlView === 'function') {
                                        controlView = panelView.getControlView('generated_content');
                                    }
                                }
                                
                                if (controlView) {
                                    // Update TinyMCE first if available
                                    if (controlView.editor && controlView.editor.setContent) {
                                        controlView.editor.setContent(content, { format: 'raw' });
                                        controlView.editor.save();
                                    }
                                    
                                    // Then update control view value - this should trigger preview refresh
                                    if (controlView.setValue) {
                                        controlView.setValue(content);
                                    }
                                    
                                    // Fire change event on TinyMCE
                                    if (controlView.editor && controlView.editor.fire) {
                                        setTimeout(function() {
                                            controlView.editor.fire('change');
                                        }, 50);
                                    }
                                } else {
                                    // Try alternative method to get control view
                                    if (elementor && elementor.panels) {
                                        var currentPageView = elementor.panels.currentView;
                                        if (currentPageView && currentPageView.children) {
                                            currentPageView.children.each(function(child) {
                                                if (child.model && child.model.get('name') === 'generated_content') {
                                                    controlView = child;
                                                    if (controlView.setValue) {
                                                        controlView.setValue(content);
                                                    }
                                                }
                                            });
                                        }
                                    }
                                    
                                    // Fallback: Update TinyMCE directly
                                    if (!controlView) {
                                        var $field = $('[data-setting="generated_content"] textarea');
                                        if ($field.length) {
                                            var editorId = $field.attr('id');
                                            if (typeof tinymce !== 'undefined' && editorId) {
                                                var editor = tinymce.get(editorId);
                                                if (editor) {
                                                    editor.setContent(content, { format: 'raw' });
                                                    editor.save();
                                                    editor.fire('change');
                                                }
                                            } else {
                                                $field.val(content).trigger('input').trigger('change');
                                            }
                                        }
                                    }
                                }
                            } catch(e) {
                                console.error('Error updating control view:', e);
                            }
                        }, 100);
                        
                        // Force preview to refresh by re-rendering the element view
                        // This is needed for content_template() widgets
                        var elementId = widgetModel.get('id');
                        
                        // Use Elementor's built-in method to refresh the element
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
                                                    // This ensures the preview updates even if view isn't found
                                                    var currentContent = settings.get('generated_content');
                                                    if (currentContent) {
                                                        var $contentDiv = $element.find('.dinopack-ai-description');
                                                        if ($contentDiv.length) {
                                                            $contentDiv.html(currentContent);
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
                        $status.html('<span style="color: green;">✓ Description generated successfully!</span>').show();
                        
                        setTimeout(function() {
                            var $contentControl = $button.closest('.elementor-control-section').find('[data-setting="generated_content"]');
                            if ($contentControl.length) {
                                $('html, body').animate({
                                    scrollTop: $contentControl.offset().top - 100
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
                        
                        $status.html('<span style="color: red;">Error: ' + (response.data?.message || 'Failed to generate description') + '</span>').show();
                        setTimeout(function() {
                            $status.fadeOut();
                        }, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    var settings = widgetModel.get('settings');
                    settings.set({
                        _is_generating: false
                    });
                    
                    $button.prop('disabled', false);
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-edit"></span>');
                    $status.html('<span style="color: red;">Network error. Please try again.</span>').show();
                    setTimeout(function() {
                        $status.fadeOut();
                    }, 5000);
                }
            });
        });
    });

})(jQuery);

