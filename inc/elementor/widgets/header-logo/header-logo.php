<?php
/**
 * Header Logo Widget
 *
 * @package DinoPack
 * @since 1.0.0
 */

namespace Dinopack\Widgets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;

/**
 * Header Logo widget.
 *
 * Displays site logo or custom image for use in header templates.
 *
 * @since 1.0.0
 */
class Header_Logo extends Widget_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style(
			'dinopack-header-logo',
			plugins_url( 'frontend.css', __FILE__ ),
			[],
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0'
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Style slugs.
	 */
	public function get_style_depends() {
		return [ 'dinopack-header-logo' ];
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dinopack-header-logo';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Header Logo', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'dinopack-for-elementor', 'dinopack-header' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'header', 'logo', 'site', 'branding' ];
	}

	/**
	 * Register Header Logo widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Logo', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'logo_type',
			[
				'label'   => esc_html__( 'Logo type', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom' => esc_html__( 'Custom image', 'dinopack-for-elementor' ),
					'site'   => esc_html__( 'Site logo (customizer)', 'dinopack-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'logo_image',
			[
				'label'     => esc_html__( 'Choose image', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
				'condition' => [ 'logo_type' => 'custom' ],
			]
		);

		$this->add_control(
			'link_to',
			[
				'label'   => esc_html__( 'Link', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'home',
				'options' => [
					'none' => esc_html__( 'None', 'dinopack-for-elementor' ),
					'home' => esc_html__( 'Home', 'dinopack-for-elementor' ),
					'custom' => esc_html__( 'Custom URL', 'dinopack-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'link_url',
			[
				'label'       => esc_html__( 'URL', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://', 'dinopack-for-elementor' ),
				'condition'   => [ 'link_to' => 'custom' ],
			]
		);

		$this->add_control(
			'open_new_tab',
			[
				'label'     => esc_html__( 'Open in new tab', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 'link_to' => 'custom' ],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => esc_html__( 'Alignment', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Left', 'dinopack-for-elementor' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'dinopack-for-elementor' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'dinopack-for-elementor' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'left',
				'selectors' => [ '{{WRAPPER}}.elementor-widget-dinopack-header-logo .dinopack-header-logo' => 'text-align: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_logo',
			[
				'label' => esc_html__( 'Logo', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_width',
			[
				'label'      => esc_html__( 'Width', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 600 ], '%' => [ 'min' => 5, 'max' => 100 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'logo_max_width',
			[
				'label'      => esc_html__( 'Max width', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 600 ], '%' => [ 'min' => 5, 'max' => 100 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'max-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'logo_height',
			[
				'label'      => esc_html__( 'Height', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 300 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'logo_min_height',
			[
				'label'      => esc_html__( 'Min height', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'min-height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'logo_object_fit',
			[
				'label'   => esc_html__( 'Object fit', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''        => esc_html__( 'Default', 'dinopack-for-elementor' ),
					'fill'    => esc_html__( 'Fill', 'dinopack-for-elementor' ),
					'cover'   => esc_html__( 'Cover', 'dinopack-for-elementor' ),
					'contain' => esc_html__( 'Contain', 'dinopack-for-elementor' ),
					'none'    => esc_html__( 'None', 'dinopack-for-elementor' ),
				],
				'selectors' => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'object-fit: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'logo_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.01 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'opacity: {{SIZE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'logo_css_filters',
				'selector' => '{{WRAPPER}} .dinopack-header-logo__img',
			]
		);

		$this->add_responsive_control(
			'logo_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'logo_border',
				'selector' => '{{WRAPPER}} .dinopack-header-logo__img',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'logo_box_shadow',
				'selector' => '{{WRAPPER}} .dinopack-header-logo__img',
			]
		);

		$this->add_responsive_control(
			'logo_padding',
			[
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'logo_margin',
			[
				'label'      => esc_html__( 'Margin', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_link',
			[
				'label' => esc_html__( 'Link', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'link_padding',
			[
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'link_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_hover',
			[
				'label' => esc_html__( 'Hover', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'logo_hover_opacity',
			[
				'label'      => esc_html__( 'Opacity', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.01 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-logo__link:hover .dinopack-header-logo__img, {{WRAPPER}} .dinopack-header-logo:hover .dinopack-header-logo__img' => 'opacity: {{SIZE}};' ],
			]
		);

		$this->add_control(
			'hover_transition_duration',
			[
				'label'     => esc_html__( 'Transition duration', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 3, 'step' => 0.1 ] ],
				'selectors' => [ '{{WRAPPER}} .dinopack-header-logo__img' => 'transition-duration: {{SIZE}}s;' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'logo_box_shadow_hover',
				'label'    => esc_html__( 'Box shadow', 'dinopack-for-elementor' ),
				'selector' => '{{WRAPPER}} .dinopack-header-logo__link:hover .dinopack-header-logo__img, {{WRAPPER}} .dinopack-header-logo:hover .dinopack-header-logo__img',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Header Logo widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		$logo_url = '';
		$logo_alt = get_bloginfo( 'name' );

		if ( $s['logo_type'] === 'site' && has_custom_logo() ) {
			$logo_id = get_theme_mod( 'custom_logo' );
			$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
			if ( $logo_id ) {
				$logo_alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', true ) ?: $logo_alt;
			}
		} elseif ( ! empty( $s['logo_image']['url'] ) ) {
			$logo_url = $s['logo_image']['url'];
			$logo_alt = $s['logo_image']['alt'] ?: $logo_alt;
		}

		$link_url = '';
		if ( $s['link_to'] === 'home' ) {
			$link_url = home_url( '/' );
		} elseif ( $s['link_to'] === 'custom' && ! empty( $s['link_url']['url'] ) ) {
			$link_url = $s['link_url']['url'];
		}

		$open_new = ! empty( $s['open_new_tab'] ) && $s['link_to'] === 'custom';
		$this->add_render_attribute( 'link', 'class', 'dinopack-header-logo__link' );
		if ( $link_url ) {
			$this->add_render_attribute( 'link', 'href', esc_url( $link_url ) );
			if ( $open_new ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
				$this->add_render_attribute( 'link', 'rel', 'noopener noreferrer' );
			}
		}

		if ( ! $logo_url ) {
			echo '<div class="dinopack-header-logo elementor-widget-empty-icon">' . esc_html__( 'Choose a logo image or use site logo.', 'dinopack-for-elementor' ) . '</div>';
			return;
		}

		$img = '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $logo_alt ) . '" class="dinopack-header-logo__img" />';
		echo '<div class="dinopack-header-logo">';
		if ( $link_url ) {
			echo '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $img . '</a>';
		} else {
			echo $img;
		}
		echo '</div>';
	}
}
