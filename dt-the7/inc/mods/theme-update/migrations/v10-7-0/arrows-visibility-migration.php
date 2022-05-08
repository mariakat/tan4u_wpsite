<?php
/**
 * Removes default value for `arrows_tablet` and `arrows_mobile`.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_7_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Arrows_Visibility_Migration class.
 */
class Arrows_Visibility_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * Default widget migration logic here.
	 *
	 * @see The7_Elementor_Widget_Migrations::migrate()
	 */
	public function do_apply() {
		$remove_default = [
			'arrows_tablet',
			'arrows_mobile',
		];
		foreach ( $remove_default as $key ) {
			if ( $this->get( $key ) === 'default' ) {
				$this->remove( $key );
			}
		}
	}

	/**
	 * List of widgets to apply migration.
	 *
	 * @return \string[][]
	 */
	public static function get_callback_args_array() {
		return [
			[ 'the7-wc-products-carousel' ],
			[ 'the7_elements_carousel' ],
			[ 'the7-elements-simple-posts-carousel' ],
			[ 'the7-simple-product-categories-carousel' ],
			[ 'the7-elements-woo-simple-products-carousel' ],
		];
	}

}
