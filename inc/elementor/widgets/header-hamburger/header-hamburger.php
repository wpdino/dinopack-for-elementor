<?php
/**
 * Header Hamburger Widget
 *
 * @package DinoPack
 * @since 1.0.0
 */

namespace Dinopack\Widgets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Header Trigger widget.
 *
 * Toggle button (with optional label and icon) for opening/closing an offset side panel or mobile menu.
 *
 * @since 1.0.0
 */
class Header_Hamburger extends Widget_Base {

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
			'dinopack-header-hamburger',
			plugins_url( 'frontend.css', __FILE__ ),
			[],
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0'
		);
		wp_register_script(
			'dinopack-header-hamburger',
			plugins_url( 'frontend.js', __FILE__ ),
			[ 'jquery' ],
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0',
			true
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
		return [ 'dinopack-header-hamburger' ];
	}

	/**
	 * Get script dependencies.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Script slugs.
	 */
	public function get_script_depends() {
		return [ 'dinopack-header-hamburger' ];
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dinopack-header-hamburger';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Trigger', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
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
		return [ 'header', 'trigger', 'hamburger', 'menu', 'toggle', 'mobile' ];
	}

	/**
	 * Register Header Hamburger widget controls.
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
				'label' => esc_html__( 'Trigger', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'target',
			[
				'label'       => esc_html__( 'Target (panel ID or class)', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '#dinopack-offset-panel',
				'placeholder' => '#dinopack-offset-panel',
				'description' => esc_html__( 'CSS selector of the panel to open/close (e.g. #my-menu or .off-canvas).', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'body_class',
			[
				'label'       => esc_html__( 'Body class when open', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'menu-open',
				'description' => esc_html__( 'Optional: class added to body when panel is open (for overlay/scroll lock).', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'label_text',
			[
				'label'       => esc_html__( 'Label', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Menu', 'dinopack-for-elementor' ),
				'description' => esc_html__( 'Optional text shown on the same line as the icon. Leave empty for icon only.', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'label_position',
			[
				'label'     => esc_html__( 'Label position', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => esc_html__( 'Left of icon', 'dinopack-for-elementor' ),
					'right' => esc_html__( 'Right of icon', 'dinopack-for-elementor' ),
				],
				'condition' => [ 'label_text!' => '' ],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'eicon-menu-bar',
					'library' => 'eicons',
				],
			]
		);

		$this->add_control(
			'aria_label',
			[
				'label'   => esc_html__( 'Accessibility label', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Open menu', 'dinopack-for-elementor' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Button', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 16, 'max' => 64 ] ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-hamburger__icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-hamburger__btn' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-hamburger__btn:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-hamburger__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'label_heading',
			[
				'label'     => esc_html__( 'Label', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .dinopack-header-hamburger__label',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-hamburger__label' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'label_color_hover',
			[
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-hamburger__btn:hover .dinopack-header-hamburger__label' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label'      => esc_html__( 'Label spacing', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ], 'em' => [ 'min' => 0, 'max' => 3 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 10 ],
				'selectors'  => [
					'{{WRAPPER}} .dinopack-header-hamburger__label.dinopack-header-hamburger__label--before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dinopack-header-hamburger__label.dinopack-header-hamburger__label--after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Header Hamburger widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		$target     = ! empty( $s['target'] ) ? esc_attr( $s['target'] ) : '';
		$body_class = ! empty( $s['body_class'] ) ? esc_attr( $s['body_class'] ) : '';
		$aria_label = ! empty( $s['aria_label'] ) ? esc_attr( $s['aria_label'] ) : esc_attr__( 'Open menu', 'dinopack-for-elementor' );
		$label_text = ! empty( $s['label_text'] ) ? trim( $s['label_text'] ) : '';
		$label_pos  = ! empty( $s['label_position'] ) ? $s['label_position'] : 'right';
		$icon_value = isset( $s['icon']['value'] ) ? $s['icon']['value'] : '';
		$show_icon  = $icon_value !== '' && ! preg_match( '/^\d+$/', (string) $icon_value );
		$show_label = $label_text !== '';

		$this->add_render_attribute( 'btn', 'class', 'dinopack-header-hamburger__btn' );
		$this->add_render_attribute( 'btn', 'type', 'button' );
		$this->add_render_attribute( 'btn', 'aria-label', $aria_label );
		$this->add_render_attribute( 'btn', 'aria-expanded', 'false' );
		if ( $target ) {
			$this->add_render_attribute( 'btn', 'data-dinopack-target', $target );
		}
		if ( $body_class ) {
			$this->add_render_attribute( 'btn', 'data-dinopack-body-class', $body_class );
		}

		$default_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M3 6h18v2H3V6zm0 5h18v2H3v-2z"/></svg>';
		$icon_inner = $default_svg;
		if ( $show_icon ) {
			ob_start();
			Icons_Manager::render_icon( $s['icon'], [ 'aria-hidden' => 'true' ] );
			$captured = ob_get_clean();
			if ( $captured !== '' && trim( strip_tags( $captured ) ) !== '1' ) {
				$icon_inner = $captured;
			}
		}
		?>
		<div class="dinopack-header-hamburger">
			<button <?php echo $this->get_render_attribute_string( 'btn' ); ?>>
				<?php if ( $show_label && $label_pos === 'left' ) : ?>
					<span class="dinopack-header-hamburger__label dinopack-header-hamburger__label--before"><?php echo esc_html( $label_text ); ?></span>
				<?php endif; ?>
				<?php if ( $show_icon || ! $show_label ) : ?>
					<span class="dinopack-header-hamburger__icon"><?php echo $icon_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<?php if ( $show_label && $label_pos === 'right' ) : ?>
					<span class="dinopack-header-hamburger__label dinopack-header-hamburger__label--after"><?php echo esc_html( $label_text ); ?></span>
				<?php endif; ?>
			</button>
		</div>
		<?php
	}
}
