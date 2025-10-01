<?php

namespace DinoPack\Widgets;

use \Elementor\Widget_Base;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use Elementor\Utils;
use Elementor\Icons_Manager;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Dinopack Elementor Widgets - Advanced Heading Widget.
 *
 * Elementor widget that inserts a customizable advanced heading.
 *
 * @since 1.0.0
 */
class Advanced_Heading extends Widget_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 
			'dinopack-advanced-heading', 
			plugins_url( 'frontend.css', __FILE__ ), 
			[], 
			DINOPACK_VERSION 
		);
		wp_register_script( 
			'dinopack-advanced-heading', 
			plugins_url( 'frontend.js', __FILE__ ), 
			[ 'jquery' ], 
			DINOPACK_VERSION, 
			true 
		);
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dinopack-for-elementor-advanced-heading';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Advanced Heading', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-heading';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'dinopack-for-elementor' ];
	}

	/**
	 * Style Dependencies.
	 *
	 * Returns all the styles the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Style slugs.
	 */
	public function get_style_depends() {
		return [ 'dinopack-advanced-heading' ];
	}

	/**
	 * Script Dependencies.
	 *
	 * Returns all the scripts the widget depends on.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Script slugs.
	 */
	public function get_script_depends() {
		return [ 'dinopack-advanced-heading' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'advanced', 'heading', 'title', 'text' ];
	}

	/**
	 * Register Controls.
	 *
	 * Registers all the controls for this widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_masking_section();
		$this->register_style_controls();
	}

	/**
	 * Register Content Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'_section_advanced_heading',	
			[
				'label' => esc_html__( 'Advanced Heading', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'heading_text',
			[
				'label' => esc_html__( 'Heading Text', 'dinopack-for-elementor' ),
				'ai' => [
					'type' => 'text',
				],
				'dynamic' => [
					'active' => true,
				],
				'type'    => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your heading text', 'dinopack-for-elementor' ),
				'default' => esc_html__( 'Advanced Heading Text', 'dinopack-for-elementor' ),
			]
		);

		// Add rotator helper description (only shown when word rotator is enabled)
		$this->add_control(
			'rotator_helper',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					'<div class="elementor-control-description">%s <code>%s</code> %s</div>',
					esc_html__( 'Use', 'dinopack-for-elementor' ),
					esc_html__( '[ROTATOR]', 'dinopack-for-elementor' ),
					esc_html__( 'to add rotating words in your heading text.', 'dinopack-for-elementor' )
				),
				'condition' => [
					'enable_word_rotator' => 'yes',
				],
			]
		);

		$this->add_control(
			'heading_tag',
			[
				'label' => esc_html__( 'Heading Tag', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'p' => 'P',
					'div' => 'Div',
				],
				'default' => 'h2',
			]
		);

		$this->add_control(
			'heading_link',
			[
				'label'   => esc_html__( 'Link', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
			]
		);

		$this->end_controls_section();

		// Word Rotator Section
		$this->start_controls_section(
			'section_word_rotator',
			[
				'label' => esc_html__('Word Rotator', 'dinopack-for-elementor'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_word_rotator',
			[
				'label' => esc_html__('Enable Word Rotator', 'dinopack-for-elementor'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'dinopack-for-elementor'),
				'label_off' => esc_html__('No', 'dinopack-for-elementor'),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rotator_placement_info',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					/* translators: %1$s and %2$s are the opening and closing code tags. %3$s is the placeholder */
					esc_html__('Insert %1$s%3$s%2$s in your heading text where you want the rotating words to appear.', 'dinopack-for-elementor'),
					'<code>',
					'</code>',
					'[ROTATOR]'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'enable_word_rotator' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'rotating_word',
			[
				'label' => esc_html__('Word', 'dinopack-for-elementor'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Amazing', 'dinopack-for-elementor'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'rotating_words',
			[
				'label' => esc_html__('Rotating Words', 'dinopack-for-elementor'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'rotating_word' => esc_html__('Creative', 'dinopack-for-elementor'),
					],
					[
						'rotating_word' => esc_html__('Strong', 'dinopack-for-elementor'),
					],
					[
						'rotating_word' => esc_html__('Dynamic', 'dinopack-for-elementor'),
					],
				],
				'title_field' => '{{{ rotating_word }}}',
				'condition' => [
					'enable_word_rotator' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rotator_animation_type',
			[
				'label' => esc_html__('Animation Type', 'dinopack-for-elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'blur',
				'options' => [
					'blur'    => esc_html__('Blur', 'dinopack-for-elementor'),
					'typing'  => esc_html__('Typing', 'dinopack-for-elementor'),
					'shuffle' => esc_html__('Shuffle', 'dinopack-for-elementor'),
				],
				'condition' => [
					'enable_word_rotator' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rotator_speed',
			[
				'label'   => esc_html__( 'Animation Speed', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 500,
				'max'     => 10000,
				'step'    => 100,
				'condition' => [ 
					'enable_word_rotator' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Register the Mask section.
	 *
	 * @return void
	 */
	private function register_masking_section() {

		$this->start_controls_section(
			'heading_mask_section',
			[
				'label' => esc_html__( 'Mask', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'heading_mask_switch',
			[
				'label' => esc_html__( 'Enable Mask', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'dinopack-for-elementor' ),
				'label_off' => esc_html__( 'Off', 'dinopack-for-elementor' ),
				'default' => '',
			]
		);

		$this->add_control(
			'heading_mask_image',
			[
				'label' => esc_html__( 'Mask Image', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'media_types' => [ 'image' ],
				'dynamic' => [
					'active' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-image: url( {{URL}} );',
				],
				'condition' => [
					'heading_mask_switch!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_mask_size',
			[
				'label' => esc_html__( 'Mask Background Size', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''        => esc_html__( 'Default', 'dinopack-for-elementor' ),
					'cover'   => esc_html__( 'Cover', 'dinopack-for-elementor' ),
					'contain' => esc_html__( 'Contain', 'dinopack-for-elementor' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-size: {{VALUE}};'
				],
				'condition' => [
					'heading_mask_switch!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_mask_position',
			[
				'label' => esc_html__( 'Position', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__( 'Center Center', 'dinopack-for-elementor' ),
					'center left'   => esc_html__( 'Center Left', 'dinopack-for-elementor' ),
					'center right'  => esc_html__( 'Center Right', 'dinopack-for-elementor' ),
					'top center'    => esc_html__( 'Top Center', 'dinopack-for-elementor' ),
					'top left'      => esc_html__( 'Top Left', 'dinopack-for-elementor' ),
					'top right'     => esc_html__( 'Top Right', 'dinopack-for-elementor' ), 
					'bottom center' => esc_html__( 'Bottom Center', 'dinopack-for-elementor' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'dinopack-for-elementor' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'dinopack-for-elementor' ),
					'custom'        => esc_html__( 'Custom', 'dinopack-for-elementor' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-position: {{VALUE}};'
				],
				'condition' => [
					'heading_mask_switch!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_mask_position_x',
			[
				'label' => esc_html__( 'X Position', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-position-x: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'heading_mask_switch!' => '',
					'heading_mask_position' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'heading_mask_position_y',
			[
				'label' => esc_html__( 'Y Position', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-position-y: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'heading_mask_switch!' => '',
					'heading_mask_position' => 'custom',
				],
			]
		);

		$this->add_responsive_control(
			'heading_mask_repeat',
			[
				'label' => esc_html__( 'Repeat', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''          => esc_html__( 'Default', 'dinopack-for-elementor' ),
					'no-repeat' => esc_html__( 'No-repeat', 'dinopack-for-elementor' ),
					'repeat'    => esc_html__( 'Repeat', 'dinopack-for-elementor' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'dinopack-for-elementor' ),
					'repeat-Y'  => esc_html__( 'Repeat-y', 'dinopack-for-elementor' ),
					'round'     => esc_html__( 'Round', 'dinopack-for-elementor' ),
					'space'     => esc_html__( 'Space', 'dinopack-for-elementor' ),
				],
				'default' => 'no-repeat',
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'background-repeat: {{VALUE}};'
				],
				'condition' => [
					'heading_mask_switch!' => '',
					'heading_mask_size!' => 'cover',
				],
			]
		);



		$this->end_controls_section();

	}

	/**
	 * Register Style Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function register_style_controls() {
		
		$this->start_controls_section(
			'_section_style_item',
			[
				'label' => esc_html__( 'Advanced Heading', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'dinopack-for-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'dinopack-for-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'dinopack-for-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'dinopack-for-elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .dinopack-advanced-heading-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .dinopack-advanced-heading-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .dinopack-advanced-heading-text',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					''            => esc_html__( 'Normal', 'dinopack-for-elementor' ),
					'multiply'    => esc_html__( 'Multiply', 'dinopack-for-elementor' ),
					'screen'      => esc_html__( 'Screen', 'dinopack-for-elementor' ),
					'overlay'     => esc_html__( 'Overlay', 'dinopack-for-elementor' ),
					'darken'      => esc_html__( 'Darken', 'dinopack-for-elementor' ),
					'lighten'     => esc_html__( 'Lighten', 'dinopack-for-elementor' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'dinopack-for-elementor' ),
					'saturation'  => esc_html__( 'Saturation', 'dinopack-for-elementor' ),
					'color'       => esc_html__( 'Color', 'dinopack-for-elementor' ),
					'difference'  => esc_html__( 'Difference', 'dinopack-for-elementor' ),
					'exclusion'   => esc_html__( 'Exclusion', 'dinopack-for-elementor' ),
					'hue'         => esc_html__( 'Hue', 'dinopack-for-elementor' ),
					'luminosity'  => esc_html__( 'Luminosity', 'dinopack-for-elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'separator',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'title_colors' );

		$this->start_controls_tab(
			'title_colors_normal',
			[
				'label' => esc_html__( 'Normal', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_colors_hover',
			[
				'label' => esc_html__( 'Hover', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__( 'Link Color', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text a:hover, {{WRAPPER}} .dinopack-advanced-heading-text a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'dinopack-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 's',
				],
				'selectors' => [
					'{{WRAPPER}} .dinopack-advanced-heading-text a' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Add this to the style controls

		$this->start_controls_section(
			'section_style_word_rotator',
			[
				'label' => esc_html__('Word Rotator', 'dinopack-for-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_word_rotator' => 'yes',
				],
			]
		);

		$this->add_control(
			'rotator_color',
			[
				'label' => esc_html__('Text Color', 'dinopack-for-elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dinopack-rotating-word-current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rotator_typography',
				'selector' => '{{WRAPPER}} .dinopack-rotating-word-current',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'rotator_text_stroke',
				'selector' => '{{WRAPPER}} .dinopack-rotating-word-current',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'rotator_text_shadow',
				'selector' => '{{WRAPPER}} .dinopack-rotating-word-current',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the Widget.
	 *
	 * Renders the widget on the frontend.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( '' === $settings['heading_text'] ) {
			return;
		}

		$this->add_render_attribute( 'heading_text', 'class', 'dinopack-advanced-heading-text' );

		$this->add_render_attribute( 'advanced-heading', 'class', 'dinopack-advanced-heading-container' );

		if ( 'yes' === $settings['heading_mask_switch'] ) {
			$this->add_render_attribute( 'advanced-heading', 'class', 'has-mask' );
		}

		$heading_text = $this->should_sanitize( $settings ) ? wp_kses_post( $settings['heading_text'] ) : $settings['heading_text'];

		if ( 'yes' === $settings['enable_word_rotator'] && !empty($settings['rotating_words']) ) {
			$this->add_render_attribute( 'advanced-heading', 'class', 'has-word-rotator' );
			$this->add_render_attribute( 'advanced-heading', 'data-rotator-animation', $settings['rotator_animation_type'] );
			$this->add_render_attribute( 'advanced-heading', 'data-rotator-speed', $settings['rotator_speed'] );
			
			// Replace [ROTATOR] placeholder with the rotating words container
			$rotator_placeholder = '[ROTATOR]';
			if ( strpos( $heading_text, $rotator_placeholder ) !== false ) {
				// Start building the rotator HTML
				$rotator_html = '<span class="dinopack-word-rotator">';
				
				// Add each rotating word
				foreach ( $settings['rotating_words'] as $index => $word ) {
					$class = 0 === $index ? 'dinopack-rotating-word-current' : 'dinopack-rotating-word';
					$rotator_html .= '<span class="' . esc_attr( $class ) . '">' . esc_html( $word['rotating_word'] ) . '</span>';
				}
				
				// Close the rotator container
				$rotator_html .= '</span>';
				
				// Replace the placeholder with the rotator HTML
				$heading_text = str_replace( $rotator_placeholder, $rotator_html, $heading_text );
			}
		}

		$this->add_inline_editing_attributes( 'heading_text' );

		if ( ! empty( $settings['heading_link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['heading_link'] );

			$heading_text = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $heading_text );
		}

		$heading_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['heading_tag'] ), $this->get_render_attribute_string( 'heading_text' ), $heading_text );
		$heading_html = sprintf( '<div %1$s>%2$s</div>', $this->get_render_attribute_string( 'advanced-heading' ), $heading_html );

		// PHPCS - the variable $heading_html holds safe data.
		echo $heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped


	}

	/**
	 * Check if the content should be sanitized. Sanitizing should be applied for non-admin users in the editor and for shortcodes.
	 *
	 * @return bool
	 */
	private function should_sanitize( array $settings ): bool {
		return ( is_admin() && ! current_user_can( 'manage_options' ) ) || ! empty( $settings['isShortcode'] );
	}
	
}