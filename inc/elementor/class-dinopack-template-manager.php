<?php
/**
 * DinoPack Template Manager – AJAX for template library view and preview.
 *
 * @package DinoPack
 * @since 1.0.6
 */

namespace DinoPack;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'DinoPack_Template_Manager' ) ) {

	class DinoPack_Template_Manager {

		private static $_instance = null;
		static $library_source = null;

		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		private function __construct() {
			self::$library_source = 'https://api.wpdino.com/elementor/templates/';
			add_action( 'wp_ajax_dinopack_get_templates_library_view', array( $this, 'get_templates_library_view' ) );
			add_action( 'wp_ajax_dinopack_get_preview', array( $this, 'ajax_get_preview' ) );
		}

		public function get_templates_library_view() {
			$thumb_url    = self::$library_source . 'assets/thumbs/';
			$local_file   = DINOPACK_PATH . 'inc/elementor/data/templates/json/info.json';
			$template_list = array();

			$response = wp_remote_get( self::$library_source . 'info.json', array( 'timeout' => 60 ) );
			if ( ! is_wp_error( $response ) ) {
				$body          = wp_remote_retrieve_body( $response );
				$template_list = json_decode( $body, true );
			}
			if ( empty( $template_list ) && file_exists( $local_file ) ) {
				$data          = file_get_contents( $local_file );
				$template_list = json_decode( $data, true );
				$thumb_url     = DINOPACK_URL . 'inc/elementor/data/templates/thumbs/';
			}
			if ( ! is_array( $template_list ) ) {
				$template_list = array();
			}

			echo '<div class="dinopack-main-tiled-view">';
			if ( count( $template_list ) > 0 ) {
				foreach ( $template_list as $i => $item ) {
					$slug       = isset( $item['id'] ) ? strtolower( str_replace( ' ', '-', $item['id'] ) ) : 'template-' . $i;
					$theme      = isset( $item['theme'] ) ? $item['theme'] : '';
					$cat        = isset( $item['category'] ) ? $item['category'] : '';
					$name       = isset( $item['name'] ) ? $item['name'] : $slug;
					$thumb      = isset( $item['thumbnail'] ) ? $item['thumbnail'] : $slug;
					$sep        = isset( $item['separator'] ) ? $item['separator'] : '';
					$theme_slug = strtolower( str_replace( ' ', '-', $theme ) );
					$cat_slug   = strtolower( str_replace( ' ', '-', $cat ) );

					if ( $sep !== '' ) {
						echo '<h2 class="dinopack-templates-library-template-category" data-theme="' . esc_attr( $theme_slug ) . '" data-category="' . esc_attr( $cat_slug ) . '">' . esc_html( $sep ) . '</h2>';
					}
					?>
					<div class="dinopack-templates-library-template dinopack-item" data-theme="<?php echo esc_attr( $theme_slug ); ?>" data-category="<?php echo esc_attr( $cat_slug ); ?>">
						<div class="dinopack-template-title"><?php echo esc_html( $name ); ?></div>
						<div class="dinopack-template-thumb dinopack-index-<?php echo esc_attr( $i ); ?>" data-index="<?php echo esc_attr( $i ); ?>" data-template="<?php echo esc_attr( wp_json_encode( $item ) ); ?>">
							<img src="<?php echo esc_url( $thumb_url . $thumb . '-thumb.png' ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="dinopack-thumb-image" onerror="this.style.display='none'; this.parentNode.style.background='#f0f0f0';">
						</div>
						<div class="dinopack-action-bar">
							<div class="dinopack-grow"></div>
							<div class="dinopack-btn-template-insert" data-template-name="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Insert Template', 'dinopack-for-elementor' ); ?></div>
						</div>
					</div>
					<?php
				}
			} else {
				echo '<div class="dinopack-no-results">' . esc_html__( 'No templates found.', 'dinopack-for-elementor' ) . '</div>';
			}
			echo '</div>';
			wp_die();
		}

		public function ajax_get_preview() {
			if ( ! isset( $_POST['data'] ) || ! is_array( $_POST['data'] ) ) {
				wp_die();
			}
			$this->render_preview_template( wp_unslash( $_POST['data'] ) );
			wp_die();
		}

		private function render_preview_template( $data ) {
			$thumb = isset( $data['thumbnail'] ) ? $data['thumbnail'] : '';
			$name  = isset( $data['name'] ) ? $data['name'] : '';
			if ( ! empty( $thumb ) && wp_http_validate_url( $thumb ) ) {
				$thumb_url = $thumb;
			} else {
				$thumb_url = self::$library_source . 'assets/thumbs/' . $thumb;
			}
			?>
			<div id="dinopack-elementor-template-library-preview">
				<img src="<?php echo esc_url( $thumb_url . ( strpos( $thumb_url, '.png' ) !== false ? '' : '-full.png' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>">
			</div>
			<?php
		}
	}

	DinoPack_Template_Manager::init();
	require_once DINOPACK_PATH . 'inc/elementor/class-dinopack-template-library.php';
}
