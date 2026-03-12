<?php
/**
 * Register custom post types for Elementor templates: Header, Footer, Side Panel.
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
 * Class DinoPack_Templates_CPT
 */
class DinoPack_Templates_CPT {

	/**
	 * Post type slugs.
	 */
	const HEADER     = 'dinopack-header';
	const FOOTER     = 'dinopack-footer';
	const SIDE_PANEL = 'dinopack-side-panel';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
		add_action( 'elementor/init', array( $this, 'add_elementor_support' ) );
		add_action( 'template_redirect', array( $this, 'redirect_cpt_single_unless_editor_or_used' ), 5 );
		add_filter( 'template_include', array( $this, 'template_include' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_template_wrapper_styles' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'hide_theme_header_footer_in_editor' ) );
	}

	/**
	 * Limit CPT single view: allow only in editor (preview) or when used as part of the site.
	 *
	 * Redirect direct visits to Header/Footer/Side Panel single URLs to home, so templates
	 * are only visible in the Elementor editor or when used (header/footer conditions, panel widget).
	 *
	 * @since 1.0.0
	 */
	public function redirect_cpt_single_unless_editor_or_used() {
		if ( is_admin() || ! is_singular( array( self::HEADER, self::FOOTER, self::SIDE_PANEL ) ) ) {
			return;
		}
		// Allow when in Elementor preview iframe (editor).
		if ( isset( $_GET['elementor-preview'] ) ) {
			return;
		}
		// Direct frontend visit to CPT single: redirect to home.
		wp_safe_redirect( home_url( '/' ), 302 );
		exit;
	}

	/**
	 * In Elementor preview for Header/Footer/Side Panel CPTs: hide theme header/footer in editor.
	 *
	 * Fallback CSS in case the theme outputs header/footer via another path.
	 */
	public function hide_theme_header_footer_in_editor() {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}
		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, array( self::HEADER, self::FOOTER, self::SIDE_PANEL ), true ) ) {
			return;
		}
		$css = '.site-header, #masthead, header[role="banner"]:not(.dinopack-template-wrapper--header), .elementor-location-header:not(.dinopack-template-wrapper--header), .site-footer, #colophon, footer[role="contentinfo"]:not(.dinopack-template-wrapper--footer), .elementor-location-footer:not(.dinopack-template-wrapper--footer) { display: none !important; }';
		wp_add_inline_style( 'editor-preview', $css );
	}

	/**
	 * Enqueue wrapper styles when viewing a template CPT single.
	 */
	public function enqueue_template_wrapper_styles() {
		if ( ! is_singular( array( self::HEADER, self::FOOTER, self::SIDE_PANEL ) ) ) {
			return;
		}
		wp_enqueue_style(
			'dinopack-template-wrapper',
			DINOPACK_URL . 'assets/css/dinopack-template-wrapper.css',
			array(),
			defined( 'DINOPACK_VERSION' ) ? DINOPACK_VERSION : '1.0.0'
		);
	}

	/**
	 * Use plugin single template for Header, Footer, Side Panel CPTs (with wrapper).
	 *
	 * @param string $template Current template path.
	 * @return string
	 */
	public function template_include( $template ) {
		if ( ! is_singular( array( self::HEADER, self::FOOTER, self::SIDE_PANEL ) ) ) {
			return $template;
		}
		$plugin_template = DINOPACK_PATH . 'templates/single-dinopack-template.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
		return $template;
	}

	/**
	 * Explicitly add Elementor support for template CPTs (runs after Elementor loads).
	 */
	public function add_elementor_support() {
		add_post_type_support( self::HEADER, 'elementor' );
		add_post_type_support( self::FOOTER, 'elementor' );
		add_post_type_support( self::SIDE_PANEL, 'elementor' );
	}

	/**
	 * Register Header, Footer and Side Panel post types.
	 */
	public function register_post_types() {
		$this->register_header();
		$this->register_footer();
		$this->register_side_panel();
	}

	/**
	 * Register Header CPT.
	 */
	protected function register_header() {
		$labels = array(
			'name'                => _x( 'DinoPack Headers', 'Post Type General Name', 'dinopack-for-elementor' ),
			'singular_name'       => _x( 'DinoPack Header', 'Post Type Singular Name', 'dinopack-for-elementor' ),
			'all_items'           => __( 'Headers', 'dinopack-for-elementor' ),
			'name_admin_bar'      => __( 'Header', 'dinopack-for-elementor' ),
			'add_new_item'        => __( 'Add New Header', 'dinopack-for-elementor' ),
			'new_item'            => __( 'New Header', 'dinopack-for-elementor' ),
			'edit_item'           => __( 'Edit Header', 'dinopack-for-elementor' ),
			'update_item'         => __( 'Update Header', 'dinopack-for-elementor' ),
			'view_item'           => __( 'View Header', 'dinopack-for-elementor' ),
			'search_items'        => __( 'Search Header', 'dinopack-for-elementor' ),
		);
		$args = array(
			'label'               => __( 'Header', 'dinopack-for-elementor' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'revisions', 'elementor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( self::HEADER, $args );
	}

	/**
	 * Register Footer CPT.
	 */
	protected function register_footer() {
		$labels = array(
			'name'                => _x( 'Footers', 'Post Type General Name', 'dinopack-for-elementor' ),
			'singular_name'       => _x( 'Footer', 'Post Type Singular Name', 'dinopack-for-elementor' ),
			'all_items'           => __( 'Footers', 'dinopack-for-elementor' ),
			'name_admin_bar'      => __( 'Footer', 'dinopack-for-elementor' ),
			'add_new_item'        => __( 'Add New Footer', 'dinopack-for-elementor' ),
			'new_item'            => __( 'New Footer', 'dinopack-for-elementor' ),
			'edit_item'           => __( 'Edit Footer', 'dinopack-for-elementor' ),
			'update_item'         => __( 'Update Footer', 'dinopack-for-elementor' ),
			'view_item'           => __( 'View Footer', 'dinopack-for-elementor' ),
			'search_items'        => __( 'Search Footer', 'dinopack-for-elementor' ),
		);
		$args = array(
			'label'               => __( 'Footer', 'dinopack-for-elementor' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'revisions', 'elementor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( self::FOOTER, $args );
	}

	/**
	 * Register Side Panel CPT.
	 */
	protected function register_side_panel() {
		$labels = array(
			'name'                => _x( 'Side Panels', 'Post Type General Name', 'dinopack-for-elementor' ),
			'singular_name'       => _x( 'Side Panel', 'Post Type Singular Name', 'dinopack-for-elementor' ),
			'all_items'           => __( 'Side Panels', 'dinopack-for-elementor' ),
			'name_admin_bar'      => __( 'Side Panel', 'dinopack-for-elementor' ),
			'add_new_item'        => __( 'Add New Side Panel', 'dinopack-for-elementor' ),
			'new_item'            => __( 'New Side Panel', 'dinopack-for-elementor' ),
			'edit_item'           => __( 'Edit Side Panel', 'dinopack-for-elementor' ),
			'update_item'         => __( 'Update Side Panel', 'dinopack-for-elementor' ),
			'view_item'           => __( 'View Side Panel', 'dinopack-for-elementor' ),
			'search_items'        => __( 'Search Side Panel', 'dinopack-for-elementor' ),
		);
		$args = array(
			'label'               => __( 'Side Panel', 'dinopack-for-elementor' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'revisions', 'elementor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( self::SIDE_PANEL, $args );
	}
}
