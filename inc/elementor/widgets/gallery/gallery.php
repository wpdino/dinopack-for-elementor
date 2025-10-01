<?php
/**
 * Gallery Widget
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
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Gallery widget.
 *
 * A gallery widget for Elementor.
 *
 * @since 1.0.0
 */
class Gallery extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-gallery',
            DINOPACK_URL . 'inc/elementor/widgets/gallery/frontend.css',
            [],
            DINOPACK_VERSION 
        );
        wp_register_style(
            'glightbox',
            DINOPACK_URL . 'inc/elementor/widgets/gallery/assets/glightbox/glightbox.min.css',
            [],
            DINOPACK_VERSION
        );
        wp_register_script(
            'glightbox',
            DINOPACK_URL . 'inc/elementor/widgets/gallery/assets/glightbox/glightbox.min.js',
            [],
            DINOPACK_VERSION,
            true
        );
        wp_register_script(
            'dinopack-gallery',
            DINOPACK_URL . 'inc/elementor/widgets/gallery/frontend.js',
            ['jquery', 'glightbox'],
            DINOPACK_VERSION,
            true
        );
    }

    /**
     * Get widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     */
    public function get_name() {
        return 'dinopack-gallery';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Gallery', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
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
        return ['dinopack-gallery', 'glightbox'];
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
        return ['dinopack-gallery', 'glightbox'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['gallery', 'images', 'photos', 'dinopack'];
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
            'section_gallery',
            [
                'label' => esc_html__('Gallery', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'gallery_images',
            [
                'label' => esc_html__('Gallery Images', 'dinopack-for-elementor'),
                'type' => Controls_Manager::GALLERY,
                'default' => [],
                'description' => esc_html__('Select multiple images from the media library', 'dinopack-for-elementor'),
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'exclude' => ['custom'],
                'default' => 'medium',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'gallery_type',
            [
                'label' => esc_html__('Gallery Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'dinopack-for-elementor'),
                    'masonry' => esc_html__('Masonry', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-gallery-type-',
            ]
        );

        $this->add_responsive_control(
            'gallery_columns',
            [
                'label' => esc_html__('Columns', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class' => 'dinopack-gallery-columns-%s-',
                'device_args' => [
                    'tablet' => [
                        'default' => '2',
                    ],
                    'mobile' => [
                        'default' => '1',
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'gallery_spacing',
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
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}.dinopack-gallery-type-grid .dinopack-gallery-container' => 'column-gap: {{SIZE}}{{UNIT}}; row-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.dinopack-gallery-type-masonry .dinopack-gallery-container' => 'column-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.dinopack-gallery-type-masonry .dinopack-gallery-container .dinopack-gallery-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
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
                'default' => 'no',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'show_caption',
            [
                'label' => esc_html__('Show Caption', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'no',
				'condition' => [
					'show_title' => 'yes',
				],
            ]
        );

        $this->add_control(
            'hover_effect',
            [
                'label' => esc_html__('Hover Effect', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'default' => esc_html__('Default', 'dinopack-for-elementor'),
                    'overlay' => esc_html__('Overlay', 'dinopack-for-elementor'),
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'condition' => [
                    'hover_effect' => 'overlay',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item:hover .dinopack-gallery-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'zoom_icon',
            [
                'label' => esc_html__('Show Zoom Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Lightbox Tab
        $this->start_controls_section(
            'section_lightbox',
            [
                'label' => esc_html__('Lightbox', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_lightbox',
            [
                'label' => esc_html__('Enable Lightbox', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'lightbox_skin',
            [
                'label' => esc_html__('Skin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'clean',
                'options' => [
                    'clean' => esc_html__('Clean', 'dinopack-for-elementor'),
                    'modern' => esc_html__('Modern', 'dinopack-for-elementor'),
                    'minimal' => esc_html__('Minimal', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_open_effect',
            [
                'label' => esc_html__('Open Effect', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => esc_html__('Fade', 'dinopack-for-elementor'),
                    'zoom' => esc_html__('Zoom', 'dinopack-for-elementor'),
                    'slide' => esc_html__('Slide', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_close_effect',
            [
                'label' => esc_html__('Close Effect', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'fade' => esc_html__('Fade', 'dinopack-for-elementor'),
                    'zoom' => esc_html__('Zoom', 'dinopack-for-elementor'),
                    'slide' => esc_html__('Slide', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_slide_effect',
            [
                'label' => esc_html__('Slide Effect', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__('Slide', 'dinopack-for-elementor'),
                    'fade' => esc_html__('Fade', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'lightbox_width',
            [
                'label' => esc_html__('Width', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 90,
                    'unit' => 'vw',
                ],
                'selectors' => [
                    '{{WRAPPER}} .glightbox-container' => '--glightbox-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'lightbox_height',
            [
                'label' => esc_html__('Height', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 800,
                    ],
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 90,
                    'unit' => 'vh',
                ],
                'selectors' => [
                    '{{WRAPPER}} .glightbox-container' => '--glightbox-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_loop',
            [
                'label' => esc_html__('Loop Gallery', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_keyboard_navigation',
            [
                'label' => esc_html__('Keyboard Navigation', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_touch_navigation',
            [
                'label' => esc_html__('Touch Navigation', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_close_on_outside_click',
            [
                'label' => esc_html__('Close on Outside Click', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_zoomable',
            [
                'label' => esc_html__('Zoomable', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_draggable',
            [
                'label' => esc_html__('Draggable', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'lightbox_preload',
            [
                'label' => esc_html__('Preload Images', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_lightbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Items
        $this->start_controls_section(
            'section_style_items',
            [
                'label' => esc_html__('Items', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-gallery-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-gallery-item-inner',
            ]
        );

        $this->start_controls_tabs('tabs_item_hover');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'item_opacity',
            [
                'label' => esc_html__('Opacity', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'item_hover_opacity',
            [
                'label' => esc_html__('Opacity', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item:hover .dinopack-gallery-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'item_hover_background',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item:hover .dinopack-gallery-item-inner' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_hover_scale',
            [
                'label' => esc_html__('Scale', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-item:hover .dinopack-gallery-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Zoom Icon Style
        $this->add_control(
            'zoom_icon_heading',
            [
                'label' => esc_html__('Zoom Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'enable_lightbox' => 'yes',
                    'zoom_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'zoom_icon_color',
            [
                'label' => esc_html__('Icon Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'condition' => [
                    'enable_lightbox' => 'yes',
                    'zoom_icon' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-zoom-icon i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'zoom_icon_size',
            [
                'label' => esc_html__('Icon Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 12,
                        'max' => 48,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'condition' => [
                    'enable_lightbox' => 'yes',
                    'zoom_icon' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-zoom-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Content
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                    'show_caption' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label' => esc_html__('Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'overlay',
                'options' => [
                    'overlay' => esc_html__('Overlay', 'dinopack-for-elementor'),
                    'below' => esc_html__('Below Image', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-gallery-content-position-',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'content_alignment',
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
                    '{{WRAPPER}} .dinopack-gallery-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_background',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_title_style',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
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
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .dinopack-gallery-title',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'heading_caption_style',
            [
                'label' => esc_html__('Caption', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_caption' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'caption_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#dddddd',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-gallery-caption' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_caption' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .dinopack-gallery-caption',
                'condition' => [
                    'show_caption' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $gallery_images = $settings['gallery_images'];
        $gallery_layout = $settings['gallery_type'];
        $enable_lightbox = $settings['enable_lightbox'] === 'yes';
        
        // No gallery images, show placeholder
        if (empty($gallery_images)) {
            echo '<div class="dinopack-gallery-placeholder">' . esc_html__('Please select images from the media library', 'dinopack-for-elementor') . '</div>';
            return;
        }

        $this->add_render_attribute('gallery-container', 'class', [
            'dinopack-gallery-container',
        ]);

        // Responsive column classes are automatically added by Elementor via prefix_class

        // Add lightbox data attributes
        if ($enable_lightbox) {
            // Get lightbox settings with defaults
            $lightbox_width = isset($settings['lightbox_width']) ? $settings['lightbox_width'] : ['size' => 90, 'unit' => 'vw'];
            $lightbox_height = isset($settings['lightbox_height']) ? $settings['lightbox_height'] : ['size' => 90, 'unit' => 'vh'];
            
            $this->add_render_attribute('gallery-container', 'data-lightbox-settings', wp_json_encode([
                'skin' => isset($settings['lightbox_skin']) ? $settings['lightbox_skin'] : 'clean',
                'openEffect' => isset($settings['lightbox_open_effect']) ? $settings['lightbox_open_effect'] : 'fade',
                'closeEffect' => isset($settings['lightbox_close_effect']) ? $settings['lightbox_close_effect'] : 'fade',
                'slideEffect' => isset($settings['lightbox_slide_effect']) ? $settings['lightbox_slide_effect'] : 'slide',
                'width' => $lightbox_width['size'] . $lightbox_width['unit'],
                'height' => $lightbox_height['size'] . $lightbox_height['unit'],
                'loop' => isset($settings['lightbox_loop']) ? $settings['lightbox_loop'] === 'yes' : true,
                'keyboardNavigation' => isset($settings['lightbox_keyboard_navigation']) ? $settings['lightbox_keyboard_navigation'] === 'yes' : true,
                'touchNavigation' => isset($settings['lightbox_touch_navigation']) ? $settings['lightbox_touch_navigation'] === 'yes' : true,
                'closeOnOutsideClick' => isset($settings['lightbox_close_on_outside_click']) ? $settings['lightbox_close_on_outside_click'] === 'yes' : true,
                'zoomable' => isset($settings['lightbox_zoomable']) ? $settings['lightbox_zoomable'] === 'yes' : false,
                'draggable' => isset($settings['lightbox_draggable']) ? $settings['lightbox_draggable'] === 'yes' : false,
                'preload' => isset($settings['lightbox_preload']) ? $settings['lightbox_preload'] === 'yes' : true,
            ]));
        }
        
        ?>
        <div <?php $this->print_render_attribute_string('gallery-container'); ?>>
            <?php
            foreach ($gallery_images as $index => $image) :
                $image_url = Group_Control_Image_Size::get_attachment_image_src($image['id'], 'thumbnail', $settings);
                if (empty($image_url)) {
                    $image_url = $image['url'];
                }
                
                $full_image_url = wp_get_attachment_image_url($image['id'], 'full');
                if (empty($full_image_url)) {
                    $full_image_url = $image['url'];
                }
                
                $image_title = get_the_title($image['id']);
                $image_caption = wp_get_attachment_caption($image['id']);
                
                $item_key = 'gallery-item-' . $index;
                $hover_effect = isset($settings['hover_effect']) ? $settings['hover_effect'] : 'default';
                $show_zoom_icon = isset($settings['zoom_icon']) ? $settings['zoom_icon'] === 'yes' : true;
                
                $this->add_render_attribute($item_key, 'class', [
                    'dinopack-gallery-item',
                    'dinopack-hover-' . $hover_effect
                ]);
                
                ?>
                <div <?php $this->print_render_attribute_string($item_key); ?>>
                    <div class="dinopack-gallery-item-inner">
                        <?php if ($enable_lightbox) : ?>
                            <a href="<?php echo esc_url($full_image_url); ?>" 
                               class="glightbox dinopack-gallery-link" 
                               data-gallery="gallery-<?php echo esc_attr($this->get_id()); ?>"
                               data-title="<?php echo esc_attr($image_title); ?>"
                               data-description="<?php echo esc_attr($image_caption); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt="<?php echo esc_attr($image_title); ?>" 
                                     class="dinopack-gallery-image">
                                     
                                <?php if ($hover_effect === 'overlay') : ?>
                                    <div class="dinopack-gallery-overlay"></div>
                                <?php endif; ?>
                                
                                <?php if ($show_zoom_icon) : ?>
                                    <div class="dinopack-gallery-zoom-icon">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php else : ?>
                            <div class="dinopack-gallery-image-wrapper">
                                <img src="<?php echo esc_url($image_url); ?>" 
                                     alt="<?php echo esc_attr($image_title); ?>" 
                                     class="dinopack-gallery-image">
                                     
                                <?php if ($hover_effect === 'overlay') : ?>
                                    <div class="dinopack-gallery-overlay"></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($settings['show_title'] === 'yes' || $settings['show_caption'] === 'yes') : ?>
                            <div class="dinopack-gallery-content">
                                <?php if ($settings['show_title'] === 'yes' && !empty($image_title)) : ?>
                                    <h3 class="dinopack-gallery-title"><?php echo esc_html($image_title); ?></h3>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_caption'] === 'yes' && !empty($image_caption)) : ?>
                                    <p class="dinopack-gallery-caption"><?php echo esc_html($image_caption); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
        </div>
        <?php
    }
} 