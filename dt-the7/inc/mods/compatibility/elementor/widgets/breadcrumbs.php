<?php
/**
 * The7 breadcrumb widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;


defined( 'ABSPATH' ) || exit;

class Breadcrumbs extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-breadcrumb';
	}

	protected function the7_title() {
		return __( 'Breadcrumbs', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-navigation-horizontal';
	}

	public function get_style_depends() {
		the7_register_style( 'the7-widget', PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-widget' );

		return [ 'the7-widget' ];
	}
	public function get_script_depends() {
		return [ $this->get_name() ];
	}
	/**
	 * Register widget assets.
	 */
	protected function register_assets() {
		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-breadcrumbs.js',
			[ 'jquery' ]
		);
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_breadcrumb_content',
			[
				'label' => __( 'Content', 'the7mk2' ),
			]
		);
		$this->add_control(
			'separator',
			[
				'label'                => esc_html__( 'Separator Between', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'text'    => [
						'title' => esc_html__( 'Text', 'the7mk2' ),
						'icon'  => 'eicon-font',
					],
					'icon'  => [
						'title' => esc_html__( 'Icon', 'the7mk2' ),
						'icon'  => 'eicon-star',
					],
				],
				'default'              => 'text',
			]
		);
		$this->add_control(
			'meta_separator',
			[
				'label'     => __( 'Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '/',
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li:not(.first):before' => 'content: "{{VALUE}}"',
				],
				'condition' => [
					'separator' => 'text',
				],
			]
		);
		$this->add_control(
			'icon_separator',
			[
				'label'            => __( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before' => 'display: none',
				],
				'condition' => [
					'separator' => 'icon',
				],
				'render_type'          => 'template',
			]
		);

		$this->add_control(
			'show_act_item',
			[
				'label'        => __( 'Current item', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li.current:last-child' => 'display: inline-flex',
				],
				//'render_type'          => 'template',
			]
		);
		$this->add_control(
			'split_items',
			[
				'label'        => __( 'Split into lines', 'the7mk2' ),
				'description' => __( 'If there’s not enough space.', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'empty_value' => 'n',
				'prefix_class' => 'split-breadcrumbs-',
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs' => 'flex-flow: wrap',
				],
				'render_type'          => 'template',
			]
		);
		$this->add_control(
			'title_words_limit',
			[
				'label'       => __( 'Max number of letters in page title', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 100,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_breadcrumb_style',
			[
				'label' => __( 'Style', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_basic_responsive_control(
			'alignment',
			[
				'label'                => __( 'Alignment', 'the7mk2' ),
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
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .breadcrumbs' => 'justify-content: {{VALUE}}',
				],
			]
		);
		$this->add_basic_responsive_control(
			'min_height',
			[
				'label'     => __( 'Min Height', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs' => 'min-height: {{SIZE}}px;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .breadcrumbs',
			]
		);
		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control( 'the7-link-heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Links', 'the7mk2' ),
			'separator' => 'before',
		] );


		$this->start_controls_tabs( 'tabs_link_style' );

		$this->start_controls_tab( 'the7_tab_link_normal', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );
		$this->add_control(
			'link_color',
			[
				'label'     => __( 'Link Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control( 'the7_link_decorator', [
			'label'     => __( 'Underlined Links', 'the7mk2' ),
			'type'      => Controls_Manager::SWITCHER,
			'selectors' => [
				'{{WRAPPER}} .breadcrumbs li > a' => 'text-decoration: underline;',
				'{{WRAPPER}} .breadcrumbs li > a:hover' => 'text-decoration: none;',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'the7_tab_link_hover', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );

		$this->add_control(
			'link_color_hover',
			[
				'label'     => __( 'Link Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li > a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control( 'the7_link_hover_decorator', [
			'label'     => __( 'Underlined Links', 'the7mk2' ),
			'type'      => Controls_Manager::SWITCHER,
			'dynamic'   => [],
			'selectors' => [
				'{{WRAPPER}} .breadcrumbs li > a:hover' => 'text-decoration: underline;',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'the7-separator-heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Separator', 'the7mk2' ),
			'separator' => 'before',
		] );


		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Separator Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_basic_responsive_control(
			'divider_size',
			[
				'label'      => __( 'Separator size', 'the7mk2' ),
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
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_basic_responsive_control(
			'divider_spacing',
			[
				'label'      => __( 'Separator Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 5,
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
					'{{WRAPPER}} .breadcrumbs li:not(:first-child):before, {{WRAPPER}} .breadcrumbs li:not(:first-child) i, {{WRAPPER}} .breadcrumbs li:not(:first-child) svg' => 'margin: 0 {{SIZE}}{{UNIT}}',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$icon_separator = "";
		if ( $settings['icon_separator'] !== '' && $settings['separator'] == 'icon' ) {
			$icon_separator = $this->get_elementor_icon_html( $settings['icon_separator'] );
		}

		$default_args = array(
			'text'              => array(
				'home'     => __( 'Home', 'the7mk2' ),
				'category' => __( 'Category "%s"', 'the7mk2' ),
				'search'   => __( 'Results for "%s"', 'the7mk2' ),
				'tag'      => __( 'Entries tagged with "%s"', 'the7mk2' ),
				'author'   => __( 'Article author %s', 'the7mk2' ),
				'404'      => __( 'Error 404', 'the7mk2' ),
			),
			'showCurrent'       => true,
			'showOnHome'        => true,
			'delimiter'         => '',
			'before'            => '<li class="current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . $icon_separator,
			'after'             => '</li>',
			'linkBefore'        => '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . $icon_separator,
			'linkAfter'         => '</li>',
			'linkAttr'          => ' itemprop="item"',
			'beforeBreadcrumbs' => '',
			'afterBreadcrumbs'  => '',
			'listAttr'          => ' class="breadcrumbs text-small rcrumbs"',
			'itemMaxChrCount' => $settings['title_words_limit'],
		);

		//$args = wp_parse_args( $args, $default_args );

		$breadcrumbs = presscore_get_breadcrumbs( $default_args );

		if ( $breadcrumbs ) {
			echo $breadcrumbs;
		}
	}

}
