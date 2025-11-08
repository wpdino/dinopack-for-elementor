<?php
/**
 * Plugin Name:         DinoPack for Elementor
 * Plugin URI:          https://wpdino.com/plugins/dinopack-for-elementor/
 * Description:         DinoPack for Elementor is a collection of creative and advanced widgets, expertly crafted by the WPDINO team to enhance your Elementor experience.
 * Version:             1.0.1
 * Author:              WPDINO
 * Author URI:          https://wpdino.com
 * Requires at least:   6.6
 * Tested up to:        6.8
 * Requires Plugins: elementor
 * Requires PHP: 7.0
 * Tags: elementor, widgets, page builder, templates, blocks, design
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: dinopack-for-elementor
 * Domain Path: /languages/
 * 
 * Privacy Policy: This plugin collects and processes personal data in the following ways:
 * 
 * 1. Newsletter Subscription: When users subscribe to newsletters via the Newsletter widget,
 *    email addresses and optional merge fields are transmitted to MailChimp API for processing.
 *    This data is handled according to MailChimp's privacy policy and terms of service.
 * 
 * 2. Plugin Settings: Admin settings are stored locally in your WordPress database.
 *    No personal data from settings is transmitted to external servers.
 * 
 * 3. No Tracking: This plugin does not track user behavior or collect analytics data.
 * 
 * For more information about MailChimp's data handling, please visit:
 * https://mailchimp.com/legal/privacy/
 * 
 * All other data remains on your website and is not transmitted to external servers.
 * 
 */

namespace DinoPack;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! defined( 'DINOPACK_VERSION' ) ) {
    define( 'DINOPACK_VERSION', get_file_data( __FILE__, [ 'Version' ] )[0] ); // phpcs:ignore
}

define( 'DINOPACK__FILE__', __FILE__ );
define( 'DINOPACK_PLUGIN_BASE', plugin_basename( DINOPACK__FILE__ ) );
define( 'DINOPACK_PLUGIN_DIR', dirname( DINOPACK_PLUGIN_BASE ) );

define( 'DINOPACK_PATH', plugin_dir_path( DINOPACK__FILE__ ) );
define( 'DINOPACK_URL', plugin_dir_url( DINOPACK__FILE__ ) );

/**
 * Main Plugin Class
 *
 * @since 1.0.0
 */
final class Plugin {	

	/**
	 * Instance
	 *
	 * @var Dinopack\Plugin The single instance of the class.
	 * @since 1.0.0
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return Dinopack\Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		self::includes();

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'plugin_css' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'plugin_css' ) );

	}

	/**
	 * Includes files
	 * @method includes
	 *
	 * @return void
	 */
	public function includes() {


		// Controls & Widgets
		include_once DINOPACK_PATH . 'inc/elementor/class-dinopack-elementor-controls.php';
		include_once DINOPACK_PATH . 'inc/elementor/class-dinopack-elementor-widgets.php';
		include_once DINOPACK_PATH . 'inc/elementor/class-dinopack-elementor-utils.php';
		
		// AJAX Handlers
		include_once DINOPACK_PATH . 'inc/class-dinopack-elementor-ajax-handlers.php';

		// Admin Menus and settins page
		include_once DINOPACK_PATH . 'inc/class-dinopack-admin-menus.php';

		// Admin page with settings
		include_once DINOPACK_PATH . 'inc/admin/settings/class-dinopack-settings-page.php';
		include_once DINOPACK_PATH . 'inc/admin/settings/class-dinopack-field-renderer.php';

		// Initialize settings page
		if ( class_exists( '\DinoPack\DinoPack_Settings' ) ) {
			\DinoPack\DinoPack_Settings::instance();
		}

	}

	/**
	 * Enqueue plugin styles.
	 */
	public function plugin_css() {	
		wp_enqueue_style( 
            'dinopack-for-elementor', 
            DINOPACK_URL . 'assets/css/dinopack-for-elementor.css', 
			[],
            DINOPACK_VERSION 
        );
	}

	/**
	 * On Plugins Loaded
	 *
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function on_plugins_loaded() {
		add_action( 'elementor/init', array( $this, 'init' ) );
	}

    /**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {}
    
}

Plugin::instance();

