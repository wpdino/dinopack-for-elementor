<?php
/**
 * Button Widget
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
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Button widget.
 *
 * A button widget for Elementor.
 *
 * @since 1.0.0
 */
class Button extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-button',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-button',
            plugins_url('frontend.js', __FILE__),
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
    }

    /**
     * Get widget name.
     *
     * Retrieve button widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     */
    public function get_name() {
        return 'dinopack-button';
    }

    /**
     * Get widget title.
     *
     * Retrieve button widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Button', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve button widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-button';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the button widget belongs to.
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
        return ['dinopack-button'];
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
        return ['dinopack-button'];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['button', 'link', 'cta', 'dinopack'];
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        // Content Tab
        $this->start_controls_section(
            'section_button',
            [
                'label' => esc_html__('Button', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_type',
            [
                'label' => esc_html__('Type', 'dinopack-for-elementor'),
				'label_block' => true,
                'type' => 'wpdino_select_image',
                'default' => 'border',
                'options' => [
					'border' => [
						'label' => esc_html__('Border', 'dinopack-for-elementor'),
						'image' => plugins_url('assets/images/border.png', __FILE__),
					],
					'fill' => [
						'label' => esc_html__('Fill', 'dinopack-for-elementor'),
						'image' => plugins_url('assets/images/fill.png', __FILE__),
					],
					'half' => [
						'label' => esc_html__('Half', 'dinopack-for-elementor'),
						'image' => plugins_url('assets/images/half.png', __FILE__),
					],
					'label' => [
						'label' => esc_html__('Label', 'dinopack-for-elementor'),
						'image' => plugins_url('assets/images/label.png', __FILE__),
					],
                ],
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => esc_html__('Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Click Here', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Click Here', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'dinopack-for-elementor'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'dinopack-for-elementor'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__('Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Stretch', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-stretch',
                    ],
                ],
                'prefix_class' => 'elementor%s-align-',
                'default' => 'center',
            ]
        );

        $this->add_responsive_control(
            'content_align',
            [
                'label' => esc_html__('Alignment', 'dinopack-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Start', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__('End', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'space-between' => [
                        'title' => esc_html__('Space between', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-content-wrapper' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'align' => 'justify',
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
                    'value' => 'fas fa-arrow-right',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'fa-solid' => [
                        'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down', 'chevron-right', 'chevron-left',
                        'chevron-up', 'chevron-down', 'play', 'pause', 'stop', 'check', 'check-circle',
                        'plus', 'minus', 'times', 'download', 'upload', 'share', 'envelope', 'phone',
                        'search', 'cog', 'star', 'heart', 'thumbs-up', 'thumbs-down', 'user', 'lock',
                        'unlock', 'eye', 'eye-slash', 'home', 'globe', 'calendar', 'clock', 'map-marker'
                    ],
                    'fa-regular' => [
                        'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down', 'chevron-right', 'chevron-left',
                        'chevron-up', 'chevron-down', 'play', 'pause', 'stop', 'check', 'check-circle',
                        'plus', 'minus', 'times', 'download', 'upload', 'share', 'envelope', 'phone',
                        'search', 'cog', 'star', 'heart', 'thumbs-up', 'thumbs-down', 'user', 'lock',
                        'unlock', 'eye', 'eye-slash', 'home', 'globe', 'calendar', 'clock', 'map-marker'
                    ],
                    'fa-brands' => [
                        'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'google',
                        'apple', 'microsoft', 'amazon', 'spotify', 'paypal', 'wordpress', 'elementor'
                    ]
                ],
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label' => esc_html__('Icon Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => [
                    'before' => esc_html__('Before', 'dinopack-for-elementor'),
                    'after' => esc_html__('After', 'dinopack-for-elementor'),
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'enable_animated_underline',
            [
                'label' => esc_html__('Enable Animated Underline', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'button_type' => 'label',
                ],
                'description' => esc_html__('Enable animated underline effect for label button type.', 'dinopack-for-elementor'),
            ]
        );

        $this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Button', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .dinopack-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .dinopack-button',
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        // Normal State
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover State
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__('Border Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'border_border!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-button:hover',
            ]
        );

        $this->add_control(
            'button_hover_transition_duration',
            [
                'label' => esc_html__('Transition Duration', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms', 'custom'],
                'default' => [
                    'unit' => 's',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button' => 'transition-duration: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__('Hover Animation', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-button',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-button',
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Icon Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 16,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
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
                    '{{WRAPPER}} .dinopack-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        // Animated Underline Styling Controls
        $this->add_control(
            'underline_heading',
            [
                'label' => esc_html__('Animated Underline', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'button_type' => 'label',
                    'enable_animated_underline' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'underline_thickness',
            [
                'label' => esc_html__('Thickness', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 2,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-label.dinopack-button-animated-underline::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'button_type' => 'label',
                    'enable_animated_underline' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'underline_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#6F9C50',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-label.dinopack-button-animated-underline::after' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_type' => 'label',
                    'enable_animated_underline' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'underline_spacing',
            [
                'label' => esc_html__('Spacing from Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-label.dinopack-button-animated-underline::after' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
                ],
                'condition' => [
                    'button_type' => 'label',
                    'enable_animated_underline' => 'yes',
                ],
            ]
        );

        // Half Button Styling Controls
        $this->add_control(
            'half_heading',
            [
                'label' => esc_html__('Half Background', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'button_type' => 'half',
                ],
            ]
        );

        $this->add_control(
            'half_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#6F9C50',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-half::before' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_type' => 'half',
                ],
            ]
        );

        $this->add_control(
            'half_background_opacity',
            [
                'label' => esc_html__('Background Opacity', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 25,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-half::before' => 'opacity: {{SIZE}}%;',
                ],
                'condition' => [
                    'button_type' => 'half',
                ],
            ]
        );

        $this->add_control(
            'half_animation_duration',
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
                'selectors' => [
                    '{{WRAPPER}} .dinopack-button-half::before' => 'transition: all {{SIZE}}{{UNIT}} ease-in-out;',
                ],
                'condition' => [
                    'button_type' => 'half',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Check if icon is empty (like Elementor's icon widget)
        if (empty($settings['selected_icon']['value'])) {
            // Show fallback icon instead of returning empty
            $settings['selected_icon'] = [
                'value' => 'fas fa-arrow-right',
                'library' => 'fa-solid',
            ];
        }

        $this->add_render_attribute('wrapper', 'class', 'dinopack-button-wrapper');
        
        $button_classes = [
            'dinopack-button',
            'dinopack-button-' . $settings['button_type'],
        ];

        // Add animated underline class for label type if enabled
        if ($settings['button_type'] === 'label' && $settings['enable_animated_underline'] === 'yes') {
            $button_classes[] = 'dinopack-button-animated-underline';
        }

        // Add hover animation class
        if (!empty($settings['hover_animation'])) {
            $button_classes[] = 'elementor-animation-' . $settings['hover_animation'];
        }

        $this->add_render_attribute('button', 'class', $button_classes);

        if (!empty($settings['link']['url'])) {
            $this->add_link_attributes('button', $settings['link']);
        }

        // Font Awesome 4 migration support (like Elementor's icon widget)
        if (empty($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
            $settings['icon'] = 'fa fa-arrow-right';
        }

        if (!empty($settings['icon'])) {
            $this->add_render_attribute('icon', 'class', $settings['icon']);
            $this->add_render_attribute('icon', 'aria-hidden', 'true');
        }

        $migrated = isset($settings['__fa4_migrated']['selected_icon']);
        $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

        ?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <a <?php $this->print_render_attribute_string('button'); ?>>
                <span class="dinopack-button-content-wrapper">
                    <?php if (!empty($settings['selected_icon']['value']) && $settings['icon_position'] === 'before') : ?>
                        <span class="dinopack-button-icon dinopack-button-icon-before">
                            <?php 
                            // Always try to render the selected icon first
                            if (!empty($settings['selected_icon']['value'])) {
                                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                            } elseif ($is_new || $migrated) {
                                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                            } else {
                                echo '<i ' . esc_attr( $this->get_render_attribute_string('icon') ) . '></i>';
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                    
                    <span class="dinopack-button-text"><?php echo esc_html($settings['text']); ?></span>
                    
                    <?php if (!empty($settings['selected_icon']['value']) && $settings['icon_position'] === 'after') : ?>
                        <span class="dinopack-button-icon dinopack-button-icon-after">
                            <?php 
                            // Always try to render the selected icon first
                            if (!empty($settings['selected_icon']['value'])) {
                                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                            } elseif ($is_new || $migrated) {
                                Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
                            } else {
                                echo '<i ' . esc_attr( $this->get_render_attribute_string('icon') ) . '></i>';
                            }
                            ?>
                        </span>
                    <?php endif; ?>
                </span>
            </a>
        </div>
        <?php
    }
} 