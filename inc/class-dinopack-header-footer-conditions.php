<?php
/**
 * Header and Footer display conditions: metaboxes and get_header/get_footer override.
 *
 * @since   1.0.0
 * @package DinoPack
 */

namespace DinoPack;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DinoPack_Header_Footer_Conditions
 */
class DinoPack_Header_Footer_Conditions {

	const META_DISPLAY        = '_dinopack_display';
	const META_DISPLAY_SPECIFIC = '_dinopack_display_specific';

	/**
	 * Cache for template ID per request (header/footer).
	 *
	 * @var array<string, int|false>
	 */
	private static $template_id_cache = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'get_header', array( $this, 'maybe_override_header' ), 0 );
		add_action( 'get_footer', array( $this, 'maybe_override_footer' ), 0 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_hide_theme_hf_css' ), 999 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Add metabox for Display on (Header/Footer CPTs).
	 */
	public function add_meta_boxes() {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( DinoPack_Templates_CPT::HEADER, DinoPack_Templates_CPT::FOOTER ), true ) ) {
			return;
		}
		add_meta_box(
			'dinopack_display_conditions',
			__( 'Display on', 'dinopack-for-elementor' ),
			array( $this, 'render_meta_box' ),
			$screen->post_type,
			'side',
			'default'
		);
	}

	/**
	 * Render Display on metabox.
	 *
	 * @param \WP_Post $post Current post.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'dinopack_display_conditions', 'dinopack_display_conditions_nonce' );
		$display = get_post_meta( $post->ID, self::META_DISPLAY, true );
		$specific = get_post_meta( $post->ID, self::META_DISPLAY_SPECIFIC, true );
		if ( ! is_array( $specific ) ) {
			$specific = array();
		}
		$options = $this->get_display_options();
		?>
		<p>
			<label for="dinopack_display"><?php esc_html_e( 'Display on', 'dinopack-for-elementor' ); ?></label>
			<select name="dinopack_display" id="dinopack_display" class="widefat">
				<option value=""><?php esc_html_e( '— Select —', 'dinopack-for-elementor' ); ?></option>
				<?php foreach ( $options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $display, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="dinopack-display-specific" style="<?php echo $display === 'specifics' ? '' : 'display:none'; ?>">
			<label for="dinopack_display_specific"><?php esc_html_e( 'Specific pages/posts (IDs, comma-separated)', 'dinopack-for-elementor' ); ?></label>
			<input type="text" name="dinopack_display_specific" id="dinopack_display_specific" value="<?php echo esc_attr( implode( ', ', $specific ) ); ?>" class="widefat" />
		</p>
		<script>
		(function(){
			var sel = document.getElementById('dinopack_display');
			var specific = document.querySelector('.dinopack-display-specific');
			if (sel && specific) {
				sel.addEventListener('change', function(){
					specific.style.display = this.value === 'specifics' ? '' : 'none';
				});
			}
		})();
		</script>
		<?php
	}

	/**
	 * Get display location options (same structure for header and footer).
	 *
	 * @return array<string, string>
	 */
	public function get_display_options() {
		$options = array(
			'global'      => __( 'Entire site', 'dinopack-for-elementor' ),
			'front'       => __( 'Front page', 'dinopack-for-elementor' ),
			'blog'        => __( 'Blog / Posts page', 'dinopack-for-elementor' ),
			'archives'    => __( 'All archives', 'dinopack-for-elementor' ),
			'404'         => __( '404 page', 'dinopack-for-elementor' ),
			'search'      => __( 'Search results', 'dinopack-for-elementor' ),
			'singulars'   => __( 'All singular', 'dinopack-for-elementor' ),
			'all-page'    => __( 'All pages', 'dinopack-for-elementor' ),
			'specifics'   => __( 'Specific pages/posts', 'dinopack-for-elementor' ),
		);
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $pt ) {
			if ( in_array( $pt->name, array( 'attachment', DinoPack_Templates_CPT::HEADER, DinoPack_Templates_CPT::FOOTER, DinoPack_Templates_CPT::SIDE_PANEL ), true ) ) {
				continue;
			}
			$options[ $pt->name . '-singulars' ] = sprintf( __( 'Single %s', 'dinopack-for-elementor' ), $pt->labels->singular_name );
			$options[ $pt->name . '-archive' ]   = sprintf( __( '%s archive', 'dinopack-for-elementor' ), $pt->labels->singular_name );
		}
		if ( function_exists( 'is_shop' ) ) {
			$options['woo-shop'] = __( 'WooCommerce shop', 'dinopack-for-elementor' );
		}
		$options['date']   = __( 'Date archive', 'dinopack-for-elementor' );
		$options['author'] = __( 'Author archive', 'dinopack-for-elementor' );
		return $options;
	}

	/**
	 * Save Display on metabox.
	 *
	 * @param int     $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 */
	public function save_post( $post_id, $post ) {
		if ( ! isset( $_POST['dinopack_display_conditions_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dinopack_display_conditions_nonce'] ) ), 'dinopack_display_conditions' ) ) {
			return;
		}
		if ( ! in_array( $post->post_type, array( DinoPack_Templates_CPT::HEADER, DinoPack_Templates_CPT::FOOTER ), true ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$display = isset( $_POST['dinopack_display'] ) ? sanitize_text_field( wp_unslash( $_POST['dinopack_display'] ) ) : '';
		update_post_meta( $post_id, self::META_DISPLAY, $display );
		$specific_raw = isset( $_POST['dinopack_display_specific'] ) ? sanitize_text_field( wp_unslash( $_POST['dinopack_display_specific'] ) ) : '';
		$specific = array_filter( array_map( 'absint', explode( ',', $specific_raw ) ) );
		update_post_meta( $post_id, self::META_DISPLAY_SPECIFIC, $specific );
	}

	/**
	 * Get the template ID (header or footer) that matches the current request.
	 *
	 * @since   1.0.0
	 * @param string $type 'header' or 'footer'.
	 * @return int|false Post ID or false.
	 */
	public static function get_template_id_for_request( $type ) {
		if ( isset( self::$template_id_cache[ $type ] ) ) {
			return self::$template_id_cache[ $type ];
		}
		// On single DinoPack template CPT pages, use theme default header/footer (no override).
		$template_cpts = array(
			DinoPack_Templates_CPT::HEADER,
			DinoPack_Templates_CPT::FOOTER,
			DinoPack_Templates_CPT::SIDE_PANEL,
		);
		if ( is_singular( $template_cpts ) ) {
			self::$template_id_cache['header'] = false;
			self::$template_id_cache['footer'] = false;
			return false;
		}
		$cpt    = $type === 'footer' ? DinoPack_Templates_CPT::FOOTER : DinoPack_Templates_CPT::HEADER;
		$query  = new \WP_Query( array(
			'post_type'      => $cpt,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'fields'         => 'ids',
		) );
		$templates           = array();
		$templates_specifics = array();
		foreach ( $query->posts as $post_id ) {
			$location = get_post_meta( $post_id, self::META_DISPLAY, true );
			$specific = get_post_meta( $post_id, self::META_DISPLAY_SPECIFIC, true );
			if ( ! is_array( $specific ) ) {
				$specific = array();
			}
			if ( $location === 'specifics' ) {
				$templates_specifics[] = array( 'id' => $post_id, 'ids' => $specific );
			} elseif ( $location !== '' ) {
				$templates[ $location ] = $post_id;
			}
		}
		wp_reset_postdata();

		$current_id = get_queried_object_id();
		if ( ! is_home() && ! is_archive() && ! empty( $templates_specifics ) && $current_id ) {
			foreach ( $templates_specifics as $item ) {
				if ( in_array( (int) $current_id, array_map( 'intval', $item['ids'] ), true ) ) {
					self::$template_id_cache[ $type ] = (int) $item['id'];
					return (int) $item['id'];
				}
			}
		}
		if ( is_404() && isset( $templates['404'] ) ) {
			self::$template_id_cache[ $type ] = (int) $templates['404'];
			return (int) $templates['404'];
		}
		if ( is_search() && isset( $templates['search'] ) ) {
			self::$template_id_cache[ $type ] = (int) $templates['search'];
			return (int) $templates['search'];
		}
		if ( is_front_page() && isset( $templates['front'] ) ) {
			self::$template_id_cache[ $type ] = (int) $templates['front'];
			return (int) $templates['front'];
		}
		if ( is_home() && isset( $templates['blog'] ) ) {
			self::$template_id_cache[ $type ] = (int) $templates['blog'];
			return (int) $templates['blog'];
		}
		if ( is_archive() ) {
			if ( is_date() && isset( $templates['date'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['date'];
				return (int) $templates['date'];
			}
			if ( is_author() && isset( $templates['author'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['author'];
				return (int) $templates['author'];
			}
			if ( function_exists( 'is_shop' ) && is_shop() && isset( $templates['woo-shop'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['woo-shop'];
				return (int) $templates['woo-shop'];
			}
			$archive_key = get_post_type() . '-archive';
			if ( isset( $templates[ $archive_key ] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates[ $archive_key ];
				return (int) $templates[ $archive_key ];
			}
			if ( isset( $templates['archives'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['archives'];
				return (int) $templates['archives'];
			}
		}
		if ( is_singular() ) {
			if ( get_post_type() === 'page' && isset( $templates['all-page'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['all-page'];
				return (int) $templates['all-page'];
			}
			$singular_key = get_post_type() . '-singulars';
			if ( isset( $templates[ $singular_key ] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates[ $singular_key ];
				return (int) $templates[ $singular_key ];
			}
			if ( isset( $templates['singulars'] ) ) {
				self::$template_id_cache[ $type ] = (int) $templates['singulars'];
				return (int) $templates['singulars'];
			}
		}
		if ( isset( $templates['global'] ) ) {
			self::$template_id_cache[ $type ] = (int) $templates['global'];
			return (int) $templates['global'];
		}
		self::$template_id_cache[ $type ] = false;
		return false;
	}

	/**
	 * Override theme header when a DinoPack header template is set for this request.
	 *
	 * @since   1.0.0
	 * @param string $name Header name (e.g. '' or 'mini').
	 */
	public function maybe_override_header( $name ) {
		$template_id = self::get_template_id_for_request( 'header' );
		if ( ! $template_id ) {
			return;
		}
		$this->render_template( $template_id, 'header' );
	}

	/**
	 * Override theme footer when a DinoPack footer template is set for this request.
	 *
	 * @since   1.0.0
	 * @param string $name Footer name.
	 */
	public function maybe_override_footer( $name ) {
		$template_id = self::get_template_id_for_request( 'footer' );
		if ( ! $template_id ) {
			return;
		}
		$this->render_template( $template_id, 'footer' );
	}

	/**
	 * Output Elementor template content inside a semantic container.
	 *
	 * @since   1.0.0
	 * @param int    $template_id Post ID.
	 * @param string $type       'header' or 'footer'.
	 */
	protected function render_template( $template_id, $type ) {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		$document = \Elementor\Plugin::instance()->documents->get( $template_id );
		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return;
		}
		$template_id = (int) $template_id;
		$tag         = $type === 'footer' ? 'footer' : 'header';
		$role        = $type === 'footer' ? 'contentinfo' : 'banner';
		$id_attr     = 'dinopack-' . $type . '-' . $template_id;
		$classes     = 'dinopack-template-wrapper dinopack-template-wrapper--' . $type . ' elementor-location-' . $type;
		echo '<' . $tag . ' id="' . esc_attr( $id_attr ) . '" class="' . esc_attr( $classes ) . '" role="' . esc_attr( $role ) . '" data-dinopack-type="' . esc_attr( $type ) . '" data-dinopack-template="' . esc_attr( (string) $template_id ) . '">';
		echo '<div class="dinopack-template-inner">';
		echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		echo '</div>';
		echo '</' . $tag . '>';
	}

	/**
	 * Add body classes when using custom header/footer.
	 *
	 * @since   1.0.0
	 * @param array $classes Existing body classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( self::get_template_id_for_request( 'header' ) ) {
			$classes[] = 'dinopack-custom-header';
		}
		if ( self::get_template_id_for_request( 'footer' ) ) {
			$classes[] = 'dinopack-custom-footer';
		}
		return $classes;
	}

	/**
	 * Enqueue CSS to hide theme header/footer when DinoPack template is active.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_hide_theme_hf_css() {
		$has_header = self::get_template_id_for_request( 'header' );
		$has_footer = self::get_template_id_for_request( 'footer' );
		if ( ! $has_header && ! $has_footer ) {
			return;
		}
		$css = '';
		if ( $has_header ) {
			$css .= 'body.dinopack-custom-header .site-header, body.dinopack-custom-header #masthead, body.dinopack-custom-header header[role="banner"]:not(.dinopack-template-wrapper--header), body.dinopack-custom-header .elementor-location-header:not(.dinopack-template-wrapper--header) { display: none !important; }';
		}
		if ( $has_footer ) {
			$css .= 'body.dinopack-custom-footer .site-footer, body.dinopack-custom-footer #colophon, body.dinopack-custom-footer footer[role="contentinfo"]:not(.dinopack-template-wrapper--footer), body.dinopack-custom-footer .elementor-location-footer:not(.dinopack-template-wrapper--footer) { display: none !important; }';
		}
		wp_register_style( 'dinopack-hide-theme-hf', false, array(), defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0' );
		wp_enqueue_style( 'dinopack-hide-theme-hf' );
		wp_add_inline_style( 'dinopack-hide-theme-hf', $css );
	}
}
