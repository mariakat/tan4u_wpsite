<?php
/**
 * The7 Product Categories widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use The7\Mods\Compatibility\Elementor\Walkers\Product_Cat_List as Product_Cat_List_Walker;

defined( 'ABSPATH' ) || exit;

/**
 * Product_Categories class.
 */
class Product_Categories extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7_product-categories';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return __( 'Product categories list', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-product-categories';
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-product-categories.js',
			[ 'jquery' ]
		);
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-product-categories.css'
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'woocommerce-elements', 'shop', 'store', 'categories', 'product' ];
	}

	/**
	 * Get the7 widget categories.
	 *
	 * @return string[]
	 */
	protected function the7_categories() {
		return [ 'woocommerce-elements' ];
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'the7mk2' ),
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'   => __( 'Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Widget title',

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
				'condition'    => [
					'widget_title_text!' => '',
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
					'toggle!'            => '',
					'widget_title_text!' => '',
				],
				'prefix_class' => '',
				'render_type'  => 'template',
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
					'toggle!'            => '',
					'widget_title_text!' => '',
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
					'widget_title_text!'  => '',
				],
				'separator'        => 'after',
			]
		);

		$this->add_control(
			'submenu_display',
			[
				'label'              => __( 'Display the subcategories', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'on_click',
				'options'            => [
					'always'         => __( 'Standard', 'the7mk2' ),
					'all_categories' => __( 'All categories at once', 'the7mk2' ),
					'only_children'  => __( 'Only children of the category', 'the7mk2' ),
					'on_click'       => __( 'Drop down', 'the7mk2' ),
				],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'show_hierarchical',
			[
				'label'        => __( 'Show hierarchy', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'submenu_display' => 'all_categories',
				],
				'return_value' => 'y',
				'default'      => 'y',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'              => __( 'Order by', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'name',
				'options'            => [
					'order' => __( 'Category order', 'the7mk2' ),
					'name'  => __( 'Name', 'the7mk2' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'count',
			[
				'label'        => __( 'Product counts', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'default'      => 'y',
				'return_value' => 'y',
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'        => __( 'empty categories', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'default'      => 'y',
				'return_value' => 'y',
			]
		);

		$this->add_control(
			'max_depth',
			[
				'label'      => __( 'Maximum depth', 'the7mk2' ),
				'type'       => Controls_Manager::TEXT,
				'default'    => '',
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '=',
									'value'    => 'all_categories',
								],
								[
									'name'     => 'show_hierarchical',
									'operator' => '=',
									'value'    => 'y',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '!=',
									'value'    => 'all_categories',
								],
							],
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_icon',
			[
				'label'     => __( 'Subcategory Indicator Icons', 'the7mk2' ),
				'condition' => [
					'submenu_display' => 'on_click',
				],
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'       => __( 'Main level icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-right',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label'       => __( 'Main level active icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'selected_icon[value]!' => '',
				],
			]
		);
		$this->add_control(
			'selected_sub_icon',
			[
				'label'       => __( 'Secondary levels icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-right',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'description' => esc_html__( 'If none is selected, inherits from â€œMain levelâ€ icon', 'the7mk2' ),
			]
		);

		$this->add_control(
			'selected_sub_active_icon',
			[
				'label'       => __( 'Secondary levels Active icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'selected_icon[value]!' => '',
				],
				'description' => esc_html__( 'If none is selected, inherits from â€œMain levelâ€ icon', 'the7mk2' ),
			]
		);

		$this->end_controls_section();

		// Style.
		$this->add_widget_title_style_controls();

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => __( 'Main categories', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'list_heading',
			[
				'label' => __( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-product-categories > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0;',
					'{{WRAPPER}}.widget-divider-yes .dt-product-categories > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .dt-product-categories > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .dt-product-categories' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'elementor' ),
				'label_on'     => __( 'On', 'elementor' ),
				'prefix_class' => 'widget-divider-',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-product-categories > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.widget-divider-yes .dt-product-categories > li:first-child:before' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}} .first-item-border-hide.dt-product-categories > li:first-child:before' => ' border-top-style: none;',
					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide.dt-product-categories > li:first-child' => 'padding-top: 0;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide.dt-product-categories > li:last-child:after' => 'border-bottom-style: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide.dt-product-categories > li:last-child' => 'padding-bottom: 0;',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.widget-divider-yes .dt-product-categories > li:after, {{WRAPPER}}.widget-divider-yes .dt-product-categories > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_first_border',
			[
				'label'        => __( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_last_border',
			[
				'label'        => __( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'items_heading',
			[
				'label'     => __( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'align_items',
			[
				'label'                => __( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-product-categories_align-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: center; text-align: left; --justify-count: flex-start',
					'center' => 'justify-content: center; align-items: center; text-align: center; --justify-count: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right; --justify-count: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-product-categories > li > a' => ' --justify-count: {{VALUE}};',
					'{{WRAPPER}} .dt-product-categories > li > a, {{WRAPPER}} .dt-product-categories > li > a .item-content' => ' {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-product-categories > li > a',
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label'                => __( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'with_text' => [
						'title' => __( 'With text', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'side'      => [
						'title' => __( 'Side', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'with_text',
				'toggle'               => false,
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
				'condition'            => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-product-categories' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-product-categories > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-product-categories > li > a .next-level-button, {{WRAPPER}} .dt-product-categories > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_basic_responsive_control(
			'icon_space',
			[
				'label'     => __( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories' => '--icon-spacing: {{SIZE}}{{UNIT}}',

					'{{WRAPPER}} .dt-product-categories > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .dt-icon-align-side .dt-product-categories > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-product-categories_align-center .dt-icon-align-side .dt-product-categories > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);
		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
			'border_menu_item_width',
			[
				'label'      => __( 'Border width', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-product-categories > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_menu_item',
			[
				'label'      => __( 'Item paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-product-categories > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dt-product-categories > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'category_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);


		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-product-categories > li > a svg'                => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a .next-level-button:hover ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .dt-product-categories > li > a svg:hover' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_hover',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_hover',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li > a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li.current-cat > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li.open-sub > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-product-categories > li.open-sub > a svg'                 => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_menu_item_active',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li.current-cat > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_menu_item_active',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-product-categories > li.current-cat > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		
		$this->add_control(
			'items_count',
			[
				'label'     => __( 'Items Count', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$selector = '{{WRAPPER}} .dt-product-categories > li > a .count';

		$this->add_control(
			'item_count_align',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'toggle'               => false,
				'selectors_dictionary' => [
					'right' => 'justify-content: space-between;',
					'left'  => 'justify-content: var(--justify-count);',
				],
				'selectors'            => [
					'{{WRAPPER}} .dt-product-categories > li > a' => ' {{VALUE}};',
				],
				'prefix_class'         => 'category-count-align-',
				'condition'            => [
					'count'          => 'y',
					'icon_alignment' => 'side',
				],
			]
		);

		$this->add_control(
			'item_count_align_hidden',
			[
				'label'        => __( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::HIDDEN,
				'default'      => 'left',
				'prefix_class' => 'filter-count-align-',
				'condition'    => [
					'count' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'item_count_typography',
				'selector'  => $selector,
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'item_count_border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					$selector => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
				'condition'  => [
					'count' => 'y',
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
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'item_count_min_width',
			[
				'label'      => __( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector     => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => ' --count-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'item_count_min_height',
			[
				'label'      => __( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector     => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => ' --count-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'item_count_space',
			[
				'label'     => __( 'Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					$selector     => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => ' --count-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->start_controls_tabs( 'item_count_tabs_style' );

		$this->add_items_count_tab_controls( 'normal_', __( 'Normal', 'the7mk2' ) );

		$this->add_items_count_tab_controls( 'hover_', __( 'Hover', 'the7mk2' ) );

		$this->add_items_count_tab_controls( 'active_', __( 'Active', 'the7mk2' ) );

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label'      => __( 'Sub Categories', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '=',
									'value'    => 'all_categories',
								],
								[
									'name'     => 'show_hierarchical',
									'operator' => '=',
									'value'    => 'y',
								],
							],
						],
						[
							'terms' => [
								[
									'name'     => 'submenu_display',
									'operator' => '!=',
									'value'    => 'all_categories',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label' => __( 'List', 'the7mk2' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu',
			[
				'label'      => __( '2 menu level Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-product-categories > li > .children' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_sub_menu',
			[
				'label'      => __( '3+ menu level Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .children .children' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '0',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .children > li:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}); margin-bottom: 0; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:first-child' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .children .children > li:first-child' => 'margin-top: calc({{SIZE}}{{UNIT}}/2); padding-top: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}} .first-sub-item-border-hide.dt-product-categories > li > .children > li:first-child' => 'padding-top: 0;',

					'{{WRAPPER}}.sub-widget-divider-yes .children > li:last-child' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .children .children > li:last-child' => 'margin-bottom: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.sub-widget-divider-yes .last-sub-item-border-hide.dt-product-categories > li > .children > li:last-child' => 'padding-bottom: 0;',
					'{{WRAPPER}} .dt-product-categories > li > .children .children' => 'margin-bottom: calc(-{{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'sub_divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'elementor' ),
				'label_on'     => __( 'On', 'elementor' ),
				'prefix_class' => 'sub-widget-divider-',
			]
		);

		$this->add_control(
			'sub_divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:first-child:before' => 'border-top-style: {{VALUE}};',

					'{{WRAPPER}} .first-sub-item-border-hide .children > li:first-child:before' => ' border-top-style: none;',

					'{{WRAPPER}} .last-sub-item-border-hide .children > li:last-child:after, {{WRAPPER}} .last-sub-item-border-hide .children .children > li:last-child:after' => ' border-bottom-style: none;',
				],
			]
		);

		$this->add_control(
			'sub_divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes' => '--divider-sub-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'sub_divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'sub_divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.sub-widget-divider-yes .children > li:after, {{WRAPPER}}.sub-widget-divider-yes .children > li:before' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_sub_first_border',
			[
				'label'        => __( 'First Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_sub_last_border',
			[
				'label'        => __( 'Last Divider', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'sub_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'sub_item_heading',
			[
				'label'     => __( 'Item', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'align_sub_items',
			[
				'label'                => __( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'prefix_class'         => 'dt-sub-category_align-',
				'selectors_dictionary' => [
					'left'   => 'justify-content: flex-start; align-items: center; text-align: left; --justify-count: flex-start',
					'center' => 'justify-content: center; align-items: center; text-align: center; --justify-count: center;',
					'right'  => 'justify-content: flex-end;  align-items: flex-end; text-align: right; --justify-count: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .children > li' => ' --justify-count: {{VALUE}};',
					'{{WRAPPER}} .children > li > a .item-content' => ' {{VALUE}};',
					'{{WRAPPER}}.dt-sub-menu_align-center .dt-sub-icon-align-side .children > li a' => 'padding: 0 var(--icon-size);',

					'(desktop) {{WRAPPER}}.dt-sub-menu_align-left .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--icon-size) + var(--icon-spacing)) 0 0',
					'(desktop) {{WRAPPER}}.dt-sub-menu_align-right .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--icon-size) + var(--icon-spacing)) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--sub-icon-size) + var(--sub-icon-spacing)) 0 0',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--sub-icon-size) + var(--sub-icon-spacing)) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-center .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--sub-icon-size) + var(--sub-icon-spacing))',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left .dt-sub-icon-align-side .children > li a' => 'padding: 0 calc(var(--sub-icon-size) + var(--sub-icon-spacing)) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right .dt-sub-icon-align-side .children > li a' => ' padding: 0 aclc(var(--sub-icon-size) + var(--sub-icon-spacing)) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center .dt-sub-icon-align-side .children > li a' => 'padding: 0 alc(var(--sub-icon-size) + var(--sub-icon-spacing))',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .children > li, {{WRAPPER}} .children > li a',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'                => __( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'with_text' => [
						'title' => __( 'With text', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'side'      => [
						'title' => __( 'Side', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => 'with_text',
				'toggle'               => false,
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'justify-content: space-between;',
				],
				'condition'            => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'sub_icon_size',
			[
				'label'      => __( 'Indicator size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .children' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .children > li > a .next-level-button i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .children > li > a .next-level-button, {{WRAPPER}} .children > li > a .next-level-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_icon_space',
			[
				'label'     => __( 'Indicator Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .children' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .children > li > a  .next-level-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-align-side .dt-product-categories > li > a .item-content ' => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop) {{WRAPPER}}.dt-sub-menu_align-center .dt-sub-icon-align-side .children > li > a  .item-content ' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);
		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'sub_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_basic_responsive_control(
			'border_sub_menu_item_width',
			[
				'label'      => __( 'Border width', 'the7mk2' ),
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
					'{{WRAPPER}} .children li a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu_item',
			[
				'label'      => __( 'Item paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .children li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .dt-sub-icon-align-side .children li a' => 'padding-right: calc({{RIGHT}}{{UNIT}} + var(--sub-icon-size) + var(--sub-icon-spacing));',
					'{{WRAPPER}}.sub-category-count-align-left.dt-sub-menu_align-center .dt-sub-icon-align-side .children li a, {{WRAPPER}}.sub-category-count-align-right.dt-sub-menu_align-center .dt-sub-icon-align-side .children li a' => 'padding-right: calc({{RIGHT}}{{UNIT}} + var(--sub-icon-size) + var(--sub-icon-spacing)); padding-left: calc({{LEFT}}{{UNIT}} + var(--sub-icon-size) + var(--sub-icon-spacing));',
				],
			]
		);

		$this->add_basic_responsive_control(
			'menu_sub_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .children li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subcategory_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);


		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		$this->start_controls_tab(
			'tab_sub_menu_item_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .children > li > a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .children > li > a svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_hover',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_hover_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dt-sub-menu-display-on_click .children > li > a .next-level-button:hover, {{WRAPPER}} .dt-sub-menu-display-on_item_click .children > li > a:hover .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .dt-sub-menu-display-on_click .children > li > a svg:hover,  {{WRAPPER}} .dt-sub-menu-display-on_item_click .children > li > a:hover svg'                                    => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_hover',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_hover',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_active',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_active',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sub_menu_icon_active_color',
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .children .current-cat a .next-level-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .children .current-cat a svg'                => 'fill: {{VALUE}};',
				],
				'condition' => [
					'selected_icon[value]!' => '',
					'submenu_display'       => 'on_click',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu_item_active',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_item_active',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .children li.current-cat > a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		
		$this->add_control(
			'sub_items_count',
			[
				'label'     => __( 'Items Count', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$selector = '{{WRAPPER}} .dt-product-categories  .children .count';

		$this->add_control(
			'sub_item_count_align',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => __( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'toggle'               => false,
				'selectors_dictionary' => [
					'right' => 'justify-content: space-between;',
					'left'  => 'justify-content: var(--justify-count);',
				],
				'selectors'            => [
					'{{WRAPPER}} .children > li a' => ' {{VALUE}};',
				],
				'prefix_class'         => 'sub-category-count-align-',
				'condition'            => [
					'count'              => 'y',
					'sub_icon_alignment' => 'side',
				],
			]
		);

		$this->add_control(
			'sub_item_count_align_hidden',
			[
				'label'     => __( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::HIDDEN,
				'default'   => 'left',
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_item_count_typography',
				'selector'  => $selector,
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_item_count_border_width',
			[
				'label'      => __( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					$selector => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_item_count_border_radius',
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
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_item_count_min_width',
			[
				'label'      => __( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector               => 'min-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .children' => ' --sub-count-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			'sub_item_count_min_height',
			[
				'label'      => __( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector     => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => ' --sub-count-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'count' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_item_count_space',
			[
				'label'     => __( 'Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					$selector               => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .children' => ' --count-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->start_controls_tabs( 'sub_item_count_tabs_style' );

		$this->add_sub_items_count_tab_controls( 'normal_', __( 'Normal', 'the7mk2' ) );

		$this->add_sub_items_count_tab_controls( 'hover_', __( 'Hover', 'the7mk2' ) );

		$this->add_sub_items_count_tab_controls( 'active_', __( 'Active', 'the7mk2' ) );

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_items_count_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.current-cat';
		}

		$is_hover = '';
		if ( $prefix_name === 'hover_' ) {
			$is_hover = ':hover';
		}

		$selector = '{{WRAPPER}} .dt-product-categories > li' . $extra_class . ' > a' . $is_hover . ' .count';

		$this->start_controls_tab(
			$prefix_name . 'item_count_style',
			[
				'label'     => $box_name,
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_color',
			[
				'label'     => __( 'Text  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->end_controls_tab();
	}

	protected function add_sub_items_count_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.current-cat';
		}

		$is_hover = '';
		if ( $prefix_name === 'hover_' ) {
			$is_hover = ':hover';
		}

		$selector = '{{WRAPPER}} .children .cat-item' . $extra_class . ' a' . $is_hover . ' .count';

		$this->start_controls_tab(
			$prefix_name . 'sub_item_count_style',
			[
				'label'     => $box_name,
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'sub_item_count_color',
			[
				'label'     => __( 'Text  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'sub_item_count_background_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'sub_item_count_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'count' => 'y',
				],
			]
		);

		$this->end_controls_tab();
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		global $wp_query;

		$settings           = $this->get_settings_for_display();
		$hierarchical       = ( $settings['show_hierarchical'] === '' && $settings['submenu_display'] === 'all_categories' ) || $settings['submenu_display'] === 'only_children' ? false : true;
		$show_children_only = $settings['submenu_display'] === 'only_children';

		$this->add_render_attribute( 'product-categories', 'class', 'the7-product-categories' );

		if ( $settings['toggle'] === 'yes' ) {
			$this->add_render_attribute( 'product-categories', 'class', 'collapsible' );

			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'dt-product-categories', 'style', 'display:none' );
			}
		}

		$list_args = [
			'show_count'   => isset( $settings['count'] ) ? $settings['count'] : '',
			'hierarchical' => $hierarchical,
			'taxonomy'     => 'product_cat',
			// Inverted logic here. On purpose.
			'hide_empty'   => $settings['hide_empty'] !== 'y',
			'walker'       => new Product_Cat_List_Walker( $this ),
		];
		$max_depth = isset( $settings['max_depth'] ) ? absint( $settings['max_depth'] ) : 0;

		$list_args['menu_order'] = false;
		$list_args['depth']      = $max_depth;
		if ( isset( $settings['orderby'] ) && 'order' === $settings['orderby'] ) {
			$list_args['orderby']  = 'meta_value_num';
			$list_args['meta_key'] = 'order';
		}

		$current_cat   = false;
		$cat_ancestors = [];

		if ( $wp_query && is_tax( 'product_cat' ) ) {
			$current_cat   = $wp_query->queried_object;
			$cat_ancestors = get_ancestors( $current_cat->term_id, 'product_cat' );

		} elseif ( is_singular( 'product' ) ) {
			$terms = wc_get_product_terms(
				$post->ID,
				'product_cat',
				apply_filters(
					'woocommerce_product_categories_widget_product_terms_args',
					[
						'orderby' => 'parent',
						'order'   => 'DESC',
					]
				)
			);

			if ( $terms ) {
				$main_term     = apply_filters( 'woocommerce_product_categories_widget_main_term', $terms[0], $terms );
				$current_cat   = $main_term;
				$cat_ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
			}
		}
		$list_args['echo']                       = false;
		$list_args['title_li']                   = '';
		$list_args['pad_counts']                 = 1;
		$list_args['show_option_none']           = __( 'No product categories exist.', 'woocommerce' );
		$list_args['current_category']           = $current_cat ? $current_cat->term_id : '';
		$list_args['current_category_ancestors'] = $cat_ancestors;
		$list_args['max_depth']                  = $max_depth;
		if ( $show_children_only && $current_cat ) {
			if ( $hierarchical ) {
				$include = array_merge(
					$cat_ancestors,
					[ $current_cat->term_id ],
					get_terms(
						'product_cat',
						[
							'fields'       => 'ids',
							'parent'       => 0,
							'hierarchical' => true,
							'hide_empty'   => false,
						]
					),
					get_terms(
						'product_cat',
						[
							'fields'       => 'ids',
							'parent'       => $current_cat->term_id,
							'hierarchical' => true,
							'hide_empty'   => false,
						]
					)
				);
				// Gather siblings of ancestors.
				if ( $cat_ancestors ) {
					foreach ( $cat_ancestors as $ancestor ) {
						$include = array_merge(
							$include,
							get_terms(
								'product_cat',
								[
									'fields'       => 'ids',
									'parent'       => $ancestor,
									'hierarchical' => false,
									'hide_empty'   => false,
								]
							)
						);
					}
				}
			} else {
				// Direct children.
				$include = get_terms(
					'product_cat',
					[
						'fields'       => 'ids',
						'parent'       => $current_cat->term_id,
						'hierarchical' => true,
						'hide_empty'   => false,
					]
				);
			}

			$list_args['include'] = implode( ',', $include );

			if ( empty( $include ) ) {
				return;
			}
		} elseif ( $show_children_only ) {
			$list_args['depth']        = 1;
			$list_args['child_of']     = 0;
			$list_args['hierarchical'] = 1;
		}

		$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
		if ( empty( $settings['widget_title_text'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'empty' );
		}
		$categories_html = wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $list_args ) );

		echo '<div ' . $this->get_render_attribute_string( 'product-categories' ) . '>';

		// Widget title.
		if ( $settings['widget_title_text'] || ! empty( $settings['toggle_icon']['value'] ) ) {
			echo '<div class="filter-header widget-title">';
				echo '<div ' . $this->get_render_attribute_string( 'filter-title' ) . '>';
					echo esc_html( $settings['widget_title_text'] );
				echo '</div>';
			if ( ! empty( $settings['toggle_icon']['value'] ) ) {
				echo '<div class="filter-toggle-icon">';
					echo '<span class="elementor-icon filter-toggle-closed">';
						Icons_Manager::render_icon( $settings['toggle_icon'] );
					echo '</span>';
				if ( ! empty( $settings['toggle_active_icon']['value'] ) ) {
					echo '<span class="elementor-icon filter-toggle-active">';
					Icons_Manager::render_icon( $settings['toggle_active_icon'] );
					echo '</span>';
				}
				echo '</div>';
			}
			echo '</div>';
		}

		$class     = [
			'dt-product-categories',
			'dt-sub-menu-display-' . $settings['submenu_display'],
			'dt-icon-align-' . $settings['icon_alignment'],
			'dt-sub-icon-align-' . $settings['sub_icon_alignment'],
		];
		$switchers = [
			'show_first_border'     => 'first-item-border-hide',
			'show_last_border'      => 'last-item-border-hide',
			'show_sub_first_border' => 'first-sub-item-border-hide',
			'show_sub_last_border'  => 'last-sub-item-border-hide',

		];
		foreach ( $switchers as $control => $class_to_add ) {
			if ( isset( $settings[ $control ] ) && $settings[ $control ] !== 'y' ) {
				$class[] = $class_to_add;
			}
		}
		$this->add_render_attribute( 'dt-product-categories', 'class', $class );

		echo '<ul ' . $this->get_render_attribute_string( 'dt-product-categories' ) . '>';

		echo $categories_html;

		echo '</ul>';
		echo '</div>';
	}

	protected function add_widget_title_style_controls() {
		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => __( 'Widget Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'widget_title_typography',
				'selector'  => '{{WRAPPER}} .filter-title',
				'condition' => [
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'widget_title_text!' => '',
				],
			]
		);

		$this->add_control(
			'widget_title_bottom_margin',
			[
				'label'      => __( 'Spacing Below Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
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
					'#the7-body {{WRAPPER}} .collapsible .dt-product-categories' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'widget_title_text!' => '',
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
				'selectors' => [
					'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'toggle!'            => '',
					'widget_title_text!' => '',
				],
			]
		);

		$this->start_controls_tabs(
			'title_arrow_tabs_style',
			[
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
					'widget_title_text!'  => '',
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
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}};',
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

		$this->end_controls_section();
	}

}