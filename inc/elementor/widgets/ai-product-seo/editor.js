/**
 * AI Product SEO Widget Editor Script
 * Handles SEO meta generation and updates the controls
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
    elementor.hooks.addAction('panel/open_editor/widget/dinopack-ai-product-seo', function(panel, model, view) {
        currentWidgetModel = model;
    });

    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
        if (model && model.get('widgetType') === 'dinopack-ai-product-seo') {
            currentWidgetModel = model;
        }
    });

    // Wait for Elementor to be ready
    $(window).on('elementor:init', function() {
        // Handle generate button click in the panel
        $(document).on('click', '.dinopack-ai-generate-seo-btn', function(e) {
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
            var seoType = settings.seo_type || 'all';
            var customPrompt = settings.custom_prompt || '';
            var autoSave = settings.auto_save_to_product || 'no';
            
            if (!productId) {
                $status.html('<span style="color: red;">Please select a product first.</span>').show();
                setTimeout(function() {
                    $status.fadeOut();
                }, 3000);
                return;
            }
            
            // Show loading state
            $button.prop('disabled', true);
            $button.find('.eicon-search').replaceWith('<span class="eicon-loading eicon-animation-spin"></span>');
            $status.html('<span style="color: #0073aa;">Generating SEO meta...</span>').show();
            
            // Set loading state in widget
            var settingsModel = widgetModel.get('settings');
            settingsModel.set({
                _is_generating: true
            });
            
            // Check if AJAX data is available
            if (typeof dinopackAjax === 'undefined' || !dinopackAjax.ajaxurl) {
                $status.html('<span style="color: red;">AJAX configuration not loaded. Please refresh the page.</span>').show();
                $button.prop('disabled', false);
                $button.find('.eicon-loading').replaceWith('<span class="eicon-search"></span>');
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
                    action: 'dinopack_generate_product_seo',
                    nonce: dinopackAjax.nonce,
                    product_id: productId,
                    seo_type: seoType,
                    custom_prompt: customPrompt,
                    auto_save: autoSave,
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-search"></span>');
                    
                    if (response.success && response.data) {
                        var seoData = response.data;
                        
                        // Update settings
                        var settings = widgetModel.get('settings');
                        
                        if (!settings) {
                            console.error('Settings object is null!');
                            return;
                        }
                        
                        var updates = {
                            _is_generating: false
                        };
                        
                        if (seoData.seo_title) {
                            updates.seo_title = seoData.seo_title;
                        }
                        if (seoData.meta_description) {
                            updates.meta_description = seoData.meta_description;
                        }
                        if (seoData.focus_keywords) {
                            updates.focus_keywords = seoData.focus_keywords;
                        }
                        
                        // Apply updates
                        settings.set(updates);
                        
                        // Also update via setSetting
                        if (seoData.seo_title) {
                            widgetModel.setSetting('seo_title', seoData.seo_title);
                        }
                        if (seoData.meta_description) {
                            widgetModel.setSetting('meta_description', seoData.meta_description);
                        }
                        if (seoData.focus_keywords) {
                            widgetModel.setSetting('focus_keywords', seoData.focus_keywords);
                        }
                        widgetModel.setSetting('_is_generating', false);
                        widgetModel.trigger('change:settings');
                        
                        // Update control views
                        setTimeout(function() {
                            try {
                                var controlView = null;
                                if (elementor && elementor.panels && elementor.panels.currentView) {
                                    var panelView = elementor.panels.currentView;
                                    if (typeof panelView.getControlView === 'function') {
                                        if (seoData.seo_title) {
                                            controlView = panelView.getControlView('seo_title');
                                            if (controlView && controlView.setValue) {
                                                controlView.setValue(seoData.seo_title);
                                            }
                                        }
                                        if (seoData.meta_description) {
                                            controlView = panelView.getControlView('meta_description');
                                            if (controlView && controlView.setValue) {
                                                controlView.setValue(seoData.meta_description);
                                            }
                                        }
                                        if (seoData.focus_keywords) {
                                            controlView = panelView.getControlView('focus_keywords');
                                            if (controlView && controlView.setValue) {
                                                controlView.setValue(seoData.focus_keywords);
                                            }
                                        }
                                    }
                                }
                                
                                // Try alternative method to get control views
                                if (elementor && elementor.panels) {
                                    var currentPageView = elementor.panels.currentView;
                                    if (currentPageView && currentPageView.children) {
                                        currentPageView.children.each(function(child) {
                                            if (child.model) {
                                                var controlName = child.model.get('name');
                                                if (controlName === 'seo_title' && seoData.seo_title) {
                                                    if (child.setValue) {
                                                        child.setValue(seoData.seo_title);
                                                    }
                                                } else if (controlName === 'meta_description' && seoData.meta_description) {
                                                    if (child.setValue) {
                                                        child.setValue(seoData.meta_description);
                                                    }
                                                } else if (controlName === 'focus_keywords' && seoData.focus_keywords) {
                                                    if (child.setValue) {
                                                        child.setValue(seoData.focus_keywords);
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                                
                                // Fallback: Update controls directly via DOM
                                if (seoData.seo_title) {
                                    var $titleField = $('[data-setting="seo_title"] input[type="text"]');
                                    if ($titleField.length) {
                                        $titleField.val(seoData.seo_title).trigger('input').trigger('change');
                                    }
                                }
                                if (seoData.meta_description) {
                                    var $descField = $('[data-setting="meta_description"] textarea');
                                    if ($descField.length) {
                                        $descField.val(seoData.meta_description).trigger('input').trigger('change');
                                    }
                                }
                                if (seoData.focus_keywords) {
                                    var $keywordsField = $('[data-setting="focus_keywords"] input[type="text"]');
                                    if ($keywordsField.length) {
                                        $keywordsField.val(seoData.focus_keywords).trigger('input').trigger('change');
                                    }
                                }
                            } catch(e) {
                                console.error('Error updating control views:', e);
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
                                                    var currentSeoTitle = settings.get('seo_title');
                                                    var currentMetaDesc = settings.get('meta_description');
                                                    var currentKeywords = settings.get('focus_keywords');
                                                    var showTitle = settings.get('show_title') || 'yes';
                                                    var showDescription = settings.get('show_description') || 'yes';
                                                    var showKeywords = settings.get('show_keywords') || 'yes';
                                                    
                                                    if (currentSeoTitle || currentMetaDesc || currentKeywords) {
                                                        var $seoContent = $element.find('.dinopack-ai-seo-content');
                                                        if ($seoContent.length) {
                                                            // Update SEO title
                                                            if (currentSeoTitle && showTitle === 'yes') {
                                                                var $titleItem = $seoContent.find('.dinopack-ai-seo-title');
                                                                if ($titleItem.length) {
                                                                    var $titleSpan = $titleItem.find('span');
                                                                    if ($titleSpan.length) {
                                                                        $titleSpan.text(currentSeoTitle);
                                                                    } else {
                                                                        $titleItem.find('strong').after('<span>' + currentSeoTitle + '</span>');
                                                                    }
                                                                    var charCount = currentSeoTitle.length;
                                                                    var $charCount = $titleItem.find('.dinopack-char-count');
                                                                    if ($charCount.length) {
                                                                        $charCount.text('(' + charCount + ' characters)');
                                                                    }
                                                                }
                                                            }
                                                            
                                                            // Update meta description
                                                            if (currentMetaDesc && showDescription === 'yes') {
                                                                var $descItem = $seoContent.find('.dinopack-ai-seo-description');
                                                                if ($descItem.length) {
                                                                    var $descP = $descItem.find('p');
                                                                    if ($descP.length) {
                                                                        $descP.text(currentMetaDesc);
                                                                    } else {
                                                                        $descItem.find('strong').after('<p>' + currentMetaDesc + '</p>');
                                                                    }
                                                                    var charCount = currentMetaDesc.length;
                                                                    var $charCount = $descItem.find('.dinopack-char-count');
                                                                    if ($charCount.length) {
                                                                        $charCount.text('(' + charCount + ' characters)');
                                                                    }
                                                                }
                                                            }
                                                            
                                                            // Update keywords
                                                            if (currentKeywords && showKeywords === 'yes') {
                                                                var $keywordsItem = $seoContent.find('.dinopack-ai-seo-keywords');
                                                                if ($keywordsItem.length) {
                                                                    var $keywordsSpan = $keywordsItem.find('span');
                                                                    if ($keywordsSpan.length) {
                                                                        $keywordsSpan.text(currentKeywords);
                                                                    } else {
                                                                        $keywordsItem.find('strong').after('<span>' + currentKeywords + '</span>');
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            // Create SEO content if it doesn't exist
                                                            var seoHtml = '<div class="dinopack-ai-seo-content">';
                                                            if (currentSeoTitle && showTitle === 'yes') {
                                                                seoHtml += '<div class="dinopack-ai-seo-item dinopack-ai-seo-title"><strong>SEO Title:</strong> <span>' + currentSeoTitle + '</span> <small class="dinopack-char-count">(' + currentSeoTitle.length + ' characters)</small></div>';
                                                            }
                                                            if (currentMetaDesc && showDescription === 'yes') {
                                                                seoHtml += '<div class="dinopack-ai-seo-item dinopack-ai-seo-description"><strong>Meta Description:</strong> <p>' + currentMetaDesc + '</p> <small class="dinopack-char-count">(' + currentMetaDesc.length + ' characters)</small></div>';
                                                            }
                                                            if (currentKeywords && showKeywords === 'yes') {
                                                                seoHtml += '<div class="dinopack-ai-seo-item dinopack-ai-seo-keywords"><strong>Focus Keywords:</strong> <span>' + currentKeywords + '</span></div>';
                                                            }
                                                            seoHtml += '</div>';
                                                            
                                                            var $placeholder = $element.find('.dinopack-ai-seo-placeholder');
                                                            if ($placeholder.length) {
                                                                $placeholder.replaceWith(seoHtml);
                                                            } else {
                                                                $element.append(seoHtml);
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
                        var message = '✓ SEO meta generated successfully!';
                        if (seoData.saved_to_product) {
                            message += ' (Saved to product)';
                        }
                        $status.html('<span style="color: green;">' + message + '</span>').show();
                        
                        setTimeout(function() {
                            $status.fadeOut();
                        }, 5000);
                    } else {
                        settingsModel.set({
                            _is_generating: false
                        });
                        
                        var errorMessage = 'Failed to generate SEO meta';
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
                    $button.find('.eicon-loading').replaceWith('<span class="eicon-search"></span>');
                    $status.html('<span style="color: red;">Network error. Please try again.</span>').show();
                    setTimeout(function() {
                        $status.fadeOut();
                    }, 5000);
                }
            });
        });
    });

})(jQuery);

