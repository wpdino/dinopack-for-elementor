<?php
/**
 * DinoPack Settings Page
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
 * Class for settings page.
 */
class DinoPack_Settings {

	/**
	 * Settings option name
	 */
	const OPTION_NAME = 'dinopack_settings';

	/**
	 * Default settings (generated dynamically from field configuration)
	 */
	private $defaults = null;

	/**
	 * Current settings
	 */
	public static $settings = array();

	/**
	 * Field renderer instance
	 */
	private $field_renderer;

	/**
	 * This class instance.
	 *
	 * @var DinoPack_Settings
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Provides singleton instance.
	 *
	 * @since 1.0.0
	 * @return self instance
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new DinoPack_Settings();
		}

		return self::$instance;
	}

	/**
	 * The Constructor.
	 */
	public function __construct() {

		add_action( 'wpdino_dinopack_admin_page', array( $this, 'settings_page' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_wpdino_reset_settings', array( $this, 'ajax_reset_settings' ) );
		add_action( 'wp_ajax_wpdino_export_settings', array( $this, 'ajax_export_settings' ) );
		add_action( 'wp_ajax_wpdino_import_settings', array( $this, 'ajax_import_settings' ) );

		// Defer settings loading until init (when translations are safe)
		add_action( 'init', array( $this, 'init_settings' ) );
	}

	/**
	 * Initialize settings at init hook (when translations are safe)
	 */
	public function init_settings() {
		// Reset defaults to null so they get regenerated with translations
		$this->defaults = null;
		
		// Load settings
		$this->load_settings();

		// Initialize field renderer
		$this->field_renderer = new DinoPack_Field_Renderer( $this );
	}

	/**
	 * Generate default settings from field configuration
	 */
	private function get_defaults() {
		if ( $this->defaults === null ) {
			$this->defaults = array();
			
			// Check if init has run to avoid early translation calls
			if ( ! did_action( 'init' ) ) {
				// Use hardcoded defaults when called too early (before init)
				$this->defaults = $this->get_fallback_defaults();
			} else {
				// Use dynamic defaults from field configuration
				$all_fields = $this->get_all_fields();
				
				foreach ( $all_fields as $field_id => $field ) {
					if ( isset( $field['default'] ) ) {
						$this->defaults[ $field_id ] = $field['default'];
					} else {
						// Set sensible defaults based on field type
						switch ( $field['type'] ) {
							case 'checkbox':
								$this->defaults[ $field_id ] = false;
								break;
							case 'number':
							case 'range':
								$this->defaults[ $field_id ] = 0;
								break;
							case 'multiple_select':
								$this->defaults[ $field_id ] = array();
								break;
							default:
								$this->defaults[ $field_id ] = '';
								break;
						}
					}
				}
			}
		}
		
		return $this->defaults;
	}

	/**
	 * Fallback defaults for early initialization (before init)
	 */
	private function get_fallback_defaults() {
		$defaults = array();
		$sections = $this->get_settings_sections();
		
		foreach ( $sections as $section ) {
			if ( ! empty( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					// Handle row type (nested fields)
					if ( isset( $field['type'] ) && $field['type'] === 'row' && ! empty( $field['fields'] ) ) {
						foreach ( $field['fields'] as $row_field ) {
							if ( isset( $row_field['id'] ) && isset( $row_field['default'] ) ) {
								$defaults[ $row_field['id'] ] = $row_field['default'];
							}
						}
					} elseif ( isset( $field['id'] ) && isset( $field['default'] ) ) {
						$defaults[ $field['id'] ] = $field['default'];
					}
				}
			}
		}
		
		return $defaults;
	}

	/**
	 * Load settings from database
	 */
	private function load_settings() {
		$saved_settings = get_option( self::OPTION_NAME, array() );
		self::$settings = wp_parse_args( $saved_settings, $this->get_defaults() );
	}

	/**
	 * Get setting value
	 */
	public function get_setting( $key, $default = null ) {
		// Ensure settings are loaded
		if ( empty( self::$settings ) ) {
			$this->load_settings();
		}
		
		if ( isset( self::$settings[ $key ] ) ) {
			return self::$settings[ $key ];
		}
		$defaults = $this->get_defaults();
		return $default !== null ? $default : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : null );
	}

	/**
	 * Update setting value
	 */
	public function update_setting( $key, $value ) {
		self::$settings[ $key ] = $value;
		return update_option( self::OPTION_NAME, self::$settings );
	}

	/**
	 * Get all settings
	 */
	public static function get_all_settings() {
		return self::$settings;
	}

	/**
	 * Update all settings
	 */
	public function update_all_settings( $new_settings ) {
		self::$settings = wp_parse_args( $new_settings, $this->get_defaults() );
		return update_option( self::OPTION_NAME, self::$settings );
	}

	/**
	 * Process form submission (dynamic version based on field configuration)
	 */
	public function process_form() {
		if ( ! isset( $_POST['wpdino_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpdino_settings_nonce'] ) ), 'wpdino_settings_save' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get the settings array from POST
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via sanitize_field_value() below
		$post_settings = isset( $_POST[ self::OPTION_NAME ] ) ? wp_unslash( $_POST[ self::OPTION_NAME ] ) : array();
		$updated_settings = array();

		// Get all field configurations
		$all_fields = $this->get_all_fields();

		// Process each field dynamically
		foreach ( $all_fields as $field_id => $field ) {
			$field_name = isset( $field['name'] ) ? $field['name'] : $field_id;
			$default_value = isset( $field['default'] ) ? $field['default'] : '';
			
			// Get the posted value for this field
			$posted_value = null;
			if ( $field['type'] === 'checkbox' ) {
				// For checkboxes, check if the field name exists in POST data
				$posted_value = isset( $post_settings[ $field_name ] );
			} else {
				// For other fields, get the actual value
				$posted_value = $post_settings[ $field_name ] ?? $default_value;
			}

			// Sanitize the value based on field type
			$updated_settings[ $field_id ] = $this->sanitize_field_value( $field, $posted_value );
		}

		// Update settings
		if ( $this->update_all_settings( $updated_settings ) ) {
			// Redirect to same page with success parameter
			wp_safe_redirect( add_query_arg( 'settings-updated', 'true', wp_get_referer() ) );
			exit;
		}
	}

	/**
	 * Render admin notices
	 */
	private function render_admin_notices() {
		// Check for settings updated
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just checking for display flag
		if ( isset( $_GET['settings-updated'] ) && sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) === 'true' ) {
			?>
			<div class="wpdino-admin-notice wpdino-admin-notice-success">
				<span class="dashicons dashicons-yes-alt"></span>
				<span><?php esc_html_e( 'Settings saved successfully!', 'dinopack-for-elementor' ); ?></span>
				<button type="button" class="wpdino-notice-dismiss">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
			</div>
			<?php
		}
		
		// Check for settings reset
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just checking for display flag
		if ( isset( $_GET['settings-reset'] ) && sanitize_text_field( wp_unslash( $_GET['settings-reset'] ) ) === 'true' ) {
			?>
			<div class="wpdino-admin-notice wpdino-admin-notice-reset">
				<span class="dashicons dashicons-backup"></span>
				<span><?php esc_html_e( 'Settings have been reset to defaults.', 'dinopack-for-elementor' ); ?></span>
				<button type="button" class="wpdino-notice-dismiss">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
			</div>
			<?php
		}
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function enqueue_scripts( $hook ) {

		// Check for our settings page hook - try multiple possible variations 
		$valid_hooks = array(
			'toplevel_page_dinopack-settings'
		);
		
		// Also load on our post type admin pages as fallback
		$is_dinopack_page = (
			in_array( $hook, $valid_hooks ) ||
			strpos( $hook, 'dinopack-settings' ) !== false
		);
		
		if ( ! $is_dinopack_page ) {
			return;
		}

		// Enqueue color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		
		// Enqueue media uploader
		wp_enqueue_media();

		wp_enqueue_style(
			'wpdino-admin',
			DINOPACK_URL . 'inc/admin/assets/css/admin.css',
			array( 'wp-color-picker' ),
			DINOPACK_VERSION
		);

		wp_enqueue_script(
			'wpdino-admin',
			DINOPACK_URL . 'inc/admin/assets/js/admin.js',
			array( 'jquery', 'wp-color-picker', 'media-upload', 'thickbox' ),
			DINOPACK_VERSION,
			true
		);

		wp_localize_script( 'wpdino-admin', 'wpdinoAdmin', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'wpdino_admin_action' ),
			'strings' => array(
				'confirmReset'  => esc_html__( 'Are you sure you want to reset all settings to defaults? This action cannot be undone.', 'dinopack-for-elementor' ),
				'confirmImport' => esc_html__( 'Are you sure you want to import these settings? This will overwrite your current settings.', 'dinopack-for-elementor' ),
				'resetSuccess'  => esc_html__( 'Settings have been reset to defaults.', 'dinopack-for-elementor' ),
				'exportSuccess' => esc_html__( 'Settings exported successfully!', 'dinopack-for-elementor' ),
				'importSuccess' => esc_html__( 'Settings imported successfully!', 'dinopack-for-elementor' ),
				'copySuccess'   => esc_html__( 'System info copied to clipboard!', 'dinopack-for-elementor' ),
				'copyError'     => esc_html__( 'Failed to copy. Please select and copy manually.', 'dinopack-for-elementor' ),
				'error'         => esc_html__( 'An error occurred. Please try again.', 'dinopack-for-elementor' ),
				'invalidFile'   => esc_html__( 'Please select a valid JSON file.', 'dinopack-for-elementor' ),
			)
		) );
	}

	/**
	 * AJAX Reset Settings
	 */
	public function ajax_reset_settings() {
		check_ajax_referer( 'wpdino_admin_action', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'dinopack-for-elementor' ) );
		}

		$this->update_all_settings( $this->get_defaults() );

		wp_send_json_success( array(
			'message' => esc_html__( 'Settings have been reset to defaults.', 'dinopack-for-elementor' ),
			'redirect' => add_query_arg( 'settings-reset', 'true', admin_url( 'admin.php?page=dinopack-settings' ) )
		) );
	}

	/**
	 * AJAX Export Settings
	 */
	public function ajax_export_settings() {
		check_ajax_referer( 'wpdino_admin_action', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'dinopack-for-elementor' ) );
		}

		wp_send_json_success( array(
			'settings' => self::get_all_settings(),
			'filename' => 'dinopack-settings-' . gmdate( 'Y-m-d-H-i-s' ) . '.json'
		) );
	}

	/**
	 * AJAX Import Settings
	 */
	public function ajax_import_settings() {
		check_ajax_referer( 'wpdino_admin_action', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'dinopack-for-elementor' ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized on the next line
		$settings_json = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';
		$settings_json = sanitize_textarea_field( $settings_json );
		$settings = json_decode( $settings_json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Invalid JSON format.', 'dinopack-for-elementor' )
			) );
		}

		$this->update_all_settings( $settings );

		wp_send_json_success( array(
			'message' => esc_html__( 'Settings imported successfully!', 'dinopack-for-elementor' )
		) );
	}

	/**
	 * Get all fields from all sections (flattened)
	 */
	private function get_all_fields() {
		$all_fields = array();
		$sections = $this->get_settings_sections();
		
		foreach ( $sections as $section ) {
			if ( ! empty( $section['fields'] ) ) {
				foreach ( $section['fields'] as $field ) {
					// Handle row type (nested fields)
					if ( isset( $field['type'] ) && $field['type'] === 'row' && ! empty( $field['fields'] ) ) {
						foreach ( $field['fields'] as $row_field ) {
							if ( isset( $row_field['id'] ) ) {
								$all_fields[ $row_field['id'] ] = $row_field;
							}
						}
					} elseif ( isset( $field['id'] ) ) {
						$all_fields[ $field['id'] ] = $field;
					}
				}
			}
		}
		
		return $all_fields;
	}

	/**
	 * Sanitize field value based on its type
	 */
	private function sanitize_field_value( $field, $value ) {
		$default = isset( $field['default'] ) ? $field['default'] : '';
		
		switch ( $field['type'] ) {
			case 'text':
				return sanitize_text_field( $value );
				
			case 'textarea':
				return sanitize_textarea_field( $value );
				
			case 'email':
				return sanitize_email( $value );
				
			case 'url':
				return esc_url_raw( $value );
				
			case 'number':
			case 'range':
				$num_value = is_numeric( $value ) ? floatval( $value ) : $default;
				
				// Apply min constraint
				if ( isset( $field['min'] ) && $num_value < $field['min'] ) {
					$num_value = $field['min'];
				}
				
				// Apply max constraint  
				if ( isset( $field['max'] ) && $num_value > $field['max'] ) {
					$num_value = $field['max'];
				}
				
				// For range and integer number fields, return as integer
				if ( $field['type'] === 'range' || ( isset( $field['step'] ) && $field['step'] == 1 ) ) {
					return intval( $num_value );
				}
				
				return $num_value;
				
			case 'checkbox':
				return ! empty( $value );
				
			case 'select':
			case 'radio':
			case 'image_select':
				// Validate against available options
				if ( isset( $field['options'] ) && array_key_exists( $value, $field['options'] ) ) {
					return sanitize_text_field( $value );
				}
				return $default;
				
			case 'multiple_select':
				if ( is_array( $value ) ) {
					$sanitized = array();
					foreach ( $value as $item ) {
						if ( isset( $field['options'] ) && array_key_exists( $item, $field['options'] ) ) {
							$sanitized[] = sanitize_text_field( $item );
						}
					}
					return $sanitized;
				}
				return $default;
				
			case 'colorpicker':
				return sanitize_hex_color( $value ) ?: $default;
				
			case 'editor':
				return wp_kses_post( $value );
				
			case 'file':
				// Validate image URL and check if it's a valid attachment
				$url = esc_url_raw( $value );
				if ( empty( $url ) ) {
					return '';
				}
				
				// Check if it's a valid WordPress attachment URL
				$attachment_id = attachment_url_to_postid( $url );
				if ( $attachment_id ) {
					// Verify it's an image attachment
					if ( wp_attachment_is_image( $attachment_id ) ) {
						return $url;
					}
				} elseif ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					// For external URLs, check if it has an image extension
					$path_info = pathinfo( wp_parse_url( $url, PHP_URL_PATH ) );
					$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp' );
					
					if ( isset( $path_info['extension'] ) && 
						 in_array( strtolower( $path_info['extension'] ), $image_extensions ) ) {
						return $url;
					}
				}
				
				return $default;
				
			default:
				// For custom field types, apply basic text sanitization
				if ( is_array( $value ) ) {
					return array_map( 'sanitize_text_field', $value );
				}
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Settings sections configuration
	 */
	public function get_settings_sections() {
		return array(
			'general' => array(
				'id'          => 'general',
				'title'       => esc_html__( 'General', 'dinopack-for-elementor' ),
				'description' => esc_html__( 'Configure basic DinoPack settings.', 'dinopack-for-elementor' ),
				'callback'    => null,
				'icon'        => 'dashicons-admin-generic',
				'fields'      => array(
					array(
						'type' => 'password',
						'id'   => 'dinopack_mailchimp_api_key',
						'name' => 'dinopack_mailchimp_api_key',
						'label' => esc_html__( 'MailChimp API Key', 'dinopack-for-elementor' ),
						/* translators: MailChimp API key description */
						'description' => sprintf( wp_kses_post( __( 'To use Newsletter widget enter your MailChimp API key. You can find it in your MailChimp account under <a target="_blank" href="%s">Account > Extras > API keys</a>.', 'dinopack-for-elementor' ) ), esc_url( 'https://mailchimp.com/help/about-api-keys/' ) ),
						'default' => '',
					),
				),
			),
			'tools' => array(
				'id' => 'tools',
				'title' => esc_html__( 'Tools', 'dinopack-for-elementor' ),
				'description' => esc_html__( 'Import, export, and manage your DinoPack settings with powerful backup and restore tools.', 'dinopack-for-elementor' ),
				'callback' => array( $this, 'render_tools_section' ),
				'icon' => 'dashicons-admin-settings',
				'fields' => array(),
			),
		);
	}

	/**
	 * Render a field based on its type
	 */
	private function render_field( $field ) {
		// Ensure field renderer is initialized
		if ( ! $this->field_renderer ) {
			$this->field_renderer = new DinoPack_Field_Renderer( $this );
		}
		
		$this->field_renderer->render_field( $field, self::OPTION_NAME );
	}

	/**
	 * Render tools section content
	 */
	private function render_tools_section() {
		?>
		<!-- Import/Export Tools -->
		<div class="wpdino-tools-grid">
			<div class="wpdino-tool-item">
				<div class="wpdino-tool-icon">
					<span class="dashicons dashicons-download"></span>
				</div>
				<div class="wpdino-tool-content">
					<h4><?php esc_html_e( 'Export Settings', 'dinopack-for-elementor' ); ?></h4>
					<p><?php esc_html_e( 'Download your current settings as a JSON file.', 'dinopack-for-elementor' ); ?></p>
					<button type="button" id="export-settings" class="wpdino-btn wpdino-btn-secondary">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Export Settings', 'dinopack-for-elementor' ); ?>
					</button>
				</div>
			</div>

			<div class="wpdino-tool-item">
				<div class="wpdino-tool-icon">
					<span class="dashicons dashicons-upload"></span>
				</div>
				<div class="wpdino-tool-content">
					<h4><?php esc_html_e( 'Import Settings', 'dinopack-for-elementor' ); ?></h4>
					<p><?php esc_html_e( 'Upload a settings file to restore your configuration.', 'dinopack-for-elementor' ); ?></p>
					<div class="wpdino-file-upload">
						<input type="file" id="import-file" accept=".json" style="display: none;" />
						<button type="button" id="import-settings" class="wpdino-btn wpdino-btn-secondary">
							<span class="dashicons dashicons-upload"></span>
							<?php esc_html_e( 'Choose File', 'dinopack-for-elementor' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- System Information -->
		<div class="wpdino-section">
			<h3 class="wpdino-section-title">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'System Information', 'dinopack-for-elementor' ); ?>
			</h3>
			<p class="wpdino-section-description"><?php esc_html_e( 'Copy this information when contacting support for faster troubleshooting.', 'dinopack-for-elementor' ); ?></p>
			
			<div class="wpdino-system-info">
				<div class="wpdino-system-info-header">
					<button type="button" id="copy-system-info" class="wpdino-btn wpdino-btn-secondary">
						<span class="dashicons dashicons-admin-page"></span>
						<?php esc_html_e( 'Copy System Info', 'dinopack-for-elementor' ); ?>
					</button>
				</div>
				<textarea id="system-info-content" class="wpdino-system-info-content" readonly><?php echo esc_textarea( $this->get_system_info() ); ?></textarea>
			</div>
		</div>
		<?php
	}

	/**
	 * Get system information for debugging
	 */
	private function get_system_info() {
		global $wpdb;
		
		// Include plugin functions if not already loaded
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$info = '### DinoPack System Info ###' . "\n\n";
		
		// WordPress & Server Info
		$info .= '--- WordPress & Server ---' . "\n";
		$info .= 'Site URL: ' . site_url() . "\n";
		$info .= 'Home URL: ' . home_url() . "\n";
		$info .= 'WordPress Version: ' . get_bloginfo( 'version' ) . "\n";
		$info .= 'PHP Version: ' . PHP_VERSION . "\n";
		$info .= 'MySQL Version: ' . $wpdb->db_version() . "\n";
		$info .= 'Server: ' . ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : 'Unknown' ) . "\n";
		$info .= 'WP_DEBUG: ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$info .= 'Memory Limit: ' . WP_MEMORY_LIMIT . "\n";
		$info .= 'Max Upload Size: ' . size_format( wp_max_upload_size() ) . "\n";
		$info .= 'Max Execution Time: ' . ini_get( 'max_execution_time' ) . ' seconds' . "\n";
		$info .= "\n";
		
		// Image Settings
		$info .= '--- Image Settings ---' . "\n";
		$info .= 'Thumbnail Size: ' . get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' ) . "\n";
		$info .= 'Medium Size: ' . get_option( 'medium_size_w' ) . 'x' . get_option( 'medium_size_h' ) . "\n";
		$info .= 'Large Size: ' . get_option( 'large_size_w' ) . 'x' . get_option( 'large_size_h' ) . "\n";
		$info .= "\n";
		
		// Plugin Info
		$info .= '--- Plugin Info ---' . "\n";
		$info .= 'DinoPack Version: ' . DINOPACK_VERSION . "\n"; 
		$info .= 'DinoPack URL: ' . DINOPACK_URL . "\n";
		$info .= 'DinoPack PRO: ' . ( defined( 'DINOPACK_PRO_VERSION' ) ? DINOPACK_PRO_VERSION : 'Not Installed' ) . "\n"; 
		$info .= "\n";
		
		// Theme Info
		$info .= '--- Active Theme ---' . "\n";
		$theme = wp_get_theme();
		$info .= 'Name: ' . $theme->get( 'Name' ) . "\n";
		$info .= 'Version: ' . $theme->get( 'Version' ) . "\n";
		$info .= 'Author: ' . $theme->get( 'Author' ) . "\n";
		$info .= "\n";
		
		// Active Plugins
		$info .= '--- Active Plugins ---' . "\n";
		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins' );
		
		foreach ( $plugins as $plugin_file => $plugin_data ) {
			if ( in_array( $plugin_file, $active_plugins ) ) {
				$info .= $plugin_data['Name'] . ' v' . $plugin_data['Version'] . ' (' . $plugin_file . ')' . "\n";
			}
		}
		$info .= "\n";
		
		// DinoPack Settings
		$info .= '--- DinoPack Settings ---' . "\n";
		$settings = self::get_all_settings();
		
		// Define password field IDs
		$password_fields = ['dinopack_mailchimp_api_key'];
		
		foreach ( $settings as $key => $value ) {
			// Mask password values
			if ( in_array( $key, $password_fields ) && ! empty( $value ) ) {
				$value = str_repeat( '*', strlen( $value ) );
			}
			
			if ( is_array( $value ) ) {
				$info .= $key . ': ' . implode( ', ', $value ) . "\n";
			} else {
				$info .= $key . ': ' . $value . "\n";
			}
		}
		
		return $info;
	}

	/**
	 * Settings page content
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wpdino-settings-wrap">
			
			<!-- Header -->
			<div class="wpdino-header">
				<div class="wpdino-header-content">					
					<h1>
						<?php echo esc_html( 'DinoPack', 'dinopack-for-elementor' ); ?>
						<small><?php esc_html_e('Lite', 'dinopack-for-elementor')?></small>
					</h1>
					<span class="wpdino-version"><?php esc_html_e('v', 'dinopack-for-elementor'); ?><?php echo esc_html( DINOPACK_VERSION ); ?></span>					
				</div>
			</div>

			<!-- Admin Notices -->
			<?php $this->render_admin_notices(); ?>

			<div class="wpdino-main">
				
				<!-- Settings Content -->
				<div class="wpdino-content">
					<div class="wpdino-card">
						
						<!-- Tab Navigation -->
						<div class="wpdino-tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Settings tabs', 'dinopack-for-elementor' ); ?>">
							<?php
							$sections = $this->get_settings_sections();
							$first_section = true;
							foreach ( $sections as $section_id => $section ) :
								$is_active = $first_section ? 'active' : '';
								$aria_selected = $first_section ? 'true' : 'false';
								$first_section = false;
							?>
							<button type="button" class="wpdino-tab-btn <?php echo esc_attr( $is_active ); ?>" data-tab="<?php echo esc_attr( $section_id ); ?>" role="tab" aria-selected="<?php echo esc_attr( $aria_selected ); ?>" aria-controls="tab-<?php echo esc_attr( $section_id ); ?>" id="tab-<?php echo esc_attr( $section_id ); ?>-btn">
								<span class="dashicons <?php echo esc_attr( $section['icon'] ); ?>"></span>
								<?php echo esc_html( $section['title'] ); ?>
							</button>
							<?php endforeach; ?>
						</div>
						
						<form method="post" action="" class="wpdino-form">
							<?php wp_nonce_field( 'wpdino_settings_save', 'wpdino_settings_nonce' ); ?>
							
							<?php
							$sections = $this->get_settings_sections();
							$first_section = true;
							foreach ( $sections as $section_id => $section ) :
								$is_active = $first_section ? 'active' : '';
								$first_section = false;
							?>
							<!-- <?php echo esc_html( $section['title'] ); ?> Settings Tab -->
							<div class="wpdino-tab-content <?php echo esc_attr( $is_active ); ?>" id="tab-<?php echo esc_attr( $section_id ); ?>" role="tabpanel" aria-labelledby="tab-<?php echo esc_attr( $section_id ); ?>-btn">
								
								<?php if ( ! empty( $section['title'] ) || ! empty( $section['description'] ) ) : ?>
								<!-- Section Header -->
								<div class="wpdino-section">
									<?php if ( ! empty( $section['title'] ) ) : ?>
									<h3 class="wpdino-section-title">
										<span class="dashicons <?php echo esc_attr( $section['icon'] ); ?>"></span>
										<?php echo esc_html( $section['title'] ); ?>
									</h3>
									<?php endif; ?>
									<?php if ( ! empty( $section['description'] ) ) : ?>
									<p class="wpdino-section-description"><?php echo wp_kses_post( $section['description'] ); ?></p>
									<?php endif; ?>
								</div>
								<?php endif; ?>
								
								<?php
								if ( ! empty( $section['fields'] ) ) {
									foreach ( $section['fields'] as $field ) {
										$this->render_field( $field );
									}
								}
								
								if ( ! empty( $section['callback'] ) && is_callable( $section['callback'] ) ) {
									call_user_func( $section['callback'] );
								}
								?>
								
							</div>
							<?php endforeach; ?>

							<!-- Form Footer - Always Visible -->
							<div class="wpdino-form-footer">
								<div class="wpdino-form-actions-left">
									<button type="submit" class="wpdino-btn wpdino-btn-primary">
										<span class="dashicons dashicons-yes"></span>
										<?php esc_html_e( 'Save Changes', 'dinopack-for-elementor' ); ?>
									</button>
								</div>
								<div class="wpdino-form-actions-right">
									<button type="button" id="reset-settings" class="wpdino-btn wpdino-btn-danger">
										<span class="dashicons dashicons-backup"></span>
										<?php esc_html_e( 'Reset to Defaults', 'dinopack-for-elementor' ); ?>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div class="wpdino-footer">
				<div class="wpdino-footer-content">
					<div class="wpdino-footer-left">
						<p>
							<?php 
							printf( 
								/* translators: DinoPack version and WPDINO link */
								esc_html__( 'DinoPack v%1$s by %2$s', 'dinopack-for-elementor' ), 
								esc_attr( DINOPACK_VERSION ),
								'<a href="https://wpdino.com" target="_blank">WPDINO</a>' 
							); ?>
						</p>
					</div>
					<!-- <div class="wpdino-footer-right">
						<a href="https://wpdino.com" target="_blank"><?php esc_html_e( 'WPDINO.com', 'dinopack-for-elementor' ); ?></a>
						<span>|</span>
						<a href="https://wpdino.com/docs/dinopack-for-elementor/" target="_blank"><?php esc_html_e( 'Documentation', 'dinopack-for-elementor' ); ?></a>
						<span>|</span>
						<a href="https://wpdino.com/support/" target="_blank"><?php esc_html_e( 'Support', 'dinopack-for-elementor' ); ?></a>
					</div> -->
				</div>
			</div>
		</div>
		<?php
	}
}
