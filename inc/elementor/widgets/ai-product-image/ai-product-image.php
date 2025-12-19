<?php
/**
 * AI Product Image Widget
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
 * AI Product Image widget.
 *
 * An AI-powered product image generator widget for Elementor.
 *
 * @since 1.0.0
 */
class Ai_Product_Image extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-ai-product-image',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-image/frontend.css',
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-ai-product-image',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-image/frontend.js',
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
        
        // Editor script for Elementor
        wp_register_script(
            'dinopack-ai-product-image-editor',
            DINOPACK_URL . 'inc/elementor/widgets/ai-product-image/editor.js',
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
        wp_enqueue_script('dinopack-ai-product-image-editor');
        
        // Localize editor script for AJAX
        wp_localize_script(
            'dinopack-ai-product-image-editor',
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
        return 'dinopack-ai-product-image';
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
        return esc_html__('AI Product Image', 'dinopack-for-elementor');
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
        return 'eicon-image';
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
        return ['dinopack-ai-product-image'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return ['dinopack-ai-product-image'];
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
        return ['ai', 'product', 'image', 'woocommerce', 'dall-e', 'openai', 'dinopack'];
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
                'description' => esc_html__('Select a WooCommerce product to generate image for.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'image_style',
            [
                'label' => esc_html__('Image Style', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'photorealistic' => esc_html__('Photorealistic', 'dinopack-for-elementor'),
                    'illustration' => esc_html__('Illustration', 'dinopack-for-elementor'),
                    '3d_render' => esc_html__('3D Render', 'dinopack-for-elementor'),
                    'lifestyle' => esc_html__('Lifestyle', 'dinopack-for-elementor'),
                    'product_shot' => esc_html__('Product Shot', 'dinopack-for-elementor'),
                    'minimal' => esc_html__('Minimal', 'dinopack-for-elementor'),
                ],
                'default' => 'photorealistic',
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => esc_html__('Image Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1024x1024' => esc_html__('Square (1024x1024)', 'dinopack-for-elementor'),
                    '1024x1792' => esc_html__('Portrait (1024x1792)', 'dinopack-for-elementor'),
                    '1792x1024' => esc_html__('Landscape (1792x1024)', 'dinopack-for-elementor'),
                ],
                'default' => '1024x1024',
            ]
        );

        $this->add_control(
            'custom_prompt',
            [
                'label' => esc_html__('Custom Instructions', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'placeholder' => esc_html__('Add any specific instructions for the image generation...', 'dinopack-for-elementor'),
                'description' => esc_html__('Optional: Add custom instructions to guide the AI image generation.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'generate_button_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => '<button type="button" class="dinopack-ai-generate-image-btn elementor-button elementor-button-default" style="width: 100%; margin-top: 10px;">
                    <span class="eicon-image"></span> ' . esc_html__('Generate Image', 'dinopack-for-elementor') . '
                </button>
                <div class="dinopack-ai-generate-status" style="margin-top: 10px; display: none;"></div>',
            ]
        );

        // Media control for generated image
        $this->add_control(
            'generated_image',
            [
                'label' => esc_html__('Generated Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                    'id' => '',
                ],
                'description' => esc_html__('AI-generated image will appear here. You can set it as the product featured image.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'set_as_featured',
            [
                'label' => esc_html__('Set as Product Featured Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'default' => 'no',
                'description' => esc_html__('Automatically set the generated image as the product featured image.', 'dinopack-for-elementor'),
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

        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Width', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Height', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'object_fit',
            [
                'label' => esc_html__('Object Fit', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'cover' => esc_html__('Cover', 'dinopack-for-elementor'),
                    'contain' => esc_html__('Contain', 'dinopack-for-elementor'),
                    'fill' => esc_html__('Fill', 'dinopack-for-elementor'),
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'scale-down' => esc_html__('Scale Down', 'dinopack-for-elementor'),
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-image img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_align',
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
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-image' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .dinopack-ai-product-image img',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-ai-product-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-ai-product-image img',
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
        wp_enqueue_style('dinopack-ai-product-image');
        wp_enqueue_script('dinopack-ai-product-image');

        // Get generated image
        $generated_image = isset($settings['generated_image']) && !empty($settings['generated_image']['url']) ? $settings['generated_image'] : null;

        $this->add_render_attribute('wrapper', 'class', 'dinopack-ai-product-image');
        $this->add_render_attribute('wrapper', 'data-product-id', $product_id);
        $this->add_render_attribute('wrapper', 'data-image-style', $settings['image_style']);
        $this->add_render_attribute('wrapper', 'data-image-size', $settings['image_size']);
        $this->add_render_attribute('wrapper', 'data-custom-prompt', $settings['custom_prompt']);
        $this->add_render_attribute('wrapper', 'data-ajax-url', esc_url(admin_url('admin-ajax.php')));
        $this->add_render_attribute('wrapper', 'data-ajax-nonce', wp_create_nonce('dinopack_ajax'));

        ?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php if ($generated_image && !empty($generated_image['url'])): ?>
                <div class="dinopack-ai-image-container" data-widget-id="<?php echo esc_attr($widget_id); ?>">
                    <img src="<?php echo esc_url($generated_image['url']); ?>" alt="<?php echo esc_attr($settings['product_id'] ? get_the_title($settings['product_id']) : ''); ?>" />
                </div>
            <?php else: ?>
                <div class="dinopack-ai-image-placeholder">
                    <?php
                    if (!empty($product_id) && class_exists('WooCommerce')) {
                        $product = wc_get_product($product_id);
                        if ($product) {
                            $featured_image = $product->get_image_id();
                            if ($featured_image) {
                                echo wp_get_attachment_image($featured_image, 'large');
                            } else {
                                echo '<p class="dinopack-placeholder">' . esc_html__('Use the widget settings to generate an AI-powered product image.', 'dinopack-for-elementor') . '</p>';
                            }
                        } else {
                            echo '<p class="dinopack-placeholder">' . esc_html__('Please select a product and use the widget settings to generate image.', 'dinopack-for-elementor') . '</p>';
                        }
                    } else {
                        echo '<p class="dinopack-placeholder">' . esc_html__('Please select a product and use the widget settings to generate image.', 'dinopack-for-elementor') . '</p>';
                    }
                    ?>
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
        var generatedImage = settings.generated_image || {};
        var imageUrl = generatedImage.url || '';
        var isGenerating = settings._is_generating || false;
        #>
        <div class="dinopack-ai-product-image">
            <# if (isGenerating) { #>
                <div class="dinopack-ai-loading-state">
                    <div class="dinopack-ai-spinner"></div>
                    <p style="color: #0073aa; text-align: center; margin-top: 15px;"><?php echo esc_html__('Generating image...', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } else if (imageUrl) { #>
                <div class="dinopack-ai-image-container">
                    <img src="{{ imageUrl }}" alt="" />
                </div>
            <# } else { #>
                <div class="dinopack-ai-image-placeholder">
                    <p class="dinopack-placeholder"><?php echo esc_html__('Use the widget settings to generate an AI-powered product image.', 'dinopack-for-elementor'); ?></p>
                </div>
            <# } #>
        </div>
        <?php
    }
}

