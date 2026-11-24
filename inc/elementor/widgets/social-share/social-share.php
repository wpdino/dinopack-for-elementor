<?php
/**
 * Social Share Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Share current page to social networks.
 */
class Social_Share extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-social-share',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
		wp_register_script(
			'dinopack-social-share',
			plugins_url( 'frontend.js', __FILE__ ),
			array( 'jquery' ),
			DINOPACK_VERSION,
			true
		);
	}

	public function get_name() {
		return 'dinopack-social-share';
	}

	public function get_title() {
		return esc_html__( 'Social Share', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor' );
	}

	public function get_keywords() {
		return array( 'share', 'social', 'facebook', 'twitter', 'linkedin', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-social-share' );
	}

	public function get_script_depends() {
		return array( 'dinopack-social-share' );
	}

	/**
	 * Available share networks.
	 *
	 * @return array
	 */
	private function get_networks() {
		return array(
			'facebook'  => array(
				'label' => esc_html__( 'Facebook', 'dinopack-for-elementor' ),
				'color' => '#1877f2',
			),
			'x'         => array(
				'label' => esc_html__( 'X (Twitter)', 'dinopack-for-elementor' ),
				'color' => '#000000',
			),
			'linkedin'  => array(
				'label' => esc_html__( 'LinkedIn', 'dinopack-for-elementor' ),
				'color' => '#0a66c2',
			),
			'whatsapp'  => array(
				'label' => esc_html__( 'WhatsApp', 'dinopack-for-elementor' ),
				'color' => '#25d366',
			),
			'pinterest' => array(
				'label' => esc_html__( 'Pinterest', 'dinopack-for-elementor' ),
				'color' => '#e60023',
			),
			'telegram'  => array(
				'label' => esc_html__( 'Telegram', 'dinopack-for-elementor' ),
				'color' => '#229ed9',
			),
			'reddit'    => array(
				'label' => esc_html__( 'Reddit', 'dinopack-for-elementor' ),
				'color' => '#ff4500',
			),
			'email'     => array(
				'label' => esc_html__( 'Email', 'dinopack-for-elementor' ),
				'color' => '#6F9C50',
			),
			'copy'      => array(
				'label' => esc_html__( 'Copy', 'dinopack-for-elementor' ),
				'color' => '#6F9C50',
			),
		);
	}

	/**
	 * Inline SVG icon markup for a network.
	 *
	 * @param string $network Network key.
	 * @return string
	 */
	private function get_network_icon( $network ) {
		$icons = array(
			'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H8v3h3v7h3v-7h3l1-3h-4V9c0-.6.4-1 1-1z"/></svg>',
			'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.2 3H21l-6.6 7.5L22 21h-6.2l-4.3-5.4L6 21H3.2l7-8L2 3h6.3l3.9 5L18.2 3zm-1.1 16.2h1.7L7 4.7H5.2l11.9 14.5z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.3 9.5H3.5V20h2.8V9.5zM4.9 4C4 4 3.2 4.8 3.2 5.7S4 7.4 4.9 7.4s1.7-.8 1.7-1.7S5.8 4 4.9 4zM20.5 20h-2.8v-5.6c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9V20H11V9.5h2.7v1.4h.1c.4-.7 1.3-1.5 2.8-1.5 3 0 3.5 2 3.5 4.5V20z"/></svg>',
			'whatsapp'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3a8.9 8.9 0 0 0-7.7 13.4L3 21l4.8-1.3A8.9 8.9 0 1 0 12 3zm0 16.2c-1.4 0-2.7-.4-3.8-1l-.3-.2-2.8.7.8-2.7-.2-.3a6.3 6.3 0 1 1 6.3 3.5zm3.5-4.7c-.2-.1-1.1-.5-1.3-.6s-.3-.1-.5.1-.5.6-.7.7-.2.2-.5.1a5.2 5.2 0 0 1-1.5-.9 5.7 5.7 0 0 1-1-1.3c-.1-.2 0-.3.1-.5l.4-.5c.1-.1.1-.2.2-.4s0-.3 0-.4-.5-1.2-.7-1.6-.4-.4-.5-.4h-.4c-.1 0-.4.1-.6.3s-.8.8-.8 1.9.8 2.2.9 2.3c.1.2 1.6 2.5 3.9 3.4.5.2 1 .4 1.3.5.6.2 1.1.1 1.5.1.5-.1 1.1-.5 1.3-.9.2-.5.2-.9.1-1-.1-.1-.2-.1-.4-.2z"/></svg>',
			'pinterest' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3C7.6 3 4.2 6.4 4.2 10.8c0 3.1 1.9 5.8 4.6 6.9-.1-.6-.1-1.5 0-2.2.2-.7 1.2-5 1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.4 1.8-2.4.9 0 1.3.6 1.3 1.4 0 .9-.5 2.1-.8 3.3-.2 1 .5 1.8 1.5 1.8 1.8 0 3-2.2 3-4.8 0-2-1.4-3.5-3.9-3.5-2.8 0-4.6 2.1-4.6 4.5 0 .8.3 1.6.6 2.1.1.1.1.2.1.3l-.2.9c0 .1-.1.2-.3.1-1.1-.5-1.6-1.8-1.6-3.3 0-2.5 2.1-5.4 6.2-5.4 3.3 0 5.5 2.4 5.5 5 0 3.4-1.9 5.9-4.7 5.9-.9 0-1.8-.5-2.1-1.1l-.6 2.2c-.2.8-.7 1.7-1.1 2.3.9.3 1.8.4 2.8.4 4.4 0 7.8-3.4 7.8-7.8C19.8 6.4 16.4 3 12 3z"/></svg>',
			'telegram'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.5 4.3 3.8 11c-1.2.5-1.2 1.2-.2 1.5l4.5 1.4 1.7 5.3c.2.6.4.8 1 .8.5 0 .7-.2 1-.6l2.5-2.4 5.2 3.8c1 .5 1.6.3 1.9-.9L22.9 5.5c.3-1.2-.4-1.7-1.4-1.2zM9.7 14.5l-.3 3.7 1.5-2.1 7.7-6.9c.3-.2 0-.4-.4-.2L9.7 14.5z"/></svg>',
			'reddit'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14.2 3.3c.1.6.4 1.1.9 1.4-1.7.2-3.2 1-4.1 2.1C9.5 6 7.7 5.6 6.2 6c-.8.2-1.4.8-1.6 1.6-.1.4-.1.8.1 1.2-.9.5-1.5 1.5-1.5 2.6 0 2.5 2.9 4.5 6.5 4.9-.2.6-.3 1.2-.3 1.8 0 .5.1 1 .3 1.4-.9.2-1.7.7-1.7 1.4 0 .8 1.5 1.4 3.4 1.4s3.4-.6 3.4-1.4c0-.7-.8-1.2-1.7-1.4.2-.4.3-.9.3-1.4 0-.6-.1-1.2-.3-1.8 3.6-.4 6.5-2.4 6.5-4.9 0-1.1-.6-2.1-1.5-2.6.2-.4.2-.8.1-1.2-.2-.8-.8-1.4-1.6-1.6-1.5-.4-3.3 0-4.8.8.7-.8 1.7-1.4 2.9-1.6.5-.4.8-1 .9-1.6l-1.8-.3zM8.8 12.2a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4zm6.4 0a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4zm-6.3 3.5c.4.8 1.6 1.4 3.1 1.4s2.7-.6 3.1-1.4H9z"/></svg>',
			'email'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 6h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1zm8 7L4.8 8.4h14.4L12 13zm0 1.6 7.5-4.8V17H4.5v-7.2L12 14.6z"/></svg>',
			'copy'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10 5h8a2 2 0 0 1 2 2v8h-2V7h-8V5zm-4 4h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2zm0 2v8h8v-8H6z"/></svg>',
		);

		return isset( $icons[ $network ] ) ? $icons[ $network ] : $icons['copy'];
	}

	protected function register_controls() {
		$networks = $this->get_networks();
		$network_options = array();
		foreach ( $networks as $key => $network ) {
			$network_options[ $key ] = $network['label'];
		}

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Share Buttons', 'dinopack-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'network',
			array(
				'label'   => esc_html__( 'Network', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => $network_options,
			)
		);

		$repeater->add_control(
			'custom_label',
			array(
				'label'       => esc_html__( 'Custom Label', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Leave empty for default', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'share_buttons',
			array(
				'label'       => esc_html__( 'Buttons', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'network' => 'facebook' ),
					array( 'network' => 'x' ),
					array( 'network' => 'linkedin' ),
					array( 'network' => 'whatsapp' ),
					array( 'network' => 'copy' ),
				),
				'title_field' => '{{{ network }}}',
			)
		);

		$this->add_control(
			'copy_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div style="font-size:12px;line-height:1.5;color:#646970;"><strong>' . esc_html__( 'Copy button:', 'dinopack-for-elementor' ) . '</strong> ' . esc_html__( 'When a visitor clicks Copy, the current page URL is copied to their clipboard (the link of the page they are viewing). Social buttons open a share dialog for that same page.', 'dinopack-for-elementor' ) . '</div>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => esc_html__( 'View', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'      => esc_html__( 'Icon', 'dinopack-for-elementor' ),
					'icon-text' => esc_html__( 'Icon + Text', 'dinopack-for-elementor' ),
					'text'      => esc_html__( 'Text', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'skin',
			array(
				'label'   => esc_html__( 'Skin', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'brand',
				'options' => array(
					'brand'   => esc_html__( 'Official Colors', 'dinopack-for-elementor' ),
					'flat'    => esc_html__( 'Flat', 'dinopack-for-elementor' ),
					'outline' => esc_html__( 'Outline', 'dinopack-for-elementor' ),
					'minimal' => esc_html__( 'Minimal', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'   => esc_html__( 'Shape', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'square'  => esc_html__( 'Square', 'dinopack-for-elementor' ),
					'rounded' => esc_html__( 'Rounded', 'dinopack-for-elementor' ),
					'circle'  => esc_html__( 'Circle', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-social-share' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'copy_success_text',
			array(
				'label'       => esc_html__( 'Copy Success Text', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Copied!', 'dinopack-for-elementor' ),
				'description' => esc_html__( 'Shown briefly after the page URL is copied to the clipboard.', 'dinopack-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Buttons', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => esc_html__( 'Size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 28,
						'max' => 72,
					),
				),
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-share-btn' => '--dinopack-share-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 36,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-share-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-social-share' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'label_typography',
				'selector'  => '{{WRAPPER}} .dinopack-share-label',
				'condition' => array(
					'view!' => 'icon',
				),
			)
		);

		$this->add_control(
			'custom_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-social-share.skin-flat .dinopack-share-btn' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .dinopack-social-share.skin-outline .dinopack-share-btn' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .dinopack-social-share.skin-minimal .dinopack-share-btn' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin!' => 'brand',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .dinopack-share-btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build share URL for a network.
	 *
	 * @param string $network Network key.
	 * @param string $url     Page URL.
	 * @param string $title   Page title.
	 * @return string
	 */
	private function get_share_url( $network, $url, $title ) {
		$encoded_url   = rawurlencode( $url );
		$encoded_title = rawurlencode( $title );

		switch ( $network ) {
			case 'facebook':
				return 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
			case 'x':
				return 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
			case 'linkedin':
				return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
			case 'whatsapp':
				return 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;
			case 'pinterest':
				return 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&description=' . $encoded_title;
			case 'telegram':
				return 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
			case 'reddit':
				return 'https://reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title;
			case 'email':
				return 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url;
			default:
				return '';
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['share_buttons'] ) ) {
			return;
		}

		$networks = $this->get_networks();
		$view     = ! empty( $settings['view'] ) ? $settings['view'] : 'icon';
		$skin     = ! empty( $settings['skin'] ) ? $settings['skin'] : 'brand';
		$shape    = ! empty( $settings['shape'] ) ? $settings['shape'] : 'rounded';
		$show_label = ( 'icon' !== $view );

		$url   = get_permalink();
		$title = get_the_title();

		if ( empty( $url ) ) {
			$url = home_url( '/' );
		}

		$classes = array(
			'dinopack-social-share',
			'skin-' . sanitize_html_class( $skin ),
			'shape-' . sanitize_html_class( $shape ),
			'view-' . sanitize_html_class( $view ),
		);

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'             => $classes,
				'role'              => 'group',
				'aria-label'        => esc_attr__( 'Share this page', 'dinopack-for-elementor' ),
				'data-copy-success' => ! empty( $settings['copy_success_text'] ) ? $settings['copy_success_text'] : esc_html__( 'Copied!', 'dinopack-for-elementor' ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			foreach ( $settings['share_buttons'] as $index => $item ) :
				$network_key = isset( $item['network'] ) ? $item['network'] : '';
				if ( empty( $network_key ) || ! isset( $networks[ $network_key ] ) ) {
					continue;
				}

				$network = $networks[ $network_key ];
				$label   = ! empty( $item['custom_label'] ) ? $item['custom_label'] : $network['label'];
				$btn_key = 'share_btn_' . $index;

				$aria_label = sprintf(
					/* translators: %s: network name */
					esc_attr__( 'Share on %s', 'dinopack-for-elementor' ),
					$network['label']
				);

				if ( 'copy' === $network_key ) {
					$aria_label = esc_attr__( 'Copy page URL to clipboard', 'dinopack-for-elementor' );
				}

				$this->add_render_attribute(
					$btn_key,
					array(
						'class'      => array(
							'dinopack-share-btn',
							'dinopack-share-' . sanitize_html_class( $network_key ),
						),
						'aria-label' => $aria_label,
					)
				);

				if ( 'brand' === $skin ) {
					$this->add_render_attribute( $btn_key, 'style', '--dinopack-share-brand: ' . esc_attr( $network['color'] ) . ';' );
				}

				if ( 'copy' === $network_key ) {
					$this->add_render_attribute(
						$btn_key,
						array(
							'href'           => esc_url( $url ),
							'role'           => 'button',
							'title'          => esc_attr__( 'Copy this page URL to your clipboard', 'dinopack-for-elementor' ),
							'data-share'     => 'copy',
							'data-share-url' => esc_url( $url ),
						)
					);
				} else {
					$share_url = $this->get_share_url( $network_key, $url, $title );
					$this->add_render_attribute(
						$btn_key,
						array(
							'href'   => esc_url( $share_url ),
							'target' => ( 'email' === $network_key ) ? '_self' : '_blank',
							'rel'    => 'noopener noreferrer',
						)
					);
				}
				?>
				<a <?php $this->print_render_attribute_string( $btn_key ); ?>>
					<?php if ( 'text' !== $view ) : ?>
						<span class="dinopack-share-icon" aria-hidden="true">
							<?php echo $this->get_network_icon( $network_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG markup. ?>
						</span>
					<?php endif; ?>
					<?php if ( $show_label ) : ?>
						<span class="dinopack-share-label"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
