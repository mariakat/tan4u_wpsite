<?php
/**
 * The7 "Filter By Price" Elementor widget.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\The7_WC_Widget_Price_Filter;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\Woocommerce_Support;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use WC_Query;
use WC_Tax;

defined( 'ABSPATH' ) || exit;

/**
 * The7 "Filter By Price" Elementor widget class.
 */
class Filter_Price extends The7_Elementor_Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'the7-woocommerce-filter-price';
	}

	/**
	 * @return string[]
	 */
	public function get_categories() {
		return [ 'woocommerce-elements-single', 'woocommerce-elements-archive' ];
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ $this->get_name()];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-woocommerce-filter-price'
		);

		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/woocommerce-filter-price.js',
			[ 'jquery', 'wc-price-slider' ]
		);
	}

	/**
	 * @return string|void
	 */
	protected function the7_title() {
		return __( 'Filter By Price', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * @return string[]
	 */
	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'filter', 'price' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content Tab.
		$this->add_title_area_content_controls();
		$this->add_filter_area_content_controls();

		// Styles tab.
		$this->add_title_styles_controls();
		$this->add_slider_line_styles_controls();
		$this->add_tips_styles_controls();
		$this->add_price_styles_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[],
			[
				'gap_above_button' => null,
			]
		);

		$this->start_injection(
			[
				'of' => 'button_size',
				'at' => 'after',
			]
		);
		$this->add_control(
			'btn_position',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => [
					'left'  => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class'         => 'btn-align-',
				'selectors_dictionary' => [
					'left'  => 'order: 1; align-self: flex-start;',
					'right' => 'order: 2; align-self: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .box-button' => '{{VALUE}}',
				],
			]
		);
		$this->end_injection();
	}

	/**
	 * Add title content controls.
	 */
	protected function add_title_area_content_controls() {
		$this->start_controls_section(
			'title_area_section',
			[
				'label' => __( 'Title Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => __( 'Widget Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Widget Title', 'the7mk2' ),
			]
		);

		$this->add_control(
			'toggle',
			[
				'label'        => __( 'Widget Toggle', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'the7mk2' ),
				'label_off'    => __( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_closed_by_default',
			[
				'label'        => __( 'Closed By Default', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'closed',
				'default'      => '',
				'condition'    => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'            => __( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block'      => false,
				'skin'             => 'inline',
				'condition'        => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_active_icon',
			[
				'label'            => __( 'Active Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default'          => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'             => 'inline',
				'label_block'      => false,
				'condition'        => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add filter area content controls.
	 */
	protected function add_filter_area_content_controls() {
		$this->start_controls_section(
			'filter_area_section',
			[
				'label' => __( 'Filter Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'price_range_display',
			[
				'label'              => __( 'Display Price Range', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'above',
				'options'            => [
					'above' => __( 'Above slider tips', 'the7mk2' ),
					'next'  => __( 'Next to button', 'the7mk2' ),
				],
				'prefix_class'       => 'display-price-',
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'min_text',
			[
				'label'     => __( 'Min Price Label', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Min: ', 'the7mk2' ),
				'condition' => [
					'price_range_display' => 'above',
				],
			]
		);

		$this->add_control(
			'max_text',
			[
				'label'     => __( 'Max Price Label', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Max: ', 'the7mk2' ),
				'condition' => [
					'price_range_display' => 'above',
				],
			]
		);

		$this->add_control(
			'price_text',
			[
				'label'     => __( 'Price Label', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Price:', 'the7mk2' ),
				'condition' => [
					'price_range_display' => 'next',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => __( 'Button Text', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Filter', 'the7mk2' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add title style controls.
	 */
	protected function add_title_styles_controls() {
		$this->start_controls_section(
			'title_section',
			[
				'label'      => __( 'Title Area', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,

				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .filter-title',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-title' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_basic_responsive_control(
			'title_arrow_size',
			[
				'label'     => __( 'Toggle Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 16,
				],
				'condition' => [
					'toggle!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'title_arrow_tabs_style',
			[
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tab(
			'normal_title_arrow_style',
			[
				'label' => __( 'Closed', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_arrow_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_title_arrow_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_title_arrow_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.no-touchevents {{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
					'.no-touchevents {{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_title_arrow_style',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_title_arrow_color',
			[
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'title_space',
			[
				'label'      => __( 'Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'    => [
					'size' => 15,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter-container' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator'  => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'title_text',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'toggle',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add slider line style controls.
	 */
	protected function add_slider_line_styles_controls() {
		$this->start_controls_section(
			'slider_line_section',
			[
				'label' => __( 'Slider line', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$wrapper_selector = '{{WRAPPER}} .price_slider_inner_wrapper_wrapper';
		$selector         = '{{WRAPPER}} .ui-slider-horizontal, ' . $wrapper_selector;

		$this->add_control(
			'slider_line_height',
			[
				'label'      => __( 'Thickness', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					$selector => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}  .ui-slider-handle:last-of-type:after' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_count_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .price_slider_inner_wrapper_wrapper' => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ui-slider-range' => 'border-top-left-radius: {{SIZE}}{{UNIT}}; border-bottom-left-radius: {{SIZE}}{{UNIT}}; border-top-right-radius: 0; border-bottom-right-radius: 0;',
					'{{WRAPPER}}  .ui-slider-handle:last-of-type:after' => 'border-top-right-radius: {{SIZE}}{{UNIT}}; border-bottom-right-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'slider_line_color',
			[
				'label'     => __( 'Active Range Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .widget_price_filter .ui-slider .ui-slider-range' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_background_color',
			[
				'label'     => __( 'Inactive Range Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price_slider_inner_wrapper_wrapper' => 'background-color: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
			'slider_above_space',
			[
				'label'     => __( 'Gap Above Slider ', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => '10',
				],
				'selectors' => [
					$wrapper_selector => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'price_range_display' => 'above',
				],
			]
		);

		$this->add_basic_responsive_control(
			'slider_below_space',
			[
				'label'     => __( 'Gap Below Slider ', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => '10',
				],
				'selectors' => [
					$wrapper_selector => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Add tips style controls.
	 */
	protected function add_tips_styles_controls() {
		$this->start_controls_section(
			'tips_section',
			[
				'label' => __( 'Slider tips', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}} .widget_price_filter .ui-slider-handle:before';

		$this->add_control(
			'tips_width',
			[
				'label'      => __( 'Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .widget_price_filter .ui-slider-handle, {{WRAPPER}} .widget_price_filter .ui-slider-handle:before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .widget_price_filter .price_slider' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'tips_height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .widget_price_filter .ui-slider-handle, {{WRAPPER}} .widget_price_filter .ui-slider-handle:before' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tips_border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .ui-slider-handle:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
				],
			]
		);

		$this->add_control(
			'tips_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->start_controls_tabs( 'tips_tabs_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tips_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ui-slider-handle:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tips_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ui-slider-handle:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tips_box_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .ui-slider-handle:before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tips_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tips_bg_hover_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ui-slider-handle.ui-state-hover:before, {{WRAPPER}} .ui-slider-handle.ui-state-active:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tips_hover_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ui-slider-handle.ui-state-hover:before, {{WRAPPER}} .ui-slider-handle.ui-state-active:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tips_hover_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .ui-slider-handle.ui-state-hover:before, {{WRAPPER}} .ui-slider-handle.ui-state-active:before',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tips_scale',
			[
				'label'        => __( 'Scale on hover', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'the7mk2' ),
				'label_off'    => __( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'tips-scale-',
				'separator'    => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add price style controls.
	 */
	protected function add_price_styles_controls() {
		$this->start_controls_section(
			'price_section',
			[
				'label' => __( 'Price', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labler_typography',
				'label'    => __( 'Label Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .from-label, {{WRAPPER}} .to-label',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => __( 'Label Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .from-label, {{WRAPPER}} .to-label, {{WRAPPER}} .dash-label' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .from, {{WRAPPER}} .to, {{WRAPPER}} .dash-label',
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => __( 'Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .from, {{WRAPPER}} .to, {{WRAPPER}} .dash-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		if ( ! $this->isPreview() && ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}

		global $wp;

		$settings = $this->get_settings_for_display();

		// Round values to nearest 10 by default.
		$step = max( apply_filters( 'woocommerce_price_filter_widget_step', 10 ), 1 );

		// Find min and max price in current result set.
        if ($this->isPreview()){
	        $min_price = 1;
	        $max_price = 100;
        }
        else{
	        $wc_widget_price_filter = new The7_WC_Widget_Price_Filter();
	        $prices    = $wc_widget_price_filter->get_filtered_price();
	        $min_price = $prices->min_price;
	        $max_price = $prices->max_price;
        }

		Woocommerce_Support::add_fake_wc_query();

		// Check to see if we should add taxes to the prices if store are excl tax but display incl.
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

		if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.

			$tax_rates = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
				$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
			}
		}

		$min_price = apply_filters( 'woocommerce_price_filter_widget_min_amount', floor( $min_price / $step ) * $step );
		$max_price = apply_filters( 'woocommerce_price_filter_widget_max_amount', ceil( $max_price / $step ) * $step );

		// If both min and max are equal, we don't need a slider.
		if ( $min_price === $max_price ) {
			return;
		}

		$current_min_price = isset( $_GET['min_price'] ) ? floor( floatval( wp_unslash( $_GET['min_price'] ) ) / $step ) * $step : $min_price; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_max_price = isset( $_GET['max_price'] ) ? ceil( floatval( wp_unslash( $_GET['max_price'] ) ) / $step ) * $step : $max_price; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( '' === get_option( 'permalink_structure' ) ) {
			$form_action = remove_query_arg( array( 'page', 'paged', 'product-page' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
		}
		if ( $settings['toggle'] === 'yes' ) {
			$this->add_render_attribute( 'product-price-filter', 'class', 'collapsible' );
			$this->add_render_attribute( 'product-price-filter', 'class', $settings['toggle_closed_by_default'] );
			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'filter-container', 'style', 'display:none' );
			}
		}
		$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
		if ( empty( $settings['title_text'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'empty' );
		}

		$this->add_render_attribute( 'filter-container', 'class', 'filter-container' );
		$this->add_render_attribute( 'product-price-filter', 'class', 'the7-product-price-filter widget_price_filter' );
		if ( $settings['price_range_display'] === 'above' ) {
			$price_label_min = $settings['min_text'];
			$price_label_max = $settings['max_text'];
		} else {
			$price_label_min = $settings['price_text'];
			$price_label_max = '';
		}
		?>
		<div <?php $this->print_render_attribute_string( 'product-price-filter' ); ?>>
		<div class="filter-header widget-title">
				<div <?php $this->print_render_attribute_string( 'filter-title' ); ?>>
					<?php echo esc_html( $settings['title_text'] ); ?>
				</div>
				<?php if ( ! empty( $settings['toggle_icon']['value'] ) ) : ?>
					<div class="filter-toggle-icon">
						<span class="elementor-icon filter-toggle-closed">
							<?php Icons_Manager::render_icon( $settings['toggle_icon'] ); ?>
						</span>
						<?php if ( ! empty( $settings['toggle_active_icon']['value'] ) ) : ?>
							<span class="elementor-icon filter-toggle-active">
								<?php Icons_Manager::render_icon( $settings['toggle_active_icon'] ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div <?php $this->print_render_attribute_string( 'filter-container' ); ?> >
				<form method="get" action="<?php echo esc_url( $form_action ); ?>">
					<div class="price_slider_wrapper">

						<div class="price_slider_inner_wrapper_wrapper">
							<div class="price_slider" style="display:none;"></div>
						</div>

						<?php
						if ( $settings['price_range_display'] === 'next' ) {
							echo '<div class="wrap-btn-price">';
						}
						?>
						<div class="price_slider_amount" data-step="<?php echo esc_attr( $step ); ?>">

							<input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( $current_min_price ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php echo esc_attr__( 'Min price', 'the7mk2' ); ?>" />
							<input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( $current_max_price ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr__( 'Max price', 'the7mk2' ); ?>" />

							<div class="price_label" style="display:none;">
								<div class="min-wrap"><span class="from-label"><?php echo esc_html( $price_label_min ); ?></span> <span class="from"></span>
								<?php if ( $settings['price_range_display'] === 'next' ) { ?>
									<span class="dash-label"> &mdash; </span>
								<?php } ?></div>
								<div class="max-wrap"><span class="to-label"><?php echo esc_html( $price_label_max ); ?></span> <span class="to"></span></div>
							</div>
							<?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
							<div class="clear"></div>
						</div>
						<?php
						// Cleanup button render attributes.
						$this->remove_render_attribute( 'box-button' );

						$this->add_render_attribute( 'box-button', 'class', 'button' );

						$this->template( Button::class )->render_button(
							'box-button',
							esc_html( $settings['button_text'] ),
							'button'
						);

						if ( $settings['price_range_display'] === 'next' ) {
							echo '</div>';
						}
						?>
					</div>
				</form>
		<?php
		echo '</div>';
		echo '</div>';
	}
	private function isPreview() {
		return $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode();
	}
}
