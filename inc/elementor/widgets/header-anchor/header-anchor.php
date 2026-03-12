<?php
/**
 * Header Anchor Widget
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
 * Header Anchor widget.
 *
 * Link that scrolls to a section by ID (anchor). For use in header templates.
 *
 * @since 1.0.0
 */
class Header_Anchor extends Widget_Base {

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
			'dinopack-header-anchor',
			plugins_url( 'frontend.css', __FILE__ ),
			[],
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0'
		);
		wp_register_script(
			'dinopack-header-anchor',
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
		return [ 'dinopack-header-anchor' ];
	}

	/**
	 * Get script dependencies.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Script slugs.
	 */
	public function get_script_depends() {
		return [ 'dinopack-header-anchor' ];
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dinopack-header-anchor';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Header Anchor', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-link';
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
		return [ 'header', 'anchor', 'link', 'scroll' ];
	}

	/**
	 * Register Header Anchor widget controls.
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
				'label' => esc_html__( 'Anchor', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'anchor_id',
			[
				'label'       => esc_html__( 'Anchor ID', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'section-1',
				'description' => esc_html__( 'ID of the target element (without #).', 'dinopack-for-elementor' ),
			]
		);

		$this->add_control(
			'text',
			[
				'label'       => esc_html__( 'Text', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Scroll to section', 'dinopack-for-elementor' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'dinopack-for-elementor' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'   => esc_html__( 'Icon position', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => [
					'before' => esc_html__( 'Before text', 'dinopack-for-elementor' ),
					'after'  => esc_html__( 'After text', 'dinopack-for-elementor' ),
				],
				'condition' => [ 'icon[value]!' => '' ],
			]
		);

		$this->add_control(
			'smooth_scroll',
			[
				'label'   => esc_html__( 'Smooth scroll', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Link', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .dinopack-header-anchor__link',
			]
		);

		$this->add_control(
			'color',
			[
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-anchor__link' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'color_hover',
			[
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-anchor__link:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'      => esc_html__( 'Icon spacing', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => -10, 'max' => 20 ] ],
				'selectors'  => [
					'{{WRAPPER}} .dinopack-header-anchor__icon--before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dinopack-header-anchor__icon--after'  => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Header Anchor widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		$anchor_id = ! empty( $s['anchor_id'] ) ? trim( $s['anchor_id'] ) : '';
		$anchor_id = preg_replace( '/^#/', '', $anchor_id );
		$text      = ! empty( $s['text'] ) ? $s['text'] : '';
		$smooth    = ! empty( $s['smooth_scroll'] );

		if ( ! $anchor_id ) {
			echo '<div class="dinopack-header-anchor">' . esc_html__( 'Set the anchor ID in the widget settings.', 'dinopack-for-elementor' ) . '</div>';
			return;
		}

		$href = '#' . esc_attr( $anchor_id );
		$this->add_render_attribute( 'link', 'href', $href );
		$this->add_render_attribute( 'link', 'class', 'dinopack-header-anchor__link' );
		if ( $smooth ) {
			$this->add_render_attribute( 'link', 'data-dinopack-smooth', '1' );
		}
		?>
		<div class="dinopack-header-anchor">
			<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
				<?php
				if ( ! empty( $s['icon']['value'] ) && $s['icon_position'] === 'before' ) {
					echo '<span class="dinopack-header-anchor__icon dinopack-header-anchor__icon--before">';
					Icons_Manager::render_icon( $s['icon'], [ 'aria-hidden' => 'true' ] );
					echo '</span>';
				}
				echo '<span class="dinopack-header-anchor__text">' . esc_html( $text ) . '</span>';
				if ( ! empty( $s['icon']['value'] ) && $s['icon_position'] === 'after' ) {
					echo '<span class="dinopack-header-anchor__icon dinopack-header-anchor__icon--after">';
					Icons_Manager::render_icon( $s['icon'], [ 'aria-hidden' => 'true' ] );
					echo '</span>';
				}
				?>
			</a>
		</div>
		<?php
	}
}
