<?php
/**
 * Simple widget base class.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Simple widget base class.
 *
 * Contains common to all simple widgets.
 */
abstract class Simple_Widget_Base extends The7_Elementor_Widget_Base {

	/**
	 * Common simple image style settings.
	 *
	 * @param array $condition Section condition.
	 */
	protected function add_image_style_controls( array $condition ) {
		$this->start_controls_section(
			'fetatured_image_style',
			[
				'label'     => __( 'Featured Image', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			]
		);

		$this->add_control(
			'position_title',
			[
				'label' => __( 'Position', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
			'align_image',
			[
				'label'                => __( 'Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'left',
				'toggle'               => false,
				'device_args'          => [
					'tablet' => [
						'toggle' => true,
					],
					'mobile' => [
						'toggle' => true,
					],
				],
				'prefix_class'         => 'img-align%s-',
				'selectors_dictionary' => [
					'top'   => 'flex-flow: column wrap;',
					'left'  => 'flex-flow: row nowrap;',
					'right' => 'flex-flow: row nowrap;',
				],
				'selectors'            => [
					'{{WRAPPER}} .post-content-wrapper' => '{{VALUE}}',
				],
			]
		);

		$img_position_options            = [
			'start'  => __( 'Start', 'the7mk2' ),
			'center' => __( 'Center', 'the7mk2' ),
			'end'    => __( 'End', 'the7mk2' ),
		];
		$img_position_options_on_devices = [
			'' => __( 'Default', 'the7mk2' ),
		] + $img_position_options;

		$this->add_basic_responsive_control(
			'image_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'start',
				'options'              => $img_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $img_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $img_position_options_on_devices,
					],
				],
				'prefix_class'         => 'image-vertical-align%s-',
				'selectors_dictionary' => [
					'start'  => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'end'    => 'align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .the7-simple-post-thumb, {{WRAPPER}} .post-entry-content' => '{{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_space',
			[
				'label'     => __( 'Image Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} [class*="the7-simple-widget-"]'                                                                             => '--image-spacing: {{SIZE}}{{UNIT}}',
					'(tablet) {{WRAPPER}}.img-align-tablet-left .the7-simple-post-thumb' => 'margin: 0 var(--image-spacing) 0 0; order: 0;',
					'(tablet) {{WRAPPER}}.img-align-tablet-right .the7-simple-post-thumb' => 'margin: 0 0 0 var(--image-spacing); order: 2;',
					'(tablet) {{WRAPPER}}.img-align-tablet-left .post-entry-content, {{WRAPPER}}.img-align-tablet-right .post-entry-content' => 'width: calc(100% - var(--image-size) - var(--image-spacing))',
					'(tablet) {{WRAPPER}}.img-align-tablet-top .post-entry-content' => 'width: 100%',
					'(mobile) {{WRAPPER}}.img-align-mobile-left .the7-simple-post-thumb' => 'margin: 0 var(--image-spacing) 0 0; order: 0;',
					'(mobile) {{WRAPPER}}.img-align-mobile-right .the7-simple-post-thumb' => 'margin: 0 0 0 var(--image-spacing); order: 2;',
					'(tablet) {{WRAPPER}}.img-align-tablet-top .the7-simple-post-thumb' => 'margin: 0 0 var(--image-spacing) 0; order: 0;',
					'(mobile) {{WRAPPER}}.img-align-mobile-left .post-entry-content, {{WRAPPER}}.img-align-mobile-right .post-entry-content' => 'width: calc(100% - var(--image-size) - var(--image-spacing))',
					'(mobile) {{WRAPPER}}.img-align-mobile-top .the7-simple-post-thumb' => 'margin: 0 0 var(--image-spacing) 0; order: 0;',
					'(mobile) {{WRAPPER}}.img-align-mobile-top .post-entry-content'                                                          => 'width: 100%',
				],
			]
		);

		$this->add_control(
			'size_title',
			[
				'label'     => __( 'Size', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'image_size',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 5,
						'max' => 130,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} [class*="the7-simple-widget-"]' => '--image-size: {{SIZE}}{{UNIT}}; --image-ratio: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .the7-simple-post-thumb' => 'width: var(--image-size);',
				],
			]
		);

		$this->add_control(
			'item_preserve_ratio',
			[
				'label'        => __( 'Preserve Image Proportions', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'y',
				'prefix_class' => 'preserve-img-ratio-',
			]
		);

		$this->add_basic_responsive_control(
			'item_ratio',
			[
				'label'      => __( 'Image Ratio', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 1,
				],
				'range'      => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'item_preserve_ratio',
							'operator' => '!=',
							'value'    => 'y',
						],
					],
				],
				'selectors'  => [
					'{{WRAPPER}}:not(.preserve-img-ratio-y) .img-ratio-wrapper' => 'padding-bottom:  calc( {{SIZE}} * 100% )',
				],
			]
		);

		$this->add_control(
			'icon_title',
			[
				'label'     => __( 'Hover icon', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hover_icon',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::ICONS,
				'skin'  => 'inline',
			]
		);

		$this->add_basic_responsive_control(
			'hover_icon_size',
			[
				'label'      => __( 'Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '24',
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
					'{{WRAPPER}} .the7-hover-icon'     => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .the7-hover-icon svg' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'hover_icon_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .the7-hover-icon'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .the7-hover-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'hover_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'style_title',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .the7-simple-post-thumb',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_basic_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .the7-simple-post-thumb, {{WRAPPER}} .post-thumbnail-rollover, {{WRAPPER}} .the7-simple-post-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .the7-simple-post-thumb .layzr-bg'                                 => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Overlay', 'the7mk2' ),
					],
				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: none; }
				{{WRAPPER}} .post-thumbnail-rollover:before,
				{{WRAPPER}} .post-thumbnail-rollover:after
				',
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .the7-simple-post-thumb' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '
				{{WRAPPER}} .the7-simple-post-thumb
				',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_filters',
				'selector' => '
				{{WRAPPER}} .post-thumbnail-rollover img
				',
			]
		);

		$this->add_control(
			'thumbnail_opacity',
			[
				'label'      => __( 'Image opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-thumbnail-rollover img' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'overlay_hover_background',
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => [
					'background' => [
						'label' => __( 'Overlay', 'the7mk2' ),
					],
					'color'      => [
						'selectors' => [
							'
							{{SELECTOR}},
							{{WRAPPER}} .post-thumbnail-rollover:before, {{WRAPPER}} .post-thumbnail-rollover:after { transition: opacity 0.3s ease; } {{SELECTOR}}' => 'background: {{VALUE}};',
						],
					],

				],
				'selector'       => '{{WRAPPER}} .post-thumbnail-rollover:after',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.post:hover .the7-simple-post-thumb,
					{{WRAPPER}} .the7-simple-post-thumb:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_hover_shadow',
				'selector' => '
					{{WRAPPER}} a:hover .the7-simple-post-thumb,
					{{WRAPPER}} .the7-simple-post-thumb:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img
				',
			]
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			[
				'label'      => __( 'Image opacity', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => [ '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'
					{{WRAPPER}} .the7-simple-post-thumb img { transition: opacity 0.3s ease; }
					{{WRAPPER}} a:hover .the7-simple-post-thumb img,
					{{WRAPPER}} .post-thumbnail-rollover:hover img ' => 'opacity: calc({{SIZE}}/100)',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Common simple box style settings.
	 */
	protected function add_box_content_style_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => __( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'adaptive_height',
			[
				'label'        => __( 'Adaptive Height', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
				'prefix_class' => 'auto-height-',
			]
		);

		$this->add_basic_responsive_control(
			'box_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post.wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'adaptive_height!' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'content_position',
			[
				'label'                => __( 'Content Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
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
				'default'              => 'top',
				'prefix_class'         => 'icon-box-vertical-align%s-',
				'selectors_dictionary' => [
					'top'    => 'align-items: flex-start;align-content: flex-start;',
					'center' => 'align-items: center;align-content: center;',
					'bottom' => 'align-items: flex-end;align-content: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .post.wrapper' => '{{VALUE}}',
				],
				'condition'            => [
					'adaptive_height!' => 'y',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => __( 'Border', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper',
				'exclude'  => [
					'color',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .post.wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-carousel' => '--box-padding-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .post.wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_box_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'bg_hover_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post.wrapper:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_hover_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .post.wrapper:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
}
