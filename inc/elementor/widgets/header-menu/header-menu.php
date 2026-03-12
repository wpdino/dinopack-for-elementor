<?php
/**
 * Header Menu Widget
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Nav menu walker that adds level classes for styling (menu-item-level-1, -2, -3).
 *
 * @since 1.0.0
 */
class Header_Menu_Walker extends \Walker_Nav_Menu {

	/**
	 * Start element output; add level class to list item.
	 *
	 * @since 1.0.0
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$item->classes[] = 'menu-item-level-' . ( $depth + 1 );
		parent::start_el( $output, $item, $depth, $args, $id );
	}
}

/**
 * Header Menu widget.
 *
 * Displays a WordPress navigation menu for use in header templates.
 * Supports up to 3 levels with dropdown styling options.
 *
 * @since 1.0.0
 */
class Header_Menu extends Widget_Base {

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
			'dinopack-header-menu',
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
		return [ 'dinopack-header-menu' ];
	}

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'dinopack-header-menu';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Header Menu', 'dinopack-for-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
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
		return [ 'header', 'menu', 'nav', 'navigation' ];
	}

	/**
	 * Register Header Menu widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$menus = wp_get_nav_menus();
		$options = [ '' => esc_html__( '— Select menu —', 'dinopack-for-elementor' ) ];
		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Menu', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu',
			[
				'label'   => esc_html__( 'Menu', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $options,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'dinopack-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'dinopack-for-elementor' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_menu',
			[
				'label' => esc_html__( 'Top level (Level 1)', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .dinopack-header-menu__list > li > a',
			]
		);

		$this->add_control(
			'menu_color',
			[
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-menu__list > li > a' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'menu_color_hover',
			[
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-menu__list > li > a:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'menu_color_active',
			[
				'label'     => esc_html__( 'Active / current color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dinopack-header-menu__list > li.current-menu-item > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dinopack-header-menu__list > li.current-menu-ancestor > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_link_padding',
			[
				'label'      => esc_html__( 'Link padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-menu__list > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'menu_spacing',
			[
				'label'      => esc_html__( 'Spacing between items', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'selectors'  => [
					'{{WRAPPER}}.dinopack-header-menu--horizontal .dinopack-header-menu__list > li' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.dinopack-header-menu--horizontal .dinopack-header-menu__list > li:last-child' => 'margin-right: 0;',
					'{{WRAPPER}}.dinopack-header-menu--vertical .dinopack-header-menu__list > li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => esc_html__( 'Dropdown panel', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_bg',
			[
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'dropdown_padding',
			[
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 8, 'right' => 0, 'bottom' => 8, 'left' => 0, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'dropdown_min_width',
			[
				'label'      => esc_html__( 'Min width', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 140, 'max' => 400 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 200 ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu' => 'min-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'dropdown_border_radius',
			[
				'label'      => esc_html__( 'Border radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .dinopack-header-menu__list .sub-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} .dinopack-header-menu__list .sub-menu',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown_links',
			[
				'label' => esc_html__( 'Dropdown links (Level 2 & 3)', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_typography',
				'selector' => '{{WRAPPER}} .dinopack-header-menu__list .sub-menu li a',
			]
		);

		$this->add_control(
			'dropdown_link_color',
			[
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu li a' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'dropdown_link_color_hover',
			[
				'label'     => esc_html__( 'Hover color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu li a:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'dropdown_link_color_active',
			[
				'label'     => esc_html__( 'Active / current color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dinopack-header-menu__list .sub-menu li.current-menu-item > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_link_padding',
			[
				'label'      => esc_html__( 'Link padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'unit' => 'px' ],
				'selectors'  => [ '{{WRAPPER}} .dinopack-header-menu__list .sub-menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Header Menu widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$menu_id = $this->get_settings_for_display( 'menu' );
		$layout  = $this->get_settings_for_display( 'layout' );
		$this->add_render_attribute( '_wrapper', 'class', 'dinopack-header-menu--' . sanitize_html_class( $layout ) );
		$class   = 'dinopack-header-menu';

		if ( ! $menu_id ) {
			echo '<div class="' . esc_attr( $class ) . '">' . esc_html__( 'Select a menu in the widget settings.', 'dinopack-for-elementor' ) . '</div>';
			return;
		}

		$this->add_render_attribute( 'wrap', 'class', $class );
		?>
		<nav <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php
			wp_nav_menu( [
				'menu'            => (int) $menu_id,
				'menu_class'      => 'dinopack-header-menu__list',
				'container'       => false,
				'fallback_cb'     => false,
				'depth'           => 3,
				'walker'          => new Header_Menu_Walker(),
			] );
			?>
		</nav>
		<?php
	}
}
