/**
 * AI Product Description Widget JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        $(document).on('click', '.dinopack-generate-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const $widget = $btn.closest('.dinopack-ai-product-description');
            const $description = $widget.find('.dinopack-ai-description');
            const $error = $widget.find('.dinopack-ai-error');
            
            const productId = $widget.data('product-id');
            const descriptionType = $widget.data('description-type');
            const tone = $widget.data('tone');
            const customPrompt = $widget.data('custom-prompt');
            const widgetId = $btn.data('widget-id');
            const ajaxUrl = $widget.data('ajax-url') || (typeof dinopackAjax !== 'undefined' ? dinopackAjax.ajaxurl : '');
            const ajaxNonce = $widget.data('ajax-nonce') || (typeof dinopackAjax !== 'undefined' ? dinopackAjax.nonce : '');
            
            // Check if AJAX data is available
            if (!ajaxUrl || !ajaxNonce) {
                $error.text('AJAX configuration not loaded. Please refresh the page.').show();
                return;
            }
            
            // Disable button and show loader
            $btn.prop('disabled', true);
            $btn.find('.dinopack-btn-text').hide();
            $btn.find('.dinopack-btn-loader').show();
            $error.hide();
            
            // Make AJAX request
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dinopack_generate_product_description',
                    nonce: ajaxNonce,
                    product_id: productId,
                    description_type: descriptionType,
                    tone: tone,
                    custom_prompt: customPrompt,
                    widget_id: widgetId
                },
                success: function(response) {
                    if (response.success && response.data.content) {
                        $description.html(response.data.content);
                        $description.removeClass('dinopack-placeholder');
                    } else {
                        const errorMsg = response.data && response.data.message 
                            ? response.data.message 
                            : 'Failed to generate description. Please try again.';
                        $error.text(errorMsg).show();
                    }
                },
                error: function() {
                    $error.text('An error occurred. Please check your AI settings and try again.').show();
                },
                complete: function() {
                    // Re-enable button and hide loader
                    $btn.prop('disabled', false);
                    $btn.find('.dinopack-btn-text').show();
                    $btn.find('.dinopack-btn-loader').hide();
                }
            });
        });
    });
})(jQuery);

