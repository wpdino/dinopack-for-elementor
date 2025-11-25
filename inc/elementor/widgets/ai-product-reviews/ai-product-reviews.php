<?php
/**
 * AI Product Reviews Widget
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
 * AI Product Reviews widget.
 *
 * An AI-powered product review summarizer widget for Elementor.
 *
 * @since 1.0.0
 */
class Ai_Product_Reviews extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-ai-product-reviews',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-reviews/frontend.css',
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-ai-product-reviews',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-reviews/frontend.js',
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
        
        // Editor script for Elementor
        wp_register_script(
            'dinopack-ai-product-reviews-editor',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-reviews/editor.js',
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
        wp_enqueue_script('dinopack-ai-product-reviews-editor');
        
        // Localize editor script for AJAX
        wp_localize_script(
            'dinopack-ai-product-reviews-editor',
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
        return 'dinopack-ai-product-reviews';
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
        return esc_html__('AI Product Reviews', 'dinopack-for-elementor');
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
        return 'eicon-star';
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
        return ['dinopack-ai-product-reviews'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return ['dinopack-ai-product-reviews'];
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
        return ['ai', 'product', 'reviews', 'woocommerce', 'openai', 'gpt', 'summary', 'dinopack'];
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
                'description' => esc_html__('Select a WooCommerce product to summarize reviews for.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'summary_type',
            [
                'label' => esc_html__('Summary Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'overview' => esc_html__('Overview', 'dinopack-for-elementor'),
                    'pros_cons' => esc_html__('Pros & Cons', 'dinopack-for-elementor'),
                    'key_points' => esc_html__('Key Points', 'dinopack-for-elementor'),
                    'sentiment' => esc_html__('Sentiment Analysis', 'dinopack-for-elementor'),
                ],
                'default' => 'overview',
            ]
        );

        $this->add_control(
            'max_reviews',
            [
                'label' => esc_html__('Max Reviews to Analyze', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 1,
                'max' => 50,
                'description' => esc_html__('Maximum number of reviews to analyze (1-50).', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'generate_button_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<button type="button" class="dinopack-ai-generate-summary-btn elementor-button elementor-button-default" style="width: 100%; margin-top: 10px;">
                    <span class="eicon-edit"></span> ' . esc_html__('Summarize Reviews', 'dinopack-for-elementor') . '
                </button>
                <div class="dinopack-ai-generate-status" style="margin-top: 10px; display: none;"></div>',
            ]
        );

        // WYSIWYG Editor for generated content
        $this->add_control(
            'generated_content',
            [
                'label' => esc_html__('Generated Summary', 'dinopack-for-elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => '',
                'description' => esc_html__('AI-generated review summary will appear here. You can edit it directly.', 'dinopack-for-elementor'),
                'dynamic' => [
                    'active' => true,
                ],
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
            'text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-review-summary' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .dinopack-ai-review-summary',
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => esc_html__('Alignment', 'dinopack-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-review-summary' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-review-summary' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .dinopack-ai-review-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .dinopack-ai-review-summary',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-review-summary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Style Section
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__('Button Style', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_generate_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-generate-summary-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-generate-summary-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .dinopack-generate-summary-btn',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-generate-summary-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .dinopack-generate-summary-btn',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-generate-summary-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        wp_enqueue_style('dinopack-ai-product-reviews');
        wp_enqueue_script('dinopack-ai-product-reviews');

        // Get generated content from WYSIWYG control
        $generated_content = isset($settings['generated_content']) ? $settings['generated_content'] : '';

        $this->add_render_attribute('wrapper', 'class', 'dinopack-ai-product-reviews');
        $this->add_render_attribute('wrapper', 'data-product-id', $product_id);
        $this->add_render_attribute('wrapper', 'data-summary-type', $settings['summary_type']);
        $this->add_render_attribute('wrapper', 'data-max-reviews', $settings['max_reviews']);
        $this->add_render_attribute('wrapper', 'data-ajax-url', esc_url(admin_url('admin-ajax.php')));
        $this->add_render_attribute('wrapper', 'data-ajax-nonce', wp_create_nonce('dinopack_ajax'));

        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <div class="dinopack-ai-review-summary" data-widget-id="<?php echo esc_attr($widget_id); ?>">
                <?php if (!empty($generated_content)): ?>
                    <div class="elementor-widget-container" 
                         data-elementor-setting-key="generated_content"
                         data-elementor-inline-editing-toolbar="basic">
                        <?php echo wp_kses_post($generated_content); ?>
                    </div>
                <?php else: ?>
                    <p class="dinopack-placeholder"><?php echo esc_html__('Use the widget settings to generate an AI-powered review summary.', 'dinopack-for-elementor'); ?></p>
                <?php endif; ?>
            </div>
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
        var generatedContent = settings.generated_content || '';
        var isGenerating = settings._is_generating || false;
        #>
        <div class="dinopack-ai-product-reviews">
            <# if (isGenerating) { #>
                <div class="dinopack-ai-loading-state">
                    <div class="dinopack-ai-spinner"></div>
                    <p style="color: #0073aa; text-align: center; margin-top: 15px;"><?php echo esc_html__('Summarizing reviews...', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } else if (generatedContent) { #>
                <div class="dinopack-ai-review-summary">
                    <div class="elementor-widget-container" 
                         data-elementor-setting-key="generated_content"
                         data-elementor-inline-editing-toolbar="basic">
                        {{{ generatedContent }}}
                    </div>
                </div>
            <# } else { #>
                <div class="dinopack-ai-review-summary">
                    <p class="dinopack-placeholder"><?php echo esc_html__('Use the widget settings to generate an AI-powered review summary.', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } #>
        </div>
        <?php
    }
}

