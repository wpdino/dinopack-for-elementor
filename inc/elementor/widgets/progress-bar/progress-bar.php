<?php
/**
 * Progress Bar Widget
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
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Progress Bar widget.
 *
 * A progress bar widget for Elementor.
 *
 * @since 1.0.0
 */
class Progress_Bar extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        // Register styles and scripts
        wp_register_style(
            'dinopack-progress-bar',
            DINOPACK_URL . 'inc/elementor/widgets/progress-bar/frontend.css',
            [],
            DINOPACK_VERSION 
        );
        wp_register_script(
            'dinopack-progress-bar',
            DINOPACK_URL . 'inc/elementor/widgets/progress-bar/frontend.js',
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
        return 'dinopack-progress-bar';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Progress Bar', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-skill-bar';
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
     * @return array Style dependencies.
     * @since 1.0.0
     * @access public
     */
    public function get_style_depends() {
        return ['dinopack-progress-bar'];
    }

    /**
     * Get script dependencies.
     *
     * @return array Script dependencies.
     * @since 1.0.0
     * @access public
     */
    public function get_script_depends() {
        return ['dinopack-progress-bar'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['progress', 'bar', 'skill', 'dinopack'];
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
            'section_progress',
            [
                'label' => esc_html__('Progress Bar', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Skill Name', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter progress bar title', 'dinopack-for-elementor'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'progress_value',
            [
                'label' => esc_html__('Progress Value', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 75,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'progress_prefix',
            [
                'label' => esc_html__('Progress Prefix', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__('e.g., $', 'dinopack-for-elementor'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'progress_suffix',
            [
                'label' => esc_html__('Progress Suffix', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '%',
                'placeholder' => esc_html__('e.g., %', 'dinopack-for-elementor'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'display_label',
            [
                'label' => esc_html__('Show Progress Value', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'progress_type',
            [
                'label' => esc_html__('Type', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'line',
                'options' => [
                    'line' => esc_html__('Line', 'dinopack-for-elementor'),
                    'circle' => esc_html__('Circle', 'dinopack-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'animation_duration',
            [
                'label' => esc_html__('Animation Duration', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1500,
                'min' => 100,
                'step' => 100,
                'description' => esc_html__('Animation duration in milliseconds', 'dinopack-for-elementor'),
            ]
        );

        $this->end_controls_section();

        // Style Tab - Progress
        $this->start_controls_section(
            'section_style_progress',
            [
                'label' => esc_html__('Progress Bar', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bar_color',
            [
                'label' => esc_html__('Progress Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#6F9C50',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-progress' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .dinopack-progress-bar-circle svg .path' => 'stroke: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bar_bg_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#E8F5E8',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-track' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .dinopack-progress-bar-circle svg .track' => 'stroke: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bar_height',
            [
                'label' => esc_html__('Thickness', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 60,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-track' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'progress_type' => 'line',
                ],
            ]
        );

        $this->add_control(
            'circle_size',
            [
                'label' => esc_html__('Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'condition' => [
                    'progress_type' => 'circle',
                ],
            ]
        );

        $this->add_control(
            'stroke_width',
            [
                'label' => esc_html__('Thickness', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 40,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'condition' => [
                    'progress_type' => 'circle',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Roundness', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-track, {{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-progress' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'progress_type' => 'line',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'bar_box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-progress-bar-line .dinopack-progress-bar-progress',
                'condition' => [
                    'progress_type' => 'line',
                ],
            ]
        );


        $this->end_controls_section();

        // Style Tab - Title
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title & Percentage', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .dinopack-progress-bar-title',
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => esc_html__('Label Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-progress-bar-label' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'display_label' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .dinopack-progress-bar-label',
                'condition' => [
                    'display_label' => 'yes',
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
                    '{{WRAPPER}} .dinopack-progress-bar-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        $title = $settings['title'];
        $progress_value = $settings['progress_value'];
        $progress_prefix = $settings['progress_prefix'];
        $progress_suffix = $settings['progress_suffix'];
        $progress_type = $settings['progress_type'];
        $animation_duration = $settings['animation_duration'];
        $display_label = $settings['display_label'] === 'yes';
        
        $this->add_render_attribute('progress-bar', [
            'class' => 'dinopack-progress-bar',
            'data-progress' => $progress_value,
            'data-label-text' => $progress_value,
            'data-label-prefix' => $progress_prefix,
            'data-label-suffix' => $progress_suffix,
            'data-animation-duration' => $animation_duration,
        ]);


        $title_html = '<div class="dinopack-progress-bar-title">' . esc_html($title) . '</div>';
        $label_html = $display_label ? '<div class="dinopack-progress-bar-label"><span class="dinopack-progress-bar-label-prefix">' . esc_html($progress_prefix) . '</span><span class="dinopack-progress-bar-label-text">' . esc_html($progress_value) . '</span><span class="dinopack-progress-bar-label-suffix">' . esc_html($progress_suffix) . '</span></div>' : '';
        
        if ($progress_type === 'line') {
            $this->add_render_attribute('progress-bar', 'class', 'dinopack-progress-bar-line');
            ?>
            <div <?php $this->print_render_attribute_string('progress-bar'); ?>>
                <div class="dinopack-progress-bar-header">
                    <?php echo wp_kses_post($title_html); ?>
                    <?php echo wp_kses_post($label_html); ?>
                </div>
                
                <div class="dinopack-progress-bar-container">
                    <div class="dinopack-progress-bar-track">
                        <div class="dinopack-progress-bar-progress">
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            // Circle
            $this->add_render_attribute('progress-bar', 'class', 'dinopack-progress-bar-circle');
            $size = isset($settings['circle_size']['size']) ? $settings['circle_size']['size'] : 150;
            $stroke_width = isset($settings['stroke_width']['size']) ? $settings['stroke_width']['size'] : 20;
            
            $radius = ($size / 2) - ($stroke_width / 2);
            $circumference = 2 * M_PI * $radius;
            $progress_value_circumference = $circumference - ($progress_value / 100) * $circumference;
            
            ?>
            <div <?php $this->print_render_attribute_string('progress-bar'); ?>>
                <div class="dinopack-progress-bar-circle-container">
                    <svg width="<?php echo esc_attr($size); ?>" height="<?php echo esc_attr($size); ?>" viewBox="0 0 <?php echo esc_attr($size); ?> <?php echo esc_attr($size); ?>" class="dinopack-progress-svg">
                        <!-- Background track -->
                        <circle 
                            class="track" 
                            cx="<?php echo esc_attr($size / 2); ?>" 
                            cy="<?php echo esc_attr($size / 2); ?>" 
                            r="<?php echo esc_attr($radius); ?>" 
                            fill="none" 
                            stroke-width="<?php echo esc_attr($stroke_width); ?>"
                        ></circle>
                        
                        <!-- Progress path -->
                        <circle 
                            class="path" 
                            cx="<?php echo esc_attr($size / 2); ?>" 
                            cy="<?php echo esc_attr($size / 2); ?>" 
                            r="<?php echo esc_attr($radius); ?>" 
                            fill="none" 
                            stroke-width="<?php echo esc_attr($stroke_width); ?>"
                            data-circumference="<?php echo esc_attr($circumference); ?>"
                            data-progress-value="<?php echo esc_attr($progress_value_circumference); ?>"
                            stroke-dasharray="<?php echo esc_attr($circumference); ?> <?php echo esc_attr($circumference); ?>"
                            stroke-dashoffset="<?php echo esc_attr($circumference); ?>"
                        ></circle>
                    </svg>
                    
                    <?php if ($display_label) : ?>
                        <div class="dinopack-progress-bar-circle-label">
                            <?php echo wp_kses_post($label_html); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="dinopack-progress-bar-header">
                    <?php echo wp_kses_post($title_html); ?>
                </div>
            </div>
            <?php
        }
    }
} 