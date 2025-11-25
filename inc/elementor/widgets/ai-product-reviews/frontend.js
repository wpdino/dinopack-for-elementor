/**
 * AI Product Reviews Widget JavaScript
 */
(function($) {
    'use strict';

    // Check if dinopackAjax is available, if not define it
    if (typeof dinopackAjax === 'undefined') {
        window.dinopackAjax = {
            ajaxurl: typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php',
            nonce: ''
        };
    }

    $(document).ready(function() {
        // Handle generate button click
        $(document).on('click', '.dinopack-generate-summary-btn', function(e) {
            e.preventDefault();
            generateSummary($(this));
        });

        // Auto-generate on load if enabled
        $('.dinopack-ai-product-reviews[data-auto-generate="yes"]').each(function() {
            const $widget = $(this);
            const $btn = $widget.find('.dinopack-generate-summary-btn');
            if ($btn.length) {
                setTimeout(function() {
                    generateSummary($btn);
                }, 500);
            }
        });

        function generateSummary($btn) {
            const $widget = $btn.closest('.dinopack-ai-product-reviews');
            const $summary = $widget.find('.dinopack-ai-review-summary');
            const $error = $widget.find('.dinopack-ai-error');
            
            const productId = $widget.data('product-id');
            const summaryType = $widget.data('summary-type');
            const maxReviews = $widget.data('max-reviews');
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
                    action: 'dinopack_summarize_product_reviews',
                    nonce: ajaxNonce,
                    product_id: productId,
                    summary_type: summaryType,
                    max_reviews: maxReviews,
                    widget_id: widgetId
                },
                success: function(response) {
                    if (response.success && response.data.content) {
                        $summary.html(response.data.content);
                        $summary.removeClass('dinopack-placeholder');
                    } else {
                        const errorMsg = response.data && response.data.message 
                            ? response.data.message 
                            : 'Failed to generate summary. Please try again.';
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
        }
    });
})(jQuery);

