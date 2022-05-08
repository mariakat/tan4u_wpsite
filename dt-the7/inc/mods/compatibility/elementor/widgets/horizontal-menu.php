<?php
/**
 * The7 'Horizontal Menu' widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\Popup\Module as PopupModule;
use The7\Mods\Compatibility\Elementor\Modules\Mega_Menu\Mega_Menu;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal_Menu class.
 */
class Horizontal_Menu extends The7_Elementor_Widget_Base {

	const STICKY_WRAPPER = '.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}';

	/**
	 * Get element name.
	 */
	public function get_name() {
		return 'the7_horizontal-menu';
	}

	/**
	 * Get element title.
	 */
	protected function the7_title() {
		return __( 'Horizontal Menu', 'the7mk2' );
	}

	/**
	 * Get element icon.
	 */
	protected function the7_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'the7-horizontal-menu-widget' ];
	}

	/**
	 * @return string[]
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
			THE7_ELEMENTOR_JS_URI . '/the7-horizontal-menu.js',
			[ 'the7-elementor-frontend-common', ]
		);
	}

	/**
	 * Get element keywords.
	 *
	 * @return string[] Element keywords.
	 */
	protected function the7_keywords() {
		return [ 'nav', 'menu' ];
	}

	/**
	 * Define what element data to export.
	 *
	 * @param array $element Element data.
	 *
	 * @return array Element data.
	 */
	public function on_export( $element ) {
		unset( $element['settings']['menu'] );

		return $element;
	}

	/**
	 * Get available menus list.
	 *
	 * @return array List of menus.
	 */
	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
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

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label'        => __( 'Menu', 'the7mk2' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => $menus,
					'default'      => array_keys( $menus )[0],
					'save_default' => true,
					'description'  => sprintf(
					// translators: %s - edit menu admin page.
						__( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'the7mk2' ),
						admin_url( 'nav-menus.php' )
					),
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => '<strong>' . __( 'There are no menus in your site.', 'the7mk2' ) . '</strong><br>' . sprintf(
						// translators: %s - edit menu admin page.
						__( 'Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'the7mk2' ),
						admin_url( 'nav-menus.php?action=edit&menu=0' )
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_control(
			'parent_is_clickable',
			[
				'label'        => __( 'Parent menu items clickable', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'parent-item-clickable-',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'submenu_display',
			[
				'label'              => __( 'Show submenu on', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'hover',
				'options'            => [
					'hover' => __( 'Hover', 'the7mk2' ),
					'click' => __( 'Click', 'the7mk2' ),
				],
				'prefix_class'       => 'show-sub-menu-on-',
				'condition'          => [
					'parent_is_clickable' => '',
				],
				'render_type'        => 'template',
			]
		);

		$this->add_control(
			'heading_mobile_dropdown',
			[
				'label'     => __( 'Mobile Dropdown', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$breakpoints = Responsive::get_breakpoints();
		$this->add_control(
			'dropdown',
			[
				'label'        => __( 'Breakpoint', 'the7mk2' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'tablet',
				'options'      => [
					/* translators: %d: Breakpoint number. */
					'mobile'  => sprintf( __( 'Mobile (< %dpx)', 'the7mk2' ), $breakpoints['md'] ),
					/* translators: %d: Breakpoint number. */
					'tablet'  => sprintf( __( 'Tablet (< %dpx)', 'the7mk2' ), $breakpoints['lg'] ),
					/* translators: %d: Breakpoint number. */
					'desktop' => sprintf( __( 'Desktop (> %dpx)', 'the7mk2' ), $breakpoints['lg'] ),
					'none'    => __( 'None', 'the7mk2' ),
				],
				//'render_type'  => 'template',
				'prefix_class' => 'horizontal-menu--dropdown-',
				'selectors'            => [
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap:not(.horizontal-menu-dropdown) > .horizontal-menu-toggle' => 'display: inline-flex;',
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap:not(.horizontal-menu-dropdown) > .dt-nav-menu-horizontal--main' => 'display: none;',
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap.horizontal-menu-dropdown > .dt-nav-menu-horizontal--main' => 'display: inline-flex;',

					'(tablet){{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap:not(.horizontal-menu-dropdown) > .horizontal-menu-toggle' => 'display: inline-flex;',
					'(tablet){{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap:not(.horizontal-menu-dropdown) > .dt-nav-menu-horizontal--main' => 'display: none;',
					'(tablet){{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap.horizontal-menu-dropdown > .dt-nav-menu-horizontal--main' => 'display: inline-flex;',

					'(tablet){{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap.horizontal-menu-dropdown > .dt-nav-menu-horizontal--main' => 'display: none;',
					'(mobile){{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap:not(.horizontal-menu-dropdown) > .horizontal-menu-toggle' => 'display: inline-flex;',
					'(mobile){{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap.horizontal-menu-dropdown > .dt-nav-menu-horizontal--main' => 'display: inline-flex;',
				],
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);

		if ( the7_elementor_pro_is_active() ) {
			$this->add_control(
				'dropdown_type',
				[
					'label'        => __( 'Display mobile menu as', 'the7mk2' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'dropdown',
					'options'      => [
						'dropdown' => __( 'Dropdown', 'the7mk2' ),
						'popup'    => __( 'Popup', 'the7mk2' ),
					],
					'frontend_available' => true,
					'render_type'  => 'template',
					'prefix_class' => 'mob-menu-',
					'condition'    => [
						'dropdown!' => 'none',
					],
				]
			);

			$this->add_control(
				'popup_link',
				[
					'label'      => __( 'Popup', 'the7mk2' ),
					'type'       => Controls_Manager::SELECT2,
					'options'    => $this->get_popups_list(),
					'conditions' => [
						'relation' => 'and',
						'terms'    => [
							[
								'name'     => 'dropdown_type',
								'operator' => '=',
								'value'    => 'popup',
							],
							[
								'name'     => 'dropdown',
								'operator' => '!=',
								'value'    => 'none',
							],
						],
					],
				]
			);
		} else {
			// Show only dropdown in case we do not have PRO Elements.
			$this->add_control(
				'dropdown_type',
				[
					'type'         => Controls_Manager::HIDDEN,
					'default'      => 'dropdown',
					'prefix_class' => 'mob-menu-',
					'render_type'  => 'none',
					'condition'    => [
						'dropdown!' => 'none',
					],
				]
			);
		}

		$this->add_control(
			'toggle_text',
			[
				'label'       => __( 'Toggle Text', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,

				'default'     => __( 'Menu', 'the7mk2' ),
				'placeholder' => __( 'Menu', 'the7mk2' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => __( 'Main Menu', 'the7mk2' ),
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
		$this->add_control(
			'items_position',
			[
				'label'              => __( 'Items position', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'start',
				'options'            => [
					'start'     => __( 'Start', 'the7mk2' ),
					'center'    => __( 'Center', 'the7mk2' ),
					'end'       => __( 'End', 'the7mk2' ),
					'around'    => __( 'Space Around', 'the7mk2' ),
					'between'   => __( 'Space Between', 'the7mk2' ),
					'evenly'    => __( 'Space Evenly', 'the7mk2' ),
					'justified' => __( 'Stretch', 'the7mk2' ),
					'fullwidth' => __( 'Equal Width', 'the7mk2' ),
				],
				'render_type'        => 'template',
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => __( 'Gap between items', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],

				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}}' => ' --grid-row-gap: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}} .dt-nav-menu-horizontal > li:not(.item-divider):not(:first-child):not(:last-child) ' => '  padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',

					'{{WRAPPER}}.widget-divider-yes .first-item-border-hide .dt-nav-menu-horizontal > li:nth-child(2)' => 'padding-left: 0',

					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu-horizontal > li:nth-last-child(2)' => 'padding-right: 0',
				],
			]
		);

		$this->add_control(
			'height',
			[
				'label'      => __( 'Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 225,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'height_sticky',
			[
				'label'       => __( 'Change height', 'the7mk2' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 0,
						'max' => 225,
					],
				],
				'selectors'   => [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'description' => sprintf(
					__( 'When “Sticky” and “Transitions On Scroll” are ON for the parent section.', 'the7mk2' )
				),
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'the7mk2' ),
				'label_on'     => __( 'On', 'the7mk2' ),
				'return_value' => 'yes',
				'empty_value'  => 'no',
				'render_type'  => 'template',
				'prefix_class' => 'widget-divider-',
				'separator'    => 'before',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-left-style: {{VALUE}}',
					'{{WRAPPER}} .first-item-border-hide .dt-nav-menu-horizontal > .item-divider:first-child' => 'display: none;',
					'{{WRAPPER}}.widget-divider-yes .last-item-border-hide .dt-nav-menu-horizontal > .item-divider:last-child' => 'display: none;',
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
			'divider_height',
			[
				'label'     => __( 'Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,

				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--divider-height: {{SIZE}}{{UNIT}}',
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
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'condition'    => [
					'divider' => 'yes',
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
					'{{WRAPPER}}.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'sticky_divider_color',
			[
				'label'       => __( 'Change color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [
					'divider' => 'yes',
				],
				'selectors'   => [
					self::STICKY_WRAPPER . '.widget-divider-yes .dt-nav-menu-horizontal > .item-divider' => 'border-color: {{VALUE}}',
				],
				'description' => sprintf(
					__( 'When “Sticky” and “Transitions On Scroll” are ON for the parent section.', 'the7mk2' )
				),
			]
		);

		$this->add_control(
			'submenu_icon',
			[
				'label'     => __( 'Submenu indicator icons', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'       => __( 'Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
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
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a .submenu-indicator i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a .submenu-indicator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'selected_icon[value]!' => '',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--icon-spacing: {{SIZE}}{{UNIT}}',

					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a  .submenu-indicator' => 'margin-left: {{SIZE}}{{UNIT}};',

				],
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'sub_decoration_heading',
			[
				'label'     => __( 'Decoration', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'decoration',
			[
				'label'        => __( 'Decoration', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'the7mk2' ),
				'label_on'     => __( 'On', 'the7mk2' ),
				'prefix_class' => 'items-decoration-',
			]
		);

		$this->add_control(
			'decoration_height',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal' => '--decoration-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a:after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'decoration_position',
			[
				'label'        => __( 'Position', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'decoration-position-',
				'default'      => 'bottom',
				'toggle'       => false,
				'condition'    => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'decoration_direction',
			[
				'label'              => __( 'Direction', 'the7mk2' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'center',
				'options'            => [
					'left-to-right' => __( 'Left to right', 'the7mk2' ),
					'center'        => __( 'From center', 'the7mk2' ),
					'upwards'       => __( 'Upwards', 'the7mk2' ),
					'downwards'     => __( 'Downwards', 'the7mk2' ),
					'fade'          => __( 'Fade', 'the7mk2' ),
				],
				'prefix_class'       => 'decoration-',
				'render_type'        => 'template',
				'condition'          => [
					'decoration!' => '',
				],
			]
		);

		$this->add_control(
			'items_heading',
			[
				'label'     => __( 'Items', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'separator' => 'before',
				'selector'  => ' {{WRAPPER}} .dt-nav-menu-horizontal > li > a .menu-item-text',
			]
		);

		$this->add_basic_responsive_control(
			'padding_menu_item',
			[
				'label'      => __( 'Paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'item_colors',
			[
				'label'     => __( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_menu_item_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_item_normal', [ 'label' => __( 'Normal', 'the7mk2' ) ] );
		$this->add_item_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_item_hover', [ 'label' => __( 'Hover', 'the7mk2' ) ] );
		$this->add_item_color_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_item_active', [ 'label' => __( 'Active', 'the7mk2' ) ] );
		$this->add_item_color_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'item_color_sticky',
			[
				'label'       => __( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => sprintf(
					__( 'When “Sticky” and “Transitions On Scroll” are ON for the parent section.', 'the7mk2' )
				),
			]
		);

		$this->start_controls_tabs(
			'tabs_menu_item_sticky_style',
			[
				'condition' => [
					'item_color_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_menu_item_sticky', [ 'label' => __( 'Normal', 'the7mk2' ) ] );
		$this->add_item_color_sticky_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_menu_item_sticky_hover', [ 'label' => __( 'Hover', 'the7mk2' ) ] );
		$this->add_item_color_sticky_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_menu_item_sticky_active', [ 'label' => __( 'Active', 'the7mk2' ) ] );
		$this->add_item_color_sticky_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub-menu',
			[
				'label' => __( 'Drop down & mobile menu', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'sub_menu_gap',
			[
				'label'      => __( 'Submenu Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}}' => '--sub-menu-gap: {{TOP}}{{UNIT}}; --sub-menu-right-gap: {{RIGHT}}{{UNIT}}; --sub-menu-left-gap: {{LEFT}}{{UNIT}}; --sub-menu-bottom-gap: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'top: calc(100% + {{TOP}}{{UNIT}});',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_menu_position',
			[
				'label'                => __( 'Submenu Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
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
				'options'              => [
					'left'    => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left'    => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'calc(var(--first-level-submenu-offset))',
							'right'             => 'auto',
							'first-item-offset' => '0px',
							'last-item-offset'  => 'auto',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'right'   => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'auto',
							'right'             => 'calc(var(--first-level-submenu-offset))',
							'first-item-offset' => 'auto',
							'last-item-offset'  => '0px',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'center'  => $this->combine_to_css_vars_definition_string(
						[
							'position'          => 'relative',
							'width'             => 'var(--sub-menu-width)',
							'sub-width'         => '100%',
							'sub-left'          => '0px',
							'sub-paddings'      => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 0px)) var(--submenu-padding-right, 20px) var(--submenu-padding-bottom, 20px) var(--submenu-padding-left, 20px)',
							'sub-margins'       => '0 var(--sub-menu-right-gap, 0px) 0 var(--sub-menu-left-gap, 0px)',
							'left'              => 'auto',
							'right'             => 'auto',
							'first-item-offset' => 'auto',
							'last-item-offset'  => 'auto',
							'submenu-max-width' => 'var(--default-submenu-max-width)',
						]
					),
					'justify' => $this->combine_to_css_vars_definition_string(
						[
							'position'                   => 'static',
							'width'                      => 'calc(100vw - var(--sub-menu-right-gap, 0px) - var(--sub-menu-left-gap, 0px))',
							'sub-width'                  => 'calc(100% - var(--sub-menu-right-gap, 0px) - var(--sub-menu-left-gap, 0px))',
							'sub-left'                   => 'var(--sub-menu-left-gap, 0px)',
							'sub-paddings'               => 'calc(var(--sub-menu-gap, 0px) + var(--submenu-padding-top, 20px)) calc(var(--sub-menu-right-gap, 0px) + var(--submenu-padding-right, 20px)) var(--submenu-padding-bottom, 20px) calc(var(--sub-menu-left-gap, 0px) + var(--submenu-padding-left, 20px))',
							'sub-margins'                => '0',
							'left'                       => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'right'                      => 'auto',
							'first-item-offset'          => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'first-level-submenu-offset' => 'calc(var(--dynamic-justified-submenu-left-offset) + var(--sub-menu-left-gap, 0px))',
							'last-item-offset'           => 'auto',
							'submenu-max-width'          => 'calc(100vw - var(--scrollbar-width, 0px))',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-menu-wrap' => '{{VALUE}}',
					'{{WRAPPER}} .dt-nav-menu-horizontal .depth-0 > .horizontal-sub-nav' => '{{VALUE}}',
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap' => 'align-items: center;',
					'(tablet) {{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap' => 'align-items: center;',
					'(mobile) {{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap' => 'align-items: center;',
				],
				'prefix_class'         => 'sub-menu-position%s-',
			]
		);

		$this->add_control(
			'submenu_heading',
			[
				'label'     => __( 'Box', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'sub_menu_width',
			[
				'label'      => __( 'Background width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vw' ],

				'range'      => [
					'px' => [
						'max' => 1000,
					],
					'vw' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav' => 'min-width: calc({{SIZE}}{{UNIT}}); --sub-menu-width: {{SIZE}}{{UNIT}};',
					' {{WRAPPER}} .horizontal-menu-dropdown' => ' --sub-menu-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu',
			[
				'label'      => __( 'Background Paddings', 'the7mk2' ),
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
					'{{WRAPPER}}' => '--submenu-padding-top: {{TOP}}{{UNIT}}; --submenu-padding-right: {{RIGHT}}{{UNIT}}; --submenu-padding-bottom: {{BOTTOM}}{{UNIT}}; --submenu-padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					// '{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav' => 'padding:  calc(var(--sub-menu-gap, 0px) + {{TOP}}{{UNIT}}) calc(var(--sub-menu-right-gap, 0px) + {{RIGHT}}{{UNIT}}) {{BOTTOM}}{{UNIT}} calc(var(--sub-menu-left-gap, 0px) + {{LEFT}}{{UNIT}})',
				],
			]
		);

		$this->add_control(
			'bg_sub_menu',
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'border_sub_menu_width',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => '--submenu-border-right: {{RIGHT}}{{UNIT}}; border-style: solid; border-top-width: {{TOP}}{{UNIT}}; border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_sub_menu_color',
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'submenu_shadow',
				'label'    => __( 'Box Shadow', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav:before, {{WRAPPER}} .dt-nav-menu-horizontal > li > .horizontal-sub-nav .horizontal-sub-nav, {{WRAPPER}} .horizontal-menu-dropdown .dt-nav-menu-horizontal--main',
			]
		);

		$this->add_control(
			'sub_list_heading',
			[
				'label'     => __( 'List', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'sub_rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],

				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav > li:not(:last-child)' => 'padding-bottom: {{SIZE}}{{UNIT}}; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-menu-dropdown .horizontal-sub-nav .horizontal-sub-nav' => 'padding-top: {{SIZE}}{{UNIT}}; --sub-grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_divider',
			[
				'label'        => __( 'Dividers', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'Off', 'the7mk2' ),
				'label_on'     => __( 'On', 'the7mk2' ),
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav li:after' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}} .horizontal-menu-dropdown > ul .horizontal-sub-nav:before' => 'border-bottom-style: {{VALUE}}',
					'{{WRAPPER}} .horizontal-sub-nav li:last-child:after' => ' border-bottom-style: none;',
				],
			]
		);

		$this->add_control(
			'sub_divider_weight',
			[
				'label'     => __( 'Height', 'the7mk2' ),
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav' => '--divider-sub-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}}.sub-widget-divider-yes .horizontal-sub-nav > li:after, {{WRAPPER}} .horizontal-menu-dropdown > ul .horizontal-sub-nav:before' => 'border-color: {{VALUE}} !important',
				],
			]
		);

		$this->add_control(
			'submenu_indicator_icon',
			[
				'label'     => __( 'Submenu indicator icons', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_icon',
			[
				'label'       => __( 'Drop down Icon', 'the7mk2' ),
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
			'dropdown_icon',
			[
				'label'       => __( 'Mobile Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-down',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'dropdown_type' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'dropdown_icon_act',
			[
				'label'       => __( 'Mobile Active Icon', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-caret-up',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'dropdown_icon[value]!' => '',
					'dropdown_type'         => 'dropdown',
				],
			]
		);

		$this->add_control(
			'sub_icon_align',
			[
				'label'                => __( 'Indicator Position', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => is_rtl() ? 'left' : 'right',
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
				'toggle'               => false,
				'selectors_dictionary' => [
					'right' => 'order: 2; margin-left: var(--sub-icon-spacing);',
					'left'  => 'order: 0; margin-right: var(--sub-icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav > li a .submenu-indicator, {{WRAPPER}} .horizontal-menu-dropdown > ul > li a .submenu-indicator' => ' {{VALUE}};',
				],
				'prefix_class'         => 'sub-icon_position-',
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'sub_icon_alignment',
			[
				'label'                => __( 'Indicator Align', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'with_text',
				'options'              => [
					'with_text' => __( 'With text', 'the7mk2' ),
					'side'      => __( 'Side', 'the7mk2' ),
				],
				'selectors_dictionary' => [
					'with_text' => '',
					'side'      => 'flex: 1;',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav > li a .item-content, {{WRAPPER}} .horizontal-sub-nav > li a .menu-item-text, {{WRAPPER}} .horizontal-menu-dropdown > ul > li .item-content' => ' {{VALUE}};',
				],
				'prefix_class'         => 'sub-icon_align-',
				'render_type'          => 'template',
				'conditions'           => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],

					],
				],
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
					'{{WRAPPER}} .horizontal-sub-nav' => '--sub-icon-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .horizontal-sub-nav .submenu-indicator i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-sub-nav .submenu-indicator, {{WRAPPER}} .horizontal-sub-nav .submenu-indicator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_icon_space',
			[
				'label'      => __( 'Indicator Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => '5',
				],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav' => '--sub-icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'sub_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
						[
							'name'     => 'dropdown_icon[value]',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'sub_item_heading',
			[
				'label'     => __( 'Items', 'the7mk2' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'align_sub_items',
			[
				'label'                => __( 'Text alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => is_rtl() ? 'right' : 'left',
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
				'prefix_class'         => 'dt-sub-menu_align%s-',
				'selectors_dictionary' => [
					'left'   => '--h-menu-sub-nav-justify-content: flex-start; --h-menu-sub-nav-align-items: flex-start; --h-menu-sub-nav-text-align: left; --submenu-side-gap: 20px;',
					'center' => '--h-menu-sub-nav-justify-content: center; --h-menu-sub-nav-align-items: center; --h-menu-sub-nav-text-align: center; --submenu-side-gap: 0px;',
					'right'  => '--h-menu-sub-nav-justify-content: flex-end;  --h-menu-sub-nav-align-items: flex-end; --h-menu-sub-nav-text-align: right; --submenu-side-gap: 20px;',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-sub-nav' => '{{VALUE}};',

					// TODO: It looks like there is room for improvement here.
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-left.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(tablet) {{WRAPPER}}.dt-sub-menu_align-tablet-center.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text ' => 'margin: 0 var(--icon-spacing); padding: 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right.sub-icon_position-left.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 0 0 var(--sub-icon-spacing); padding: 0 0 0 var(--sub-icon-size)',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-left.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-tablet-right.sub-icon_position-right.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',
					'(mobile) {{WRAPPER}}.dt-sub-menu_align-right.sub-icon_position-right.sub-icon_align-side:not(.dt-sub-menu_align-tablet-center) .horizontal-sub-nav > li .menu-item-text' => 'margin: 0 var(--sub-icon-spacing) 0 0; padding: 0 var(--sub-icon-size) 0 0',

					'(mobile) {{WRAPPER}}.dt-sub-menu_align-mobile-center.sub-icon_align-side .horizontal-sub-nav > li .menu-item-text ' => 'margin: 0 var(--icon-spacing) !important; padding: 0 var(--sub-icon-size) !important',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sub_menu_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .horizontal-sub-nav > li a .menu-item-text',
				'separator' => 'before',
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
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'border-top-width: {{TOP}}{{UNIT}};
					border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'padding_sub_menu_item',
			[
				'label'      => __( 'Paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .horizontal-sub-nav'      => '--submenu-item-padding-right: {{RIGHT}}{{UNIT}}; --submenu-item-padding-left: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'sub_menu_item_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-sub-nav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'sub_colors_heading',
			[
				'label'     => __( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_sub_menu_item_normal', [ 'label' => __( 'Normal', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_sub_menu_item_hover', [ 'label' => __( 'Hover', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_sub_menu_item_active', [ 'label' => __( 'Active', 'the7mk2' ) ] );
		$this->add_sub_menu_item_color_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'style_toggle',
			[
				'label'     => __( 'Toggle Button', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_basic_responsive_control(
			'toggle_align',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'center',
				'options'              => [
					'left'    => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'the7mk2' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left'    => '--justify: flex-start;',
					'center'  => '--justify: center;',
					'right'   => '--justify: flex-end;',
					'justify' => '--justify: stretch;',
				],
				'selectors'            => [
					'{{WRAPPER}} .horizontal-menu-wrap' => '{{VALUE}}',
					'{{WRAPPER}}.horizontal-menu--dropdown-desktop .horizontal-menu-wrap, {{WRAPPER}} .horizontal-menu-toggle' => 'align-self: var(--justify, center)',
					'(tablet) {{WRAPPER}}.horizontal-menu--dropdown-tablet .horizontal-menu-wrap' => 'align-self: var(--justify, center)',
					'(mobile) {{WRAPPER}}.horizontal-menu--dropdown-mobile .horizontal-menu-wrap' => 'align-self: var(--justify, center)',
				],
				'prefix_class'         => 'toggle-align%s-',
				'condition'            => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_control(
			'toggle_align_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'dropdown!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'toggle_typography',
				'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => ' {{WRAPPER}} .toggle-text',
				'condition' => [
					'toggle_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_typography_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'condition' => [
					'toggle_text!' => '',
				],
			]
		);
		$this->add_control(
			'toggle_icon_heading',
			[
				'label' => __( 'Icon', 'the7mk2' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'       => __( 'Icon to open menu', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'toggle_open_icon',
			[
				'label'       => __( 'Icon to close menu', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'condition'   => [
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_size',
			[
				'label'     => __( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .menu-toggle-icons' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon_align',
			[
				'label'                => __( 'Icon Position', 'the7mk2' ),
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
				'condition'            => [
					'toggle_icon[value]!' => '',
					'toggle_text!'        => '',
				],
				'selectors_dictionary' => [
					'right' => 'order: 0; margin-right: var(--toggle-icon-spacing);',
					'left'  => 'order: 2; margin-left: var(--toggle-icon-spacing);',
				],
				'selectors'            => [
					'{{WRAPPER}} .toggle-text' => ' {{VALUE}};',
				],
				'prefix_class'         => 'toggle-icon_position-',
				'default'              => is_rtl() ? 'left' : 'right',
				'toggle'               => false,
			]
		);

		$this->add_basic_responsive_control(
			'toggle_icon_space',
			[
				'label'     => __( 'Icon Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .horizontal-menu-toggle' => '--toggle-icon-spacing: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'toggle_icon[value]!' => '',
					'toggle_text!'        => '',
				],
			]
		);
		$this->add_control(
			'toggle_box_heading',
			[
				'label'     => __( 'Box', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_basic_responsive_control(
			'padding_toggle',
			[
				'label'      => __( 'Paddings', 'the7mk2' ),
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
					'{{WRAPPER}} .horizontal-menu-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'toggle_min_width',
			[
				'label'     => __( 'Min Width', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .horizontal-menu-toggle' => 'min-width: {{SIZE}}px;',
				],
			]
		);

		$this->add_basic_responsive_control(
			'toggle_min_height',
			[
				'label'     => __( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .horizontal-menu-toggle' => 'min-height: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_border',
				'selector' => '{{WRAPPER}} .horizontal-menu-toggle',
				'exclude'  => [ 'color' ],
			]
		);

		$this->add_responsive_control(
			'toggle_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .horizontal-menu-toggle' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'toggle_colors_heading',
			[
				'label'     => __( 'Colors', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_toggle_style' );

		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_normal', [ 'label' => __( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '' );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_hover', [ 'label' => __( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover' );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_toggle_style_active', [ 'label' => __( 'Active', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_active' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tabs_toggle_sticky',
			[
				'label'       => __( 'Change Colors', 'the7mk2' ),
				'type'        => Controls_Manager::SWITCHER,
				'separator'   => 'before',
				'description' => sprintf(
					__( 'When “Sticky” and “Transitions On Scroll” are ON for the parent section.', 'the7mk2' )
				),
			]
		);

		$this->start_controls_tabs(
			'tabs_toggle_sticky_style',
			[
				'condition' => [
					'tabs_toggle_sticky!' => '',
				],
			]
		);

		// Normal colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_normal', [ 'label' => __( 'Normal', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '', true );
		$this->end_controls_tab();

		// Hover colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_hover', [ 'label' => __( 'Hover', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_hover', true );
		$this->end_controls_tab();

		// Active colors.
		$this->start_controls_tab( 'tab_toggle_style_sticky_active', [ 'label' => __( 'Active', 'the7mk2' ) ] );
		$this->add_toggle_button_color_controls( '_active', true );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @param string $prefix Control name prefix.
	 */
	private function add_item_color_controls( $prefix ) {
		switch ( $prefix ) {
			case '_hover':
				$css_prefix = 'li:not(.act)';
				$selectors  = [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li:not(.act) > a:hover',
					'{{WRAPPER}} .dt-nav-menu-horizontal > li.parent-clicked > a',
				];
				break;
			case '_active':
				$css_prefix = 'li.act';
				$selectors  = [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li.act > a',
				];
				break;
			default:
				$css_prefix = 'li';
				$selectors  = [
					'{{WRAPPER}} .dt-nav-menu-horizontal > li > a',
				];
		}

		$this->add_control(
			'color_menu_item' . $prefix,
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						''     => 'color: {{VALUE}}',
						' svg' => 'fill: {{VALUE}};',
					]
				),
			]
		);

		$this->add_control(
			'icon_color' . $prefix,
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}};',
					]
				),
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_menu_item' . $prefix,
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'background-color: {{VALUE}}' ),
			]
		);

		$this->add_control(
			'border_menu_item' . $prefix,
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		if ( $prefix ) {
			$this->add_control(
				'decoration_menu_item' . $prefix,
				[
					'label'     => __( 'Decoration color', 'the7mk2' ),
					'type'      => Controls_Manager::COLOR,
					'alpha'     => true,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .dt-nav-menu-horizontal > ' . $css_prefix . ' > a:after' => 'background: {{VALUE}}',
					],
					'condition' => [
						'decoration!' => '',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_item_shadow' . $prefix,
				'label'    => __( 'Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * @param  string $prefix Control name prefix.
	 */
	private function add_item_color_sticky_controls( $prefix = '' ) {
		switch ( $prefix ) {
			case '_hover':
				$selectors            = [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li:not(.act) > a:hover',
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li.parent-clicked > a',
				];
				$decoration_selectors = [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li:not(.act) > a',
				];
				break;
			case '_active':
				$selectors            = [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li.act > a',
				];
				$decoration_selectors = $selectors;
				break;
			default:
				$selectors            = [
					self::STICKY_WRAPPER . ' .dt-nav-menu-horizontal > li > a',
				];
				$decoration_selectors = [];
		}

		$item_prefix = '_sticky' . $prefix;

		$this->add_control(
			'color_menu_item' . $item_prefix,
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						''     => 'color: {{VALUE}}',
						' svg' => 'fill: {{VALUE}};',
					]
				),
			]
		);

		$this->add_control(
			'icon_color' . $item_prefix,
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->give_me_megaselectors(
					$selectors,
					[
						' .submenu-indicator' => 'color: {{VALUE}}',
						' svg'                => 'fill: {{VALUE}};',
					]
				),
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'bg_menu_item' . $item_prefix,
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'background-color: {{VALUE}}' ),
			]
		);

		$this->add_control(
			'border_menu_item' . $item_prefix,
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => $this->give_me_megaselectors( $selectors, 'border-color: {{VALUE}}' ),
			]
		);

		if ( $prefix ) {
			$this->add_control(
				'decoration_menu_item' . $item_prefix,
				[
					'label'     => __( 'Decoration color', 'the7mk2' ),
					'type'      => Controls_Manager::COLOR,
					'alpha'     => true,
					'default'   => '',
					'selectors' => $this->give_me_megaselectors(
						$decoration_selectors,
						[
							':after' => 'background: {{VALUE}}',
						]
					),
					'condition' => [
						'decoration!' => '',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_item_shadow' . $item_prefix,
				'label'    => __( 'Shadow', 'the7mk2' ),
				'selector' => implode( ', ', $selectors ),
			]
		);
	}

	/**
	 * @param string $prefix Control name prefix.
	 * @param bool   $is_sticky Set true for sricky control set.
	 */
	private function add_sub_menu_item_color_controls( $prefix, $is_sticky = false ) {
		$wrapper       = '{{WRAPPER}}';
		$sticky_prefix = '';
		if ( $is_sticky ) {
			$wrapper       = self::STICKY_WRAPPER;
			$sticky_prefix = 'sticky_';
		}
		$css_prefix   = 'li';
		$hover_prefix = '';
		switch ( $prefix ) {
			case '_hover':
				$css_prefix   = '> li:not(.act)';
				$hover_prefix = ':hover';
				break;
			case '_active':
				$css_prefix = '> li.act';
				break;
		}

		$selectors[ $wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix ] = 'color: {{VALUE}}';
		if ( empty( $prefix ) ) {
			$selectors[ $wrapper ] = '--submenu-item-color: {{VALUE}}';
		}
		$this->add_control(
			$sticky_prefix . 'color_sub_menu_item' . $prefix,
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $selectors,
			]
		);

		$this->add_control(
			$sticky_prefix . 'sub_menu_icon_color' . $prefix,
			[
				'label'     => __( 'Indicator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix . ' .submenu-indicator' => 'color: {{VALUE}};',
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix . ' .submenu-indicator svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'sub_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			$sticky_prefix . 'bg_sub_menu_item' . $prefix,
			[
				'label'     => __( 'Background color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'selectors' => [
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			$sticky_prefix . 'border_sub_menu_item' . $prefix,
			[
				'label'     => __( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix  => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $sticky_prefix . 'sub_menu_item_shadow' . $prefix,
				'label'    => __( 'Shadow', 'the7mk2' ),
				'selector' => $wrapper . ' .horizontal-sub-nav ' . $css_prefix . ' > a' . $hover_prefix,
			]
		);
	}

	/**
	 * @param string $prefix Control name prefix.
	 * @param bool   $sticky Is sticky control.
	 */
	private function add_toggle_button_color_controls( $prefix, $sticky = false ) {
		$css_prefix       = '';
		$css_hover_prefix = '';
		$sticky_prefix    = '';
		if ( $sticky ) {
			$sticky_prefix = '_sticky_';
		}
		switch ( $prefix ) {
			case '_hover':
				$css_prefix       = ':hover';
				$css_hover_prefix = '.no-touchevents ';
				break;
			case '_active':
				$css_prefix = '.elementor-active';
				break;
		}

		if ( $sticky ) {
			$selector = $css_hover_prefix . self::STICKY_WRAPPER . ' .horizontal-menu-toggle' . $css_prefix;
		} else {
			$selector = $css_hover_prefix . '{{WRAPPER}} .horizontal-menu-toggle' . $css_prefix;
		}

		$this->add_control(
			'toggle_color' . $sticky_prefix . $prefix,
			[
				'label'     => __( 'Text & icon color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector          => 'color: {{VALUE}}',
					$selector . ' svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$fields_options = [
			'background' => [
				'label' => __( 'Background', 'the7mk2' ),
			],
		];

		if ( ! empty( $prefix ) ) {
			$fields_options['color'] = [
				'selectors' => [
					'{{SELECTOR}}' => 'background: {{VALUE}}',
				],
			];
		}

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'           => 'toggle_background_color' . $sticky_prefix . $prefix,
				'types'          => [ 'classic', 'gradient' ],
				'exclude'        => [ 'image' ],
				'fields_options' => $fields_options,
				'selector'       => $selector,
			]
		);

		$this->add_control(
			'toggle_border_color' . $sticky_prefix . $prefix,
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'toggle_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_shadow' . $sticky_prefix . $prefix,
				'selector' => $selector,
			]
		);
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 */
	protected function render() {
		if ( ! $this->get_available_menus() ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$class     = [
			'dt-nav-menu-horizontal--main',
			'dt-nav-menu-horizontal__container',
			'justify-content-' . esc_attr( $settings['items_position'] ),
			'widget-divider-' . esc_attr( $settings['divider'] ),
		];
		$switchers = [
			'show_first_border' => 'first-item-border-hide',
			'show_last_border'  => 'last-item-border-hide',
		];
		foreach ( $switchers as $control => $class_to_add ) {
			if ( isset( $settings[ $control ] ) && $settings[ $control ] !== 'y' ) {
				$class[] = $class_to_add;
			}
		}
		$this->add_render_attribute(
			'main-menu',
			[
				'class' => $class,
				'role'  => 'navigation',
			]
		);

		if ( $settings['selected_icon'] && $settings['selected_icon']['value'] === '' ) {
			$this->add_render_attribute( 'main-menu', 'class', 'indicator-off' );
		}

		echo '<div class="horizontal-menu-wrap">';

		if ( $settings['dropdown'] !== 'none' ) {
			$this->add_render_attribute(
				'menu-toggle',
				[
					'class'         => 'horizontal-menu-toggle',
					'role'          => 'button',
					'tabindex'      => '0',
					'aria-label'    => __( 'Menu Toggle', 'the7mk2' ),
					'aria-expanded' => 'false',
				]
			);

			$tag = 'div';
			if ( $settings['dropdown_type'] === 'popup' && the7_elementor_pro_is_active() ) {
				$this->add_render_attribute( 'menu-toggle', 'href', $this->get_popup_url( $settings['popup_link'] ) );
				$tag = 'a';
			}

			echo "<{$tag} " . $this->get_render_attribute_string( 'menu-toggle' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$this->add_render_attribute(
				'menu-toggle-icon',
				[
					'class'       => $settings['sub_icon_align'] ? $settings['sub_icon_align'] . ' menu-toggle-icons' : '',
					'aria-hidden' => 'true',
					'role'        => 'presentation',
				]
			);

			echo '<span ' . $this->get_render_attribute_string( 'menu-toggle-icon' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			Icons_Manager::render_icon(
				$settings['toggle_icon'],
				[
					'class'       => 'open-button',
					'aria-hidden' => 'true',
				]
			);
			Icons_Manager::render_icon(
				$settings['toggle_open_icon'],
				[
					'class'       => 'icon-active',
					'aria-hidden' => 'true',
				]
			);
			echo '</span>';

			if ( $settings['toggle_text'] !== '' ) {
				echo '<span class="toggle-text">' . esc_html( $settings['toggle_text'] ) . '</span>';
			}

			echo "</{$tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<nav ' . $this->get_render_attribute_string( 'main-menu' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul class="dt-nav-menu-horizontal d-flex flex-row justify-content-' . esc_attr( $settings['items_position'] ) . '">';

		add_filter( 'presscore_nav_menu_link_after', [ $this, 'add_submenu_icons_filter' ], 20, 4 );
		add_filter( 'presscore_nav_menu_el_after', [ $this, 'add_divider_elements_for_the_top_menu_level_filter' ], 20, 4 );

		$mega_menu_handler = new Mega_Menu();
		$mega_menu_handler->add_hooks();

		// Add the first divider if dividers are enabled.
		$items_wrap = $this->add_divider_elements_for_the_top_menu_level_filter( '', null, [], 0 );

		presscore_nav_menu(
			[
				'menu'                => $settings['menu'],
				// Prevent caching by placing unique value.
				'menu_id'             => $this->get_id(),
				'theme_location'      => 'the7_nav-menu',
				'items_wrap'          => $items_wrap . '%3$s',
				'submenu_class'       => 'horizontal-sub-nav',
				'link_before'         => '<span class="item-content">',
				'link_after'          => '</span>',
				'parent_is_clickable' => $settings['parent_is_clickable'],
			]
		);

		$mega_menu_handler->remove_hooks();

		remove_filter( 'presscore_nav_menu_link_after', [ $this, 'add_submenu_icons_filter' ], 20 );
		remove_filter( 'presscore_nav_menu_el_after', [ $this, 'add_divider_elements_for_the_top_menu_level_filter' ], 20 );

		echo '</ul></nav></div>';
	}

	/**
	 * @param string   $link_after A code after a link.
	 * @param WP_Post  $item Menu item data object.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item.
	 *
	 * @return string
	 */
	public function add_submenu_icons_filter( $link_after, $item, $args, $depth ) {
		$settings = $this->get_settings_for_display();

		$sub_menu_icon_html = $this->get_elementor_icon_html(
			( $depth === 0 ? $settings['selected_icon'] : $settings['sub_icon'] ),
			'i',
			[
				'class' => 'desktop-menu-icon',
			]
		);

		$dropdown_icon_html = '';
		if ( $settings['dropdown'] !== 'none' ) {
			$dropdown_icon_html .= $this->get_elementor_icon_html(
				$settings['dropdown_icon'],
				'i',
				[
					'class' => 'mobile-menu-icon',
				]
			);

			$dropdown_icon_html .= $this->get_elementor_icon_html(
				$settings['dropdown_icon_act'],
				'i',
				[
					'class' => 'mobile-act-icon',
				]
			);
		}

		return '<span class="submenu-indicator" >' . $sub_menu_icon_html . '<span class="submenu-mob-indicator" >' . $dropdown_icon_html . '</span></span>' . $link_after;
	}

	/**
	 * @param  string   $after_menu_item  A code after an item.
	 * @param  WP_Post  $item  Page data object. Not used.
	 * @param  stdClass $args  An object of wp_nav_menu() arguments.
	 * @param  int      $depth  Depth of page. Not Used.
	 *
	 * @return string
	 */
	public function add_divider_elements_for_the_top_menu_level_filter( $after_menu_item, $item, $args, $depth ) {
		if ( $depth === 0 && $this->get_settings_for_display( 'divider' ) === 'yes' ) {
			$after_menu_item .= '<li class="item-divider" aria-hidden="true"></li>';
		}

		return $after_menu_item;
	}

	/**
	 * Render widget plain content.
	 *
	 * No plain content here.
	 */
	public function render_plain_content() {
	}

	/**
	 * @return array
	 */
	protected function get_popups_list() {
		$popups_query = new \WP_Query(
			[
				'post_type'      => Source_Local::CPT,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_key'       => '_elementor_template_type',
				'meta_value'     => PopupModule::DOCUMENT_TYPE,
			]
		);

		return wp_list_pluck( $popups_query->posts, 'post_title', 'ID' );
	}

	/**
	 * @param array $popup_id Popup id.
	 *
	 * @return string
	 */
	protected function get_popup_url( $popup_id ) {
		if ( ! $popup_id ) {
			return '';
		}

		$link_action_url = Plugin::instance()->frontend->create_action_hash(
			'popup:open',
			[
				'id'     => $popup_id,
				'toggle' => false,
			]
		);

		PopupModule::add_popup_to_location( $popup_id );

		return $link_action_url;
	}

	/**
	 * @param array $array Array of css vars like ['var' => 'value'].
	 *
	 * @return string
	 */
	protected function combine_to_css_vars_definition_string( $array ) {
		return implode( ' ', presscore_convert_indexed2numeric_array( ':', $array, '--', '%s;' ) );
	}

	/**
	 * @param string[]     $selectors Selectors array.
	 * @param array|string $values Common values with selector prefix.
	 *
	 * @return array
	 */
	protected function give_me_megaselectors( $selectors, $values ) {
		if ( ! is_array( $values ) ) {
			$values = [ '' => $values ];
		}

		$megaselectors = [];
		foreach ( $values as $selector_prefix => $value ) {
			if ( $selector_prefix ) {
				$selectors = array_map(
					static function ( $e ) use ( $selector_prefix ) {
						return $e . $selector_prefix;
					},
					$selectors
				);
			}

			$megaselectors += array_fill_keys( $selectors, $value );
		}

		return $megaselectors;
	}
}
