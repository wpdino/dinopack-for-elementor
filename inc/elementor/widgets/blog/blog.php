<?php
/**
 * Blog Widget
 *
 * @package DinoPack
 * @since 1.0.0
 */

namespace DinoPack\Widgets;

// Exit if accessed directly
defined('ABSPATH') || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Blog widget.
 *
 * A blog widget for Elementor.
 *
 * @since 1.0.0
 */
class Blog extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        // Register and enqueue styles for both frontend and editor
        wp_register_style(
            'dinopack-blog',
            DINOPACK_URL . 'inc/elementor/widgets/blog/frontend.css',
            [],
            DINOPACK_VERSION
        );

        // Register and enqueue scripts for AJAX load more
        wp_register_script(
            'dinopack-blog',
            DINOPACK_URL . 'inc/elementor/widgets/blog/frontend.js',
            ['jquery', 'elementor-frontend'],
            DINOPACK_VERSION,
            true
        );

        // Register editor script
        wp_register_script(
            'dinopack-blog-editor',
            DINOPACK_URL . 'inc/elementor/widgets/blog/editor.js',
            ['jquery', 'elementor-frontend'],
            DINOPACK_VERSION,
            true
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
        wp_enqueue_style('dinopack-blog');
    }

    /**
     * Get widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     */
    public function get_name() {
        return 'dinopack-blog';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Blog', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-posts-grid';
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
        return ['dinopack-blog'];
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
        return ['dinopack-blog', 'dinopack-blog-editor'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['blog', 'posts', 'article', 'grid', 'dinopack'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        // Content Tab
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Blog Settings', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'dinopack-for-elementor'),
                    'list' => esc_html__('List', 'dinopack-for-elementor'),
                    'masonry' => esc_html__('Masonry', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-blog-layout-',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
                'condition' => [
                    'layout!' => ['list'],
                ],
            ]
        );

        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Post Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'post',
                'options' => $this->get_post_types(),
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => esc_html__('Order By', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'dinopack-for-elementor'),
                    'title' => esc_html__('Title', 'dinopack-for-elementor'),
                    'rand' => esc_html__('Random', 'dinopack-for-elementor'),
                    'comment_count' => esc_html__('Comment Count', 'dinopack-for-elementor'),
                    'modified' => esc_html__('Last Modified', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => esc_html__('Descending', 'dinopack-for-elementor'),
                    'ASC' => esc_html__('Ascending', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => esc_html__('Pagination Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'pagination' => esc_html__('Pagination', 'dinopack-for-elementor'),
                    'load_more' => esc_html__('Load More Button', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'pagination_position',
            [
                'label' => esc_html__('Pagination Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'center',
                'options' => [
                    'left' => esc_html__('Left', 'dinopack-for-elementor'),
                    'center' => esc_html__('Center', 'dinopack-for-elementor'),
                    'right' => esc_html__('Right', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'pagination_type' => 'pagination',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_text',
            [
                'label' => esc_html__('Load More Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Load More', 'dinopack-for-elementor'),
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );

        $this->add_control(
            'load_more_position',
            [
                'label' => esc_html__('Load More Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'center',
                'options' => [
                    'left' => esc_html__('Left', 'dinopack-for-elementor'),
                    'center' => esc_html__('Center', 'dinopack-for-elementor'),
                    'right' => esc_html__('Right', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

        // Post Content Section
        $this->start_controls_section(
            'section_post_content',
            [
                'label' => esc_html__('Post Content', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => esc_html__('Show Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => esc_html__('Show Excerpt', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => esc_html__('Excerpt Length', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 25,
                'min' => 0,
                'max' => 200,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_meta',
            [
                'label' => esc_html__('Show Meta', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'meta_data',
            [
                'label' => esc_html__('Meta Data', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['date', 'author'],
                'options' => [
                    'date' => esc_html__('Date', 'dinopack-for-elementor'),
                    'author' => esc_html__('Author', 'dinopack-for-elementor'),
                    'categories' => esc_html__('Categories', 'dinopack-for-elementor'),
                    'comments' => esc_html__('Comments', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_read_more',
            [
                'label' => esc_html__('Show Read More', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label' => esc_html__('Read More Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Read More', 'dinopack-for-elementor'),
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon',
            [
                'label' => esc_html__('Read More Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-arrow-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'read_more_icon_position',
            [
                'label' => esc_html__('Icon Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'after',
                'options' => [
                    'before' => esc_html__('Before Text', 'dinopack-for-elementor'),
                    'after' => esc_html__('After Text', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style - Posts
        $this->start_controls_section(
            'section_style_posts',
            [
                'label' => esc_html__('Posts', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Columns Gap', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-grid' => 'grid-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-blog-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'post_bg_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'post_border',
                'selector' => '{{WRAPPER}} .dinopack-blog-item',
            ]
        );

        $this->add_control(
            'post_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'post_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-blog-item',
            ]
        );

        $this->add_responsive_control(
            'post_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style - Title
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .dinopack-blog-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style - Meta Data
        $this->start_controls_section(
            'section_style_meta',
            [
                'label' => esc_html__('Meta Data', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_meta' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-meta',
            ]
        );

        $this->add_control(
            'meta_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_link_color',
            [
                'label' => esc_html__('Link Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_link_hover_color',
            [
                'label' => esc_html__('Link Hover Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'meta_icon_color',
            [
                'label' => esc_html__('Icon Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_spacing',
            [
                'label' => esc_html__('Spacing Between Items', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta span:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'meta_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style - Read More Button
        $this->start_controls_section(
            'section_style_read_more',
            [
                'label' => esc_html__('Read More Button', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_read_more' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'read_more_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-read-more-btn',
            ]
        );

        $this->start_controls_tabs('read_more_color_tabs');

        $this->start_controls_tab(
            'read_more_color_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'read_more_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'read_more_color_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'read_more_hover_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_hover_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'read_more_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'read_more_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'read_more_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'read_more_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-read-more-btn',
            ]
        );

        $this->add_control(
            'read_more_icon_spacing',
            [
                'label' => esc_html__('Icon Spacing', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-read-more-btn i:first-child' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-blog-read-more-btn i:last-child' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style - Pagination
        $this->start_controls_section(
            'section_style_pagination',
            [
                'label' => esc_html__('Pagination', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'pagination',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-pagination .page-numbers',
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'pagination_hover_color',
            [
                'label' => esc_html__('Hover Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_background_color',
            [
                'label' => esc_html__('Hover Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_current_color',
            [
                'label' => esc_html__('Current Page Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers.current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_current_background_color',
            [
                'label' => esc_html__('Current Page Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-pagination .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-pagination .page-numbers',
            ]
        );

        $this->end_controls_section();

        // Style - Load More Button
        $this->start_controls_section(
            'section_style_load_more',
            [
                'label' => esc_html__('Load More Button', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type' => 'load_more',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'load_more_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn',
            ]
        );

        $this->add_control(
            'load_more_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_hover_color',
            [
                'label' => esc_html__('Hover Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_hover_background_color',
            [
                'label' => esc_html__('Hover Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'load_more_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'load_more_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'load_more_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'load_more_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-blog-load-more .load-more-btn',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get all registered post types
     *
     * @return array
     */
    private function get_post_types() {
        $post_types = get_post_types(['public' => true], 'objects');
        $options = ['post' => 'Post'];

        foreach ($post_types as $post_type) {
            if ($post_type->name !== 'post' && $post_type->name !== 'page' && $post_type->name !== 'attachment') {
                $options[$post_type->name] = $post_type->label;
            }
        }

        return $options;
    }

    /**
     * Get custom excerpt
     *
     * @param int $limit
     * @return string
     */
    private function get_excerpt($limit) {
        $excerpt = get_the_excerpt();
        $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);
        $excerpt = wp_strip_all_tags( $excerpt );
        $excerpt = wp_trim_words( $excerpt, $limit, '...' );
        
        return $excerpt;
    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Handle pagination
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        $args = [
            'post_type' => $settings['post_type'],
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
            'ignore_sticky_posts' => 1,
        ];
        
        // Add pagination if enabled
        if ($settings['pagination_type'] === 'pagination') {
            $args['paged'] = $paged;
        }
        
        $query = new \WP_Query($args);
        
        if (!$query->have_posts()) {
            return;
        }
        
        $layout_class = 'dinopack-blog-' . $settings['layout'];
        $columns_class = '';
        
        if ($settings['layout'] === 'grid') {
            $layout_class .= ' dinopack-blog-grid';
            $columns_class = 'dinopack-blog-columns-' . (isset($settings['columns']) ? $settings['columns'] : '3');
        } elseif ($settings['layout'] === 'masonry') {
            $layout_class .= ' dinopack-blog-masonry';
            $columns_class = 'dinopack-blog-columns-' . (isset($settings['columns']) ? $settings['columns'] : '3');
        }
        
        ?>
        <div class="dinopack-blog-container <?php echo esc_attr($layout_class . ' ' . $columns_class); ?>">
            
            <?php 
            $count = 0;
            while ($query->have_posts()) : 
                $query->the_post(); 
                $count++;
                $post_class = 'dinopack-blog-item';
                
            ?>
                <div class="<?php echo esc_attr($post_class); ?>">
                    
                    <?php if ($settings['show_image'] === 'yes') : ?>
                        <div class="dinopack-blog-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            <?php else : ?>
                                <div class="dinopack-blog-no-image">
                                    <i class="eicon-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="dinopack-blog-content">
                        
                        <?php if ($settings['show_meta'] === 'yes' && !empty($settings['meta_data'])) : ?>
                            <div class="dinopack-blog-meta">
                                <?php if (in_array('date', $settings['meta_data'])) : ?>
                                    <span class="dinopack-blog-date">
                                        <i class="eicon-calendar"></i>
                                        <?php echo esc_html( get_the_date() ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (in_array('author', $settings['meta_data'])) : ?>
                                    <span class="dinopack-blog-author">
                                        <i class="eicon-user-circle-o"></i>
                                        <?php the_author_posts_link(); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (in_array('categories', $settings['meta_data'])) : ?>
                                    <span class="dinopack-blog-categories">
                                        <i class="eicon-folder"></i>
                                        <?php the_category(', '); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (in_array('comments', $settings['meta_data'])) : ?>
                                    <span class="dinopack-blog-comments">
                                        <i class="eicon-comment"></i>
                                        <?php comments_number('0', '1', '%'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($settings['show_title'] === 'yes') : ?>
                            <h3 class="dinopack-blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                        <?php endif; ?>
                        
                        <?php if ($settings['show_excerpt'] === 'yes') : ?>
                            <div class="dinopack-blog-excerpt">
                                <?php echo wp_kses_post( $this->get_excerpt($settings['excerpt_length']) ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($settings['show_read_more'] === 'yes') : ?>
                            <div class="dinopack-blog-read-more">
                                <a href="<?php the_permalink(); ?>" class="dinopack-blog-read-more-btn">
                                    <?php 
                                    $read_more_text = esc_html($settings['read_more_text']);
                                    $icon_position = $settings['read_more_icon_position'];
                                    
                                    if (!empty($settings['read_more_icon']['value'])) {
                                        $icon_html = '<i class="' . esc_attr($settings['read_more_icon']['value']) . '"></i>';
                                        
                                        if ($icon_position === 'before') {
                                            echo wp_kses_post( $icon_html ) . ' ' . esc_html( $read_more_text );
                                        } else {
                                            echo esc_html( $read_more_text ) . ' ' . wp_kses_post( $icon_html );
                                        }
                                    } else {
                                        echo esc_html( $read_more_text );
                                    }
                                    ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <?php
        // Display pagination if enabled
        if ($settings['pagination_type'] === 'pagination') {
            echo wp_kses_post( $this->get_pagination($query) );
        }
        
        // Display load more button if enabled
        if ($settings['pagination_type'] === 'load_more' && $query->max_num_pages > 1) {
            echo wp_kses_post( $this->get_load_more_button($query, $settings) );
        }
        ?>
        
        <?php
        
        wp_reset_postdata();
    }

    /**
     * Get pagination markup
     *
     * @param WP_Query $query
     * @return string
     */
    private function get_pagination($query) {
        $pagination = '';
        
        if ($query->max_num_pages > 1) {
            $pagination = paginate_links([
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'format' => '?paged=%#%',
                'show_all' => false,
                'type' => 'list',
                'end_size' => 2,
                'mid_size' => 1,
                'prev_next' => true,
                'prev_text' => '<i class="fas fa-angle-left"></i>',
                'next_text' => '<i class="fas fa-angle-right"></i>',
                'add_args' => false,
                'add_fragment' => '',
            ]);
            
            if ($pagination) {
                $pagination = '<div class="dinopack-blog-pagination">' . $pagination . '</div>';
            }
        }
        
        return $pagination;
    }

    /**
     * Get load more button markup
     *
     * @param WP_Query $query
     * @param array $settings
     * @return string
     */
    private function get_load_more_button($query, $settings) {
        $load_more_text = !empty($settings['load_more_text']) ? $settings['load_more_text'] : esc_html__('Load More', 'dinopack-for-elementor');
        
        // Get read more settings
        $read_more_text = !empty($settings['read_more_text']) ? $settings['read_more_text'] : 'Read More';
        $read_more_icon = !empty($settings['read_more_icon']['value']) ? $settings['read_more_icon']['value'] : '';
        $read_more_icon_position = !empty($settings['read_more_icon_position']) ? $settings['read_more_icon_position'] : 'after';
        
        // Get excerpt length
        $excerpt_length = !empty($settings['excerpt_length']) ? $settings['excerpt_length'] : 20;
        
        // Get content display settings
        $show_image = !empty($settings['show_image']) ? $settings['show_image'] : 'yes';
        $show_title = !empty($settings['show_title']) ? $settings['show_title'] : 'yes';
        $show_meta = !empty($settings['show_meta']) ? $settings['show_meta'] : 'yes';
        $show_excerpt = !empty($settings['show_excerpt']) ? $settings['show_excerpt'] : 'yes';
        $show_read_more = !empty($settings['show_read_more']) ? $settings['show_read_more'] : 'yes';
        $meta_data = !empty($settings['meta_data']) ? $settings['meta_data'] : ['date', 'author'];
        
        $button = '<div class="dinopack-blog-load-more">';
        $button .= '<button class="load-more-btn" data-page="1" data-max-pages="' . $query->max_num_pages . '" data-posts-per-page="' . $settings['posts_per_page'] . '" data-post-type="' . $settings['post_type'] . '" data-order-by="' . $settings['order_by'] . '" data-order="' . $settings['order'] . '" data-read-more-text="' . esc_attr($read_more_text) . '" data-read-more-icon="' . esc_attr($read_more_icon) . '" data-read-more-icon-position="' . esc_attr($read_more_icon_position) . '" data-excerpt-length="' . esc_attr($excerpt_length) . '" data-show-image="' . esc_attr($show_image) . '" data-show-title="' . esc_attr($show_title) . '" data-show-meta="' . esc_attr($show_meta) . '" data-show-excerpt="' . esc_attr($show_excerpt) . '" data-show-read-more="' . esc_attr($show_read_more) . '" data-meta-data="' . esc_attr(json_encode($meta_data)) . '">';
        $button .= '<span class="load-more-text">' . esc_html($load_more_text) . '</span>';
        $button .= '<span class="loading-icon"></span>';
        $button .= '</button>';
        $button .= '</div>';
        
        return $button;
    }
} 