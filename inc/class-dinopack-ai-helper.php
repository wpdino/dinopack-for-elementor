<?php
/**
 * AI Helper Class for DinoPack
 *
 * Handles AI API calls for various widgets.
 *
 * @package DinoPack
 * @since 1.0.0
 */

namespace DinoPack;

defined('ABSPATH') || exit;

/**
 * AI Helper Class
 */
class AI_Helper {

	/**
	 * Get OpenAI API key from settings.
	 *
	 * @return string API key.
	 */
	public static function get_api_key() {
		if ( ! class_exists( '\DinoPack\DinoPack_Settings' ) ) {
			return '';
		}

		$settings = DinoPack_Settings::get_all_settings();
		return isset( $settings['openai_api_key'] ) ? $settings['openai_api_key'] : '';
	}

	/**
	 * Get AI model from settings.
	 *
	 * @return string Model name.
	 */
	public static function get_model() {
		if ( ! class_exists( '\DinoPack\DinoPack_Settings' ) ) {
			return 'gpt-3.5-turbo';
		}

		$settings = DinoPack_Settings::get_all_settings();
		$model = isset( $settings['ai_model'] ) ? $settings['ai_model'] : 'gpt-3.5-turbo';
		
		// Fallback to gpt-3.5-turbo if an invalid model is selected
		$valid_models = array( 'gpt-3.5-turbo', 'gpt-4', 'gpt-4-turbo-preview', 'gpt-4-0125-preview' );
		if ( ! in_array( $model, $valid_models, true ) ) {
			$model = 'gpt-3.5-turbo';
		}
		
		return $model;
	}

	/**
	 * Get AI temperature from settings.
	 *
	 * @return float Temperature value.
	 */
	public static function get_temperature() {
		if ( ! class_exists( '\DinoPack\DinoPack_Settings' ) ) {
			return 0.7;
		}

		$settings = DinoPack_Settings::get_all_settings();
		return isset( $settings['ai_temperature'] ) ? floatval( $settings['ai_temperature'] ) : 0.7;
	}

	/**
	 * Make OpenAI API request.
	 *
	 * @param string $prompt The prompt to send.
	 * @param array  $options Additional options.
	 * @return array|WP_Error Response data or error.
	 */
	public static function make_request( $prompt, $options = array() ) {
		$api_key = self::get_api_key();
		
		if ( empty( $api_key ) ) {
			return new \WP_Error( 'no_api_key', esc_html__( 'OpenAI API key is not configured. Please add it in DinoPack settings.', 'dinopack-for-elementor' ) );
		}

		$model = isset( $options['model'] ) ? $options['model'] : self::get_model();
		$temperature = isset( $options['temperature'] ) ? $options['temperature'] : self::get_temperature();
		$max_tokens = isset( $options['max_tokens'] ) ? $options['max_tokens'] : 1000;

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 60,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'body' => wp_json_encode(
					array(
						'model'       => $model,
						'messages'    => array(
							array(
								'role'    => 'user',
								'content' => $prompt,
							),
						),
						'temperature' => $temperature,
						'max_tokens'  => $max_tokens,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			return new \WP_Error( 'api_error', $data['error']['message'] );
		}

		if ( isset( $data['choices'][0]['message']['content'] ) ) {
			$content = $data['choices'][0]['message']['content'];
			
			// Ensure content has proper HTML paragraph formatting
			// If content doesn't have HTML tags, convert line breaks to paragraphs
			if ( $content && ! preg_match( '/<p>|<h[1-6]>/i', $content ) ) {
				// Split by double line breaks and wrap in paragraphs
				$paragraphs = preg_split( '/\n\s*\n/', trim( $content ) );
				$formatted_content = '';
				foreach ( $paragraphs as $paragraph ) {
					$paragraph = trim( $paragraph );
					if ( ! empty( $paragraph ) ) {
						// Check if it's a heading (starts with #)
						if ( preg_match( '/^#+\s+(.+)$/', $paragraph, $matches ) ) {
							$level = strlen( $paragraph ) - strlen( ltrim( $paragraph, '#' ) );
							$level = min( $level, 6 );
							$heading_text = trim( $matches[1] );
							$formatted_content .= '<h' . $level . '>' . $heading_text . '</h' . $level . '>';
						} else {
							// Regular paragraph - preserve line breaks but don't double-encode
							$paragraph = wp_kses_post( $paragraph );
							$paragraph = preg_replace( '/\n/', '<br>', $paragraph );
							$formatted_content .= '<p>' . $paragraph . '</p>';
						}
					}
				}
				$content = $formatted_content;
			}
			
			// Clean up unnecessary &nbsp; entities
			$content = preg_replace( '/&nbsp;(?=\s|$)/', ' ', $content );
			$content = preg_replace( '/\s+&nbsp;/', ' ', $content );
			$content = preg_replace( '/&nbsp;&nbsp;/', ' ', $content );
			
			return array(
				'content' => $content,
				'usage'   => isset( $data['usage'] ) ? $data['usage'] : array(),
			);
		}

		return new \WP_Error( 'invalid_response', esc_html__( 'Invalid response from OpenAI API.', 'dinopack-for-elementor' ) );
	}

	/**
	 * Generate image using DALL-E.
	 *
	 * @param string $prompt Image prompt.
	 * @param string $size Image size.
	 * @param string $model DALL-E model (dall-e-2 or dall-e-3).
	 * @return array|WP_Error Image URL or error.
	 */
	public static function generate_image( $prompt, $size = '1024x1024', $model = 'dall-e-3' ) {
		$api_key = self::get_api_key();
		
		if ( empty( $api_key ) ) {
			return new \WP_Error( 'no_api_key', esc_html__( 'OpenAI API key is not configured.', 'dinopack-for-elementor' ) );
		}

		// Validate model
		$valid_models = array( 'dall-e-2', 'dall-e-3' );
		if ( ! in_array( $model, $valid_models, true ) ) {
			$model = 'dall-e-3';
		}

		// Build request body
		$request_body = array(
			'prompt' => $prompt,
			'size'   => $size,
			'n'      => 1,
		);

		// DALL-E 3 requires model parameter and has quality/style options
		if ( 'dall-e-3' === $model ) {
			$request_body['model'] = 'dall-e-3';
			$request_body['quality'] = 'standard'; // 'standard' or 'hd'
			$request_body['style'] = 'natural'; // 'natural' or 'vivid'
		}

		$response = wp_remote_post(
			'https://api.openai.com/v1/images/generations',
			array(
				'timeout' => 60,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'body' => wp_json_encode( $request_body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			return new \WP_Error( 'api_error', $data['error']['message'] );
		}

		if ( isset( $data['data'][0]['url'] ) ) {
			return array(
				'url' => $data['data'][0]['url'],
			);
		}

		return new \WP_Error( 'invalid_response', esc_html__( 'Invalid response from OpenAI API.', 'dinopack-for-elementor' ) );
	}
}


