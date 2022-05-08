<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bullets.
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Bullets extends Abstract_Template {

	/**
	 * @return void
	 */
	public function add_content_controls() {
		$this->widget->start_controls_section(
			'bullets_section',
			[
				'label' => __( 'Bullets', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$layouts            = [
			'y' => __( 'Show', 'the7mk2' ),
			'n' => __( 'Hide', 'the7mk2' ),
		];
		$responsive_layouts = [ '' => __( 'No change', 'the7mk2' ) ] + $layouts;

		$this->widget->add_basic_responsive_control(
			'show_bullets',
			[
				'label'       => __( 'Show Bullets', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'y',
				'options'     => $layouts,
				'device_args' => [
					'tablet' => [
						'options' => $responsive_layouts,
					],
					'mobile' => [
						'options' => $responsive_layouts,
					],
				],
			]
		);

		$this->widget->end_controls_section();
	}

	/**
	 * @return void
	 */
	public function add_style_controls() {
		$this->widget->start_controls_section(
			'bullets_style_block',
			[
				'label'      => __( 'Bullets', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'show_bullets',
							'value' => 'y',
						],
						[
							'name'  => 'show_bullets_tablet',
							'value' => 'y',
						],
						[
							'name'  => 'show_bullets_mobile',
							'value' => 'y',
						],
					],
				],
			]
		);

		$this->widget->add_control(
			'bullets_Style_heading',
			[
				'label'     => __( 'Bullets Style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'bullets_style',
			[
				'label'   => __( 'Choose Bullets Style', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'small-dot-stroke',
				'options' => [
					'small-dot-stroke' => 'Small dot stroke',
					'scale-up'         => 'Scale up',
					'stroke'           => 'Stroke',
					'fill-in'          => 'Fill in',
					'ubax'             => 'Square',
					'etefu'            => 'Rectangular',
				],
			]
		);

		$this->widget->add_control(
			'bullet_size',
			[
				'label'      => __( 'Bullets Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->add_control(
			'bullet_gap',
			[
				'label'      => __( 'Gap Between Bullets', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->widget->start_controls_tabs( 'bullet_style_tabs' );

		$this->widget->start_controls_tab(
			'bullet_colors',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_hover_colors',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_hover',
			[
				'label'     => __( 'Hover Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-hover-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->start_controls_tab(
			'bullet_active_colors',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->widget->add_control(
			'bullet_color_active',
			[
				'label'     => __( 'Active Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-dot' => '--the7-carousel-bullet-active-color: {{VALUE}}',
				],
			]
		);

		$this->widget->end_controls_tab();

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'bullets_position_heading',
			[
				'label'     => __( 'Bullets Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->widget->add_control(
			'bullets_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'bottom',
			]
		);

		$this->widget->add_control(
			'bullets_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'center',
			]
		);

		$this->widget->add_control(
			'bullets_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->widget->add_control(
			'bullets_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->widget->end_controls_section();
	}

}
