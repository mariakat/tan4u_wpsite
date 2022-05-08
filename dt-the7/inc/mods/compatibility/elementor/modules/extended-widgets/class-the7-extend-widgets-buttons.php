<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Group_Control_Box_Shadow;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class The7_Extend_Widgets_Buttons {
	const BACKGROUND_STYLE = 'background-color: {{VALUE}}; background-image:none;';
	public function __construct() {
		//inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'button'                    => [
				'section_name' => [ 'section_style', ],
			],
			'form'                      => [
				'section_name' => [ 'section_button_style', ],
			],
			'woocommerce-checkout-page' => [
				'section_name' => [ 'section_checkout_tabs_purchase_button', 'section_checkout_tabs_forms', ],
			],
			'woocommerce-cart'          => [
				'section_name' => [ 'section_cart_tabs_forms', 'section_cart_tabs_checkout_button', ],
			],
			'woocommerce-my-account'    => [
				'section_name' => [ 'forms_section', 'tables_section' ],
			],
			'woocommerce-product-add-to-cart' => [
				'section_name' => [ 'section_atc_button_style' ],
			],

		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		if ( $widget->get_name() == 'button' ) {
			if ( $section_id == 'section_style' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'background_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_hover_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button, {{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					],
				];
				The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'typography_font_size', $control_data );

				//add box shadow hover, but before move shadow into normal tab
				$widget->remove_control( 'button_box_shadow_box_shadow' );
				$widget->remove_control( 'button_box_shadow_box_shadow_position' );
				$widget->remove_control( 'button_box_shadow_box_shadow_type' );

				$widget->start_injection( [
					'of' => 'button_text_color',
					'at' => 'before',
				] );

				$widget->add_group_control( Group_Control_Box_Shadow::get_type(), [
					'name'     => 'button_box_shadow',
					'selector' => '{{WRAPPER}} .elementor-button',
				] );

				$widget->end_injection();

				$widget->start_injection( [
					'of' => 'hover_color',
					'at' => 'before',
				] );

				$widget->add_group_control( Group_Control_Box_Shadow::get_type(), [
					'name'     => 'button_box_shadow_hover',
					'selector' => '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus',
				] );

				$widget->end_injection();
			}
		}
		if ( $widget->get_name() == 'form' ) {
			if ( $section_id == 'section_button_style' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .e-form__buttons__wrapper__button-next' => self::BACKGROUND_STYLE,
						'{{WRAPPER}} .elementor-button[type="submit"]'       => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .e-form__buttons__wrapper__button-next:hover' => self::BACKGROUND_STYLE,
						'{{WRAPPER}} .elementor-button[type="submit"]:hover'       => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_background_hover_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .elementor-button, {{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					],
				];
				The7_Elementor_Widgets::update_responsive_control_fields( $widget, 'button_typography_font_size', $control_data );
			}
		}
		if ( $widget->get_name() == 'woocommerce-product-add-to-cart' ) {
			if ( $section_id == 'section_atc_button_style' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .cart button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'button_bg_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .cart button:hover' => self::BACKGROUND_STYLE,
					],
				];

				The7_Elementor_Widgets::update_control_fields( $widget, 'button_bg_color_hover', $control_data );
			}

		}
		if ( $widget->get_name() == 'woocommerce-checkout-page' ) {
			if ( $section_id == 'section_checkout_tabs_purchase_button' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} #payment #place_order' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'purchase_button_normal_background_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} #payment #place_order:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'purchase_button_hover_background_color', $control_data );
			}
			if ( $section_id == 'section_checkout_tabs_forms' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .woocommerce-button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_normal_background_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .woocommerce-button:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_hover_background_color', $control_data );
			}
		}

		if ( $widget->get_name() == 'woocommerce-cart' ) {
			if ( $section_id == 'section_cart_tabs_forms' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .shop_table .button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_normal_background_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .shop_table .button:hover, {{WRAPPER}} .shop_table .button:disabled[disabled]:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_hover_background_color', $control_data );
			}
			if ( $section_id == 'section_cart_tabs_checkout_button' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .checkout-button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'checkout_button_normal_background_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .checkout-button:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'checkout_button_hover_background_color', $control_data );
			}
		}
		if ( $widget->get_name() == 'woocommerce-my-account' ) {
			if ( $section_id == 'forms_section' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} button.button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_background_color', $control_data );


				$control_data = [
					'selectors' => [
						'{{WRAPPER}} button.button:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'forms_buttons_hover_background_color', $control_data );
			}
			if ( $section_id == 'tables_section' ) {
				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .shop_table .button, {{WRAPPER}} .order-again .button, {{WRAPPER}} .woocommerce-pagination .button' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'tables_button_normal_background_color', $control_data );

				$control_data = [
					'selectors' => [
						'{{WRAPPER}} .shop_table .button:hover, {{WRAPPER}} .order-again .button:hover, {{WRAPPER}} .woocommerce-pagination .button:hover' => self::BACKGROUND_STYLE,
					],
				];
				The7_Elementor_Widgets::update_control_fields( $widget, 'tables_button_hover_background_color', $control_data );
			}
		}
	}
}