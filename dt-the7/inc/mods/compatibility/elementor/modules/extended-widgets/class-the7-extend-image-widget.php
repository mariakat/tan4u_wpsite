<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Exend_Image_Widget {

	const INLINE_CONTENT_PLACEHOLDER = '<!-- inline content -->';

	private $image_html;

	public function __construct() {
		// inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
		add_filter( 'elementor/image_size/get_attachment_image_html', [ $this, 'get_attachment_image_html' ], 20, 4 );

		add_filter( 'elementor/files/svg/allowed_attributes', [ $this, 'get_allowed_attributes' ] );
		add_filter( 'elementor/files/svg/allowed_elements', [ $this, 'get_allowed_elements' ] );
	}

	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'image' => [
				'section_name' => [ 'section_image', 'section_style_image' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		if ( $section_id == 'section_style_image' ) {
			$this->add_svg_support( $widget );
			$this->inject_sticky_settings( $widget );
		}
		if ( $section_id == 'section_image' ) {
			$control_params = [
				'label' => __( 'Inline', 'the7mk2' ),
				'type'  => Controls_Manager::SWITCHER,
			];
			if ( isset( $widgets[ $widget->get_name() ]['condition'] ) ) {
				$control_params['condition'] = $widgets[ $widget->get_name() ]['condition'];
			}

			$widget->start_injection( [
					'of'       => 'lazy_load',
					'at'       => 'before',
					'fallback' => [
						'of' => 'open_lightbox',
					],
				] );

			$widget->add_control( 'inline_image', $control_params );

			$widget->end_injection();

			$control_data = [
				'condition' => [
					'inline_image!' => [ 'yes' ],
				],
			];

			The7_Elementor_Widgets::update_control_fields( $widget, 'lazy_load', $control_data );
		}
	}

	public function add_svg_support( $widget ) {
		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'width', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'max-width: {{SIZE}}{{UNIT}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'space', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'opacity: {{SIZE}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'opacity', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}}:hover img, {{WRAPPER}}:hover svg' => 'opacity: {{SIZE}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'opacity_hover', $control_data );

		$control_data = [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} img, {{WRAPPER}} svg',
		];

		The7_Elementor_Widgets::update_control_group_fields( $widget, Group_Control_Css_Filter::get_type(), $control_data );

		$control_data = [
			'name'     => 'css_filters_hover',
			'selector' => '{{WRAPPER}}:hover img, {{WRAPPER}}:hover svg',
		];

		The7_Elementor_Widgets::update_control_group_fields( $widget, Group_Control_Css_Filter::get_type(), $control_data );

		$control_data = [
			'name'     => 'image_box_shadow',
			'selector' => '{{WRAPPER}}:hover img, {{WRAPPER}}:hover svg',
		];

		The7_Elementor_Widgets::update_control_group_fields( $widget, Group_Control_Box_Shadow::get_type(), $control_data );

		$control_data = [
			'name'     => 'image_border',
			'selector' => '{{WRAPPER}} img, {{WRAPPER}} svg',
		];

		The7_Elementor_Widgets::update_control_group_fields( $widget, Group_Control_Border::get_type(), $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'transition-duration: {{SIZE}};',
			],
		];
		The7_Elementor_Widgets::update_control_fields( $widget, 'background_hover_transition', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		];
		The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'image_border_radius', $control_data );

		$control_data = [
			'selectors' => [
				'{{WRAPPER}} img, {{WRAPPER}} svg' => 'width: {{SIZE}}{{UNIT}};',
			],
		];
		The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'width', $control_data );
	}

	public function inject_sticky_settings( $widget ) {
		$widget->start_injection( [
				'of' => 'separator_panel_style',
				'at' => 'before',
			] );

		$widget->add_control( 'the7_sticky_size', [
				'label'        => __( 'Change When Floating (Sticky)', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'prefix_class' => 'the7-img-sticky-size-effect-',
				'classes'              => 'the7-control',
			] );

		$selector = 'body .the7-e-sticky-effects .the7-img-sticky-size-effect-yes.elementor-element-{{ID}} img,
		body .the7-e-sticky-effects .the7-img-sticky-size-effect-yes.elementor-element-{{ID}} svg';

		$widget->add_responsive_control( 'the7_sticky_width', [
				'label'          => esc_html__( 'Width', 'the7mk2' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => [ '%', 'px', 'vw' ],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [
					$selector => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'the7_sticky_size!' => '',
				],
			] );

		$widget->add_responsive_control( 'the7_sticky_space', [
				'label'          => esc_html__( 'Max Width', 'the7mk2' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units'     => [ '%', 'px', 'vw' ],
				'range'          => [
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [
					$selector => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'the7_sticky_size!' => '',
				],
			] );

		$widget->add_responsive_control( 'the7_sticky_height', [
				'label'          => esc_html__( 'Height', 'the7mk2' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units'     => [ 'px', 'vh' ],
				'range'          => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [
					$selector => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'      => [
					'the7_sticky_size!' => '',
				],
			] );
		$widget->end_injection();

		$widget->start_injection( [
			'of' => 'opacity',
			'at' => 'after',
		] );

		$selector = 'body .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} img,
		body .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}} svg';

		$widget->add_control( 'the7_sticky_opacity', [
			'label'          => esc_html__( 'Sticky Opacity', 'the7mk2' ),
			'type'           => Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'max' => 1,
					'min' => 0.10,
					'step' => 0.01,
				],
			],
			'selectors'      => [
				$selector =>  'opacity: {{SIZE}};',
			],
			'classes'              => 'the7-control',
		] );
		$widget->end_injection();
	}

	public function get_allowed_attributes( $allowed_attributes ) {
		$allowed_attributes[] = 'result';
		$allowed_attributes[] = 'in';
		$allowed_attributes[] = 'slope';
		$allowed_attributes[] = 'flood-color';
		$allowed_attributes[] = 'flood-opacity';

		return $allowed_attributes;
	}

	public function get_allowed_elements( $allowed_elements ) {
		$allowed_elements[] = 'feoffset';
		$allowed_elements[] = 'femerge';
		$allowed_elements[] = 'fecomponenttransfer';
		$allowed_elements[] = 'fefunca';
		$allowed_elements[] = 'femergenode';
		$allowed_elements[] = 'fedropshadow';

		return $allowed_elements;
	}

	public function get_attachment_image_html( $html, $settings, $image_size_key, $image_key ) {
		if ( empty( $settings['inline_image'] ) ) {
			return $html;
		}

		$image = $settings[ $image_key ];
		$image_src = Group_Control_Image_Size::get_attachment_image_src( $image['id'], $image_size_key, $settings );

		$this->image_html = $this->get_inline_image_by_url( $image_src, array( 'class' => 'inline-image' ) );

		if ( $this->image_html ) {
			add_filter( 'elementor/widget/render_content', [ $this, 'render_modified_image_content' ], 10, 2 );

			$html = self::INLINE_CONTENT_PLACEHOLDER;
		}

		return $html;
	}

	/**
	 * Returns image tag or raw SVG
	 *
	 * @param string $url  image URL.
	 * @param array  $attr [description]
	 *
	 * @return string|false false if cannot get image content
	 */
	private function get_inline_image_by_url( $url = null, $attr = array() ) {

		$url = esc_url( $url );

		if ( empty( $url ) ) {
			return false;
		}

		$ext = pathinfo( $url, PATHINFO_EXTENSION );
		$attr['class'] .= ' inline-image-ext-' . $ext;

		$attr = array_merge( array(
			'alt'   => '',
			'class' => '',
		), $attr );

		$base_url = site_url( '/' );
		$image_path = str_replace( $base_url, ABSPATH, $url );
		$key = md5( $image_path . 'the7_key' );
		$image_content = get_transient( $key );
		if ( ! $image_content ) {
			$image_content = file_get_contents( $image_path );
			if ( 'svg' !== $ext ) {
				$image_content = base64_encode( $image_content );
			}
		}

		if ( ! $image_content ) {
			return false;
		}
		set_transient( $key, $image_content, DAY_IN_SECONDS );
		if ( 'svg' !== $ext ) {
			return sprintf( '<img  src="data:image/%1$s;base64,%2$s" %3$s>', $ext, $image_content, $this->get_attr( $attr ) );
		}

		unset( $attr['alt'] );

		return sprintf( '<div %2$s>%1$s</div>', $image_content, $this->get_attr( $attr ) );
	}

	/**
	 * Return attributes string from attributes array.
	 *
	 * @param array $attr Attributes string.
	 *
	 * @return string
	 */
	private function get_attr( $attr = array() ) {

		if ( empty( $attr ) || ! is_array( $attr ) ) {
			return;
		}

		$result = '';

		foreach ( $attr as $key => $value ) {
			$result .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return $result;
	}

	/**
	 * Modificate image output in order to display inline svg without kses
	 *
	 * @param array $widget_content widget content string.
	 *
	 * @return string
	 */
	public function render_modified_image_content( $widget_content, Widget_Base $widget ) {
		remove_filter( 'elementor/widget/render_content', [ $this, 'render_modified_image_content' ], 10, 2 );

		return str_replace( self::INLINE_CONTENT_PLACEHOLDER, $this->image_html, $widget_content );
	}
}
