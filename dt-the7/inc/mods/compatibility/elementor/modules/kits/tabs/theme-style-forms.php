<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\Modules\Controls\Groups\Group_Control_Typography_CSS_Vars;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Theme_Style_Forms extends The7_Tab_Base {

	function the7_title() {
		return __( 'Forms', 'the7mk2' );
	}

	function the7_id() {
		return 'forms';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function register_tab_controls() {
		$this->add_basic_form_section();
		$this->add_form_sizes_section();
	}

	private function add_basic_form_section() {
		$this->start_controls_section( 'the7_section_forms', [
			'label' => __( 'General Forms Settings', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$selector = $this->get_wrapper();

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'General Typography', 'the7mk2' ),
			'name'     => 'the7_forms_def_typography',
			'selector' => 'body#the7-body',
			'css_name' => [
				'the7-form-xs',
				'the7-form-sm',
				'the7-form-md',
				'the7-form-lg',
				'the7-form-xl',
			],
			'exclude'  => [
				'line_height',
				'font_style',
				'font_size',
				'text_decoration',
				'word_spacing',
			],
		] );

		$this->add_responsive_control( 'the7_forms_def_padding', [
			'label'      => __( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				$this->get_wrapper() => '--the7-top-input-padding:  {{TOP}}{{UNIT}}; --the7-right-input-padding: {{RIGHT}}{{UNIT}}; --the7-bottom-input-padding: {{BOTTOM}}{{UNIT}}; --the7-left-input-padding: {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'the7_forms_def_min_height', [
			'label'      => __( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$this->get_wrapper() => '--the7-input-height: {{SIZE}}{{UNIT}};',
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'size_units' => [ 'px' ],
		] );

		$this->add_control(
			'the7_forms_column_spacing',
			[
				'label' => esc_html__( 'Elementor Form Column Gap', 'the7mk2' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'.elementor-field-group' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'.elementor-form-fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_control( 'the7_forms_row_spacing', [
			'label'     => esc_html__( 'Elementor Form Row Gap', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'.elementor-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				'.elementor-field-group.recaptcha_v3-bottomleft, {{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomright' => 'margin-bottom: 0;',
				'.elementor-form-fields-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'the7_forms_text_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => '--the7-input-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'the7_forms_bg_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => '--the7-input-bg-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'           => 'the7_forms_border',
			'selector'       => $selector,
			'fields_options' => [
				'border' => [
					'options'   => [
						''       => __( 'Default', 'the7mk2' ),
						'solid'  => __( 'Solid', 'the7mk2' ),
						'double' => __( 'Double', 'the7mk2' ),
						'dotted' => __( 'Dotted', 'the7mk2' ),
						'dashed' => __( 'Dashed', 'the7mk2' ),
						'groove' => __( 'Groove', 'the7mk2' ),
					],
					'selectors' => [
						'{{SELECTOR}}' => '--the7-form-border: {{VALUE}};',
					],
				],
				'width'  => [
					'selectors' => [
						'{{SELECTOR}}' => '--the7-top-input-border-width: {{TOP}}{{UNIT}}; --the7-right-input-border-width: {{RIGHT}}{{UNIT}}; --the7-bottom-input-border-width: {{BOTTOM}}{{UNIT}}; --the7-left-input-border-width: {{LEFT}}{{UNIT}};',
					],
				],
			],
			'exclude'        => [ 'color' ],
		] );

		$this->add_control( 'the7_forms_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => "
					--the7-input-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
					--the7-input-border-radius-top: {{TOP}}{{UNIT}};
					--the7-input-border-radius-right: {{RIGHT}}{{UNIT}};
					--the7-input-border-radius-bottom: {{BOTTOM}}{{UNIT}};
					--the7-input-border-radius-left: {{LEFT}}{{UNIT}};",
			],
		] );

		$this->add_control( 'the7_forms_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$this->get_wrapper() => '--the7-input-border-color: {{VALUE}};',
			],
			'condition' => [
				'the7_forms_border_border!' => '',
			],
		] );

		$this->start_controls_tabs( 'the7_tabs_forms_style' );
		$this->start_controls_tab( 'the7_tab_forms_normal', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );
		$this->add_basic_common_controls();

		$this->end_controls_tab();
		$this->start_controls_tab( 'the7_tab_forms_hover', [
			'label' => __( 'Focus', 'the7mk2' ),
		] );
		$this->add_basic_common_controls( 'focus' );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_basic_label_controls();
		$this->end_controls_section();
	}

	protected function add_basic_common_controls( $prefix = '' ) {
		$name_prefix = 'the7_forms';
		$selector_prefix = '';
		$css_prefix = '';
		if ( ! empty( $prefix ) ) {
			$name_prefix = $name_prefix . '_' . $prefix;
			$css_prefix = "-{$prefix}";
			$selector_prefix = ":{$prefix}";
		}
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'           => $name_prefix . '_box_shadow',
			'fields_options' => [
				'box_shadow' => [
					'dynamic'   => [],
					'selectors' => [
						'{{SELECTOR}}' => '--the7-form-shadow' . $css_prefix . ': {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
					],
				],
			],
			'selector'       => $this->get_wrapper(),
		] );

		$selector_placeholder = "
			input[type=\"text\"]{$selector_prefix}::placeholder,
			input[type=\"search\"]{$selector_prefix}::placeholder,
			input[type=\"tel\"]{$selector_prefix}::placeholder,
			input[type=\"url\"]{$selector_prefix}::placeholder,
			input[type=\"email\"]{$selector_prefix}::placeholder,
			input[type=\"number\"]{$selector_prefix}::placeholder,
			input[type=\"date\"]{$selector_prefix}::placeholder,
			input[type=\"range\"]{$selector_prefix}::placeholder,
			input[type=\"password\"]{$selector_prefix}::placeholder,
			.elementor-field-group .elementor-field-textual{$selector_prefix}::placeholder";

		$this->add_control( $name_prefix . '_placeholder_opacity', [
			'label'     => __( 'Placeholder Opacity', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'step' => 0.01,
				],
			],
			'selectors' => [
				$selector_placeholder => 'opacity: {{SIZE}}',
				$this->get_wrapper()  => '--the7-form-placeholder-opacity' . $css_prefix . ': {{SIZE}};',
			],
		] );
	}

	protected function add_basic_label_controls() {
		$selector = '.elementor-field-label';

		$this->add_control( 'the7_forms_label_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Labels', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => __( 'Typography', 'the7mk2' ),
			'name'     => 'the7_forms_label_typography',
			'selector' => $selector,
		] );


		$this->add_control( 'the7_forms_label_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$selector => 'color: {{VALUE}};',
			],
		] );


		$this->add_control( 'the7_forms_label_spacing', [
			'label'     => esc_html__( 'Spacing', 'the7mk2' ),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'size' => 0,
			],
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'.elementor-labels-inline .elementor-field-group > .elementor-field-label' => 'padding-left: {{SIZE}}{{UNIT}};',
				// for the label position = inline option
				'.elementor-labels-inline .elementor-field-group > .elementor-field-label' => 'padding-right: {{SIZE}}{{UNIT}};',
				// for the label position = inline option
				'.elementor-labels-above .elementor-field-group > .elementor-field-label'  => 'padding-bottom: {{SIZE}}{{UNIT}};',
				// for the label position = above option
			],
		] );
	}

	private function add_form_sizes_section() {
		$buttons = [
			'xs' => esc_html__( 'Extra Small Forms', 'the7mk2' ),
			'sm' => esc_html__( 'Small Forms', 'the7mk2' ),
			'md' => esc_html__( 'Medium Forms', 'the7mk2' ),
			'lg' => esc_html__( 'Large Forms', 'the7mk2' ),
			'xl' => esc_html__( 'Extra Large Forms', 'the7mk2' ),
		];

		foreach ( $buttons as $id => $label ) {
			$this->add_forms_controls( $label, $id );
		}
	}

	private function add_forms_controls( $label, $id ) {
		$prefix = "the7_form_{$id}";

		$this->start_controls_section( 'the7_section_' . $prefix, [
			'label' => $label,
			'tab'   => $this->get_id(),
		] );

		$selector = ".elementor-field-group .elementor-field-textual.elementor-size-{$id},
		.content .elementor-field-group .elementor-field-textual.elementor-size-{$id}";

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => __( 'Typography', 'the7mk2' ),
			'name'     => $prefix . '_typography',
			'selector' => $selector,
			'exclude'  => [ 'text_decoration' ],
		] );

		$this->add_responsive_control( $prefix . '_padding', [
			'label'      => __( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( $prefix . '_min_height', [
			'label'      => __( 'Min Height', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => 'min-height: {{SIZE}}{{UNIT}};',
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

		$this->add_control( $prefix .'_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
			],
		] );

		$this->end_controls_section();
	}
}
