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
	private static $goProLink = 'https://wpdino.com/plugins/dinopack-pro-for-elementor/?utm_source=wpadmin&utm_medium=dinopack-free&utm_campaign=go-pro-links';
	
	/**
	 * Documentation link.
	 *
	 * @var string
	 */
	private static $docsLink = 'https://wpdino.com/docs/dinopack-for-elementor/?utm_source=wpadmin&utm_medium=dinopack-free&utm_campaign=docs-link';

	/**
	 * The Constructor.
	 */
	public function __construct() {

		// Let's add menu item with subitems
		add_action( 'admin_menu', array( $this, 'register_menus' ), 15 );
		add_action( 'admin_menu', array( $this, 'plugin_add_go_pro_link_to_menu' ), 20 );
		add_action( 'plugin_action_links_' . DINOPACK_PLUGIN_BASE, array( $this, 'plugin_action_links' ), 10, 4 );

		add_action( 'admin_head', array( $this, 'add_css_go_pro_menu' ) );
		add_action( 'admin_footer', array( $this, 'add_target_blank_go_pro_menu' ) );
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

		// Settings link
		$settings_link = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( admin_url( 'admin.php?page=dinopack-settings' ) ),
			esc_attr__( 'Go to DinoPack Settings page', 'dinopack-for-elementor' ),
			esc_html__( 'Settings', 'dinopack-for-elementor' )
		);

		// Add settings link to the beginning of the array
		array_unshift( $links, $settings_link );

		// Add Go Pro link if PRO version is not installed
		if ( ! class_exists( 'DinoPackPro\Plugin' ) ) {
			$links['go_pro'] = sprintf( 
				'<a href="%1$s" target="_blank" class="dinopack-gopro" style="color:#6F9C50;font-weight:bold;">%2$s <span class="dinopack-premium-badge" style="background-color: #6F9C50; color: #fff; margin-left: 5px; font-size: 11px; min-height: 16px; border-radius: 8px; display: inline-block; font-weight: 600; line-height: 1.6; padding: 0 8px">%3$s</span></a>',
				esc_url( self::$goProLink ), 
				esc_html__( 'Get DinoPack PRO', 'dinopack-for-elementor' ),
				esc_html__( 'PRO', 'dinopack-for-elementor' )
			);
		}

		// Add Docs link
		$links['docs'] = sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( self::$docsLink ),
			esc_html__( 'Docs', 'dinopack-for-elementor' )
		);

		return $links;
	}

	/**
	 * Add Go Pro link to the DinoPack menu
	 */
	public function plugin_add_go_pro_link_to_menu() {

		global $submenu;

		// Add Go Pro link to the DinoPack menu if PRO version is not installed
		if ( ! class_exists( 'DinoPackPro\Plugin' ) ) { 
			$submenu['dinopack-settings'][] = array( 
				esc_html__( 'Upgrade to PRO', 'dinopack-for-elementor' ),
				'manage_options', 
				self::$goProLink 
			);
		}
	}
	
	/**
	 * Add CSS to Go Pro link in admin menu.
	 */
	public function add_css_go_pro_menu() {
		if ( ! class_exists( 'DinoPackPro\Plugin' ) ) {
			$escaped_url = esc_url( self::$goProLink );
			?>
			<style>
				#adminmenu #toplevel_page_dinopack-settings a[href="<?php echo esc_attr( $escaped_url ); ?>"],
				#adminmenu #toplevel_page_dinopack-settings a[href*="dinopack-pro-for-elementor"],
				#adminmenu #toplevel_page_dinopack-settings a.dinopack-go-pro-menu-link {
					background-color: #6F9C50 !important;
					color: #fff !important;
					font-weight: 600 !important;
				}
				#adminmenu #toplevel_page_dinopack-settings a[href="<?php echo esc_attr( $escaped_url ); ?>"]:hover,
				#adminmenu #toplevel_page_dinopack-settings a[href*="dinopack-pro-for-elementor"]:hover,
				#adminmenu #toplevel_page_dinopack-settings a.dinopack-go-pro-menu-link:hover {
					background-color: #5a7d3f !important;
					color: #fff !important;
				}
			</style>
			<?php
		}
	}

	/**
	 * Add target="_blank" to Go Pro link in admin menu.
	 */
	public function add_target_blank_go_pro_menu() {
		if ( ! class_exists( 'DinoPackPro\Plugin' ) ) {
			?>
			<script>
				jQuery( document ).ready( function( $ ) {
					var $proLink = $('.wp-submenu a[href*="dinopack-pro-for-elementor"]');
					if ( $proLink.length ) {
						$proLink.attr('target', '_blank').addClass('dinopack-go-pro-menu-link');
					}
				});
			</script>
			<?php
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
	}

}

new DinoPack_Admin_Menus();