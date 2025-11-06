<?php
/**
 * Restaurant Menu Widget
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
use Elementor\Utils;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Restaurant Menu widget.
 *
 * A restaurant menu widget for Elementor.
 *
 * @since 1.0.0
 */
class Restaurant_Menu extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-restaurant-menu',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION 
        );
        
        wp_register_script(
            'dinopack-restaurant-menu',
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
        return 'dinopack-restaurant-menu';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('Restaurant Menu', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-menu-card';
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
        return ['dinopack-restaurant-menu'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script slugs.
     */
    public function get_script_depends() {
        return ['dinopack-restaurant-menu'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['restaurant', 'menu', 'food', 'dining', 'dinopack'];
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
                'label' => esc_html__('Menu Content', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'menu_title',
            [
                'label' => esc_html__('Menu Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Our Menu', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter menu title', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'menu_items',
            [
                'label' => esc_html__('Menu Items', 'dinopack-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'item_name',
                        'label' => esc_html__('Item Name', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'default' => esc_html__('Menu Item', 'dinopack-for-elementor'),
                        'placeholder' => esc_html__('Enter item name', 'dinopack-for-elementor'),
                    ],
                    [
                        'name' => 'item_description',
                        'label' => esc_html__('Description', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::TEXTAREA,
                        'default' => esc_html__('Delicious menu item description', 'dinopack-for-elementor'),
                        'placeholder' => esc_html__('Enter item description', 'dinopack-for-elementor'),
                    ],
                    [
                        'name' => 'item_price',
                        'label' => esc_html__('Price', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::TEXT,
                        'default' => '$12.99',
                        'placeholder' => esc_html__('Enter price', 'dinopack-for-elementor'),
                    ],
                    [
                        'name' => 'item_image',
                        'label' => esc_html__('Image', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ],
                    [
                        'name' => 'spicy',
                        'label' => esc_html__('Spicy', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                        'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                        'return_value' => 'yes',
                        'default' => '',
                    ],
                    [
                        'name' => 'vegetarian',
                        'label' => esc_html__('Vegetarian', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                        'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                        'return_value' => 'yes',
                        'default' => '',
                    ],
                    [
                        'name' => 'gluten_free',
                        'label' => esc_html__('Gluten Free', 'dinopack-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                        'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                        'return_value' => 'yes',
                        'default' => '',
                    ],
                ],
                'default' => [
                    [
                        'item_name' => esc_html__('Bruschetta', 'dinopack-for-elementor'),
                        'item_description' => esc_html__('Fresh tomatoes, basil, and mozzarella on toasted bread', 'dinopack-for-elementor'),
                        'item_price' => '$8.99',
                        'vegetarian' => 'yes',
                    ],
                    [
                        'item_name' => esc_html__('Grilled Salmon', 'dinopack-for-elementor'),
                        'item_description' => esc_html__('Fresh Atlantic salmon with lemon butter sauce', 'dinopack-for-elementor'),
                        'item_price' => '$24.99',
                    ],
                    [
                        'item_name' => esc_html__('Tiramisu', 'dinopack-for-elementor'),
                        'item_description' => esc_html__('Classic Italian dessert with coffee and mascarpone', 'dinopack-for-elementor'),
                        'item_price' => '$9.99',
                    ],
                ],
                'title_field' => '{{{ item_name }}}',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium',
                'separator' => 'before',
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
                'default' => 'list',
                'options' => [
                    'list' => esc_html__('List', 'dinopack-for-elementor'),
                    'grid' => esc_html__('Grid', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-menu-layout-',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__('Show Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_images',
            [
                'label' => esc_html__('Show Images', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
                'label_off' => esc_html__('No', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_badges',
            [
                'label' => esc_html__('Show Badges', 'dinopack-for-elementor'),
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
                'selector' => '{{WRAPPER}} .dinopack-restaurant-menu',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-restaurant-menu',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-restaurant-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-restaurant-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-menu-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-menu-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Item Style
        $this->start_controls_section(
            'section_item_style',
            [
                'label' => esc_html__('Menu Item', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-menu-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Item Name Style
        $this->start_controls_section(
            'section_item_name_style',
            [
                'label' => esc_html__('Item Name', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_name_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-item-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_name_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-menu-item-name',
            ]
        );

        $this->end_controls_section();

        // Description Style
        $this->start_controls_section(
            'section_description_style',
            [
                'label' => esc_html__('Description', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-item-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-menu-item-description',
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
                    '{{WRAPPER}} .dinopack-menu-item-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-menu-item-price',
            ]
        );

        $this->end_controls_section();

        // Badge Style
        $this->start_controls_section(
            'section_badge_style',
            [
                'label' => esc_html__('Badges', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_badges' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'badge_spicy_color',
            [
                'label' => esc_html__('Spicy Badge Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff4444',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-badge-spicy' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_vegetarian_color',
            [
                'label' => esc_html__('Vegetarian Badge Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#6F9C50',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-badge-vegetarian' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'badge_gluten_free_color',
            [
                'label' => esc_html__('Gluten Free Badge Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#4a90e2',
                'selectors' => [
                    '{{WRAPPER}} .dinopack-menu-badge-gluten-free' => 'background-color: {{VALUE}};',
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
        
        if (empty($settings['menu_items'])) {
            return;
        }

        $image_size = $settings['thumbnail_size'] ?? 'medium';
        ?>
        <div class="dinopack-restaurant-menu">
            <?php if ($settings['show_title'] === 'yes' && !empty($settings['menu_title'])): ?>
                <h2 class="dinopack-menu-title"><?php echo esc_html($settings['menu_title']); ?></h2>
            <?php endif; ?>
            
            <div class="dinopack-menu-items">
                <?php foreach ($settings['menu_items'] as $index => $item): ?>
                    <?php 
                    $has_image = ($settings['show_images'] === 'yes' && !empty($item['item_image']['url']));
                    $item_classes = ['dinopack-menu-item', 'elementor-repeater-item-' . esc_attr($item['_id'])];
                    if (!$has_image) {
                        $item_classes[] = 'dinopack-menu-item-no-image';
                    }
                    ?>
                    <div class="<?php echo esc_attr(implode(' ', $item_classes)); ?>">
                        <?php if ($has_image): ?>
                            <div class="dinopack-menu-item-image">
                                <?php
                                if (!empty($item['item_image']['id'])) {
                                    // Use WordPress attachment image if ID exists
                                    echo wp_get_attachment_image(
                                        $item['item_image']['id'],
                                        $image_size,
                                        false,
                                        [
                                            'alt' => esc_attr($item['item_name']),
                                        ]
                                    );
                                } else {
                                    // Use placeholder image URL if no ID (Elementor placeholder)
                                    ?>
                                    <img src="<?php echo esc_url($item['item_image']['url']); ?>" alt="<?php echo esc_attr($item['item_name']); ?>" />
                                    <?php
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="dinopack-menu-item-content">
                            <div class="dinopack-menu-item-header">
                                <h4 class="dinopack-menu-item-name"><?php echo esc_html($item['item_name']); ?></h4>
                                <?php if (!empty($item['item_price'])): ?>
                                    <span class="dinopack-menu-item-price"><?php echo esc_html($item['item_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($item['item_description'])): ?>
                                <p class="dinopack-menu-item-description"><?php echo esc_html($item['item_description']); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($settings['show_badges'] === 'yes'): ?>
                                <div class="dinopack-menu-item-badges">
                                    <?php if ($item['spicy'] === 'yes'): ?>
                                        <span class="dinopack-menu-badge dinopack-menu-badge-spicy"><?php echo esc_html__('Spicy', 'dinopack-for-elementor'); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['vegetarian'] === 'yes'): ?>
                                        <span class="dinopack-menu-badge dinopack-menu-badge-vegetarian"><?php echo esc_html__('Vegetarian', 'dinopack-for-elementor'); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['gluten_free'] === 'yes'): ?>
                                        <span class="dinopack-menu-badge dinopack-menu-badge-gluten-free"><?php echo esc_html__('Gluten Free', 'dinopack-for-elementor'); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}

