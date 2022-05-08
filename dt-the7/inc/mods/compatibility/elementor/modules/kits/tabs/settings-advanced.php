<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Settings_Advanced extends The7_Tab_Base {

	function the7_title() {
		return __( 'Advanced Settings', 'the7mk2' );
	}

	function the7_id() {
		return 'advanced';
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-cog';
	}

	protected function register_tab_controls() {
		$this->add_general_section();
		$this->add_custom_js_section();
	}

	private function add_general_section() {
		$this->start_controls_section( 'the7_section_general', [
				'label' => __( 'Optimization', 'the7mk2' ),
				'tab'   => $this->get_id(),
			] );
		$this->add_control( 'the7_general_images_lazy_loading', [
				'label'            => esc_html__( 'Images lazy loading', 'the7mk2' ),
				'type'             => Controls_Manager::SWITCHER,
				'default'          => of_get_option( 'general-images_lazy_loading' ),
				'description'      => esc_html__( 'Can dramatically reduce page loading speed. Recommended.', 'the7mk2' ),
				'return_value'     => '1',
				'empty_value'      => '0',
				'the7_save'        => true,
				'the7_option_name' => 'general-images_lazy_loading',
				'export'           => false,
			] );

		$this->add_control( 'the7_fvm_enable_integration', [
				'label'            => esc_html__( 'FVM plugin integration', 'the7mk2' ),
				'type'             => Controls_Manager::SWITCHER,
				'default'          => of_get_option( 'advanced-fvm_enable_integration' ),
				'description'      => sprintf( __( 'Enable <a href="%1$s" target="_blank">Fast Velocity Minify</a> plugin integration', 'the7mk2' ), 'https://wordpress.org/plugins/fast-velocity-minify/' ),
				'return_value'     => '1',
				'empty_value'      => '0',
				'the7_save'        => true,
				'the7_option_name' => 'advanced-fvm_enable_integration',
				'export'           => false,
			] );


		$this->add_control( 'quantity', [
				'label'       => __( 'Delay(ms)', 'the7mk2' ),
				'description' => __( 'Delay loading of merged scripts', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 50,
				'condition'   => [
					'the7_fvm_enable_integration' => '1',
				],
			] );

		$this->end_controls_section();
	}

	private function add_custom_js_section() {
		$this->start_controls_section( 'the7_section_general_js', [
			'label' => __( 'Custom JS', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$this->add_control( 'the7_general_custom_js_title', [
				'raw'  => __( 'Tracking code (e.g. Google analytics) or arbitrary JavaScript', 'the7mk2' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			] );

		$this->add_control( 'the7_general_custom_js', [
				'type'             => Controls_Manager::CODE,
				'label'            => __( 'Custom JS', 'the7mk2' ),
				'language'         => 'html',
				'default'          => (string) of_get_option( 'general-tracking_code', '' ),
				'render_type'      => 'ui',
				'show_label'       => false,
				'separator'        => 'none',
				'the7_save'        => true,
				'the7_option_name' => 'general-tracking_code',
				'export' => false,
			] );

		$this->end_controls_section();
	}
}

