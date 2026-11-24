<?php
/**
 * Counter Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

/**
 * Animated number counter for stats and highlights.
 */
class Counter extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-counter',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
		wp_register_script(
			'dinopack-counter',
			plugins_url( 'frontend.js', __FILE__ ),
			array( 'jquery' ),
			DINOPACK_VERSION,
			true
		);
	}

	public function get_name() {
		return 'dinopack-counter';
	}

	public function get_title() {
		return esc_html__( 'Counter', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-counter';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor' );
	}

	public function get_keywords() {
		return array( 'counter', 'number', 'stats', 'animate', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-counter' );
	}

	public function get_script_depends() {
		return array( 'dinopack-counter' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Counter', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'starting_number',
			array(
				'label'   => esc_html__( 'Starting Number', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'ending_number',
			array(
				'label'   => esc_html__( 'Ending Number', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 250,
			)
		);

		$this->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Prefix', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'suffix',
			array(
				'label'   => esc_html__( 'Suffix', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Happy Clients', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
				'rows'    => 3,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'dinopack-for-elementor' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'   => esc_html__( 'Animation Duration (ms)', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 300,
				'max'     => 10000,
				'step'    => 100,
				'default' => 2000,
			)
		);

		$this->add_control(
			'thousand_separator',
			array(
				'label'        => esc_html__( 'Thousand Separator', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stacked',
				'options' => array(
					'stacked'    => esc_html__( 'Stacked', 'dinopack-for-elementor' ),
					'inline'     => esc_html__( 'Inline', 'dinopack-for-elementor' ),
					'icon-left'  => esc_html__( 'Icon Left', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'dinopack-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-counter' => 'text-align: {{VALUE}};',
				),
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
			'number_color',
			array(
				'label'     => esc_html__( 'Number Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a8960',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-counter-number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'label'    => esc_html__( 'Number Typography', 'dinopack-for-elementor' ),
				'selector' => '{{WRAPPER}} .dinopack-counter-number',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-counter-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'dinopack-for-elementor' ),
				'selector' => '{{WRAPPER}} .dinopack-counter-title',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a8960',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-counter-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 32,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-counter-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dinopack-counter-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = ! empty( $settings['layout'] ) ? $settings['layout'] : 'stacked';

		$this->add_render_attribute(
			'counter',
			array(
				'class' => array(
					'dinopack-counter',
					'dinopack-counter--' . $layout,
				),
				'data-start'       => (float) $settings['starting_number'],
				'data-end'         => (float) $settings['ending_number'],
				'data-duration'    => (int) $settings['duration'],
				'data-separator'   => ( 'yes' === $settings['thousand_separator'] ) ? '1' : '0',
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'counter' ); ?>>
			<?php if ( ! empty( $settings['icon']['value'] ) ) : ?>
				<div class="dinopack-counter-icon">
					<?php Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="dinopack-counter-content">
				<div class="dinopack-counter-number-wrap">
					<?php if ( ! empty( $settings['prefix'] ) ) : ?>
						<span class="dinopack-counter-prefix"><?php echo esc_html( $settings['prefix'] ); ?></span>
					<?php endif; ?>
					<span class="dinopack-counter-number" data-value="<?php echo esc_attr( (float) $settings['starting_number'] ); ?>"><?php echo esc_html( (string) (float) $settings['starting_number'] ); ?></span>
					<?php if ( ! empty( $settings['suffix'] ) ) : ?>
						<span class="dinopack-counter-suffix"><?php echo esc_html( $settings['suffix'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<div class="dinopack-counter-title"><?php echo esc_html( $settings['title'] ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<div class="dinopack-counter-description"><?php echo esc_html( $settings['description'] ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
