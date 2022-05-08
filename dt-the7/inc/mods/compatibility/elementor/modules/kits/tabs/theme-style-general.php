<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Theme_Style_General extends The7_Tab_Base {

	function the7_title() {
		return __( 'General Appearance', 'the7mk2' );
	}

	function the7_id() {
		return 'general';
	}

	public function get_icon() {
		return 'eicon-theme-style';
	}

	protected function register_tab_controls() {
		$this->add_general_appearance_section();
		$this->add_beautiful_loading_section();
	}

	private function add_general_appearance_section() {
		$wrapper = $this->get_wrapper();
		$this->start_controls_section( 'the7_section_appearance', [
			'label' => __( 'General Appearance', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$this->add_control( 'the7-accent-color', [
			'label'     => __( 'Accent Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$wrapper => '--the7-accent-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'the7-divider-color', [
			'label'     => __( 'Dividers Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$wrapper => '--the7-divider-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'the7-content-boxes', [
			'label'     => __( 'Content Boxes Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$wrapper => '--the7-content-boxes-bg: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	private function add_beautiful_loading_section() {
		$this->start_controls_section( 'the7_section_general_beautiful_loading', [
			'label' => __( 'Beautiful Loading', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );
		$this->add_control( 'the7_general_beautiful_loading', [
			'label'            => esc_html__( 'Beautiful loading', 'the7mk2' ),
			'type'             => Controls_Manager::SWITCHER,
			'default'          => of_get_option( 'general-beautiful_loading' ),
			'return_value'     => 'enabled',
			'empty_value'      => 'disabled',
			'the7_save'        => true,
			'the7_option_name' => 'general-beautiful_loading',
			'export'           => false,
		] );
		$selector = $this->get_wrapper();

		$this->add_control( 'the7_general_beautiful_loading_color', [
			'label'     => __( 'Spinner color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => '--the7-beautiful-spinner-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'           => 'the7_general_beautiful_loading_background',
			'types'          => [ 'classic', 'gradient' ],
			'exclude'        => [ 'image' ],
			'selector'       => $selector,
			'condition'      => [
				'the7_general_beautiful_loading' => 'enabled',
			],
			'fields_options' => [
				'background'        => [
					'default' => 'classic',
				],
				'color'             => [
					'dynamic'   => [],
					'selectors' => [
						'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: {{VALUE}};',
					],
				],
				'gradient_angle'    => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: transparent linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					],
				],
				'gradient_position' => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-elementor-beautiful-loading-bg: transparent radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					],
				],
				'color_b'           => [
					'dynamic' => [],
				],
			],
		] );

		$this->add_control( 'the7_general_beautiful_loading_style', [
			'label'            => esc_html__( 'Loader style', 'the7mk2' ),
			'type'             => Controls_Manager::SELECT,
			'default'          => of_get_option( 'general-loader_style' ),
			'separator'        => 'before',
			'label_block'      => false,
			'options'          => [
				'double_circles'    => esc_html__( 'Spinner', 'the7mk2' ),
				'square_jelly_box'  => esc_html__( 'Ring', 'the7mk2' ),
				'ball_elastic_dots' => esc_html__( 'Bars', 'the7mk2' ),
				'custom'            => esc_html__( 'Custom', 'the7mk2' ),
			],
			'condition'        => [
				'the7_general_beautiful_loading' => 'enabled',
			],
			'the7_save'        => true,
			'the7_option_name' => 'general-loader_style',
			'export'           => false,
		] );

		$this->add_control( 'the7_general_beautiful_loading_custom_style_title', [
			'raw'             => __( 'Paste HTML code of your custom pre-loader image in the field below.', 'the7mk2' ),
			'type'            => Controls_Manager::RAW_HTML,
			'condition'       => [
				'the7_general_beautiful_loading_style' => 'custom',
			],
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		] );

		$this->add_control( 'the7_general_beautiful_loading_custom_style_code', [
			'type'             => Controls_Manager::CODE,
			'label'            => __( 'Custom Loader Code', 'the7mk2' ),
			'language'         => 'html',
			'default'          => (string) of_get_option( 'general-custom_loader', '' ),
			'render_type'      => 'ui',
			'show_label'       => false,
			'separator'        => 'none',
			'the7_save'        => true,
			'the7_option_name' => 'general-custom_loader',
			'condition'        => [
				'the7_general_beautiful_loading_style' => 'custom',
			],
		] );

		$this->end_controls_section();
	}

}
