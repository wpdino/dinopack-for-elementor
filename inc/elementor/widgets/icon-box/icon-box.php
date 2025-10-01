<?php
/**
 * Icon Box Widget
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
 * Icon Box widget.
 *
 * An icon box widget for Elementor.
 *
 * @since 1.0.0
 */
class Icon_Box extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-icon-box',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-icon-box',
            plugins_url('frontend.js', __FILE__),
            ['jquery'],
            DINOPACK_VERSION,
            true
        );
    }

    /**
     * Get widget name.
     *
     * Retrieve icon box widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     */
    public function get_name() {
        return 'dinopack-icon-box';
    }

    /**
     * Get widget title.
     *
     * Retrieve icon box widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Icon Box', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve icon box widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-icon-box';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the icon box widget belongs to.
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
        return ['dinopack-icon-box'];
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
        return ['dinopack-icon-box'];
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
        return ['icon', 'box', 'icon box', 'info', 'dinopack'];
    }

    /**
     * Register icon box widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        // Content Tab
        $this->start_controls_section(
            'section_icon',
            [
                'label' => esc_html__('Icon Box', 'dinopack-for-elementor'),
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
                'recommended' => [
                    'fa-solid' => [
                        'star', 'heart', 'check', 'home', 'user', 'phone', 'envelope', 'globe', 'cog', 'shield',
                        'lightbulb', 'rocket', 'trophy', 'gem', 'fire', 'bolt', 'leaf', 'sun', 'moon', 'cloud',
                        'shopping-cart', 'credit-card', 'lock', 'unlock', 'eye', 'eye-slash', 'search', 'plus',
                        'minus', 'times', 'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down', 'download',
                        'upload', 'share', 'thumbs-up', 'thumbs-down', 'comment', 'calendar', 'clock', 'map-marker'
                    ],
                    'fa-regular' => [
                        'star', 'heart', 'check-circle', 'user', 'envelope', 'calendar', 'clock', 'thumbs-up',
                        'thumbs-down', 'comment', 'eye', 'eye-slash', 'bookmark', 'file', 'folder', 'image'
                    ],
                    'fa-brands' => [
                        'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'google', 'apple',
                        'microsoft', 'amazon', 'spotify', 'paypal', 'wordpress', 'elementor'
                    ]
                ],
            ]
        );

        $this->add_control(
            'view',
            [
                'label' => esc_html__('View', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__('Default', 'dinopack-for-elementor'),
                    'stacked' => esc_html__('Stacked', 'dinopack-for-elementor'),
                    'framed' => esc_html__('Framed', 'dinopack-for-elementor'),
                ],
                'default' => 'default',
                'prefix_class' => 'dinopack-view-',
            ]
        );

        $this->add_control(
            'shape',
            [
                'label' => esc_html__('Shape', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'circle' => esc_html__('Circle', 'dinopack-for-elementor'),
                    'square' => esc_html__('Square', 'dinopack-for-elementor'),
                ],
                'default' => 'circle',
                'condition' => [
                    'view!' => 'default',
                ],
                'prefix_class' => 'dinopack-shape-',
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('This is the heading', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your title', 'dinopack-for-elementor'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'description_text',
            [
                'label' => esc_html__('Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your description', 'dinopack-for-elementor'),
                'rows' => 10,
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__('Link', 'dinopack-for-elementor'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__('https://your-link.com', 'dinopack-for-elementor'),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'position',
            [
                'label' => esc_html__('Icon Position', 'dinopack-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => esc_html__('Top', 'dinopack-for-elementor'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'dinopack-for-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'dinopack-position-',
                'toggle' => false,
            ]
        );


        $this->add_control(
            'title_size',
            [
                'label' => esc_html__('Title HTML Tag', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__('Hover Animation', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HOVER_ANIMATION,
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Style Tab - Icon
        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('icon_colors');

        $this->start_controls_tab(
            'icon_colors_normal',
            [
                'label' => esc_html__('Normal', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Primary Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.dinopack-view-stacked .dinopack-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.dinopack-view-framed .dinopack-icon, {{WRAPPER}}.dinopack-view-default .dinopack-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label' => esc_html__('Secondary Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.dinopack-view-framed .dinopack-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.dinopack-view-stacked .dinopack-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_colors_hover',
            [
                'label' => esc_html__('Hover', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'hover_primary_color',
            [
                'label' => esc_html__('Primary Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.dinopack-view-stacked .dinopack-icon:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.dinopack-view-framed .dinopack-icon:hover, {{WRAPPER}}.dinopack-view-default .dinopack-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_secondary_color',
            [
                'label' => esc_html__('Secondary Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.dinopack-view-framed .dinopack-icon:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.dinopack-view-stacked .dinopack-icon:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->add_control(
            'rotate',
            [
                'label' => esc_html__('Rotate', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon i, {{WRAPPER}} .dinopack-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->add_control(
            'border_width',
            [
                'label' => esc_html__('Border Width', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'view' => 'framed',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab - Content
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'dinopack-for-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
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
                    'justify' => [
                        'title' => esc_html__('Justified', 'dinopack-for-elementor'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon-box-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_vertical_alignment',
            [
                'label' => esc_html__('Vertical Alignment', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'dinopack-for-elementor'),
                    'middle' => esc_html__('Middle', 'dinopack-for-elementor'),
                    'bottom' => esc_html__('Bottom', 'dinopack-for-elementor'),
                ],
                'default' => 'top',
                'prefix_class' => 'dinopack-vertical-align-',
                'condition' => [
                    'position!' => 'top',
                ],
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon-box-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .dinopack-icon-box-title',
            ]
        );

        $this->add_control(
            'heading_description',
            [
                'label' => esc_html__('Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-icon-box-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .dinopack-icon-box-description',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render icon box widget output on the frontend.
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
                'value' => 'fas fa-star',
                'library' => 'fa-solid',
            ];
        }

        $this->add_render_attribute('icon-wrapper', 'class', 'dinopack-icon-wrapper');
        $this->add_render_attribute('icon', 'class', 'dinopack-icon');
        
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('icon', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }

        $icon_tag = 'span';

        if (!empty($settings['link']['url'])) {
            $this->add_link_attributes('icon', $settings['link']);
            $icon_tag = 'a';
        }

        // Font Awesome 4 migration support (like Elementor's icon widget)
        if (empty($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
            $settings['icon'] = 'fa fa-star';
        }

        if (!empty($settings['icon'])) {
            $this->add_render_attribute('icon', 'class', $settings['icon']);
            $this->add_render_attribute('icon', 'aria-hidden', 'true');
        }

        $migrated = isset($settings['__fa4_migrated']['selected_icon']);
        $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

        $this->add_render_attribute('description_text', 'class', 'dinopack-icon-box-description');
        $this->add_render_attribute('title_text', 'class', 'dinopack-icon-box-title');

        $this->add_inline_editing_attributes('title_text', 'none');
        $this->add_inline_editing_attributes('description_text');
        
        ?>
        <div class="dinopack-icon-box">
            <?php if ($settings['position'] === 'left' || $settings['position'] === 'top') : ?>
                <div <?php $this->print_render_attribute_string('icon-wrapper'); ?>>
                    <<?php echo esc_attr( $icon_tag ); ?> <?php $this->print_render_attribute_string('icon'); ?>>
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
                    </<?php echo esc_attr( $icon_tag ); ?>>
                </div>
            <?php endif; ?>
            
            <div class="dinopack-icon-box-content">
                <<?php echo esc_attr($settings['title_size']); ?> <?php $this->print_render_attribute_string('title_text'); ?>>
                    <?php echo esc_html($settings['title_text']); ?>
                </<?php echo esc_attr($settings['title_size']); ?>>
                
                <p <?php $this->print_render_attribute_string('description_text'); ?>>
                    <?php echo wp_kses_post($settings['description_text']); ?>
				</p>
            </div>
            
            <?php if ($settings['position'] === 'right') : ?>
                <div <?php $this->print_render_attribute_string('icon-wrapper'); ?>>
                    <<?php echo esc_attr( $icon_tag ); ?> <?php $this->print_render_attribute_string('icon'); ?>>
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
                    </<?php echo esc_attr( $icon_tag ); ?>>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
} 