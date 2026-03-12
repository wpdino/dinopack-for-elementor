<?php
/**
 * Offset Side Panel Widget
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
use Elementor\Icons_Manager;

/**
 * Offset Side Panel widget.
 *
 * Renders a slide-in panel (left/right/top/bottom) with optional template content and close button.
 * Hidden from first paint via inline closed state; JS removes no-transition on DOM ready.
 *
 * @since 1.0.0
 */
class Offset_Side_Panel extends Widget_Base {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style(
			'dinopack-offset-side-panel',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0'
		);
		wp_register_script(
			'dinopack-offset-side-panel',
			plugins_url( 'frontend.js', __FILE__ ),
			array( 'jquery' ),
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0',
			true
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'dinopack-offset-side-panel' );
	}

	/**
	 * Get script dependencies.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'dinopack-offset-side-panel' );
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_name() {
		return 'dinopack-offset-side-panel';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Offset Side Panel', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-sidebar';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_categories() {
		return array( 'dinopack-for-elementor', 'dinopack-header' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_keywords() {
		return array( 'dinopack-for-elementor', 'dinopack-header', 'side', 'panel', 'offcanvas', 'drawer', 'menu' );
	}

	/**
	 * Register widget controls.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {

		// Content
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'dinopack-for-elementor' ),
			)
		);
		$templates = array( '' => __( '— Select template —', 'dinopack-for-elementor' ) );
		$posts     = get_posts( array(
			'post_type'      => 'dinopack-side-panel',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );
		foreach ( $posts as $post ) {
			$templates[ $post->ID ] = $post->post_title;
		}
		wp_reset_postdata();
		$this->add_control(
			'template_id',
			array(
				'label'   => esc_html__( 'Side Panel template', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $templates,
				'default' => '',
			)
		);
		$this->add_control(
			'panel_id',
			array(
				'label'       => esc_html__( 'Panel ID', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 'my-panel',
				'description' => esc_html__( 'Use this ID in the Trigger widget\'s Target field (e.g. #my-panel).', 'dinopack-for-elementor' ),
			)
		);
		$this->add_control(
			'body_class',
			array(
				'label'       => esc_html__( 'Body class when open', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'dinopack-panel-open',
				'placeholder' => 'dinopack-panel-open',
				'description' => esc_html__( 'Optional: class added to body when panel is open (e.g. for overlay or scroll lock).', 'dinopack-for-elementor' ),
			)
		);
		$this->end_controls_section();

		// Direction / Layout
		$this->start_controls_section(
			'section_direction',
			array(
				'label' => esc_html__( 'Direction', 'dinopack-for-elementor' ),
			)
		);
		$this->add_control(
			'direction',
			array(
				'label'   => esc_html__( 'Direction', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'   => esc_html__( 'Left → Right', 'dinopack-for-elementor' ),
					'right'  => esc_html__( 'Right → Left', 'dinopack-for-elementor' ),
					'top'    => esc_html__( 'Top → Bottom', 'dinopack-for-elementor' ),
					'bottom' => esc_html__( 'Bottom → Top', 'dinopack-for-elementor' ),
				),
			)
		);
		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => esc_html__( 'Panel width', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 800 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
					'vw' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 320 ),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__panel' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'direction' => array( 'left', 'right' ),
				),
			)
		);
		$this->add_responsive_control(
			'panel_height',
			array(
				'label'      => esc_html__( 'Panel height', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'vh', 'size' => 40 ),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__panel' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'direction' => array( 'top', 'bottom' ),
				),
			)
		);
		$this->end_controls_section();

		// Close button
		$this->start_controls_section(
			'section_close',
			array(
				'label' => esc_html__( 'Close button', 'dinopack-for-elementor' ),
			)
		);
		$this->add_control(
			'show_close',
			array(
				'label'        => esc_html__( 'Show close button', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'close_position',
			array(
				'label'     => esc_html__( 'Position', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => array(
					'top-left'     => esc_html__( 'Top left', 'dinopack-for-elementor' ),
					'top-right'    => esc_html__( 'Top right', 'dinopack-for-elementor' ),
					'bottom-left'  => esc_html__( 'Bottom left', 'dinopack-for-elementor' ),
					'bottom-right' => esc_html__( 'Bottom right', 'dinopack-for-elementor' ),
				),
				'condition' => array( 'show_close' => 'yes' ),
			)
		);
		$this->add_control(
			'close_icon',
			array(
				'label'     => esc_html__( 'Icon', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-close',
					'library' => 'eicons',
				),
				'condition' => array( 'show_close' => 'yes' ),
			)
		);
		$this->end_controls_section();

		// Style: Panel
		$this->start_controls_section(
			'section_style_panel',
			array(
				'label' => esc_html__( 'Panel', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'panel_bg',
			array(
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__panel' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '{{WRAPPER}} .dinopack-offset-side-panel__panel',
			)
		);
		$this->end_controls_section();

		// Style: Close button
		$this->start_controls_section(
			'section_style_close',
			array(
				'label'     => esc_html__( 'Close button', 'dinopack-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_close' => 'yes' ),
			)
		);
		$this->add_responsive_control(
			'close_button_size',
			array(
				'label'      => esc_html__( 'Size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 20, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'close_button_gap',
			array(
				'label'      => esc_html__( 'Distance from edge', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ), 'em' => array( 'min' => 0, 'max' => 3 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close' => '--dinopack-close-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'close_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_bg',
			array(
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_hover_color',
			array(
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close:hover' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'close_hover_bg',
			array(
				'label'     => esc_html__( 'Hover background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close:hover' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'close_border',
				'selector' => '{{WRAPPER}} .dinopack-offset-side-panel__close',
			)
		);
		$this->add_control(
			'close_border_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-offset-side-panel__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Return inline style string for panel when closed (hidden from first paint).
	 *
	 * @since 1.0.0
	 * @param string $direction left|right|top|bottom.
	 * @return string Inline style (no leading/trailing semicolon).
	 */
	protected function get_panel_closed_inline_style( $direction ) {
		$styles = array( 'visibility: hidden', 'pointer-events: none' );
		switch ( $direction ) {
			case 'left':
				$styles[] = 'transform: translate3d(-100%, 0, 0)';
				break;
			case 'right':
				$styles[] = 'transform: translate3d(100%, 0, 0)';
				break;
			case 'top':
				$styles[] = 'transform: translate3d(0, -100%, 0)';
				break;
			case 'bottom':
				$styles[] = 'transform: translate3d(0, 100%, 0)';
				break;
			default:
				$styles[] = 'transform: translate3d(-100%, 0, 0)';
		}
		return implode( '; ', $styles );
	}

	/**
	 * Render widget output.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$s = $this->get_settings_for_display();

		// In Elementor preview iframe (e.g. after reload), show the same placeholder as content_template so it stays consistent.
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$panel_id = ! empty( $s['panel_id'] ) ? esc_attr( $s['panel_id'] ) : '';
			?>
			<div class="dinopack-offset-side-panel-editor-placeholder">
				<i class="eicon-sidebar"></i>
				<div class="dinopack-offset-side-panel-editor-text">
					<span class="dinopack-offset-side-panel-editor-label"><?php echo esc_html__( 'Offset Side Panel', 'dinopack-for-elementor' ); ?></span>
					<span class="dinopack-offset-side-panel-editor-hint"><?php echo esc_html__( 'Visible on frontend', 'dinopack-for-elementor' ); ?></span>
				</div>
				<?php if ( $panel_id !== '' ) : ?>
					<span class="dinopack-offset-side-panel-editor-id">#<?php echo esc_html( $panel_id ); ?></span>
				<?php endif; ?>
			</div>
			<?php
			return;
		}

		$template_id = ! empty( $s['template_id'] ) ? (int) $s['template_id'] : 0;
		$direction   = ! empty( $s['direction'] ) ? $s['direction'] : 'left';
		$body_class  = ! empty( $s['body_class'] ) ? sanitize_html_class( $s['body_class'] ) : 'dinopack-panel-open';
		$show_close  = ! empty( $s['show_close'] ) && $s['show_close'] === 'yes';
		$close_pos   = ! empty( $s['close_position'] ) ? $s['close_position'] : 'top-right';

		$wrapper_attr = array(
			'class'          => 'dinopack-offset-side-panel dinopack-offset-side-panel--' . esc_attr( $direction ) . ' dinopack-offset-side-panel--no-transition',
			'data-direction' => $direction,
			'data-body-class' => $body_class,
		);
		if ( ! empty( $s['panel_id'] ) ) {
			$wrapper_attr['id'] = sanitize_key( $s['panel_id'] );
		}
		$this->add_render_attribute( 'wrapper', $wrapper_attr );

		$panel_inline = $this->get_panel_closed_inline_style( $direction );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="dinopack-offset-side-panel__overlay" aria-hidden="true"></div>
			<div class="dinopack-offset-side-panel__panel" style="<?php echo esc_attr( $panel_inline ); ?>">
				<?php if ( $show_close ) : ?>
					<button type="button" class="dinopack-offset-side-panel__close dinopack-offset-side-panel__close--<?php echo esc_attr( $close_pos ); ?>" aria-label="<?php esc_attr_e( 'Close panel', 'dinopack-for-elementor' ); ?>">
						<?php
						$icon_rendered = false;
						if ( ! empty( $s['close_icon']['value'] ) ) {
							ob_start();
							Icons_Manager::render_icon( $s['close_icon'], array( 'aria-hidden' => 'true' ) );
							$icon_output = ob_get_clean();
							if ( $icon_output !== '' && trim( strip_tags( $icon_output ) ) !== '' && ! preg_match( '/^\d+$/', trim( strip_tags( $icon_output ) ) ) ) {
								echo $icon_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$icon_rendered = true;
							}
						}
						if ( ! $icon_rendered ) {
							// Reliable inline SVG X so close button is always visible (e.g. when icon font/SVG doesn't load).
							echo '<span class="dinopack-offset-side-panel__close-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="1em" height="1em"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>';
						}
						?>
					</button>
				<?php endif; ?>
				<div class="dinopack-offset-side-panel__panel-inner">
					<?php
					if ( $template_id && class_exists( '\Elementor\Plugin' ) ) {
						$doc = \Elementor\Plugin::instance()->documents->get( $template_id );
						if ( $doc && $doc->is_built_with_elementor() ) {
							echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
						}
					} else {
						echo '<p>' . esc_html__( 'Select a Side Panel template in the widget settings.', 'dinopack-for-elementor' ) . '</p>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor (small icon placeholder).
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<div class="dinopack-offset-side-panel-editor-placeholder">
			<i class="eicon-sidebar"></i>
			<div class="dinopack-offset-side-panel-editor-text">
				<span class="dinopack-offset-side-panel-editor-label"><?php echo esc_html__( 'Offset Side Panel', 'dinopack-for-elementor' ); ?></span>
				<span class="dinopack-offset-side-panel-editor-hint"><?php echo esc_html__( 'Visible on frontend', 'dinopack-for-elementor' ); ?></span>
			</div>
			<# if ( settings.panel_id ) { #><span class="dinopack-offset-side-panel-editor-id">#{{ settings.panel_id }}</span><# } #>
		</div>
		<?php
	}
}
