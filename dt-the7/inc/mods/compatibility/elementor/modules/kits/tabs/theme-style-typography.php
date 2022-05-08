<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\Modules\Controls\Groups\Group_Control_Typography_CSS_Vars;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Theme_Style_Typography extends The7_Tab_Base {

	function the7_title() {
		return __( 'Typography', 'the7mk2' );
	}

	function the7_id() {
		return 'typography';
	}

	public function get_icon() {
		return 'eicon-typography-1';
	}

	protected function register_tab_controls() {
		$this->add_basic_font_section();
		$this->add_headings_section();
		$this->add_widgets_section();
	}

	private function add_basic_font_section() {
		$wrapper = $this->get_wrapper();
		$this->start_controls_section( 'section_typography_basic_font', [
			'label' => __( 'Basic Font', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$this->add_control( 'the7_base_color', [
			'label'     => __( 'Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			//'the7_save' => true,
			'selectors' => [
				$wrapper => '--the7-base-color: {{VALUE}};',
			],
			'global'    => [
				'default' => Global_Colors::COLOR_PRIMARY,
			],
		] );

		$this->add_control( 'the7-content-secondary_text_color', [
			'label'     => __( 'Secondary Text Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$wrapper => '--the7-secondary-text-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'          => __( 'Typography', 'the7mk2' ),
			'name'           => 'the7-base-typography',
			'css_name'       => 'the7-base',
			'selector'       => $wrapper,
			'fields_options' => [
				'font_size'   => [
					'selectors' => [
						$wrapper => '--the7-text-big-font-size:{{SIZE}}{{UNIT}};--the7-text-small-font-size:{{SIZE}}{{UNIT}};--the7-base-font-size:{{SIZE}}{{UNIT}};',
					],
				],
				'line_height' => [
					'selectors' => [
						$wrapper => '--the7-text-big-line-height:{{SIZE}}{{UNIT}};--the7-text-small-line-height:{{SIZE}}{{UNIT}};--the7-base-line-height:{{SIZE}}{{UNIT}};',
					],
				],
			],
		] );

		$this->add_responsive_control( 'paragraph_spacing', [
			'label'      => __( 'Paragraph Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				'.elementor-widget-text-editor p,
				.elementor-tab-content p,
				.elementor-widget-woocommerce-product-content p,
				.elementor-widget-theme-post-content > .elementor-widget-container >  p,
				#the7-body .elementor-widget-text-editor ul,
				#the7-body .elementor-tab-content ul,
				#the7-body .elementor-widget-woocommerce-product-content ul,
				#the7-body .elementor-widget-theme-post-content > .elementor-widget-container > ul,
				#the7-body .elementor-widget-text-editor ol,
				#the7-body .elementor-tab-content ol,
				#the7-body .elementor-widget-woocommerce-product-content ol,
				#the7-body .elementor-widget-theme-post-content > .elementor-widget-container > ol,
				.the7-elementor-product-comments #reviews .comment-text .description p,
				.elementor-widget-post-comments .comment-content p' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				':root' => '--the7-p-spacing: {{SIZE}}{{UNIT}}',
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'em' => [
					'min' => 0.1,
					'max' => 20,
				],
				'vh' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', 'em', 'vh' ],
		] );

		$this->add_links_controls();

		$this->end_controls_section();
	}

	private function add_links_controls() {

		$this->add_control( 'the7-link-heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Links', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->start_controls_tabs( 'tabs_link_style' );

		$this->start_controls_tab( 'the7_tab_link_normal', [
			'label' => __( 'Normal', 'the7mk2' ),
		] );

		$this->add_control( 'the7_link_color', [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$this->get_wrapper() => '--the7-links-color: {{VALUE}};',
			],
		] );

		$this->add_control( 'the7_link_decorator', [
			'label'     => __( 'Underlined Links', 'the7mk2' ),
			'type'      => Controls_Manager::SWITCHER,
			'dynamic'   => [],
			'selectors' => [
				'.elementor-tab-content a,
				.box-description a,
				.e-hotspot__tooltip a,
				.e-inner-tab-content a,
				.elementor-widget-text-editor a, 
				.comment-respond a' => 'text-decoration: underline;',
				'.elementor-tab-content a:hover,
				.box-description a:hover,
				.e-hotspot__tooltip a:hover,
				.e-inner-tab-content a:hover,
				.elementor-widget-text-editor a:hover, 
				.comment-respond a:hover' => 'text-decoration: none;',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'the7_tab_link_hover', [
			'label' => __( 'Hover', 'the7mk2' ),
		] );

		$this->add_control( 'the7_link_hover_color', [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				'a:hover' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'the7_link_hover_decorator', [
			'label'     => __( 'Underlined Links', 'the7mk2' ),
			'type'      => Controls_Manager::SWITCHER,
			'dynamic'   => [],
			'selectors' => [
				'.elementor-tab-content a:hover,
				.box-description a:hover,
				.e-hotspot__tooltip a:hover,
				.e-inner-tab-content a:hover,
				.elementor-widget-text-editor a:hover,
				.comment-respond a:hover' => 'text-decoration: underline;',
			],
		] );

		$this->end_controls_tab();
	}


	private function add_headings_section() {
		$this->start_controls_section( 'section_typography_headings_font', [
			'label' => __( 'Headings', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );

		$this->add_control( 'the7_title_color', [
			'label'     => __( 'Default Headings Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'dynamic'   => [],
			'selectors' => [
				$this->get_wrapper() => '--the7-title-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'General Header Typography', 'the7mk2' ),
			'name'     => 'the7_header_def_typography',
			'css_name' => [
				'the7-h1',
				'the7-h2',
				'the7-h3',
				'the7-h4',
				'the7-h5',
				'the7-h6',
			],
			'selector' => 'body',
			'exclude'  => [
				'font_size',
			],
		] );

		for ( $id = 1; $id <= 6; $id ++ ) {
			$this->add_heading_controls( sprintf( __( 'H%s', 'the7mk2' ), $id ), "the7_h{$id}", $this->get_wrapper() );
		}
		$this->end_controls_section();
	}

	private function add_heading_controls( $label, $prefix, $selector ) {
		$css_prefix = str_replace( '_', '-', $prefix );
		$this->add_control( $prefix . '_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => $label,
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'Typography', 'the7mk2' ),
			'name'     => $prefix . '_typography',
			'css_name' => $css_prefix,
			'selector' => $selector,
		] );

		$this->add_responsive_control(  $prefix . '_paragraph_spacing', [
			'label'      => __( 'Spacing', 'the7mk2' ),
			'type'       => Controls_Manager::SLIDER,
			'selectors'  => [
				$selector => '--' . $css_prefix .'-spacing: {{SIZE}}{{UNIT}}',
			],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
				'em' => [
					'min' => 0.1,
					'max' => 20,
				],
				'vh' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'size_units' => [ 'px', 'em', 'vh' ],
		] );

	}

	private function add_widgets_section() {
		$this->start_controls_section( 'section_typography_widgets_font', [
			'label' => __( 'WordPress Widgets', 'the7mk2' ),
			'tab'   => $this->get_id(),
		] );
		$this->add_control( 'the7_widget_title_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Widget Title', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'Typography', 'the7mk2' ),
			'name'     => 'the7-widget-title-typography',
			'css_name' => 'the7-widget-title',
			'selector' => $this->get_wrapper(),
		] );

		$this->add_control( 'title_spacing', [
				'label'      => __( 'Bottom Spacing', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					$this->get_wrapper() => '--the7-widget-gap: {{SIZE}}{{UNIT}};',
				],
			] );

		$this->add_control( 'the7_widget_content_heading', [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Widget Content', 'the7mk2' ),
			'separator' => 'before',
		] );

		$this->add_group_control( Group_Control_Typography_CSS_Vars::get_type(), [
			'label'    => __( 'Typography', 'the7mk2' ),
			'name'     => 'the7-widget-content-typography',
			'css_name' => 'the7-widget-content',
			'selector' => $this->get_wrapper(),
		] );
		$this->end_controls_section();
	}
}
