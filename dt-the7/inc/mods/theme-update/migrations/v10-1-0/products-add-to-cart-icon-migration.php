<?php
/**
 * Products add to cart icon migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_1_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Products_Add_To_Cart_Icon_Migration class.
 */
class Products_Add_To_Cart_Icon_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		if ( in_array( $this->get( 'layout' ), [ 'content_below_img', null ], true ) ) {
			if ( $this->get( 'show_add_to_cart' ) === '' ) {
				return;
			}

			if ( $this->get( 'button_icon' ) === '' ) {
				$this->add(
					'add_to_cart_icon',
					[
						'value'   => '',
						'library' => '',
					]
				);

				return;
			}
		}

		$add_to_cart_icon = of_get_option( 'woocommerce_add_to_cart_icon' );
		if ( $add_to_cart_icon ) {
			$this->add(
				'add_to_cart_icon',
				[
					'value'   => $add_to_cart_icon,
					'library' => 'fa-regular',
				]
			);
		}

		$options_icon = of_get_option( 'woocommerce_details_icon' );
		if ( $options_icon ) {
			$this->add(
				'options_icon',
				[
					'value'   => $options_icon,
					'library' => 'fa-regular',
				]
			);
		}
	}
}
