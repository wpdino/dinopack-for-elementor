<?php
/**
 * WooCommerce Products Widget
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
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * WooCommerce Products widget.
 *
 * A WooCommerce products widget for Elementor.
 *
 * @since 1.0.0
 */
class Woo_Products extends Widget_Base {
    /**
     * @var \WP_Query
     */
    private $query = null;

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-woo-products',
            DINOPACK_URL . 'inc/elementor/widgets/woo-products/frontend.css',
            ['woocommerce-general', 'woocommerce-smallscreen', 'woocommerce-layout'],
            DINOPACK_VERSION 
        );


        // Enqueue styles in editor
        add_action('elementor/editor/after_enqueue_styles', [$this, 'editor_styles']);
    }

    /**
     * Enqueue editor styles
     *
     * @since 1.0.0
     * @access public
     */
    public function editor_styles() {
        wp_enqueue_style('dinopack-woo-products');
    }

    /**
     * Get widget name.
     *
     * Retrieve the widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'dinopack-woo-products';
    }

    /**
     * Get widget title.
     *
     * Retrieve the widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('WooCommerce Products', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-products';
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     */
    public function get_categories() {
        return ['dinopack-for-elementor'];
    }

    /**
     * Get style dependencies.
     *
     * Returns all the styles the widget depends on.
     *
     * @since 1.0.0
     * @access public
     * @return array Style slugs.
     */
    public function get_style_depends() {
        return ['woocommerce-general', 'woocommerce-smallscreen', 'woocommerce-layout', 'dinopack-woo-products'];
    }

    /**
     * Get script dependencies.
     *
     * Returns all the scripts the widget depends on.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return [];
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
        return ['woocommerce', 'products', 'shop', 'ecommerce'];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        
        // Content Tab
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Products Settings', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'products_source',
            [
                'label' => esc_html__('Products Source', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => esc_html__('All Products', 'dinopack-for-elementor'),
                    'recent' => esc_html__('Recent Products', 'dinopack-for-elementor'),
                    'featured' => esc_html__('Featured Products', 'dinopack-for-elementor'),
                    'sale' => esc_html__('Sale Products', 'dinopack-for-elementor'),
                    'best_selling' => esc_html__('Best Selling', 'dinopack-for-elementor'),
                    'top_rated' => esc_html__('Top Rated', 'dinopack-for-elementor'),
                    'categories' => esc_html__('Specific Categories', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'product_categories',
            [
                'label' => esc_html__('Categories', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_categories(),
                'condition' => [
                    'products_source' => 'categories',
                ],
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => esc_html__('Products Per Page', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 8,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '4',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                ],
            ]
        );



        $this->add_control(
            'show_sale_badge',
            [
                'label' => esc_html__('Show Sale Badge', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Advanced Query Section
        $this->start_controls_section(
            'section_query',
            [
                'label' => esc_html__('Query Settings', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'filter_by',
            [
                'label' => esc_html__('Filter By', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('None', 'dinopack-for-elementor'),
                    'featured' => esc_html__('Featured', 'dinopack-for-elementor'),
                    'sale' => esc_html__('Sale', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'dinopack-for-elementor'),
                    'title' => esc_html__('Title', 'dinopack-for-elementor'),
                    'price' => esc_html__('Price', 'dinopack-for-elementor'),
                    'popularity' => esc_html__('Popularity', 'dinopack-for-elementor'),
                    'rating' => esc_html__('Rating', 'dinopack-for-elementor'),
                    'rand' => esc_html__('Random', 'dinopack-for-elementor'),
                    'menu_order' => esc_html__('Menu Order', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'asc' => esc_html__('ASC', 'dinopack-for-elementor'),
                    'desc' => esc_html__('DESC', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'product_tags',
            [
                'label' => esc_html__('Product Tags', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_product_tags(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label' => esc_html__('Offset', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'description' => esc_html__('Number of products to skip.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'exclude_current',
            [
                'label' => esc_html__('Exclude Current Product', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => '',
                'description' => esc_html__('Exclude current product from the query.', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'pagination',
            [
                'label' => esc_html__('Pagination', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'pagination_position',
            [
                'label' => esc_html__('Pagination Position', 'dinopack-for-elementor'),
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
                'condition' => [
                    'pagination' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-woo-pagination' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Products
        $this->start_controls_section(
            'section_style_products',
            [
                'label' => esc_html__('Products', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'product_spacing',
            [
                'label' => esc_html__('Spacing', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-woo-products-container' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'product_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-woo-product-item',
            ]
        );

        $this->add_control(
            'product_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-woo-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'product_box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-woo-product-item',
            ]
        );

        $this->end_controls_section();



        // Style Tab - Add to Cart Button
        $this->start_controls_section(
            'section_style_button',
            [
                'label' => esc_html__('Add to Cart Button', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .button',
            ]
        );

        $this->start_controls_tabs('button_color_tabs');

        $this->start_controls_tab(
            'button_color_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_color_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Product Title
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Product Title', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .woocommerce-loop-product__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Product Price
        $this->start_controls_section(
            'section_style_price',
            [
                'label' => esc_html__('Product Price', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sale_price_color',
            [
                'label' => esc_html__('Sale Price Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .price ins' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'regular_price_color',
            [
                'label' => esc_html__('Regular Price Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .price del' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Product Rating
        $this->start_controls_section(
            'section_style_rating',
            [
                'label' => esc_html__('Product Rating', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label' => esc_html__('Star Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .star-rating' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_size',
            [
                'label' => esc_html__('Star Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 30,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 16,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .star-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Product Container
        $this->start_controls_section(
            'section_style_container',
            [
                'label' => esc_html__('Product Container', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'container_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product',
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        // Style Tab - Sale Badge
        $this->start_controls_section(
            'section_style_sale_badge',
            [
                'label' => esc_html__('Sale Badge', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_sale_badge' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sale_badge_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .onsale',
            ]
        );

        $this->add_control(
            'sale_badge_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .onsale' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sale_badge_text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .onsale' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'sale_badge_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .onsale',
            ]
        );

        $this->add_control(
            'sale_badge_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sale_badge_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce ul.products li.product .onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'sale_badge_box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .woocommerce ul.products li.product .onsale',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get product categories for select control
     *
     * @return array
     */
    private function get_product_categories() {
        $categories = [];
        
        if (function_exists('get_terms')) {
            $terms = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ]);
            
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $categories[$term->term_id] = $term->name;
                }
            }
        }
        
        return $categories;
    }

    /**
     * Get product tags for select control
     *
     * @return array
     */
    private function get_product_tags() {
        $tags = [];
        
        if (function_exists('get_terms')) {
            $terms = get_terms([
                'taxonomy' => 'product_tag',
                'hide_empty' => false,
            ]);
            
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $tags[$term->term_id] = $term->name;
                }
            }
        }
        
        return $tags;
    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Check if WooCommerce is active
        if (!function_exists('WC')) {
            printf(
                '<div class="woocommerce error"><h2>%1$s</h2><p>%2$s</p></div>',
                esc_html__('Error!', 'dinopack-for-elementor'),
                esc_html__('The WooCommerce plugin could not be found. Please install/activate it in order to use this feature.', 'dinopack-for-elementor')
            );
            return;
        }


        // Query products using WP_Query
        $this->query_posts();
        $query = $this->get_query();
        
        if (!$query->have_posts()) {
            echo '<div class="dinopack-woo-no-products">' . esc_html__('No products found.', 'dinopack-for-elementor') . '</div>';
            return;
        }

        // Set up WooCommerce loop
        global $woocommerce_loop;
        $woocommerce_loop['columns'] = (int) $settings['columns'];

        $this->add_render_attribute( 'woo-products-container', 'class', 'dinopack-woo-products-container' );
		$this->add_render_attribute( 'woo-products-container', 'class', 'woocommerce' );
        $this->add_render_attribute( 'woo-products-container', 'class', 'columns-' . esc_attr( $woocommerce_loop['columns'] ) );
        
        // Add sale badge visibility data attribute to widget wrapper
        $this->add_render_attribute( 'wrapper', 'data-show-sale-badge', esc_attr($settings['show_sale_badge']) );
        ?>
        <div <?php $this->print_render_attribute_string('woo-products-container'); ?>>
            <?php
            // Start WooCommerce product loop
            woocommerce_product_loop_start();
            
            while ($query->have_posts()) : $query->the_post();
                wc_get_template_part('content', 'product');
            endwhile;
            
            // End WooCommerce product loop
            woocommerce_product_loop_end();
            ?>
        </div>
        
        <?php
        // Display pagination if enabled
        if ($settings['pagination'] === 'yes') {
            echo wp_kses_post( $this->get_pagination($query) );
        }
        
        // Reset WooCommerce loop and post data
        woocommerce_reset_loop();
        wp_reset_postdata();
        ?>
        <?php
    }

    /**
     * Query all products using WP_Query
     *
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function query_posts() {
        if (!function_exists('WC')) {
            $this->query = new \WP_Query();
            return;
        }

        $settings = $this->get_settings_for_display();
        global $post;

        $query_args = [
            'post_type' => 'product',
            'posts_per_page' => $settings['products_per_page'],
            'post__not_in' => array(),
        ];

        // Default ordering args
        $ordering_args = WC()->query->get_catalog_ordering_args($settings['orderby'], $settings['order']);
        $query_args['orderby'] = $ordering_args['orderby'];
        $query_args['order'] = $ordering_args['order'];

        // Handle different product sources
        switch ($settings['products_source']) {
            case 'featured':
                $product_visibility_term_ids = wc_get_product_visibility_term_ids();
                if (!empty($product_visibility_term_ids['featured'])) {
                    $query_args['tax_query'][] = [
                        'taxonomy' => 'product_visibility',
                        'field' => 'term_taxonomy_id',
                        'terms' => $product_visibility_term_ids['featured'],
                    ];
                }
                break;
                
            case 'sale':
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                if (!empty($product_ids_on_sale)) {
                    $query_args['post__in'] = $product_ids_on_sale;
                } else {
                    $query_args['post__in'] = [0];
                }
                break;
                
            case 'best_selling':
                $query_args['meta_key'] = 'total_sales';
                $query_args['orderby'] = 'meta_value_num';
                break;
                
            case 'top_rated':
                $query_args['meta_key'] = '_wc_average_rating';
                $query_args['orderby'] = 'meta_value_num';
                break;
                
            case 'categories':
                if (!empty($settings['product_categories'])) {
                    $query_args['tax_query'][] = [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $settings['product_categories'],
                    ];
                }
                break;
                
            case 'recent':
            default:
                $query_args['orderby'] = 'date';
                $query_args['order'] = 'DESC';
                break;
        }

        // Handle filter_by
        if (!empty($settings['filter_by'])) {
            switch ($settings['filter_by']) {
                case 'featured':
                    $product_visibility_term_ids = wc_get_product_visibility_term_ids();
                    if (!empty($product_visibility_term_ids['featured'])) {
                        $query_args['tax_query'][] = [
                            'taxonomy' => 'product_visibility',
                            'field' => 'term_taxonomy_id',
                            'terms' => $product_visibility_term_ids['featured'],
                        ];
                    }
                    break;
                    
                case 'sale':
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    if (!empty($product_ids_on_sale)) {
                        $query_args['post__in'] = $product_ids_on_sale;
                    } else {
                        $query_args['post__in'] = [0];
                    }
                    break;
            }
        }

        // Handle product tags
        if (!empty($settings['product_tags'])) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $settings['product_tags'],
            ];
        }

        // Handle offset
        if (!empty($settings['offset'])) {
            $query_args['offset'] = $settings['offset'];
        }

        // Handle exclude current product
        if ($settings['exclude_current'] === 'yes') {
            $query_args['post__not_in'][] = get_the_ID();
        }

        // Handle pagination
        if ($settings['pagination'] === 'yes') {
            global $paged;
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            $query_args['paged'] = $paged;
        }

        $this->query = new \WP_Query($query_args);
    }

    /**
     * Get the query
     *
     * @since 1.0.0
     * @access public
     * @return \WP_Query The current query.
     */
    public function get_query() {
        return $this->query;
    }

    /**
     * Get query arguments for products
     *
     * @param array $settings
     * @return array
     */
    private function get_query_args($settings) {
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $settings['products_per_page'],
        ];

        // Handle pagination
        if ($settings['pagination'] === 'yes') {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args['paged'] = $paged;
        }

        // Handle offset
        if (!empty($settings['offset']) && $settings['offset'] > 0) {
            $args['offset'] = $settings['offset'];
        }

        // Handle exclude current product
        if ($settings['exclude_current'] === 'yes' && is_singular('product')) {
            global $post;
            $args['post__not_in'] = [$post->ID];
        }

        // Handle product source
        switch ($settings['products_source']) {
            case 'all':
                // Show all products
                break;
                
            case 'featured':
                // Use WooCommerce taxonomy for featured products
                $product_visibility_term_ids = wc_get_product_visibility_term_ids();
                if (!empty($product_visibility_term_ids['featured'])) {
                    $args['tax_query'][] = [
                        'taxonomy' => 'product_visibility',
                        'field' => 'term_taxonomy_id',
                        'terms' => $product_visibility_term_ids['featured'],
                    ];
                }
                break;
                
            case 'sale':
                // Use WooCommerce function to get products on sale
                $product_ids_on_sale = wc_get_product_ids_on_sale();
                if (!empty($product_ids_on_sale)) {
                    $args['post__in'] = $product_ids_on_sale;
                } else {
                    // If no products on sale, return empty result
                    $args['post__in'] = [0];
                }
                break;
                
            case 'best_selling':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
                
            case 'top_rated':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
                
            case 'categories':
                if (!empty($settings['product_categories'])) {
                    $args['tax_query'] = [
                        [
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => $settings['product_categories'],
                        ]
                    ];
                }
                break;
                
            case 'recent':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }

        // Handle additional filter_by
        if (!empty($settings['filter_by'])) {
            switch ($settings['filter_by']) {
                case 'featured':
                    // Use WooCommerce taxonomy for featured products
                    $product_visibility_term_ids = wc_get_product_visibility_term_ids();
                    if (!empty($product_visibility_term_ids['featured'])) {
                        $args['tax_query'][] = [
                            'taxonomy' => 'product_visibility',
                            'field' => 'term_taxonomy_id',
                            'terms' => $product_visibility_term_ids['featured'],
                        ];
                    }
                    break;
                case 'sale':
                    // Use WooCommerce function to get products on sale
                    $product_ids_on_sale = wc_get_product_ids_on_sale();
                    if (!empty($product_ids_on_sale)) {
                        $args['post__in'] = $product_ids_on_sale;
                    } else {
                        // If no products on sale, return empty result
                        $args['post__in'] = [0];
                    }
                    break;
            }
        }

        // Handle product tags
        if (!empty($settings['product_tags'])) {
            $tag_query = [
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $settings['product_tags'],
            ];
            
            if (isset($args['tax_query'])) {
                $args['tax_query']['relation'] = 'AND';
                $args['tax_query'][] = $tag_query;
            } else {
                $args['tax_query'] = [$tag_query];
            }
        }

        // Handle orderby and order
        if (!empty($settings['orderby'])) {
            switch ($settings['orderby']) {
                case 'price':
                    $args['meta_key'] = '_price';
                    $args['orderby'] = 'meta_value_num';
                    break;
                case 'popularity':
                    $args['meta_key'] = 'total_sales';
                    $args['orderby'] = 'meta_value_num';
                    break;
                case 'rating':
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby'] = 'meta_value_num';
                    break;
                default:
                    $args['orderby'] = $settings['orderby'];
                    break;
            }
        }

        if (!empty($settings['order'])) {
            $args['order'] = strtoupper($settings['order']);
        }

        return $args;
    }

    /**
     * Get products based on settings
     *
     * @param array $settings
     * @return array
     */
    private function get_products($settings) {
        $args = $this->get_query_args($settings);

        $query = new \WP_Query($args);
        $products = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                if ($product && $product->is_visible()) {
                    $products[] = $product;
                }
            }
            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Get pagination markup
     *
     * @param \WP_Query $query
     * @return string
     */
    private function get_pagination($query) {
        $pagination = '';
        
        if ($query->max_num_pages > 1) {
            $pagination = paginate_links([
                'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $query->max_num_pages,
                'type' => 'list',
                'prev_text' => '<i class="fas fa-angle-left"></i>',
                'next_text' => '<i class="fas fa-angle-right"></i>',
            ]);
            
            if ($pagination) {
                $pagination = '<div class="dinopack-woo-pagination">' . $pagination . '</div>';
            }
        }
        
        return $pagination;
    }
}
