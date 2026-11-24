<?php
/**
 * Table of Contents Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Auto or manual table of contents with anchor links.
 */
class Table_Of_Contents extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-table-of-contents',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
		wp_register_script(
			'dinopack-table-of-contents',
			plugins_url( 'frontend.js', __FILE__ ),
			array( 'jquery' ),
			DINOPACK_VERSION,
			true
		);
	}

	public function get_name() {
		return 'dinopack-table-of-contents';
	}

	public function get_title() {
		return esc_html__( 'Table of Contents', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor' );
	}

	public function get_keywords() {
		return array( 'toc', 'table of contents', 'headings', 'anchor', 'navigation', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-table-of-contents' );
	}

	public function get_script_depends() {
		return array( 'dinopack-table-of-contents' );
	}

	/**
	 * Normalize an anchor value to a hash href.
	 *
	 * Accepts: section-id, #section-id, or full URL with hash.
	 *
	 * @param string $anchor Raw anchor value.
	 * @return string
	 */
	private function normalize_anchor( $anchor ) {
		$anchor = trim( (string) $anchor );

		if ( '' === $anchor ) {
			return '';
		}

		if ( 0 === strpos( $anchor, 'http://' ) || 0 === strpos( $anchor, 'https://' ) || 0 === strpos( $anchor, '/' ) ) {
			return $anchor;
		}

		$anchor = ltrim( $anchor, '#' );
		$anchor = sanitize_html_class( $anchor );

		return $anchor ? '#' . $anchor : '';
	}

	/**
	 * Extract target element ID from an href.
	 *
	 * @param string $href Link href.
	 * @return string
	 */
	private function get_target_id_from_href( $href ) {
		if ( false !== strpos( $href, '#' ) ) {
			$parts = explode( '#', $href, 2 );
			return sanitize_html_class( $parts[1] );
		}

		return '';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Table of Contents', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'toc_mode',
			array(
				'label'   => esc_html__( 'Mode', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto'   => esc_html__( 'Auto (from headings)', 'dinopack-for-elementor' ),
					'manual' => esc_html__( 'Manual (custom links)', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'auto_mode_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div style="font-size:12px;line-height:1.5;color:#646970;">' . esc_html__( 'Auto mode scans the page for headings (H1–H6), adds IDs if missing, and builds the list for you.', 'dinopack-for-elementor' ) . '</div>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'toc_mode' => 'auto',
				),
			)
		);

		$this->add_control(
			'manual_mode_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div style="font-size:12px;line-height:1.5;color:#646970;">' . esc_html__( 'Manual mode: add items below, then set matching CSS IDs on sections/widgets (Advanced → CSS ID). Example: anchor "features" → CSS ID "features".', 'dinopack-for-elementor' ) . '</div>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'toc_mode' => 'manual',
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Table of Contents', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Title', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'heading_levels',
			array(
				'label'       => esc_html__( 'Heading Levels', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'h2', 'h3' ),
				'options'     => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'condition'   => array(
					'toc_mode' => 'auto',
				),
			)
		);

		$this->add_control(
			'container_selector',
			array(
				'label'       => esc_html__( 'Content Container', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'body',
				'description' => esc_html__( 'CSS selector to scan for headings. Leave empty to scan the whole page.', 'dinopack-for-elementor' ),
				'condition'   => array(
					'toc_mode' => 'auto',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			array(
				'label'       => esc_html__( 'Title', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Section title', 'dinopack-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_anchor',
			array(
				'label'       => esc_html__( 'Anchor / CSS ID', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'features',
				'description' => esc_html__( 'Enter the CSS ID without #. Set the same value on the target section (Advanced → CSS ID).', 'dinopack-for-elementor' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_level',
			array(
				'label'   => esc_html__( 'Indent Level', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => esc_html__( 'Level 1', 'dinopack-for-elementor' ),
					'2' => esc_html__( 'Level 2', 'dinopack-for-elementor' ),
					'3' => esc_html__( 'Level 3', 'dinopack-for-elementor' ),
					'4' => esc_html__( 'Level 4', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'manual_items',
			array(
				'label'       => esc_html__( 'TOC Items', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_title'  => esc_html__( 'Introduction', 'dinopack-for-elementor' ),
						'item_anchor' => 'introduction',
						'item_level'  => '1',
					),
					array(
						'item_title'  => esc_html__( 'Features', 'dinopack-for-elementor' ),
						'item_anchor' => 'features',
						'item_level'  => '1',
					),
					array(
						'item_title'  => esc_html__( 'Pricing', 'dinopack-for-elementor' ),
						'item_anchor' => 'pricing',
						'item_level'  => '1',
					),
				),
				'title_field' => '{{{ item_title }}}',
				'condition'   => array(
					'toc_mode' => 'manual',
				),
			)
		);

		$this->add_control(
			'list_style',
			array(
				'label'   => esc_html__( 'List Style', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => array(
					'none'    => esc_html__( 'None', 'dinopack-for-elementor' ),
					'bullets' => esc_html__( 'Bullets', 'dinopack-for-elementor' ),
					'numbers' => esc_html__( 'Numbers', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'smooth_scroll',
			array(
				'label'        => esc_html__( 'Smooth Scroll', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'highlight_active',
			array(
				'label'        => esc_html__( 'Highlight Active Section', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'collapse_mobile',
			array(
				'label'        => esc_html__( 'Collapse on Mobile', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'offset_top',
			array(
				'label'       => esc_html__( 'Scroll Offset (px)', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 80,
				'min'         => 0,
				'max'         => 300,
				'description' => esc_html__( 'Useful when you have a sticky header.', 'dinopack-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => esc_html__( 'Box', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-toc' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 20,
					'right'  => 20,
					'bottom' => 20,
					'left'   => 20,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-toc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-toc' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .dinopack-toc',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .dinopack-toc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label'     => esc_html__( 'Title', 'dinopack-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .dinopack-toc-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1f26',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-toc-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_list',
			array(
				'label' => esc_html__( 'List', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_typography',
				'selector' => '{{WRAPPER}} .dinopack-toc-list a',
			)
		);

		$this->add_control(
			'list_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-toc-list a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-toc-list a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list_active_color',
			array(
				'label'     => esc_html__( 'Active Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-toc-list a.is-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dinopack-toc-list a.is-active::before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_gap',
			array(
				'label'      => esc_html__( 'Item Gap', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 24,
					),
				),
				'default'    => array(
					'size' => 8,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-toc-list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$mode       = ! empty( $settings['toc_mode'] ) ? $settings['toc_mode'] : 'auto';
		$levels     = ! empty( $settings['heading_levels'] ) ? $settings['heading_levels'] : array( 'h2', 'h3' );
		$list_style = ! empty( $settings['list_style'] ) ? $settings['list_style'] : 'bullets';

		$classes = array(
			'dinopack-toc',
			'dinopack-toc-mode-' . sanitize_html_class( $mode ),
			'dinopack-toc-style-' . sanitize_html_class( $list_style ),
		);

		if ( 'yes' === $settings['collapse_mobile'] ) {
			$classes[] = 'dinopack-toc-collapse-mobile';
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'                 => $classes,
				'data-mode'             => $mode,
				'data-levels'           => implode( ',', array_map( 'sanitize_text_field', $levels ) ),
				'data-container'        => ! empty( $settings['container_selector'] ) ? $settings['container_selector'] : 'body',
				'data-smooth-scroll'    => ( 'yes' === $settings['smooth_scroll'] ) ? '1' : '0',
				'data-highlight-active' => ( 'yes' === $settings['highlight_active'] ) ? '1' : '0',
				'data-offset'           => isset( $settings['offset_top'] ) ? (int) $settings['offset_top'] : 80,
			)
		);
		?>
		<nav <?php $this->print_render_attribute_string( 'wrapper' ); ?> aria-label="<?php echo esc_attr__( 'Table of Contents', 'dinopack-for-elementor' ); ?>">
			<?php if ( 'yes' === $settings['show_title'] && ! empty( $settings['title'] ) ) : ?>
				<button type="button" class="dinopack-toc-toggle" aria-expanded="true">
					<span class="dinopack-toc-title"><?php echo esc_html( $settings['title'] ); ?></span>
					<span class="dinopack-toc-toggle-icon" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
				</button>
			<?php endif; ?>

			<?php
			$manual_count = 0;
			?>
			<ol class="dinopack-toc-list">
				<?php
				if ( 'manual' === $mode && ! empty( $settings['manual_items'] ) ) :
					foreach ( $settings['manual_items'] as $item ) :
						$title  = ! empty( $item['item_title'] ) ? $item['item_title'] : '';
						$href   = $this->normalize_anchor( isset( $item['item_anchor'] ) ? $item['item_anchor'] : '' );
						$level  = ! empty( $item['item_level'] ) ? (int) $item['item_level'] : 1;
						$target = $this->get_target_id_from_href( $href );

						if ( '' === $title || '' === $href ) {
							continue;
						}
						$manual_count++;
						?>
						<li class="dinopack-toc-level-<?php echo esc_attr( $level ); ?>">
							<a href="<?php echo esc_url( $href ); ?>"<?php echo $target ? ' data-target-id="' . esc_attr( $target ) . '"' : ''; ?>>
								<?php echo esc_html( $title ); ?>
							</a>
						</li>
						<?php
					endforeach;
				endif;
				?>
			</ol>

			<div class="dinopack-toc-empty" <?php echo ( 'manual' === $mode && $manual_count > 0 ) || 'auto' === $mode ? 'hidden' : ''; ?>>
				<?php
				if ( 'manual' === $mode ) {
					esc_html_e( 'Add TOC items and matching CSS IDs on your sections.', 'dinopack-for-elementor' );
				} else {
					esc_html_e( 'No headings found on this page.', 'dinopack-for-elementor' );
				}
				?>
			</div>
		</nav>
		<?php
	}
}
