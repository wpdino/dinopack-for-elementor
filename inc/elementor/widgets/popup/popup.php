<?php
/**
 * Popup Widget
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
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Popup widget.
 *
 * A popup widget for Elementor.
 *
 * @since 1.0.0
 */
class Popup extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-popup',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-popup',
            plugins_url('frontend.js', __FILE__),
            ['jquery'],
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
        return 'dinopack-popup';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Modal Popup', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-lightbox';
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
        return ['dinopack-popup'];
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
        return ['dinopack-popup'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['popup', 'modal', 'lightbox', 'dinopack'];
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
                'label' => esc_html__('Popup Content', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'popup_title',
            [
                'label' => esc_html__('Popup Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Popup Title', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter popup title', 'dinopack-for-elementor'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'popup_content',
            [
                'label' => esc_html__('Popup Content', 'dinopack-for-elementor'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__('Content goes here!', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your content', 'dinopack-for-elementor'),
            ]
        );

        $this->end_controls_section();

        // Trigger Settings
        $this->start_controls_section(
            'section_trigger',
            [
                'label' => esc_html__('Trigger', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'trigger_type',
            [
                'label' => esc_html__('Trigger Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'button',
                'options' => [
                    'button' => esc_html__('Button', 'dinopack-for-elementor'),
                    'image' => esc_html__('Image', 'dinopack-for-elementor'),
                    'icon' => esc_html__('Icon', 'dinopack-for-elementor'),
                    'text' => esc_html__('Text', 'dinopack-for-elementor'),
                    'auto' => esc_html__('Auto (Time Delay)', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Open Popup', 'dinopack-for-elementor'),
                'condition' => [
                    'trigger_type' => 'button',
                ],
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'trigger_image',
            [
                'label' => esc_html__('Choose Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'trigger_type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'trigger_text',
            [
                'label' => esc_html__('Trigger Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Click here to open popup', 'dinopack-for-elementor'),
                'condition' => [
                    'trigger_type' => 'text',
                ],
            ]
        );

        $this->add_control(
            'delay_time',
            [
                'label' => esc_html__('Delay Time (ms)', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 100,
                'max' => 20000,
                'step' => 100,
                'condition' => [
                    'trigger_type' => 'auto',
                ],
            ]
        );

        $this->end_controls_section();

        // Popup Settings
        $this->start_controls_section(
            'section_settings',
            [
                'label' => esc_html__('Popup Settings', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'popup_width',
            [
                'label' => esc_html__('Width', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 600,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'close_button',
            [
                'label' => esc_html__('Close Button', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'close_on_esc',
            [
                'label' => esc_html__('Close on ESC Key', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'close_on_overlay_click',
            [
                'label' => esc_html__('Close on Overlay Click', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Animation Settings
        $this->start_controls_section(
            'section_animation',
            [
                'label' => esc_html__('Animation', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'popup_animation',
            [
                'label' => esc_html__('Animation Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeIn',
                'options' => [
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'fadeIn' => esc_html__('Fade In', 'dinopack-for-elementor'),
                    'slideDown' => esc_html__('Slide Down', 'dinopack-for-elementor'),
                    'slideUp' => esc_html__('Slide Up', 'dinopack-for-elementor'),
                    'slideLeft' => esc_html__('Slide Left', 'dinopack-for-elementor'),
                    'slideRight' => esc_html__('Slide Right', 'dinopack-for-elementor'),
                    'zoomIn' => esc_html__('Zoom In', 'dinopack-for-elementor'),
                    'zoomOut' => esc_html__('Zoom Out', 'dinopack-for-elementor'),
                    'bounceIn' => esc_html__('Bounce In', 'dinopack-for-elementor'),
                    'flipInX' => esc_html__('Flip Horizontal', 'dinopack-for-elementor'),
                    'flipInY' => esc_html__('Flip Vertical', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'animation_duration',
            [
                'label' => esc_html__('Animation Duration', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms'],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                    'ms' => [
                        'min' => 100,
                        'max' => 2000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'size' => 0.3,
                    'unit' => 's',
                ],
                'condition' => [
                    'popup_animation!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'animation-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'animation_delay',
            [
                'label' => esc_html__('Animation Delay', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms'],
                'range' => [
                    's' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                    'ms' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 's',
                ],
                'condition' => [
                    'popup_animation!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'animation-delay: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'animation_easing',
            [
                'label' => esc_html__('Animation Easing', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ease-out',
                'options' => [
                    'ease' => esc_html__('Ease', 'dinopack-for-elementor'),
                    'ease-in' => esc_html__('Ease In', 'dinopack-for-elementor'),
                    'ease-out' => esc_html__('Ease Out', 'dinopack-for-elementor'),
                    'ease-in-out' => esc_html__('Ease In Out', 'dinopack-for-elementor'),
                    'linear' => esc_html__('Linear', 'dinopack-for-elementor'),
                    'cubic-bezier(0.68, -0.55, 0.265, 1.55)' => esc_html__('Bounce', 'dinopack-for-elementor'),
                    'cubic-bezier(0.25, 0.46, 0.45, 0.94)' => esc_html__('Smooth', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'popup_animation!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'animation-timing-function: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_animation',
            [
                'label' => esc_html__('Overlay Animation', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeIn',
                'options' => [
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'fadeIn' => esc_html__('Fade In', 'dinopack-for-elementor'),
                    'slideDown' => esc_html__('Slide Down', 'dinopack-for-elementor'),
                    'slideUp' => esc_html__('Slide Up', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'overlay_animation_duration',
            [
                'label' => esc_html__('Overlay Animation Duration', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms'],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                    'ms' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
                'default' => [
                    'size' => 0.2,
                    'unit' => 's',
                ],
                'condition' => [
                    'overlay_animation!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-overlay' => 'animation-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Trigger
        $this->start_controls_section(
            'section_style_trigger',
            [
                'label' => esc_html__('Trigger', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'trigger_typography',
                'selector' => '{{WRAPPER}} .dinopack-popup-trigger',
                'condition' => [
                    'trigger_type' => ['button', 'text'],
                ],
            ]
        );

        // Button-specific styling
        $this->add_control(
            'button_heading',
            [
                'label' => esc_html__('Button Styling', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'trigger_type' => 'button',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'button',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'button',
                ],
            ]
        );

        // Icon-specific styling
        $this->add_control(
            'icon_heading',
            [
                'label' => esc_html__('Icon Styling', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-popup-trigger-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'icon',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'icon',
                ],
            ]
        );

        // Image-specific styling
        $this->add_control(
            'image_heading',
            [
                'label' => esc_html__('Image Styling', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'trigger_type' => 'image',
                ],
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
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 200,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'image',
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
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'image',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'image',
                ],
            ]
        );

        $this->add_control(
            'image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => esc_html__('Cover', 'dinopack-for-elementor'),
                    'contain' => esc_html__('Contain', 'dinopack-for-elementor'),
                    'fill' => esc_html__('Fill', 'dinopack-for-elementor'),
                    'scale-down' => esc_html__('Scale Down', 'dinopack-for-elementor'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-image' => 'object-fit: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => 'image',
                ],
            ]
        );

        // Text-specific styling
        $this->add_control(
            'text_heading',
            [
                'label' => esc_html__('Text Styling', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'trigger_type' => 'text',
                ],
            ]
        );

        $this->add_control(
            'text_decoration',
            [
                'label' => esc_html__('Text Decoration', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'underline',
                'options' => [
                    'none' => esc_html__('None', 'dinopack-for-elementor'),
                    'underline' => esc_html__('Underline', 'dinopack-for-elementor'),
                    'overline' => esc_html__('Overline', 'dinopack-for-elementor'),
                    'line-through' => esc_html__('Line Through', 'dinopack-for-elementor'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-text' => 'text-decoration: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => 'text',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => 'text',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_trigger_style');

        $this->start_controls_tab(
            'tab_trigger_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'trigger_text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'text', 'icon'],
                ],
            ]
        );

        $this->add_control(
            'trigger_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'icon'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_trigger_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'trigger_text_hover_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'text', 'icon'],
                ],
            ]
        );

        $this->add_control(
            'trigger_hover_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'icon'],
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'trigger_border',
                'selector' => '{{WRAPPER}} .dinopack-popup-trigger',
                'separator' => 'before',
                'condition' => [
                    'trigger_type' => ['button', 'icon', 'image'],
                ],
            ]
        );

        $this->add_control(
            'trigger_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'icon', 'image'],
                ],
            ]
        );

        $this->add_responsive_control(
            'trigger_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'trigger_type' => ['button', 'icon', 'text'],
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Popup Content
        $this->start_controls_section(
            'section_style_popup',
            [
                'label' => esc_html__('Popup', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'popup_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'popup_border',
                'selector' => '{{WRAPPER}} .dinopack-popup-content',
            ]
        );

        $this->add_control(
            'popup_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'popup_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-popup-content',
            ]
        );

        $this->add_responsive_control(
            'popup_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_background_color',
            [
                'label' => esc_html__('Overlay Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.8)',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-popup-overlay' => 'background-color: {{VALUE}};',
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
        $id = 'dinopack-popup-' . $this->get_id();

        $this->add_render_attribute('wrapper', 'class', 'dinopack-popup-wrapper');
        $trigger_classes = [
            'dinopack-popup-trigger',
            'dinopack-popup-trigger-' . $settings['trigger_type']
        ];

        // Add specific class for styling
        if ($settings['trigger_type'] === 'button') {
            $trigger_classes[] = 'dinopack-popup-trigger-button';
        } elseif ($settings['trigger_type'] === 'icon') {
            $trigger_classes[] = 'dinopack-popup-trigger-icon';
        } elseif ($settings['trigger_type'] === 'image') {
            $trigger_classes[] = 'dinopack-popup-trigger-image';
        } elseif ($settings['trigger_type'] === 'text') {
            $trigger_classes[] = 'dinopack-popup-trigger-text';
        }

        $this->add_render_attribute('trigger', 'class', $trigger_classes);
        $this->add_render_attribute('trigger', 'data-popup-id', $id);

        // Popup settings
        $popup_settings = [
            'closeButton' => $settings['close_button'] === 'yes',
            'closeOnEsc' => $settings['close_on_esc'] === 'yes',
            'closeOnOverlayClick' => $settings['close_on_overlay_click'] === 'yes',
            'autoOpen' => $settings['trigger_type'] === 'auto',
            'delay' => absint($settings['delay_time']),
            'animation' => $settings['popup_animation'],
            'overlayAnimation' => $settings['overlay_animation'],
        ];

        $this->add_render_attribute('popup-data', 'data-settings', json_encode($popup_settings));

        // Add animation classes
        $popup_classes = ['dinopack-popup'];
        if ($settings['popup_animation'] !== 'none') {
            $popup_classes[] = 'dinopack-popup-animated';
        }

        $this->add_render_attribute('popup', 'class', $popup_classes);

        $overlay_classes = ['dinopack-popup-overlay'];
        if ($settings['overlay_animation'] !== 'none') {
            $overlay_classes[] = 'dinopack-popup-overlay-animated';
        }

        $this->add_render_attribute('overlay', 'class', $overlay_classes);

        $content_classes = ['dinopack-popup-content'];
        if ($settings['popup_animation'] !== 'none') {
            $content_classes[] = 'dinopack-popup-content-animated';
            $content_classes[] = 'dinopack-animation-' . $settings['popup_animation'];
        }

        $this->add_render_attribute('content', 'class', $content_classes);
        ?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php if ($settings['trigger_type'] !== 'auto') : ?>
                <?php if ($settings['trigger_type'] === 'button') : ?>
                    <button <?php $this->print_render_attribute_string('trigger'); ?>>
                        <?php echo esc_html($settings['button_text']); ?>
                    </button>
                <?php elseif ($settings['trigger_type'] === 'icon') : ?>
                    <span <?php $this->print_render_attribute_string('trigger'); ?>>
                        <?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']); ?>
                    </span>
                <?php elseif ($settings['trigger_type'] === 'image') : ?>
                    <img <?php $this->print_render_attribute_string('trigger'); ?> src="<?php echo esc_url($settings['trigger_image']['url']); ?>" alt="<?php echo esc_attr__('Popup Trigger', 'dinopack-for-elementor'); ?>">
                <?php elseif ($settings['trigger_type'] === 'text') : ?>
                    <span <?php $this->print_render_attribute_string('trigger'); ?>>
                        <?php echo esc_html($settings['trigger_text']); ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
            
            <div id="<?php echo esc_attr($id); ?>" <?php $this->print_render_attribute_string('popup'); ?> <?php $this->print_render_attribute_string('popup-data'); ?>>
                <div <?php $this->print_render_attribute_string('overlay'); ?>></div>
                <div <?php $this->print_render_attribute_string('content'); ?>>
                    <?php if ($settings['close_button'] === 'yes') : ?>
                        <div class="dinopack-popup-close">&times;</div>
                    <?php endif; ?>
                    
                    <div class="dinopack-popup-header">
                        <h2 class="dinopack-popup-title"><?php echo esc_html($settings['popup_title']); ?></h2>
                    </div>
                    
                    <div class="dinopack-popup-body">
                        <?php echo wp_kses_post($settings['popup_content']); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} 