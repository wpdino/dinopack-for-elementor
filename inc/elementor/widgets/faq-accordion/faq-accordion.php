<?php
/**
 * FAQ Accordion Widget
 *
 * @package DinoPack
 */

namespace Dinopack\Widgets;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

/**
 * Manual FAQ accordion for landing pages and support sections.
 */
class Faq_Accordion extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			'dinopack-faq-accordion',
			plugins_url( 'frontend.css', __FILE__ ),
			array(),
			DINOPACK_VERSION
		);
		wp_register_script(
			'dinopack-faq-accordion',
			plugins_url( 'frontend.js', __FILE__ ),
			array( 'jquery' ),
			DINOPACK_VERSION,
			true
		);
	}

	public function get_name() {
		return 'dinopack-faq-accordion';
	}

	public function get_title() {
		return esc_html__( 'FAQ Accordion', 'dinopack-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return array( 'dinopack-for-elementor' );
	}

	public function get_keywords() {
		return array( 'faq', 'accordion', 'toggle', 'questions', 'dinopack' );
	}

	public function get_style_depends() {
		return array( 'dinopack-faq-accordion' );
	}

	public function get_script_depends() {
		return array( 'dinopack-faq-accordion' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'FAQ Items', 'dinopack-for-elementor' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'question',
			array(
				'label'   => esc_html__( 'Question', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'How do I get started?', 'dinopack-for-elementor' ),
			)
		);

		$repeater->add_control(
			'answer',
			array(
				'label'   => esc_html__( 'Answer', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Add your answer here. You can include links, lists, and basic formatting.', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'faq_items',
			array(
				'label'       => esc_html__( 'Items', 'dinopack-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'question' => esc_html__( 'How do I get started?', 'dinopack-for-elementor' ),
						'answer'   => esc_html__( 'Install the plugin, open Elementor, and drag the widget onto your page.', 'dinopack-for-elementor' ),
					),
					array(
						'question' => esc_html__( 'Can I customize the styles?', 'dinopack-for-elementor' ),
						'answer'   => esc_html__( 'Yes. Use the Style tab to change colors, typography, spacing, and borders.', 'dinopack-for-elementor' ),
					),
				),
				'title_field' => '{{{ question }}}',
			)
		);

		$this->add_control(
			'allow_multiple',
			array(
				'label'        => esc_html__( 'Allow Multiple Open', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'open_first',
			array(
				'label'        => esc_html__( 'Open First Item', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Toggle Icon', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => array(
					'before' => esc_html__( 'Before Question', 'dinopack-for-elementor' ),
					'after'  => esc_html__( 'After Question', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'panel_animation',
			array(
				'label'   => esc_html__( 'Open Animation', 'dinopack-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide'       => esc_html__( 'Slide', 'dinopack-for-elementor' ),
					'fade'        => esc_html__( 'Fade', 'dinopack-for-elementor' ),
					'slide-fade'  => esc_html__( 'Slide + Fade', 'dinopack-for-elementor' ),
					'slide-down'  => esc_html__( 'Slide Down', 'dinopack-for-elementor' ),
					'scale'       => esc_html__( 'Scale', 'dinopack-for-elementor' ),
					'none'        => esc_html__( 'None', 'dinopack-for-elementor' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_item',
			array(
				'label' => esc_html__( 'Item', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_gap',
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
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-faq-accordion' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_bg',
			array(
				'label'     => esc_html__( 'Background', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-item' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-faq-item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typography',
				'label'    => esc_html__( 'Question Typography', 'dinopack-for-elementor' ),
				'selector' => '{{WRAPPER}} .dinopack-faq-question',
			)
		);

		$this->add_control(
			'question_color',
			array(
				'label'     => esc_html__( 'Question Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-question' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'answer_typography',
				'label'    => esc_html__( 'Answer Typography', 'dinopack-for-elementor' ),
				'selector' => '{{WRAPPER}} .dinopack-faq-answer',
			)
		);

		$this->add_control(
			'answer_color',
			array(
				'label'     => esc_html__( 'Answer Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-answer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => esc_html__( 'Toggle Icon', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => esc_html__( 'Normal', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-accordion' => '--dinopack-faq-accent: {{VALUE}};',
					'{{WRAPPER}} .dinopack-faq-icon, {{WRAPPER}} .dinopack-faq-icon i, {{WRAPPER}} .dinopack-faq-icon-svg' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_active',
			array(
				'label' => esc_html__( 'Active', 'dinopack-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_active_color',
			array(
				'label'     => esc_html__( 'Color', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6F9C50',
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-item.is-active .dinopack-faq-icon, {{WRAPPER}} .dinopack-faq-item.is-active .dinopack-faq-icon i, {{WRAPPER}} .dinopack-faq-item.is-active .dinopack-faq-icon-svg' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Size', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 48,
					),
				),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-faq-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dinopack-faq-icon svg, {{WRAPPER}} .dinopack-faq-icon-svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'dinopack-for-elementor' ),
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
					'{{WRAPPER}} .dinopack-faq-question' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_rotate',
			array(
				'label'        => esc_html__( 'Rotate When Open', 'dinopack-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'dinopack-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'dinopack-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			array(
				'label' => esc_html__( 'Animation', 'dinopack-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'animation_duration',
			array(
				'label'      => esc_html__( 'Duration', 'dinopack-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'ms', 's' ),
				'range'      => array(
					'ms' => array(
						'min'  => 100,
						'max'  => 2000,
						'step' => 50,
					),
					's'  => array(
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.05,
					),
				),
				'default'    => array(
					'size' => 350,
					'unit' => 'ms',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dinopack-faq-accordion' => '--dinopack-faq-anim-duration: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'panel_animation!' => 'none',
				),
			)
		);

		$this->add_control(
			'animation_easing',
			array(
				'label'     => esc_html__( 'Easing', 'dinopack-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ease-out',
				'options'   => array(
					'ease'        => esc_html__( 'Ease', 'dinopack-for-elementor' ),
					'ease-in'     => esc_html__( 'Ease In', 'dinopack-for-elementor' ),
					'ease-out'    => esc_html__( 'Ease Out', 'dinopack-for-elementor' ),
					'ease-in-out' => esc_html__( 'Ease In Out', 'dinopack-for-elementor' ),
					'linear'      => esc_html__( 'Linear', 'dinopack-for-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .dinopack-faq-accordion' => '--dinopack-faq-anim-easing: {{VALUE}};',
				),
				'condition' => array(
					'panel_animation!' => 'none',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a reliable default chevron when icon fonts are unavailable.
	 */
	private function render_default_toggle_icon() {
		?>
		<svg class="dinopack-faq-icon-svg" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M3.5 5.25L7 8.75L10.5 5.25" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php
	}

	/**
	 * Render toggle icon with Elementor picker support and SVG fallback.
	 *
	 * @param array $icon_settings Icon control value.
	 */
	private function render_toggle_icon( array $icon_settings ) {
		if ( empty( $icon_settings['value'] ) ) {
			$this->render_default_toggle_icon();
			return;
		}

		if ( method_exists( Icons_Manager::class, 'enqueue_icon_styles' ) ) {
			Icons_Manager::enqueue_icon_styles( $icon_settings );
		}

		ob_start();
		Icons_Manager::render_icon( $icon_settings, array( 'aria-hidden' => 'true' ) );
		$icon_markup = trim( (string) ob_get_clean() );

		if ( '' === $icon_markup ) {
			$this->render_default_toggle_icon();
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor icon markup.
		echo $icon_markup;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['faq_items'] ) ) {
			return;
		}

		$allow_multiple = ( 'yes' === $settings['allow_multiple'] );
		$open_first     = ( 'yes' === $settings['open_first'] );
		$icon_position  = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'after';
		$panel_animation = ! empty( $settings['panel_animation'] ) ? $settings['panel_animation'] : 'slide';
		$icon_rotate    = ( 'yes' === $settings['icon_rotate'] );

		$accordion_classes = array(
			'dinopack-faq-accordion',
			'dinopack-faq-icon-' . $icon_position,
			'dinopack-faq-anim-' . $panel_animation,
		);

		if ( ! $icon_rotate ) {
			$accordion_classes[] = 'dinopack-faq-no-icon-rotate';
		}

		$this->add_render_attribute(
			'accordion',
			array(
				'class'               => $accordion_classes,
				'data-allow-multiple' => $allow_multiple ? '1' : '0',
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'accordion' ); ?>>
			<?php
			foreach ( $settings['faq_items'] as $index => $item ) :
				$is_open     = $open_first && 0 === $index;
				$item_key    = 'faq_item_' . $index;
				$button_key  = 'faq_button_' . $index;
				$panel_key   = 'faq_panel_' . $index;
				$question_id = 'dinopack-faq-q-' . $this->get_id() . '-' . $index;
				$answer_id   = 'dinopack-faq-a-' . $this->get_id() . '-' . $index;

				$this->add_render_attribute(
					$item_key,
					array(
						'class' => array(
							'dinopack-faq-item',
							$is_open ? 'is-active' : '',
						),
					)
				);

				$this->add_render_attribute(
					$button_key,
					array(
						'class'         => 'dinopack-faq-question',
						'type'          => 'button',
						'id'            => $question_id,
						'aria-expanded' => $is_open ? 'true' : 'false',
						'aria-controls' => $answer_id,
					)
				);

				$this->add_render_attribute(
					$panel_key,
					array(
						'class'           => 'dinopack-faq-answer',
						'id'              => $answer_id,
						'role'            => 'region',
						'aria-labelledby' => $question_id,
						'aria-hidden'     => $is_open ? 'false' : 'true',
					)
				);
				?>
				<div <?php $this->print_render_attribute_string( $item_key ); ?>>
					<button <?php $this->print_render_attribute_string( $button_key ); ?>>
						<?php if ( 'before' === $icon_position ) : ?>
							<span class="dinopack-faq-icon" aria-hidden="true">
								<?php $this->render_toggle_icon( $settings['icon'] ); ?>
							</span>
							<span class="dinopack-faq-question-text"><?php echo esc_html( $item['question'] ); ?></span>
						<?php else : ?>
							<span class="dinopack-faq-question-text"><?php echo esc_html( $item['question'] ); ?></span>
							<span class="dinopack-faq-icon" aria-hidden="true">
								<?php $this->render_toggle_icon( $settings['icon'] ); ?>
							</span>
						<?php endif; ?>
					</button>
					<div <?php $this->print_render_attribute_string( $panel_key ); ?>>
						<div class="dinopack-faq-answer-inner">
							<?php echo wp_kses_post( $item['answer'] ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
