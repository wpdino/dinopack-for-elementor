<?php
/**
 * AJAX Handlers
 *
 * @package DinoPack
 * @since 1.0.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Handle AJAX load more for blog widget
 */
function dinopack_blog_load_more() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_blog_ajax')) {
        wp_die('Security check failed');
    }

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
    $post_type = isset($_POST['post_type']) ? sanitize_text_field(wp_unslash($_POST['post_type'])) : 'post';
    $order_by = isset($_POST['order_by']) ? sanitize_text_field(wp_unslash($_POST['order_by'])) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : 'DESC';
    
    // Get read more settings
    $read_more_text = isset($_POST['read_more_text']) ? sanitize_text_field(wp_unslash($_POST['read_more_text'])) : 'Read More';
    $read_more_icon = isset($_POST['read_more_icon']) ? sanitize_text_field(wp_unslash($_POST['read_more_icon'])) : '';
    $read_more_icon_position = isset($_POST['read_more_icon_position']) ? sanitize_text_field(wp_unslash($_POST['read_more_icon_position'])) : 'after';
    
    // Get excerpt length
    $excerpt_length = isset($_POST['excerpt_length']) ? intval($_POST['excerpt_length']) : 20;
    
    // Get content display settings
    $show_image = isset($_POST['show_image']) ? sanitize_text_field(wp_unslash($_POST['show_image'])) : 'yes';
    $show_title = isset($_POST['show_title']) ? sanitize_text_field(wp_unslash($_POST['show_title'])) : 'yes';
    $show_meta = isset($_POST['show_meta']) ? sanitize_text_field(wp_unslash($_POST['show_meta'])) : 'yes';
    $show_excerpt = isset($_POST['show_excerpt']) ? sanitize_text_field(wp_unslash($_POST['show_excerpt'])) : 'yes';
    $show_read_more = isset($_POST['show_read_more']) ? sanitize_text_field(wp_unslash($_POST['show_read_more'])) : 'yes';
    
    // Sanitize meta_data array
    $meta_data = isset($_POST['meta_data']) ? array_map('sanitize_text_field', wp_unslash($_POST['meta_data'])) : ['date', 'author'];

    $args = [
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'orderby' => $order_by,
        'order' => $order,
        'ignore_sticky_posts' => 1,
    ];

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        ob_start();
        
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="dinopack-blog-item">
                <?php if ($show_image === 'yes' && has_post_thumbnail()): ?>
                    <div class="dinopack-blog-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="dinopack-blog-content">
                    <?php if ($show_title === 'yes'): ?>
                        <h3 class="dinopack-blog-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    <?php endif; ?>
                    
                    <?php if ($show_meta === 'yes' && !empty($meta_data)): ?>
                        <div class="dinopack-blog-meta">
                            <?php if (in_array('date', $meta_data)): ?>
                                <span class="dinopack-blog-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo esc_html( get_the_date() ); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (in_array('author', $meta_data)): ?>
                                <span class="dinopack-blog-author">
                                    <i class="fas fa-user"></i>
                                    <?php the_author_posts_link(); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (in_array('categories', $meta_data)): ?>
                                <span class="dinopack-blog-category">
                                    <i class="fas fa-folder"></i>
                                    <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (in_array('comments', $meta_data)): ?>
                                <span class="dinopack-blog-comments">
                                    <i class="fas fa-comments"></i>
                                    <?php comments_number('0 Comments', '1 Comment', '% Comments'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_excerpt === 'yes'): ?>
                        <div class="dinopack-blog-excerpt">
                            <?php 
                            $excerpt_length = !empty($excerpt_length) ? $excerpt_length : 20;
                            echo wp_kses_post( wp_trim_words(get_the_excerpt(), $excerpt_length, '...') ); 
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_read_more === 'yes'): ?>
                        <div class="dinopack-blog-read-more">
                            <a href="<?php the_permalink(); ?>" class="dinopack-blog-read-more-btn">
                                <?php 
                                $read_more_text = !empty($read_more_text) ? $read_more_text : 'Read More';
                                $icon_position = !empty($read_more_icon_position) ? $read_more_icon_position : 'after';
                                
                                if ( ! empty( $read_more_icon ) ) {
                                    $icon_html = '<i class="' . esc_attr( $read_more_icon ) . '"></i>';
                                    
                                    if ($icon_position === 'before') {
                                        echo wp_kses_post( $icon_html ) . ' ' . esc_html( $read_more_text );
                                    } else {
                                        echo esc_html($read_more_text) . ' ' . wp_kses_post( $icon_html );
                                    }
                                } else {
                                    echo esc_html($read_more_text);
                                }
                                ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        
        wp_reset_postdata();
        
        $html = ob_get_clean();
        
        wp_send_json_success([
            'html' => $html,
            'has_more' => $query->max_num_pages > $page
        ]);
    } else {
        wp_send_json_error('No more posts found');
    }
}

// Hook AJAX actions
add_action('wp_ajax_dinopack_blog_load_more', 'dinopack_blog_load_more');
add_action('wp_ajax_nopriv_dinopack_blog_load_more', 'dinopack_blog_load_more');

/**
 * Localize script for AJAX
 */
function dinopack_localize_blog_ajax() {
    wp_localize_script('dinopack-blog', 'dinopack_blog_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dinopack_blog_ajax')
    ]);
}

add_action('wp_enqueue_scripts', 'dinopack_localize_blog_ajax');

/**
 * Handle newsletter subscription
 */
function dinopack_newsletter_subscribe() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_newsletter_ajax')) {
        wp_die('Security check failed');
    }

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $widget_id = isset($_POST['widget_id']) ? sanitize_text_field(wp_unslash($_POST['widget_id'])) : '';

    if (empty($email) || empty($widget_id)) {
        wp_send_json_error('Email and widget ID are required');
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email address');
    }

    try {
        // Get API key from admin settings
        $admin_settings = get_option('dinopack_settings', []);
        $api_key = $admin_settings['dinopack_mailchimp_api_key'] ?? '';
        
        // Get list ID from widget (we need to retrieve it from Elementor data)
        // For now, we'll need to pass it differently or get it from the request
        $list_id = isset($_POST['list_id']) ? sanitize_text_field(wp_unslash($_POST['list_id'])) : '';
        
        if (empty($api_key) || empty($list_id)) {
            wp_send_json_error('MailChimp not configured');
        }
        
        // Validate API key format (should contain a dash)
        if (strpos($api_key, '-') === false) {
            wp_send_json_error('Invalid API key format');
        }

        // Include MailChimp API class
        require_once DINOPACK_PATH . 'inc/elementor/widgets/newsletter/class-mailchimp-api.php';
        
        $mailchimp = new \DinoPack_MailChimp_API($api_key);
        
        // Sanitize merge fields using filter_input for better security
        $raw_merge_fields = filter_input( INPUT_POST, 'merge_fields', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        if ( $raw_merge_fields === null ) {
            $raw_merge_fields = array();
        }
        $merge_fields = array();
        if (!empty($raw_merge_fields) && is_array($raw_merge_fields)) {
            foreach ($raw_merge_fields as $key => $value) {
                $merge_fields[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        $result = $mailchimp->subscribe($list_id, $email, $merge_fields);
        
        if ($result) {
            wp_send_json_success([
                'message' => 'Successfully subscribed to newsletter!',
                'data' => $result
            ]);
        } else {
            wp_send_json_error($mailchimp->get_last_error());
        }
        
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

/**
 * Test MailChimp API connection
 */
function dinopack_newsletter_test_connection() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_newsletter_ajax')) {
        wp_die('Security check failed');
    }

    $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    if (empty($api_key)) {
        wp_send_json_error('API key is required');
    }

    try {
        // Include MailChimp API class
        require_once DINOPACK_PATH . 'inc/elementor/widgets/newsletter/class-mailchimp-api.php';
        
        $mailchimp = new \DinoPack_MailChimp_API($api_key);
        
        if ($mailchimp->test_connection()) {
            wp_send_json_success('API connection successful!');
        } else {
            wp_send_json_error($mailchimp->get_last_error());
        }
        
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

// Hook AJAX actions for newsletter
add_action('wp_ajax_dinopack_newsletter_subscribe', 'dinopack_newsletter_subscribe');
add_action('wp_ajax_nopriv_dinopack_newsletter_subscribe', 'dinopack_newsletter_subscribe');

add_action('wp_ajax_dinopack_newsletter_test_connection', 'dinopack_newsletter_test_connection');
add_action('wp_ajax_nopriv_dinopack_newsletter_test_connection', 'dinopack_newsletter_test_connection');

/**
 * Get MailChimp lists
 */
function dinopack_newsletter_get_lists() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_newsletter_ajax')) {
        wp_die('Security check failed');
    }

    $api_key = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

    if (empty($api_key)) {
        wp_send_json_error('API key is required');
    }

    try {
        // Include MailChimp API class
        require_once DINOPACK_PATH . 'inc/elementor/widgets/newsletter/class-mailchimp-api.php';
        
        $mailchimp = new \DinoPack_MailChimp_API($api_key);
        
        $lists = $mailchimp->get_lists(100); // Get up to 100 lists
        
        if ($lists && isset($lists['lists'])) {
            $list_options = array();
            foreach ($lists['lists'] as $list) {
                $list_options[$list['id']] = $list['name'] . ' (' . $list['stats']['member_count'] . ' members)';
            }
            
            wp_send_json_success([
                'lists' => $list_options,
                'message' => 'Lists loaded successfully'
            ]);
        } else {
            wp_send_json_error('No lists found or API error');
        }
        
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

add_action('wp_ajax_dinopack_newsletter_get_lists', 'dinopack_newsletter_get_lists');
add_action('wp_ajax_nopriv_dinopack_newsletter_get_lists', 'dinopack_newsletter_get_lists');

/**
 * Localize script for newsletter AJAX
 */
function dinopack_localize_newsletter_ajax() {
    wp_localize_script('dinopack-newsletter', 'dinopack_newsletter_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dinopack_newsletter_ajax')
    ]);
}

add_action('wp_enqueue_scripts', 'dinopack_localize_newsletter_ajax');

/**
 * Generate AI Product Description
 */
function dinopack_generate_product_description() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_ajax')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(['message' => 'WooCommerce is not installed']);
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $description_type = isset($_POST['description_type']) ? sanitize_text_field(wp_unslash($_POST['description_type'])) : 'full';
    $tone = isset($_POST['tone']) ? sanitize_text_field(wp_unslash($_POST['tone'])) : 'professional';
    $custom_prompt = isset($_POST['custom_prompt']) ? sanitize_textarea_field(wp_unslash($_POST['custom_prompt'])) : '';

    if (empty($product_id)) {
        wp_send_json_error(['message' => 'Product ID is required']);
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(['message' => 'Product not found']);
    }

    // Check if AI Helper class exists
    if (!class_exists('\DinoPack\AI_Helper')) {
        wp_send_json_error(['message' => 'AI Helper class not found']);
    }

    // Build prompt based on product data
    $product_name = $product->get_name();
    $product_price = $product->get_price_html();
    $product_sku = $product->get_sku();
    $product_categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']);
    $product_tags = wp_get_post_terms($product_id, 'product_tag', ['fields' => 'names']);
    $product_attributes = $product->get_attributes();

    $prompt = "Write a " . esc_html($tone) . " product description";
    
    switch ($description_type) {
        case 'short':
            $prompt .= " (2-3 sentences)";
            break;
        case 'features':
            $prompt .= " focusing on key features";
            break;
        case 'benefits':
            $prompt .= " focusing on benefits";
            break;
        default:
            $prompt .= " (comprehensive)";
    }
    
    $prompt .= " for the following product:\n\n";
    $prompt .= "Product Name: " . $product_name . "\n";
    
    if (!empty($product_price)) {
        $prompt .= "Price: " . strip_tags($product_price) . "\n";
    }
    
    if (!empty($product_sku)) {
        $prompt .= "SKU: " . $product_sku . "\n";
    }
    
    if (!empty($product_categories)) {
        $prompt .= "Categories: " . implode(', ', $product_categories) . "\n";
    }
    
    if (!empty($product_tags)) {
        $prompt .= "Tags: " . implode(', ', $product_tags) . "\n";
    }
    
    if (!empty($product_attributes)) {
        $prompt .= "Attributes:\n";
        foreach ($product_attributes as $attribute) {
            $name = wc_attribute_label($attribute->get_name());
            $values = $attribute->get_options();
            $prompt .= "- " . $name . ": " . implode(', ', $values) . "\n";
        }
    }
    
    if (!empty($custom_prompt)) {
        $prompt .= "\nAdditional Instructions: " . $custom_prompt . "\n";
    }
    
    $prompt .= "\nMake the description engaging, SEO-friendly, and compelling for potential customers.";

    // Make AI request
    $response = \DinoPack\AI_Helper::make_request($prompt, ['max_tokens' => 500]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    if (isset($response['content'])) {
        wp_send_json_success(['content' => $response['content']]);
    }

    wp_send_json_error(['message' => 'Failed to generate description']);
}

add_action('wp_ajax_dinopack_generate_product_description', 'dinopack_generate_product_description');
add_action('wp_ajax_nopriv_dinopack_generate_product_description', 'dinopack_generate_product_description');

/**
 * Summarize Product Reviews with AI
 */
function dinopack_summarize_product_reviews() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dinopack_ajax')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(['message' => 'WooCommerce is not installed']);
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $summary_type = isset($_POST['summary_type']) ? sanitize_text_field(wp_unslash($_POST['summary_type'])) : 'overview';
    $max_reviews = isset($_POST['max_reviews']) ? intval($_POST['max_reviews']) : 10;

    if (empty($product_id)) {
        wp_send_json_error(['message' => 'Product ID is required']);
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(['message' => 'Product not found']);
    }

    // Get product reviews
    $args = [
        'post_id' => $product_id,
        'status' => 'approve',
        'number' => min($max_reviews, 50),
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    $comments = get_comments($args);

    if (empty($comments)) {
        wp_send_json_error(['message' => 'No reviews found for this product']);
    }

    // Check if AI Helper class exists
    if (!class_exists('\DinoPack\AI_Helper')) {
        wp_send_json_error(['message' => 'AI Helper class not found']);
    }

    // Build reviews text
    $reviews_text = "Product: " . $product->get_name() . "\n\n";
    $reviews_text .= "Customer Reviews:\n\n";
    
    foreach ($comments as $index => $comment) {
        $rating = get_comment_meta($comment->comment_ID, 'rating', true);
        $reviews_text .= "Review " . ($index + 1) . ":\n";
        if (!empty($rating)) {
            $reviews_text .= "Rating: " . $rating . "/5\n";
        }
        $reviews_text .= "Comment: " . $comment->comment_content . "\n\n";
    }

    // Build prompt based on summary type
    $prompt = "Analyze the following product reviews and provide a ";
    
    switch ($summary_type) {
        case 'pros_cons':
            $prompt .= "summary organized as Pros and Cons. List the main positive points and negative points mentioned by customers.";
            break;
        case 'key_points':
            $prompt .= "summary of key points mentioned in the reviews. Focus on the most frequently mentioned aspects.";
            break;
        case 'sentiment':
            $prompt .= "sentiment analysis. Describe the overall customer sentiment (positive, negative, neutral) and provide insights.";
            break;
        default:
            $prompt .= "comprehensive overview of the reviews. Summarize the main themes, customer satisfaction, and key feedback.";
    }
    
    $prompt .= "\n\n" . $reviews_text;
    $prompt .= "\n\nProvide a clear, well-structured summary that would be helpful for potential customers.";

    // Make AI request
    $response = \DinoPack\AI_Helper::make_request($prompt, ['max_tokens' => 800]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    if (isset($response['content'])) {
        // Format content for pros/cons display
        $content = $response['content'];
        if ($summary_type === 'pros_cons') {
            // Try to format as pros/cons if the AI response contains them
            if (preg_match('/pros?|advantages?|positive/i', $content) && preg_match('/cons?|disadvantages?|negative/i', $content)) {
                // The content likely already has pros/cons structure
                $content = '<div class="pros-cons">' . $content . '</div>';
            }
        }
        
        wp_send_json_success(['content' => $content]);
    }

    wp_send_json_error(['message' => 'Failed to generate summary']);
}

add_action('wp_ajax_dinopack_summarize_product_reviews', 'dinopack_summarize_product_reviews');
add_action('wp_ajax_nopriv_dinopack_summarize_product_reviews', 'dinopack_summarize_product_reviews');

