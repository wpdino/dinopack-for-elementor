/**
 * Newsletter Widget Frontend JavaScript
 * 
 * @package DinoPack
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Newsletter Widget Handler
     */
    var DinopackNewsletter = {
        
        /**
         * Initialize newsletter functionality
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind event handlers
         */
        bindEvents: function() {
            $(document).on('submit', '.dinopack-newsletter-form', this.handleSubmit);
            $(document).on('click', '.dinopack-newsletter-test-btn', this.testConnection);
        },
        
        /**
         * Handle newsletter form submission
         */
        handleSubmit: function(e) {
            var $form = $(this);
            var $button = $form.find('.dinopack-newsletter-button');
            var $message = $form.find('.dinopack-newsletter-message');
            var $email = $form.find('input[type="email"]');
            
            // Check if this is a MailChimp AJAX form
            var widgetId = $form.data('widget-id');
            var listId = $form.data('list-id');
            var isConfigured = $form.data('configured');
            
            // If not configured, let the form submit normally (external form)
            if (!isConfigured) {
                return; // Don't prevent default, let external form submit
            }
            
            // Prevent default for AJAX form
            e.preventDefault();
            
            // Get form data
            var formData = {
                action: 'dinopack_newsletter_subscribe',
                email: $email.val(),
                widget_id: widgetId,
                list_id: listId,
                merge_fields: $form.data('merge-fields') || {},
                nonce: dinopack_newsletter_ajax.nonce
            };
            
            // Validate email
            if (!formData.email) {
                DinopackNewsletter.showMessage($message, 'Please enter your email address.', 'error');
                return;
            }
            
            if (!DinopackNewsletter.isValidEmail(formData.email)) {
                DinopackNewsletter.showMessage($message, 'Please enter a valid email address.', 'error');
                return;
            }
            
            // Show loading state
            DinopackNewsletter.setLoadingState($button, true);
            DinopackNewsletter.hideMessage($message);
            
            // Make AJAX request
            $.ajax({
                url: dinopack_newsletter_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    DinopackNewsletter.setLoadingState($button, false);
                    
                    if (response.success) {
                        DinopackNewsletter.showMessage($message, response.data.message, 'success');
                        $form[0].reset(); // Reset form
                    } else {
                        DinopackNewsletter.showMessage($message, response.data || 'An error occurred. Please try again.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    DinopackNewsletter.setLoadingState($button, false);
                    DinopackNewsletter.showMessage($message, 'Network error. Please try again.', 'error');
                }
            });
        },
        
        /**
         * Test MailChimp API connection
         */
        testConnection: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $form = $button.closest('.elementor-widget-dinopack-newsletter');
            var $message = $form.find('.dinopack-newsletter-message');
            
            var apiKey = $form.find('[data-setting="mailchimp_api_key"]').val();
            
            if (!apiKey) {
                DinopackNewsletter.showMessage($message, 'Please enter your MailChimp API key first.', 'error');
                return;
            }
            
            // Show loading state
            DinopackNewsletter.setLoadingState($button, true);
            DinopackNewsletter.hideMessage($message);
            
            // Make AJAX request
            $.ajax({
                url: dinopack_newsletter_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'dinopack_newsletter_test_connection',
                    api_key: apiKey,
                    nonce: dinopack_newsletter_ajax.nonce
                },
                success: function(response) {
                    DinopackNewsletter.setLoadingState($button, false);
                    
                    if (response.success) {
                        DinopackNewsletter.showMessage($message, response.data, 'success');
                    } else {
                        DinopackNewsletter.showMessage($message, response.data || 'Connection failed. Please check your API key.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    DinopackNewsletter.setLoadingState($button, false);
                    DinopackNewsletter.showMessage($message, 'Network error. Please try again.', 'error');
                }
            });
        },
        
        /**
         * Set loading state for button
         */
        setLoadingState: function($button, loading) {
            if (loading) {
                $button.addClass('loading').prop('disabled', true);
                $button.data('original-text', $button.text());
                $button.html('<span class="loading-icon"></span> ' + $button.data('original-text'));
            } else {
                $button.removeClass('loading').prop('disabled', false);
                $button.text($button.data('original-text'));
            }
        },
        
        /**
         * Show message
         */
        showMessage: function($message, text, type) {
            $message.removeClass('success error').addClass(type).text(text).show();
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(function() {
                    DinopackNewsletter.hideMessage($message);
                }, 5000);
            }
        },
        
        /**
         * Hide message
         */
        hideMessage: function($message) {
            $message.hide().removeClass('success error');
        },
        
        /**
         * Validate email format
         */
        isValidEmail: function(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        DinopackNewsletter.init();
    });
    
})(jQuery);