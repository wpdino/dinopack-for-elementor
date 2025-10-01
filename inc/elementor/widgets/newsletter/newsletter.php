<?php
/**
 * Newsletter Widget
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
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

// Include MailChimp API class
require_once __DIR__ . '/class-mailchimp-api.php';

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Newsletter widget.
 *
 * A newsletter subscription widget for Elementor.
 *
 * @since 1.0.0
 */
class Newsletter extends Widget_Base {

    /**
     * Constructor.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style(
            'dinopack-newsletter',
            plugins_url('frontend.css', __FILE__),
            [],
            DINOPACK_VERSION
        );
        
        wp_register_script(
            'dinopack-newsletter',
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
        return 'dinopack-newsletter';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     */
    public function get_title() {
        return esc_html__('MailChimp Newsletter', 'dinopack-for-elementor');
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     */
    public function get_icon() {
        return 'eicon-mail';
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
        return ['dinopack-newsletter'];
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
        return ['dinopack-newsletter'];
    }

    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     */
    public function get_keywords() {
        return ['newsletter', 'email', 'subscription', 'mailchimp', 'dinopack'];
    }

    /**
     * Get MailChimp lists from admin settings
     *
     * @since 1.0.0
     * @access private
     * @return array List options
     */
    private function get_mailchimp_lists() {
        $lists = ['' => esc_html__('Select a list...', 'dinopack-for-elementor')];
        
        // Get API key from admin settings
        $settings = get_option('dinopack_settings', []);
        $api_key = $settings['dinopack_mailchimp_api_key'] ?? '';
        
        if (empty($api_key) || strpos($api_key, '-') === false) {
            $lists[''] = esc_html__('Please configure MailChimp API key in DinoPack Settings', 'dinopack-for-elementor');
            return $lists;
        }

        try {
            require_once DINOPACK_PATH . 'inc/elementor/widgets/newsletter/class-mailchimp-api.php';
            $mailchimp = new \DinoPack_MailChimp_API($api_key);
            $mailchimp_lists = $mailchimp->get_lists(100);
            
            if ($mailchimp_lists && isset($mailchimp_lists['lists'])) {
                foreach ($mailchimp_lists['lists'] as $list) {
                    $lists[$list['id']] = $list['name'];
                }
            }
        } catch (Exception $e) {
            $lists[''] = esc_html__('Error loading lists. Please check your API key.', 'dinopack-for-elementor');
        }

        return $lists;
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        // MailChimp API Tab
        $this->start_controls_section(
            'section_mailchimp',
            [
                'label' => esc_html__('MailChimp API', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'mailchimp_list_id',
            [
                'label' => esc_html__('MailChimp List', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__('Select your MailChimp list. Make sure to configure your API key in DinoPack Settings first.', 'dinopack-for-elementor'),
                'options' => $this->get_mailchimp_lists(),
                'default' => '',
            ]
        );

        $this->end_controls_section();

        // Content Tab
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Newsletter Content', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Subscribe to Our Newsletter', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your title', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Get the latest updates and news delivered to your inbox.', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter your description', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'email_placeholder',
            [
                'label' => esc_html__('Email Placeholder', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Enter your email address', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter placeholder text', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'dinopack-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Subscribe', 'dinopack-for-elementor'),
                'placeholder' => esc_html__('Enter button text', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'form_action',
            [
                'label' => esc_html__('Form Action URL', 'dinopack-for-elementor'),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-mailchimp-url.com', 'dinopack-for-elementor'),
                'default' => [
                    'url' => '#',
                ],
                'description' => esc_html__('Enter your MailChimp or email service form action URL', 'dinopack-for-elementor'),
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => esc_html__('Show Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'dinopack-for-elementor'),
                'label_off' => esc_html__('Hide', 'dinopack-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => esc_html__('Icon', 'dinopack-for-elementor'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-envelope',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
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
                        'min' => 10,
                        'max' => 200,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 48,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dinopack-newsletter-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .dinopack-newsletter-icon svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'dinopack-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline',
                'options' => [
                    'inline' => esc_html__('Inline', 'dinopack-for-elementor'),
                    'stacked' => esc_html__('Stacked', 'dinopack-for-elementor'),
                ],
                'prefix_class' => 'dinopack-newsletter-',
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
                'selector' => '{{WRAPPER}} .dinopack-newsletter',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-newsletter',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => esc_html__('Box Shadow', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-newsletter',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-newsletter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-newsletter-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-newsletter-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .dinopack-newsletter-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => esc_html__('Typography', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-newsletter-description',
            ]
        );

        $this->add_responsive_control(
            'description_margin',
            [
                'label' => esc_html__('Margin', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Form Style
        $this->start_controls_section(
            'section_form_style',
            [
                'label' => esc_html__('Form', 'dinopack-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'input_color',
            [
                'label' => esc_html__('Input Text Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_background',
            [
                'label' => esc_html__('Input Background', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color',
            [
                'label' => esc_html__('Input Border Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-input' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label' => esc_html__('Input Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_border_radius',
            [
                'label' => esc_html__('Input Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                'selector' => '{{WRAPPER}} .dinopack-newsletter-button',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-newsletter-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => esc_html__('Border', 'dinopack-for-elementor'),
                'selector' => '{{WRAPPER}} .dinopack-newsletter-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'dinopack-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .dinopack-newsletter-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-newsletter-button',
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
                    '{{WRAPPER}} .dinopack-newsletter-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label' => esc_html__('Background Color', 'dinopack-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dinopack-newsletter-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .dinopack-newsletter-button:hover',
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
        
        // Check if MailChimp is configured
        $admin_settings = get_option('dinopack_settings', []);
        $api_key = $admin_settings['dinopack_mailchimp_api_key'] ?? '';
        $mailchimp_configured = !empty($api_key) && !empty($settings['mailchimp_list_id']);
        
        // Enqueue scripts
        wp_enqueue_style('dinopack-newsletter');
        wp_enqueue_script('dinopack-newsletter');
        
        // Localize script for AJAX with secure data (only once)
        static $script_localized = false;
        if (!$script_localized) {
            wp_localize_script('dinopack-newsletter', 'dinopack_newsletter_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('dinopack_newsletter_ajax')
            ]);
            $script_localized = true;
        }
        
        ?>
        <div class="dinopack-newsletter" 
             <?php if ($mailchimp_configured): ?>
             data-widget-id="<?php echo esc_attr($this->get_id()); ?>"
             <?php endif; ?>>
            
            <?php if ($settings['show_icon'] === 'yes' && !empty($settings['icon']['value'])): ?>
                <div class="dinopack-newsletter-icon">
                    <?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($settings['title']): ?>
                <h3 class="dinopack-newsletter-title"><?php echo esc_html($settings['title']); ?></h3>
            <?php endif; ?>
            
            <?php if ($settings['description']): ?>
                <p class="dinopack-newsletter-description"><?php echo esc_html($settings['description']); ?></p>
            <?php endif; ?>
            
            <?php if ($mailchimp_configured): ?>
                <!-- MailChimp AJAX Form -->
                <form class="dinopack-newsletter-form" 
                      data-widget-id="<?php echo esc_attr($this->get_id()); ?>"
                      data-list-id="<?php echo esc_attr($settings['mailchimp_list_id']); ?>"
                      data-configured="1">
                    <div class="dinopack-newsletter-form-group">
                        <input type="email" 
                               name="email" 
                               class="dinopack-newsletter-input" 
                               placeholder="<?php echo esc_attr($settings['email_placeholder']); ?>" 
                               required>
                        <button type="submit" class="dinopack-newsletter-button">
                            <?php echo esc_html($settings['button_text']); ?>
                        </button>
                    </div>
                    <div class="dinopack-newsletter-message" style="display: none;"></div>
                </form>
            <?php else: ?>
                <!-- Fallback to external form -->
                <?php 
                $target = $settings['form_action']['is_external'] ? ' target="_blank"' : '';
                $nofollow = $settings['form_action']['nofollow'] ? ' rel="nofollow"' : '';
                ?>
                <form class="dinopack-newsletter-form" action="<?php echo esc_url($settings['form_action']['url']); ?>" method="post" <?php echo esc_attr( $target ) . ' ' . esc_attr( $nofollow ); ?>>
                    <div class="dinopack-newsletter-form-group">
                        <input type="email" 
                               name="EMAIL" 
                               class="dinopack-newsletter-input" 
                               placeholder="<?php echo esc_attr($settings['email_placeholder']); ?>" 
                               required>
                        <button type="submit" class="dinopack-newsletter-button">
                            <?php echo esc_html($settings['button_text']); ?>
                        </button>
                    </div>
                </form>
                
                <?php if (current_user_can('edit_posts')): ?>
                    <!-- Admin notice for missing MailChimp configuration -->
                    <div class="dinopack-newsletter-admin-notice">
                        <small style="color: #666; font-style: italic;">
                            <i class="fas fa-info-circle"></i>
                            MailChimp API not configured. Form will submit to external URL. 
                            <a href="<?php echo esc_url( admin_url('admin.php?page=dinopack-settings') ); ?>" target="_blank">
                                Configure MailChimp API
                            </a>
                        </small>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
    }
}
