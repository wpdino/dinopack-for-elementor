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
        
        // Sanitize merge fields
        $merge_fields = isset($_POST['merge_fields']) ? sanitize_text_field( wp_unslash($_POST['merge_fields'] ) ) : array();
        $sanitized_merge_fields = array();
        if (!empty($merge_fields) && is_array($merge_fields)) {
            foreach ($merge_fields as $key => $value) {
                $sanitized_merge_fields[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        $result = $mailchimp->subscribe($list_id, $email, $sanitized_merge_fields);
        
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
