<?php
/**
 * Register admin menu elements.
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
 * Class for admin menu.
 */
class DinoPack_Admin_Menus {

	/**
	 * Go Pro link.
	 *
	 * @var string
	 */
	private static $goProLink = '';

	/**
	 * The Constructor.
	 */
	public function __construct() {

		// Let's add menu item with subitems
		add_action( 'admin_menu', array( $this, 'register_menus' ), 15 );
		add_action( 'plugin_action_links_' . DINOPACK_PLUGIN_BASE, array( $this, 'plugin_action_links' ), 10, 4 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

	}

	/**
	 * Register admin menus.
	 */
	public function register_menus() {
		
		$page_title = esc_html__( 'DinoPack Settings Page', 'dinopack-for-elementor' );

		//DinoPack menu item.
		add_menu_page(
			$page_title,
			esc_html__( 'DinoPack', 'dinopack-for-elementor' ),
			'manage_options',
			'dinopack-settings', 
			array( $this, 'admin_page' ),
			'dashicons-album',
			25
		);

		add_submenu_page(
			'dinopack-settings',
			$page_title,
			esc_html__( 'Settings', 'dinopack-for-elementor' ),
			'manage_options',
			'dinopack-settings',
			array( $this, 'admin_page' )
		);

	}

	/**
	 * Wrapper for the hook to render our custom settings pages.
	 *
	 * @since 1.0.0
	 */
	public function admin_page() {
		do_action( 'wpdino_dinopack_admin_page' );
	}

	/**
	 * Wrapper for the hook to render our custom documentation pages.
	 *
	 * @since 1.0.0
	 */
	public function docs_page() {
		do_action( 'wpdino_dinopack_docs_page' );
	}

	/**
	 * Add settings and go PRO link to plugin page.
	 *
	 * @param array $links Array of links.
	 * @return array
	 */
	public function plugin_action_links( $links, $plugin_file, $plugin_data, $context ) {

		// Add settings link to the array
		/* translators: 1: Link to Settings page 2: Aria label 3: Link text */ 
		$custom['dinopack-settings'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=dinopack-settings' ) ),
			esc_attr__( 'Go to DinoPack Settings page', 'dinopack-for-elementor' ),
			esc_html__( 'Settings', 'dinopack-for-elementor' )
		);

		return array_merge( $custom, (array) $links );
		

	}

	/**
	 * Add Go Pro link to the DinoPack menu
	 */
	public function plugin_add_go_pro_link_to_menu() {

		global $submenu;

		// Add Go Pro link to the DinoPack menu
		if( ! defined( 'DINOPACK_PRO_VERSION' ) ) { 
			$submenu['dinopack-settings'][] = array( 
				'' . esc_html__( 'Upgrade to Pro', 'dinopack-for-elementor' ) . '',
				'manage_options', 
				self::$goProLink 
			);
		}
	}

	/**
	 * Enqueue admin assets using proper WordPress functions.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only load on admin pages
		if ( ! is_admin() ) {
			return;
		}

		// Enqueue jQuery (it's already included in WordPress admin)
		wp_enqueue_script( 'jquery' );

		// Add inline CSS for Go Pro link styling
		$css = sprintf(
			'#adminmenu #toplevel_page_dinopack-settings a[href="%s"] {
				background-color: #6F9C50;
				color: #fff;
				font-weight: bold;
			}',
			esc_attr( self::$goProLink )
		);
		wp_add_inline_style( 'wp-admin', $css );

		// Add inline JavaScript for Go Pro link target
		$js = sprintf(
			'jQuery( document ).ready( function( $ ) {
				$("a[href$=\\"%s\\"]").attr("target", "_blank");
			});',
			esc_js( self::$goProLink )
		);
		wp_add_inline_script( 'jquery', $js );
	}

}

new DinoPack_Admin_Menus();