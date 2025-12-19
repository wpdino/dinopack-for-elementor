<?php
/**
 * AI Product SEO Widget
 *
 * @package DinoPack
 * @since 1.0.0
 */

namespace Dinopack\Widgets;

// Exit if accessed directly
defined('ABSPATH') || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AI Product SEO widget.
 *
 * An AI-powered product SEO meta generator widget for Elementor.
 *
 * @since 1.0.0
 */
class Ai_Product_Seo extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-ai-product-seo',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-seo/frontend.css',
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-ai-product-seo',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-seo/frontend.js',
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
        
        // Editor script for Elementor
        wp_register_script(
            'dinopack-ai-product-seo-editor',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-seo/editor.js',
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
        
        // Enqueue editor script in Elementor editor
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_script']);
    }
    
    /**
     * Enqueue editor script for Elementor editor
     */
    public function enqueue_editor_script() {
        wp_enqueue_script('dinopack-ai-product-seo-editor');
        
        // Localize editor script for AJAX
        wp_localize_script(
            'dinopack-ai-product-seo-editor',
            'dinopackAjax',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('dinopack_ajax'),
            ]
        );
    }

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'dinopack-ai-product-seo';
    }

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('AI Product SEO', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-search';
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['dinopack-for-elementor'];
    }

    /**
     * Get style dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Style slugs.
     */
    public function get_style_depends() {
        return ['dinopack-ai-product-seo'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return ['dinopack-ai-product-seo'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['ai', 'product', 'seo', 'woocommerce', 'openai', 'meta', 'dinopack'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product_id',
            [
                'label' => esc_html__('Select Product', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_products_list(),
                'default' => '',
                'description' => esc_html__('Select a WooCommerce product to generate SEO meta for.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'seo_type',
            [
                'label' => esc_html__('SEO Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All (Title, Description, Keywords)', 'dinopack-for-elementor'),
                    'title' => esc_html__('SEO Title Only', 'dinopack-for-elementor'),
                    'description' => esc_html__('Meta Description Only', 'dinopack-for-elementor'),
                    'keywords' => esc_html__('Focus Keywords Only', 'dinopack-for-elementor'),
                ],
                'default' => 'all',
            ]
        );

        $this->add_control(
            'custom_prompt',
            [
                'label' => esc_html__('Custom Instructions', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'placeholder' => esc_html__('Add any specific SEO instructions...', 'dinopack-for-elementor'),
                'description' => esc_html__('Optional: Add custom instructions to guide the AI SEO generation.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'generate_button_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<button type="button" class="dinopack-ai-generate-seo-btn elementor-button elementor-button-default" style="width: 100%; margin-top: 10px;">
                    <span class="eicon-search"></span> ' . esc_html__('Generate SEO Meta', 'dinopack-for-elementor') . '
                </button>
                <div class="dinopack-ai-generate-status" style="margin-top: 10px; display: none;"></div>',
            ]
        );

        // SEO Title
        $this->add_control(
            'seo_title',
            [
                'label' => esc_html__('SEO Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__('Optimized SEO title (50-60 characters recommended).', 'dinopack-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Meta Description
        $this->add_control(
            'meta_description',
            [
                'label' => esc_html__('Meta Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => '',
                'description' => esc_html__('SEO meta description (150-160 characters recommended).', 'dinopack-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Focus Keywords
        $this->add_control(
            'focus_keywords',
            [
                'label' => esc_html__('Focus Keywords', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => esc_html__('Comma-separated list of focus keywords.', 'dinopack-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'auto_save_to_product',
            [
                'label' => esc_html__('Auto-save to Product', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'default' => 'no',
                'description' => esc_html__('Automatically save generated SEO meta to the product.', 'dinopack-for-elementor'),
            ]
        );

        $this->end_controls_section();

        // Display Section
        $this->start_controls_section(
            'display_section',
            [
                'label' => esc_html__('Display', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show SEO Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => esc_html__('Show Meta Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_keywords',
            [
                'label' => esc_html__('Show Focus Keywords', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-seo-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .dinopack-ai-seo-title',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Description Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-seo-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .dinopack-ai-seo-description',
            ]
        );

        $this->add_control(
            'keywords_color',
            [
                'label' => esc_html__('Keywords Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-seo-keywords' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'keywords_typography',
                'selector' => '{{WRAPPER}} .dinopack-ai-seo-keywords',
            ]
        );

        $this->add_responsive_control(
            'spacing',
            [
                'label' => esc_html__('Spacing', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-seo-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-seo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-seo' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .dinopack-ai-product-seo',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-seo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get products list for select control.
     *
     * @since 1.0.0
     * @access private
     *
     * @return array Products list.
     */
    private function get_products_list() {
        $products = array('' => esc_html__('— Select —', 'dinopack-for-elementor'));
        
        if (!class_exists('WooCommerce')) {
            return $products;
        }

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $products[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $product_id = $settings['product_id'];
        $widget_id = $this->get_id();

        // Enqueue scripts
        wp_enqueue_style('dinopack-ai-product-seo');
        wp_enqueue_script('dinopack-ai-product-seo');

        $this->add_render_attribute('wrapper', 'class', 'dinopack-ai-product-seo');
        $this->add_render_attribute('wrapper', 'data-product-id', $product_id);
        $this->add_render_attribute('wrapper', 'data-seo-type', $settings['seo_type']);
        $this->add_render_attribute('wrapper', 'data-custom-prompt', $settings['custom_prompt']);
        $this->add_render_attribute('wrapper', 'data-ajax-url', esc_url(admin_url('admin-ajax.php')));
        $this->add_render_attribute('wrapper', 'data-ajax-nonce', wp_create_nonce('dinopack_ajax'));

        $seo_title = isset($settings['seo_title']) ? $settings['seo_title'] : '';
        $meta_description = isset($settings['meta_description']) ? $settings['meta_description'] : '';
        $focus_keywords = isset($settings['focus_keywords']) ? $settings['focus_keywords'] : '';

        ?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php if (!empty($seo_title) || !empty($meta_description) || !empty($focus_keywords)): ?>
                <div class="dinopack-ai-seo-content" data-widget-id="<?php echo esc_attr($widget_id); ?>">
                    <?php if ($settings['show_title'] === 'yes' && !empty($seo_title)): ?>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-title">
                            <strong><?php echo esc_html__('SEO Title:', 'dinopack-for-elementor'); ?></strong>
                            <span><?php echo esc_html($seo_title); ?></span>
                            <small class="dinopack-char-count"><?php echo esc_html(sprintf(__('(%d characters)', 'dinopack-for-elementor'), strlen($seo_title))); ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_description'] === 'yes' && !empty($meta_description)): ?>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-description">
                            <strong><?php echo esc_html__('Meta Description:', 'dinopack-for-elementor'); ?></strong>
                            <p><?php echo esc_html($meta_description); ?></p>
                            <small class="dinopack-char-count"><?php echo esc_html(sprintf(__('(%d characters)', 'dinopack-for-elementor'), strlen($meta_description))); ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_keywords'] === 'yes' && !empty($focus_keywords)): ?>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-keywords">
                            <strong><?php echo esc_html__('Focus Keywords:', 'dinopack-for-elementor'); ?></strong>
                            <span><?php echo esc_html($focus_keywords); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="dinopack-ai-seo-placeholder">
                    <p class="dinopack-placeholder"><?php echo esc_html__('Use the widget settings to generate AI-powered SEO meta for your product.', 'dinopack-for-elementor'); ?></p>
                </div>
            <?php endif; ?>
            <div class="dinopack-ai-error" style="display: none;"></div>
        </div>
        <?php
    }
    
    /**
     * Render widget output in the editor.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function content_template() {
        ?>
        <#
        var seoTitle = settings.seo_title || '';
        var metaDescription = settings.meta_description || '';
        var focusKeywords = settings.focus_keywords || '';
        var isGenerating = settings._is_generating || false;
        var hasContent = seoTitle || metaDescription || focusKeywords;
        #>
        <div class="dinopack-ai-product-seo">
            <# if (isGenerating) { #>
                <div class="dinopack-ai-loading-state">
                    <div class="dinopack-ai-spinner"></div>
                    <p style="color: #0073aa; text-align: center; margin-top: 15px;"><?php echo esc_html__('Generating SEO meta...', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } else if (hasContent) { #>
                <div class="dinopack-ai-seo-content">
                    <# if (settings.show_title === 'yes' && seoTitle) { #>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-title">
                            <strong><?php echo esc_html__('SEO Title:', 'dinopack-for-elementor'); ?></strong>
                            <span>{{{ seoTitle }}}</span>
                            <small class="dinopack-char-count">({{{ seoTitle.length }}} characters)</small>
                        </div>
                    <# } #>
                    
                    <# if (settings.show_description === 'yes' && metaDescription) { #>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-description">
                            <strong><?php echo esc_html__('Meta Description:', 'dinopack-for-elementor'); ?></strong>
                            <p>{{{ metaDescription }}}</p>
                            <small class="dinopack-char-count">({{{ metaDescription.length }}} characters)</small>
                        </div>
                    <# } #>
                    
                    <# if (settings.show_keywords === 'yes' && focusKeywords) { #>
                        <div class="dinopack-ai-seo-item dinopack-ai-seo-keywords">
                            <strong><?php echo esc_html__('Focus Keywords:', 'dinopack-for-elementor'); ?></strong>
                            <span>{{{ focusKeywords }}}</span>
                        </div>
                    <# } #>
                </div>
            <# } else { #>
                <div class="dinopack-ai-seo-placeholder">
                    <p class="dinopack-placeholder"><?php echo esc_html__('Use the widget settings to generate AI-powered SEO meta for your product.', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } #>
        </div>
        <?php
    }
}

