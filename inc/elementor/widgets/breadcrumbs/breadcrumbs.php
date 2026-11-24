<?php
/**
 * Breadcrumbs Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

/**
 * SEO-friendly breadcrumb trail for pages and posts.
 */
class Breadcrumbs extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-breadcrumbs',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
	}

	public function get_name() {
		return 'dinopack-breadcrumbs';
	}

	public function get_title() {
		return esc_html__( 'Breadcrumbs', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor', 'dinopack-header' );
	}

	public function get_keywords() {
		return array( 'breadcrumb', 'navigation', 'seo', 'trail', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-breadcrumbs' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Breadcrumbs', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'home_text',
			array(
				'label'   => esc_html__( 'Home Label', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Home', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'show_home',
			array(
				'label'        => esc_html__( 'Show Home', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_current',
			array(
				'label'        => esc_html__( 'Show Current Page', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label'   => esc_html__( 'Separator', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slash',
				'options' => array(
					'slash'  => esc_html__( 'Slash /', 'dinopack-for-elementor' ),
					'chevron'=> esc_html__( 'Chevron ›', 'dinopack-for-elementor' ),
					'dash'   => esc_html__( 'Dash -', 'dinopack-for-elementor' ),
					'pipe'   => esc_html__( 'Pipe |', 'dinopack-for-elementor' ),
					'icon'   => esc_html__( 'Custom Icon', 'dinopack-for-elementor' ),
					'custom' => esc_html__( 'Custom Text', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'separator_icon',
			array(
				'label'     => esc_html__( 'Separator Icon', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'separator_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'separator_text',
			array(
				'label'     => esc_html__( 'Separator Text', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'condition' => array(
					'separator_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'enable_schema',
			array(
				'label'        => esc_html__( 'Schema Markup (JSON-LD)', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Adds BreadcrumbList structured data for SEO.', 'dinopack-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-breadcrumbs' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .dinopack-breadcrumbs',
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-breadcrumbs a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#557a3d',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-breadcrumbs a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'current_color',
			array(
				'label'     => esc_html__( 'Current Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1f26',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-breadcrumb-current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => esc_html__( 'Separator Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9aa3ad',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-breadcrumb-sep' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'spacing',
			array(
				'label'      => esc_html__( 'Item Spacing', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 2,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 8,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-breadcrumbs' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build breadcrumb trail items.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function get_trail( $settings ) {
		$items = array();

		if ( 'yes' === $settings['show_home'] ) {
			$items[] = array(
				'label' => ! empty( $settings['home_text'] ) ? $settings['home_text'] : esc_html__( 'Home', 'dinopack-for-elementor' ),
				'url'   => home_url( '/' ),
			);
		}

		if ( is_front_page() ) {
			return $items;
		}

		if ( is_home() && ! is_front_page() ) {
			$posts_page_id = (int) get_option( 'page_for_posts' );
			$label         = $posts_page_id ? get_the_title( $posts_page_id ) : esc_html__( 'Blog', 'dinopack-for-elementor' );
			$items[]       = array(
				'label'   => $label,
				'url'     => '',
				'current' => true,
			);
			return $items;
		}

		if ( is_singular() ) {
			$post_id   = get_the_ID();
			$post_type = get_post_type( $post_id );

			if ( 'post' === $post_type ) {
				$categories = get_the_category( $post_id );
				if ( ! empty( $categories ) ) {
					$primary = $categories[0];
					$items[] = array(
						'label' => $primary->name,
						'url'   => get_category_link( $primary->term_id ),
					);
				}
			} elseif ( 'product' === $post_type && function_exists( 'wc_get_product_terms' ) ) {
				$terms = get_the_terms( $post_id, 'product_cat' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$term    = array_shift( $terms );
					$items[] = array(
						'label' => $term->name,
						'url'   => get_term_link( $term ),
					);
				}
			} else {
				$ancestors = array_reverse( get_post_ancestors( $post_id ) );
				foreach ( $ancestors as $ancestor_id ) {
					$items[] = array(
						'label' => get_the_title( $ancestor_id ),
						'url'   => get_permalink( $ancestor_id ),
					);
				}
			}

			if ( 'yes' === $settings['show_current'] ) {
				$items[] = array(
					'label'   => get_the_title( $post_id ),
					'url'     => '',
					'current' => true,
				);
			}

			return $items;
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! is_wp_error( $term ) ) {
				$items[] = array(
					'label'   => $term->name,
					'url'     => '',
					'current' => true,
				);
			}
			return $items;
		}

		if ( is_search() ) {
			$items[] = array(
				'label'   => sprintf(
					/* translators: %s: search query */
					esc_html__( 'Search: %s', 'dinopack-for-elementor' ),
					get_search_query()
				),
				'url'     => '',
				'current' => true,
			);
			return $items;
		}

		if ( is_404() ) {
			$items[] = array(
				'label'   => esc_html__( 'Page Not Found', 'dinopack-for-elementor' ),
				'url'     => '',
				'current' => true,
			);
			return $items;
		}

		if ( is_archive() ) {
			$items[] = array(
				'label'   => get_the_archive_title(),
				'url'     => '',
				'current' => true,
			);
		}

		return $items;
	}

	/**
	 * Separator markup.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	private function get_separator_html( $settings ) {
		$type = ! empty( $settings['separator_type'] ) ? $settings['separator_type'] : 'slash';

		$map = array(
			'slash'   => '/',
			'chevron' => '›',
			'dash'    => '–',
			'pipe'    => '|',
		);

		if ( isset( $map[ $type ] ) ) {
			return '<span class="dinopack-breadcrumb-sep" aria-hidden="true">' . esc_html( $map[ $type ] ) . '</span>';
		}

		if ( 'custom' === $type ) {
			$text = ! empty( $settings['separator_text'] ) ? $settings['separator_text'] : '/';
			return '<span class="dinopack-breadcrumb-sep" aria-hidden="true">' . esc_html( $text ) . '</span>';
		}

		if ( 'icon' === $type ) {
			ob_start();
			echo '<span class="dinopack-breadcrumb-sep dinopack-breadcrumb-sep-icon" aria-hidden="true">';
			if ( ! empty( $settings['separator_icon']['value'] ) ) {
				Icons_Manager::render_icon( $settings['separator_icon'], array( 'aria-hidden' => 'true' ) );
			} else {
				echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><path d="M3.5 1.5L7 5L3.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			}
			echo '</span>';
			return ob_get_clean();
		}

		return '<span class="dinopack-breadcrumb-sep" aria-hidden="true">/</span>';
	}

	/**
	 * Output BreadcrumbList JSON-LD.
	 *
	 * @param array $items Trail items.
	 */
	private function render_schema( $items ) {
		$list = array();
		$pos  = 1;

		foreach ( $items as $item ) {
			$url = ! empty( $item['url'] ) ? $item['url'] : ( is_singular() ? get_permalink() : home_url( add_query_arg( array() ) ) );
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'name'     => wp_strip_all_tags( $item['label'] ),
				'item'     => $url,
			);
			$pos++;
		}

		if ( empty( $list ) ) {
			return;
		}

		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $this->get_trail( $settings );

		if ( empty( $items ) ) {
			return;
		}

		$separator = $this->get_separator_html( $settings );
		$total     = count( $items );
		?>
		<nav class="dinopack-breadcrumbs" aria-label="<?php echo esc_attr__( 'Breadcrumb', 'dinopack-for-elementor' ); ?>">
			<ol class="dinopack-breadcrumbs-list">
				<?php
				foreach ( $items as $index => $item ) :
					$is_current = ! empty( $item['current'] ) || ( $index === $total - 1 && empty( $item['url'] ) );
					?>
					<li class="dinopack-breadcrumb-item<?php echo $is_current ? ' is-current' : ''; ?>">
						<?php if ( ! empty( $item['url'] ) && ! $is_current ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
						<?php else : ?>
							<span class="dinopack-breadcrumb-current"<?php echo $is_current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( wp_strip_all_tags( $item['label'] ) ); ?></span>
						<?php endif; ?>
					</li>
					<?php
					if ( $index < $total - 1 ) {
						echo '<li class="dinopack-breadcrumb-separator" aria-hidden="true">' . $separator . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
					}
				endforeach;
				?>
			</ol>
		</nav>
		<?php
		if ( 'yes' === $settings['enable_schema'] && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$this->render_schema( $items );
		}
	}
}
