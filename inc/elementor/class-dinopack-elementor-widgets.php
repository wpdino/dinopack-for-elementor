<?php
namespace DinoPack\Widgets; 

use Elementor\Plugin;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Instance the plugin
Dinopack_Elementor_Widgets::instance();

/**
 * Main DinoPack Elementor Widgets Class
 *
 * @since 1.0.0
 */
class Dinopack_Elementor_Widgets {

	/**
	 * Instance
	 *
	 * @var Dinopack_Elementor_Widgets The single instance of the class.
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
		add_action( 'elementor/init', array( $this, 'init' ), 9 );
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

		// Add Plugin actions
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_widget_categories' ] );
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_widgets() {

		// Include Widget files
		foreach ( glob( __DIR__ . '/widgets/*', GLOB_ONLYDIR | GLOB_NOSORT ) as $path ) {
			$slug = str_replace( __DIR__ . '/widgets/', '', $path );
			$slug_ = str_replace( '-', '_', $slug );
			$file = trailingslashit( $path ) . $slug . '.php';

			if ( file_exists( $file ) ) {
				require_once( $file );
				$class_name = '\Dinopack\Widgets\\' . ucwords( $slug_, '_' );
				if ( class_exists( $class_name ) ) {
					// Register widget
					Plugin::instance()->widgets_manager->register( new $class_name() );
				}
			}
		}
	}

	/**
	 * Add Widget Categories
	 *
	 * Add custom widget categories to Elementor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'dinopack-for-elementor',
			[
				'title' => esc_attr__( 'WPDINO - DinoPack', 'dinopack-for-elementor' ),
				'icon' => 'fa fa-plug'
			]
		);
	}
}
