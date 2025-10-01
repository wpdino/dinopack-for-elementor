<?php
/**
 * Price Table Widget
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
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Price Table widget.
 *
 * A price table widget for Elementor.
 *
 * @since 1.0.0
 */
class Price_Table extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-price-table',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        
        wp_register_script(
            'dinopack-price-table',
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
        return 'dinopack-price-table';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Price Table', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-price-table';
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
        return ['dinopack-price-table'];
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
        return ['dinopack-price-table'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['price', 'table', 'pricing', 'plan', 'dinopack'];
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
                'label' => esc_html__('Price Table Content', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Basic Plan', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your title', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Perfect for small businesses', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your description', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'price',
            [
                'label' => esc_html__('Price', 'dinopack-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '59',
                'placeholder' => esc_html__('Enter price', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'currency',
            [
                'label' => esc_html__('Currency', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '$',
                'placeholder' => esc_html__('Enter currency symbol', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'period',
            [
                'label' => esc_html__('Period', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('/year', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter period', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'features',
            [
                'label' => esc_html__('Features', 'dinopack-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'feature_text',
                        'label' => esc_html__('Feature Text', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'default' => esc_html__('Feature item', 'dinopack-for-elementor'),
                        'placeholder' => esc_html__('Enter feature text', 'dinopack-for-elementor'),
                    ],
                    [
                        'name' => 'feature_icon',
                        'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::ICONS,
						'fa4compatibility' => 'icon',
                        'default' => [
                            'value' => 'fas fa-check',
                            'library' => 'fa-solid',
                        ],
						'recommended' => [
							'fa-regular' => [
								'check-square',
								'window-close',
							],
						],
						'fa-solid' => [
							'check',
						],
                    ],
                    [
                        'name' => 'feature_icon_color',
                        'label' => esc_html__('Icon Color', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} {{CURRENT_ITEM}} .dinopack-price-table-feature-icon' => 'color: {{VALUE}};',
                            '{{WRAPPER}} {{CURRENT_ITEM}} .dinopack-price-table-feature-icon svg' => 'fill: {{VALUE}};',
                        ],
                    ],
                ],
                'default' => [
                    [
                        'feature_text' => esc_html__('10 Projects', 'dinopack-for-elementor'),
                        'feature_icon' => [
                            'value' => 'fas fa-check',
                            'library' => 'fa-solid',
                        ],
                        'feature_icon_color' => '#6F9C50',
                    ],
                    [
                        'feature_text' => esc_html__('5GB Storage', 'dinopack-for-elementor'),
                        'feature_icon' => [
                            'value' => 'fas fa-check',
                            'library' => 'fa-solid',
                        ],
                        'feature_icon_color' => '#6F9C50',
                    ],
                    [
                        'feature_text' => esc_html__('Email Support', 'dinopack-for-elementor'),
                        'feature_icon' => [
                            'value' => 'fas fa-check',
                            'library' => 'fa-solid',
                        ],
                        'feature_icon_color' => '#6F9C50',
                    ],
                ],
                'title_field' => '{{{ feature_text }}}',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Get Started', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter button text', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_url',
            [
                'label' => esc_html__('Button URL', 'dinopack-for-elementor'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'dinopack-for-elementor'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'popular',
            [
                'label' => esc_html__('Popular', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'popular_text',
            [
                'label' => esc_html__('Popular Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Most Popular', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter popular text', 'dinopack-for-elementor'),
                'condition' => [
                    'popular' => 'yes',
                ],
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

        // $this->add_control(
        //     'layout',
        //     [
        //         'label' => esc_html__('Layout', 'dinopack-for-elementor'),
        //         'type' => Controls_Manager::SELECT,
        //         'default' => 'style-1',
        //         'options' => [
        //             'style-1' => esc_html__('Style 1', 'dinopack-for-elementor'),
        //             'style-2' => esc_html__('Style 2', 'dinopack-for-elementor'),
        //             'style-3' => esc_html__('Style 3', 'dinopack-for-elementor'),
        //         ],
        //         'prefix_class' => 'dinopack-price-table-',
        //     ]
        // );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'label' => esc_html__('Background', 'dinopack-for-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .dinopack-price-table',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-price-table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title Style
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Price Style
        $this->start_controls_section(
            'section_price_style',
            [
                'label' => esc_html__('Price', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table-price',
            ]
        );

        $this->add_control(
            'currency_color',
            [
                'label' => esc_html__('Currency Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-currency' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'currency_typography',
                'label' => esc_html__('Currency Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table-currency',
            ]
        );

        $this->end_controls_section();

        // Button Style
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__('Button', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table-button',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-price-table-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-price-table-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
            'button_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-price-table-button',
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
            'button_hover_color',
            [
                'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-price-table-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-price-table-button:hover',
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

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
        
        $target = $settings['button_url']['is_external'] ? ' target="_blank"' : '';
        $nofollow = $settings['button_url']['nofollow'] ? ' rel="nofollow"' : '';
        
        ?>
        <div class="dinopack-price-table">
            <?php if ($settings['popular'] === 'yes'): ?>
                <div class="dinopack-price-table-popular">
                    <span><?php echo esc_html($settings['popular_text']); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="dinopack-price-table-header">
                <?php if ($settings['title']): ?>
                    <h3 class="dinopack-price-table-title"><?php echo esc_html($settings['title']); ?></h3>
                <?php endif; ?>
                
                <?php if ($settings['description']): ?>
                    <p class="dinopack-price-table-description"><?php echo esc_html($settings['description']); ?></p>
                <?php endif; ?>
                
                <div class="dinopack-price-table-price-wrapper">
                    <span class="dinopack-price-table-currency"><?php echo esc_html($settings['currency']); ?></span>
                    <span class="dinopack-price-table-price"><?php echo esc_html($settings['price']); ?></span>
                    <span class="dinopack-price-table-period"><?php echo esc_html($settings['period']); ?></span>
                </div>
            </div>
            
            <div class="dinopack-price-table-features">
                <?php if (!empty($settings['features'])): ?>
                    <ul class="dinopack-price-table-features-list">
                        <?php foreach ($settings['features'] as $index => $feature): ?>
                            <li class="dinopack-price-table-feature elementor-repeater-item-<?php echo esc_attr($feature['_id']); ?>">
                                <?php if (!empty($feature['feature_icon']['value'])): ?>
                                    <span class="dinopack-price-table-feature-icon">
                                        <?php Icons_Manager::render_icon($feature['feature_icon'], ['aria-hidden' => 'true']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="dinopack-price-table-feature-icon">
                                        <?php 
                                        // Fallback icon using Elementor's icon system
                                        $fallback_icon = [
                                            'value' => 'fas fa-check',
                                            'library' => 'fa-solid',
                                        ];
                                        Icons_Manager::render_icon($fallback_icon, ['aria-hidden' => 'true']); 
                                        ?>
                                    </span>
                                <?php endif; ?>
                                <span class="dinopack-price-table-feature-text"><?php echo esc_html($feature['feature_text']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="dinopack-price-table-footer">
                <?php if ($settings['button_text']): ?>
                    <?php
                    $button_classes = ['dinopack-price-table-button'];
                    if (!empty($settings['button_hover_animation'])) {
                        $button_classes[] = 'elementor-animation-' . $settings['button_hover_animation'];
                    }
                    ?>
                    <a href="<?php echo esc_url($settings['button_url']['url']); ?>" 
                       class="<?php echo esc_attr(implode(' ', $button_classes)); ?>" 
                       <?php echo esc_attr( $target ) . esc_attr( $nofollow ); ?>>
                        <?php echo esc_html($settings['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
