<?php
/**
 * Car Specs Widget
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
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Car Specs widget.
 *
 * A car specifications widget for Elementor.
 *
 * @since 1.0.0
 */
class Car_Specs extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-car-specs',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        
        wp_register_script(
            'dinopack-car-specs',
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
        return 'dinopack-car-specs';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Car Specs', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-cogs';
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
     * @since 1.0.0
     * @access public
     * @return array Style slugs.
     */
    public function get_style_depends() {
        return ['dinopack-car-specs'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return ['dinopack-car-specs'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['car', 'vehicle', 'specs', 'automotive', 'dinopack'];
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
                'label' => esc_html__('Vehicle Information', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'vehicle_name',
            [
                'label' => esc_html__('Vehicle Name', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('2024 Model X', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter vehicle name', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'vehicle_image',
            [
                'label' => esc_html__('Vehicle Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'large',
                'separator' => 'after',
            ]
        );

        $this->end_controls_section();

        // Specifications Section
        $this->start_controls_section(
            'section_specs',
            [
                'label' => esc_html__('Specifications', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'specifications',
            [
                'label' => esc_html__('Specifications', 'dinopack-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'spec_category',
                        'label' => esc_html__('Category', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'default' => esc_html__('Engine', 'dinopack-for-elementor'),
                        'placeholder' => esc_html__('Enter category name', 'dinopack-for-elementor'),
                    ],
                    [
                        'name' => 'spec_items',
                        'label' => esc_html__('Specification Items', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::REPEATER,
                        'fields' => [
                            [
                                'name' => 'spec_label',
                                'label' => esc_html__('Label', 'dinopack-for-elementor'),
                                'type' => Controls_Manager::TEXT,
                                'default' => esc_html__('Engine Type', 'dinopack-for-elementor'),
                                'placeholder' => esc_html__('Enter label', 'dinopack-for-elementor'),
                            ],
                            [
                                'name' => 'spec_value',
                                'label' => esc_html__('Value', 'dinopack-for-elementor'),
                                'type' => Controls_Manager::TEXT,
                                'default' => esc_html__('V6 3.5L', 'dinopack-for-elementor'),
                                'placeholder' => esc_html__('Enter value', 'dinopack-for-elementor'),
                            ],
                            [
                                'name' => 'spec_icon',
                                'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                                'type' => Controls_Manager::ICONS,
                                'fa4compatibility' => 'icon',
                                'default' => [
                                    'value' => 'fas fa-cog',
                                    'library' => 'fa-solid',
                                ],
                            ],
                        ],
                        'default' => [
                            [
                                'spec_label' => esc_html__('Engine Type', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('V6 3.5L', 'dinopack-for-elementor'),
                            ],
                            [
                                'spec_label' => esc_html__('Horsepower', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('300 HP', 'dinopack-for-elementor'),
                            ],
                        ],
                        'title_field' => '{{{ spec_label }}}',
                    ],
                ],
                'default' => [
                    [
                        'spec_category' => esc_html__('Engine', 'dinopack-for-elementor'),
                        'spec_items' => [
                            [
                                'spec_label' => esc_html__('Engine Type', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('V6 3.5L', 'dinopack-for-elementor'),
                            ],
                            [
                                'spec_label' => esc_html__('Horsepower', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('300 HP', 'dinopack-for-elementor'),
                            ],
                        ],
                    ],
                    [
                        'spec_category' => esc_html__('Transmission', 'dinopack-for-elementor'),
                        'spec_items' => [
                            [
                                'spec_label' => esc_html__('Type', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('Automatic', 'dinopack-for-elementor'),
                            ],
                            [
                                'spec_label' => esc_html__('Gears', 'dinopack-for-elementor'),
                                'spec_value' => esc_html__('8-Speed', 'dinopack-for-elementor'),
                            ],
                        ],
                    ],
                ],
                'title_field' => '{{{ spec_category }}}',
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('Layout', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => [
                    'vertical' => esc_html__('Vertical', 'dinopack-for-elementor'),
                    'horizontal' => esc_html__('Horizontal', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-car-specs-layout-',
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => esc_html__('Show Image', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_icons',
            [
                'label' => esc_html__('Show Icons', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => esc_html__('Background', 'dinopack-for-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .dinopack-car-specs',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Vehicle Name Style
        $this->start_controls_section(
            'section_vehicle_name_style',
            [
                'label' => esc_html__('Vehicle Name', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'vehicle_name_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'vehicle_name_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs-name',
            ]
        );

        $this->add_responsive_control(
            'vehicle_name_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Category Style
        $this->start_controls_section(
            'section_category_style',
            [
                'label' => esc_html__('Category', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'category_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-category' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs-category',
            ]
        );

        $this->end_controls_section();

        // Spec Item Style
        $this->start_controls_section(
            'section_spec_item_style',
            [
                'label' => esc_html__('Specification Item', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'spec_item_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs-item',
            ]
        );

        $this->add_responsive_control(
            'spec_item_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'spec_item_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Label Style
        $this->start_controls_section(
            'section_label_style',
            [
                'label' => esc_html__('Label', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs-label',
            ]
        );

        $this->end_controls_section();

        // Value Style
        $this->start_controls_section(
            'section_value_style',
            [
                'label' => esc_html__('Value', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'value_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'value_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-car-specs-value',
            ]
        );

        $this->end_controls_section();

        // Icon Style
        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_icons' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dinopack-car-specs-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-car-specs-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-car-specs-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
        
        if (empty($settings['specifications'])) {
            return;
        }

        $image_size = $settings['thumbnail_size'] ?? 'large';
        ?>
        <div class="dinopack-car-specs">
            <?php if (!empty($settings['vehicle_name'])): ?>
                <h2 class="dinopack-car-specs-name"><?php echo esc_html($settings['vehicle_name']); ?></h2>
            <?php endif; ?>
            
            <?php if ($settings['show_image'] === 'yes' && !empty($settings['vehicle_image']['url'])): ?>
                <div class="dinopack-car-specs-image">
                    <?php
                    echo wp_get_attachment_image(
                        $settings['vehicle_image']['id'],
                        $image_size,
                        false,
                        [
                            'alt' => esc_attr($settings['vehicle_name']),
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="dinopack-car-specs-content">
                <?php foreach ($settings['specifications'] as $spec): ?>
                    <?php if (!empty($spec['spec_category'])): ?>
                        <div class="dinopack-car-specs-category-wrapper">
                            <h3 class="dinopack-car-specs-category"><?php echo esc_html($spec['spec_category']); ?></h3>
                            
                            <?php if (!empty($spec['spec_items'])): ?>
                                <div class="dinopack-car-specs-items">
                                    <?php foreach ($spec['spec_items'] as $item): ?>
                                        <div class="dinopack-car-specs-item">
                                            <?php if ($settings['show_icons'] === 'yes' && !empty($item['spec_icon']['value'])): ?>
                                                <div class="dinopack-car-specs-icon">
                                                    <?php Icons_Manager::render_icon($item['spec_icon'], ['aria-hidden' => 'true']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="dinopack-car-specs-item-content">
                                                <?php if (!empty($item['spec_label'])): ?>
                                                    <span class="dinopack-car-specs-label"><?php echo esc_html($item['spec_label']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($item['spec_value'])): ?>
                                                    <span class="dinopack-car-specs-value"><?php echo esc_html($item['spec_value']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}

