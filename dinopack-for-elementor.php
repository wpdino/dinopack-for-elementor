<?php
/**
 * Plugin Name:         DinoPack for Elementor
 * Plugin URI:          https://wpdino.com/plugins/dinopack-for-elementor/
 * Description:         DinoPack for Elementor is a collection of creative and advanced widgets, expertly crafted by the WPDINO team to enhance your Elementor experience.
 * Version:             1.0.5
 * Author:              WPDINO
 * Author URI:          https://wpdino.com
 * Requires at least:   6.6
 * Tested up to:        6.9
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
 * 2. AI Content Generation: When using AI-powered widgets (AI Product Description Generator,
 *    AI Product Review Summarizer, AI Product Image Generator, and AI Product SEO Meta Generator),
 *    product information and review data are transmitted to OpenAI API for content generation.
 *    This includes product names, descriptions, prices, SKUs, categories, attributes, and review text.
 *    OpenAI may use this data to improve their services as outlined in their privacy policy.
 *    Content generation must be manually triggered by the site administrator from the Elementor editor.
 * 
 * 3. Plugin Settings: Admin settings (including API keys) are stored locally in your WordPress
 *    database. API keys are only transmitted to their respective service providers (MailChimp
 *    or OpenAI) when making API requests. No personal data from settings is transmitted to
 *    external servers except as part of API requests.
 * 
 * 4. No Tracking: This plugin does not track user behavior or collect analytics data.
 * 
 * For more information about external service data handling:
 * - MailChimp: https://mailchimp.com/legal/privacy/
 * - OpenAI: https://openai.com/policies/privacy-policy
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

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'register_elementor_v2_editor_placeholders' ), 0 );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'plugin_css' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'plugin_css' ) );

		add_action( 'elementor/editor/footer', array( $this, 'editor_template_library_scripts' ) );
		add_action( 'elementor/editor/footer', array( $this, 'insert_editor_templates' ) );
	}

	/**
	 * Register placeholder scripts for Elementor v2 editor handles (avoids WP 6.9.1 notices).
	 */
	public function register_elementor_v2_editor_placeholders() {
		$placeholders = array(
			'elementor-v2-editor-controls',
			'elementor-v2-editor-elements',
			'elementor-v2-editor-canvas',
			'elementor-v2-editor-editing-panel',
			'elementor-v2-editor-props',
			'elementor-v2-editor-styles-repository',
		);
		foreach ( $placeholders as $handle ) {
			if ( ! wp_script_is( $handle, 'registered' ) ) {
				wp_register_script( $handle, '', array(), null );
			}
		}
	}

	/**
	 * Enqueue template library JS (and optional icon URL) in the Elementor editor.
	 */
	public function editor_template_library_scripts() {
		$icon_url = file_exists( DINOPACK_PATH . 'assets/images/letter-d.svg' )
			? DINOPACK_URL . 'assets/images/letter-d.svg'
			: ( file_exists( DINOPACK_PATH . 'assets/images/dinopack-logo.svg' ) ? DINOPACK_URL . 'assets/images/dinopack-logo.svg' : '' );
		wp_enqueue_script(
			'dinopack-template-library',
			DINOPACK_URL . 'assets/js/dinopack-template-library.js',
			array( 'jquery', 'wp-util' ),
			DINOPACK_VERSION,
			true
		);
		wp_localize_script(
			'dinopack-template-library',
			'dinopackTemplateLibraryData',
			array( 'icon_url' => $icon_url )
		);
	}

	/**
	 * Output editor modal templates (header, loading, toolbar).
	 */
	public function insert_editor_templates() {
		include DINOPACK_PATH . 'inc/elementor/editor-templates/templates.php';
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

		// AI Helper for AI-powered widgets
		include_once DINOPACK_PATH . 'inc/class-dinopack-ai-helper.php';

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
	public function init() {
		include_once DINOPACK_PATH . 'inc/elementor/class-dinopack-template-manager.php';
	}
    
}

Plugin::instance();

