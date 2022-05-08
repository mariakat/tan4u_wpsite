<?php
/**
 * Migration that moves `stage_padding` value to `carousel_margin`, left and right.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_6_1;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Carousel_Stage_Padding_Migration class.
 */
class Carousel_Stage_Padding_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Default widget migration logic here.
	 *
	 * @see The7_Elementor_Widget_Migrations::migrate()
	 */
	public function do_apply() {
		$stage_padding = (int) $this->get_subkey( 'stage_padding', 'size' );
		$this->remove( 'stage_padding' );

		if ( ! $stage_padding ) {
			return;
		}

		foreach ( static::get_responsive_devices() as $device ) {
			$margin_setting_key = 'carousel_margin' . $device;

			if ( $device && ! $this->exists( $margin_setting_key ) ) {
				continue;
			}

			$margin = wp_parse_args(
				(array) $this->get( $margin_setting_key ),
				[
					'unit'     => 'px',
					'top'      => '0',
					'bottom'   => '0',
					'left'     => '0',
					'right'    => '0',
					'isLinked' => false,
				]
			);

			$margin['left']  = (string) ( (int) $margin['left'] + $stage_padding );
			$margin['right'] = (string) ( (int) $margin['right'] + $stage_padding );

			$this->set( $margin_setting_key, $margin );
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7_content_carousel' ],
			[ 'the7-wc-products-carousel' ],
			[ 'the7_elements_carousel' ],
			[ 'the7-elements-simple-posts-carousel' ],
			[ 'the7-simple-product-categories-carousel' ],
			[ 'the7-elements-woo-simple-products-carousel' ],
			[ 'the7_testimonials_carousel' ],
		];
	}

}
