<?php
/**
 * DinoPack Template Library Source – handles template import (get content from JSON).
 *
 * @package DinoPack
 * @since 1.0.6
 */

namespace Elementor\TemplateLibrary;

defined( 'ABSPATH' ) || exit;

if ( ! did_action( 'elementor/loaded' ) ) {
	return;
}

use Elementor\TemplateLibrary\Source_Base;

class DinoPack_Library_Source extends Source_Base {

	public function __construct() {
		parent::__construct();
		add_action( 'wp_ajax_dinopack_get_content_from_export_file', array( $this, 'get_finalized_data' ) );
	}

	public function get_id() {}
	public function get_title() {}
	public function register_data() {}
	public function get_items( $args = array() ) {}
	public function get_item( $template_id ) {}
	public function get_data( array $args ) {}
	public function delete_template( $template_id ) {}
	public function save_item( $template_data ) {}
	public function update_item( $new_data ) {}
	public function export_template( $template_id ) {}

	public function get_finalized_data() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'dinopack-for-elementor' ) ) );
		}
		if ( empty( $_POST['filename'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template filename is required.', 'dinopack-for-elementor' ) ) );
		}
		$filename = sanitize_text_field( wp_unslash( $_POST['filename'] ) );
		if ( ! preg_match( '/^[a-zA-Z0-9\-_]+\.json$/', $filename ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid template filename.', 'dinopack-for-elementor' ) ) );
		}
		$local_file = DINOPACK_PATH . 'inc/elementor/data/templates/json/' . $filename;
		$data       = null;
		if ( file_exists( $local_file ) ) {
			$raw  = file_get_contents( $local_file );
			$data = json_decode( $raw, true );
		}
		if ( empty( $data ) || ! isset( $data['content'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Template data could not be loaded.', 'dinopack-for-elementor' ) ) );
		}
		$content = $data['content'];
		$content = $this->process_export_import_content( $content, 'on_import' );
		$content = $this->replace_elements_ids( $content );
		wp_send_json( $content );
	}
}

new DinoPack_Library_Source();
