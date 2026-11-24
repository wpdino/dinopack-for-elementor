<?php
/**
 * Text Marquee Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Infinite scrolling text ticker for announcements and highlights.
 */
class Text_Marquee extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-text-marquee',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
	}

	public function get_name() {
		return 'dinopack-text-marquee';
	}

	public function get_title() {
		return esc_html__( 'Text Marquee', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor' );
	}

	public function get_keywords() {
		return array( 'marquee', 'ticker', 'scroll', 'announcement', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-text-marquee' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Marquee', 'dinopack-for-elementor' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'item_text',
			array(
				'label'   => esc_html__( 'Text', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Trending design for modern websites', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_text' => esc_html__( 'Launch your next project faster', 'dinopack-for-elementor' ),
					),
					array(
						'item_text' => esc_html__( 'Built for Elementor creators', 'dinopack-for-elementor' ),
					),
					array(
						'item_text' => esc_html__( 'Customize every detail', 'dinopack-for-elementor' ),
					),
				),
				'title_field' => '{{{ item_text }}}',
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => esc_html__( 'Direction', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'dinopack-for-elementor' ),
					'right' => esc_html__( 'Right', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Speed (seconds)', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 5,
				'max'     => 120,
				'step'    => 1,
				'default' => 24,
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause on Hover', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-marquee-item' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .dinopack-marquee-item',
			)
		);

		$this->add_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'Item Gap', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-marquee-track' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'   => esc_html__( 'Separator', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '•',
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => esc_html__( 'Separator Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a8960',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-marquee-separator' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .dinopack-text-marquee' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-text-marquee' => 'padding: {{SIZE}}{{UNIT}} 0;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['items'] ) ) {
			return;
		}

		$direction = ( 'right' === $settings['direction'] ) ? 'right' : 'left';
		$speed     = max( 5, (int) $settings['speed'] );
		$separator = isset( $settings['separator'] ) ? $settings['separator'] : '•';

		$this->add_render_attribute(
			'marquee',
			array(
				'class' => array(
					'dinopack-text-marquee',
					'dinopack-text-marquee--' . $direction,
					( 'yes' === $settings['pause_on_hover'] ) ? 'dinopack-text-marquee--pause-hover' : '',
				),
				'style' => '--dinopack-marquee-duration: ' . $speed . 's;',
			)
		);

		$items_markup = '';
		foreach ( $settings['items'] as $index => $item ) {
			if ( empty( $item['item_text'] ) ) {
				continue;
			}
			if ( $items_markup ) {
				$items_markup .= '<span class="dinopack-marquee-separator" aria-hidden="true">' . esc_html( $separator ) . '</span>';
			}
			$items_markup .= '<span class="dinopack-marquee-item">' . esc_html( $item['item_text'] ) . '</span>';
		}

		if ( '' === $items_markup ) {
			return;
		}
		?>
		<div <?php $this->print_render_attribute_string( 'marquee' ); ?>>
			<div class="dinopack-marquee-viewport">
				<div class="dinopack-marquee-track" aria-hidden="true">
					<?php echo $items_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>
					<?php echo $items_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- duplicate for seamless loop. ?>
				</div>
			</div>
		</div>
		<?php
	}
}
