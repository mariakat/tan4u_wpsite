<?php
/**
 * The7 icon widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

defined( 'ABSPATH' ) || exit;

/**
 * Icon class.
 */
class Icon extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 *
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7_icon_widget';
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return __( 'Icon', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-favorite';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'the7-icon-widget' ];
	}

	/**
	 * Register assets.
	 */
	protected function register_assets() {
		the7_register_style( 'the7-icon-widget', THE7_ELEMENTOR_CSS_URI . '/the7-icon-widget.css' );
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->add_content_controls();
		$this->add_icon_style_controls();
	}

	/**
	 * Content controls.
	 */
	protected function add_content_controls() {

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Content', 'the7mk2' ),
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'            => __( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label'     => __( 'Link', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'the7mk2' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'the7mk2' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Icon style controls.
	 */
	protected function add_icon_style_controls() {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'    => [
						'title' => esc_html__( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class'         => 'elementor%s-align-',
				'selectors_dictionary' => [
					'left'    => 'display: inline-flex;',
					'center'  => 'display: inline-flex;',
					'right'   => 'display: inline-flex;',
					'justify' => 'display: flex; justify-content: center;',
				],
				'default'              => 'center',
				'selectors'            => [
					'{{WRAPPER}} .elementor-icon' => '{{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'size',
			[
				'label'     => __( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.01,
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'icon_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Background', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .elementor-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_colors_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i { transition: color 0.3s ease; } {{WRAPPER}} .elementor-icon svg { transition: fill 0.3s ease; } {{WRAPPER}} .elementor-icon:hover i'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'icon_hover_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Background', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}}',
						],
					],
				],
				'selector'       => '{{WRAPPER}} .elementor-icon:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .elementor-icon:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'icon_color_sticky',
			[
				'label' => __( 'Change colors', 'the7mk2' ),
				'type'  => Controls_Manager::SWITCHER,
				'description'  => sprintf(
					// translators: %s - edit menu admin page.
						__( 'When “Sticky” and “Transitions On Scroll” are ON for the parent section.' )
					),
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'sticky_icon_colors' );

		$this->start_controls_tab(
			'sticky_icon_colors_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon i'   => 'color: {{VALUE}};',
					self::STICKY_WRAPPER . ' .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'sticky_icon_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Background', 'the7mk2' ),
					],
				],
				'selector'       => self::STICKY_WRAPPER . ' .elementor-icon',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sticky_icon_box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => self::STICKY_WRAPPER . ' .elementor-icon',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'sticky_icon_colors_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_hover_primary_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} .elementor-icon i { transition: color 0.3s ease; } .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} .elementor-icon svg { transition: fill 0.3s ease; } .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} .elementor-icon:hover i'     => 'color: {{VALUE}};',
					self::STICKY_WRAPPER . ' .elementor-icon:hover svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_control(
			'sticky_hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					self::STICKY_WRAPPER . ' .elementor-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border_border!' => '',
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'sticky_icon_hover_bg_color',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Background', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'{{SELECTOR}}' => 'background: {{VALUE}}',
						],
					],
				],
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
				'selector'       => self::STICKY_WRAPPER . ' .elementor-icon:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sticky_icon_hover_box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => self::STICKY_WRAPPER . ' .elementor-icon:hover',
				'condition' => [
					'icon_color_sticky' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="the7-icon-wrapper the7-elementor-widget">';
		if ( $settings['selected_icon']['value'] !== '' ) {
			$this->add_render_attribute( 'icon_wrapper', 'class', 'elementor-icon' );
			$tag = 'div';
			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_link_attributes( 'icon_wrapper', $settings['link'] );
				$tag = 'a';
			}

			echo "<{$tag} " . $this->get_render_attribute_string( 'icon_wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
			echo "</{$tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	/**
	 * Content template.
	 */
	protected function content_template() {
		?>
	   <# var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
	   iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
	  iconTag = link ? 'a' : 'div';
	   #>
	   <div class="the7-icon-wrapper the7-elementor-widget">
		  <{{{ iconTag }}} class="elementor-icon" {{{ link }}}>
			 <# if ( iconHTML && iconHTML.rendered ) { #>
			 {{{ iconHTML.value }}}
			 <# } #>
		  </{{{ iconTag }}}>
	   </div>
		<?php
	}

}
