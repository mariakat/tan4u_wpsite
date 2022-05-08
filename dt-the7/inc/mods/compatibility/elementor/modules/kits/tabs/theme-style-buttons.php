<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Plugin as Elementor;
use Elementor\Core\Settings\Page\Manager as SettingsPageManager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use The7\Mods\Compatibility\Elementor\Modules\Controls\Groups\Group_Control_Border_CSS_Vars;
use The7\Mods\Compatibility\Elementor\Modules\Controls\Groups\Group_Control_Typography_CSS_Vars;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Theme_Style_Buttons extends The7_Tab_Base {

	function the7_title() {
		return __( 'Buttons', 'the7mk2' );
	}

	function the7_id() {
		return 'buttons';
	}

	public function get_icon() {
		return 'eicon-button';
	}

	/*public function get_help_url() {
		return 'https://support.dream-theme.com/';
	}*/

	/*public function get_additional_tab_content() {
		return 'Additional content text';
	}*/

	protected function register_tab_controls() {
		$this->add_basic_buttons_section();
		$this->add_buttons_sizes_section();
	}

	private function add_basic_buttons_section() {
		$this->start_controls_section( 'the7_section_buttons', [
			'label' => __( 'General Buttons Settings', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'General Typography', 'the7mk2' ),
			'name'     => 'the7_btn_def_typography',
			'css_name' => [
				'the7-btn-s',
				'the7-btn-m',
				'the7-btn-l',
				'the7-btn-lg',
				'the7-btn-xl',
			],
			'selector' => 'body',
			'exclude'  => [
				'line_height',
				'font_style',
				'font_size',
				'text_decoration',
			],
		] );

		$this->start_controls_tabs( 'the7_tabs_button_style' );
		$this->start_controls_tab( 'the7_tab_button_normal', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );
		$this->add_basic_common_controls();
		$this->end_controls_tab();
		$this->start_controls_tab( 'the7_tab_button_hover', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );
		$this->add_basic_common_controls( 'hover' );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function add_basic_common_controls( $prefix = '' ) {
		$css_prefix = '';
		$name_prefix = 'the7_button';
		if ( ! empty( $prefix ) ) {
			$css_prefix = '-' . $prefix;
			$name_prefix = $name_prefix . '_' . $prefix;
		}
		$selector = $this->get_wrapper();
		$this->add_control( $name_prefix . '_text_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => '--the7-btn' . $css_prefix . '-color: {{VALUE}};',
			],
			//'the7_reload_on_change' => true,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'           => $name_prefix . '_background',
			'types'          => [ 'classic', 'gradient' ],
			'exclude'        => [ 'image' ],
			'selector'       => $selector,
			'fields_options' => [
				'background'        => [
					'default' => 'classic',
				],
				'color'             => [
					'dynamic'   => [],
					'selectors' => [
						'{{SELECTOR}}' => '--the7-btn' . $css_prefix . '-bg: {{VALUE}};',
					],
				],
				'gradient_angle'    => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-btn' . $css_prefix . '-bg: transparent linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					],
				],
				'gradient_position' => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-btn' . $css_prefix . '-bg: transparent radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});',
					],
				],
				'color_b'           => [
					'dynamic' => [],
				],
			],
		] );

		$this->add_control( $name_prefix . '_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => '--the7-btn-border' . $css_prefix . '-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'           => $name_prefix . '_box_shadow',
			'selector'       => $selector,
			'fields_options' => [
				'box_shadow' => [
					'dynamic'   => [],
					'selectors' => [
						'{{SELECTOR}}' => '--the7-btn-shadow' . $css_prefix . ': {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
					],
				],
			],
		] );
	}

	private function add_buttons_sizes_section() {
		$buttons = [
			's'  => _x( 'Extra small buttons', 'theme-options', 'the7mk2' ),
			'm'  => _x( 'Small buttons', 'theme-options', 'the7mk2' ),
			'l'  => _x( 'Medium buttons', 'theme-options', 'the7mk2' ),
			'lg' => _x( 'Large buttons', 'theme-options', 'the7mk2' ),
			'xl' => _x( 'Extra large buttons', 'theme-options', 'the7mk2' ),
		];

		foreach ( $buttons as $id => $label ) {
			$this->add_buttons_controls( $label, $id );
		}
	}

	private function add_buttons_controls( $label, $id ) {
		$prefix = "the7_btn_{$id}";
		$css_prefix = "the7-btn-{$id}";
		$selector = $this->get_wrapper();
		$this->start_controls_section( 'the7_section_' . $prefix, [
			'label' => $label,
			'tab'   => $this->get_id(),
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'          => __( 'Typography', 'the7mk2' ),
			'name'           => $prefix . '_typography',
			'css_name'       => $css_prefix,
			'selector'       => $selector,
			'exclude'        => [ 'text_decoration' ],
		] );

		$this->add_responsive_control( $prefix . '_padding', [
			'label'      => __( 'Text Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				$selector => "--{$css_prefix}-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
			],
		] );

		$this->add_responsive_control( $prefix . '_min_width', [
			'label'      => __( 'Min Width', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => "--{$css_prefix}-min-width: {{SIZE}}{{UNIT}};",
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'size_units' => [ 'px' ],
			'separator'  => 'before',
		] );

		$this->add_responsive_control( $prefix . '_min_height', [
			'label'      => __( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => "--{$css_prefix}-min-height: {{SIZE}}{{UNIT}};",
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'size_units' => [ 'px' ],
			'separator'  => 'after',
		] );

		$this->add_group_control( Group_Control_Border_CSS_Vars::get_type(), [
			'name'     => $prefix . '_border',
			'selector' => $selector,
			'css_name' => $css_prefix . '-border',
			'exclude'  => [ 'color' ],
		] );

		$this->add_control( $prefix . '_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => "--{$css_prefix}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
			],
		] );

		$this->add_responsive_control( $prefix . '_custom_icon_size', [
			'label'      => __( 'Icon Size', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => "--{$css_prefix}-icon-size: {{SIZE}}{{UNIT}};",
			],
			'range'      => [
				'px' => [
					'min' => 1,
					'max' => 120,
				],
			],
			'size_units' => [ 'px' ],
			'separator'  => 'before',
		] );

		$this->add_responsive_control( $prefix . '_custom_icon_gap', [
			'label'      => __( 'Icon Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => "--{$css_prefix}-icon-gap: {{SIZE}}{{UNIT}};",
			],
			'range'      => [
				'px' => [
					'min' => 1,
					'max' => 120,
				],
			],
			'size_units' => [ 'px' ],
		] );

		$this->end_controls_section();
	}
}
