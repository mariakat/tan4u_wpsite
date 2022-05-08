<?php
/**
 * Posts Masonry & Grid filter gap migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_4_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Posts_Filter_Gap_Migration class.
 */
class Posts_Filter_Gap_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_elements';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$gap_settings = [
			'gap_below_category_filter',
			'gap_below_category_filter_tablet',
			'gap_below_category_filter_mobile',
		];

		foreach ( $gap_settings as $gap_setting ) {
			$value = $this->get( $gap_setting );

			if ( isset( $value['size'] ) ) {
				$value          += array_fill_keys( [ 'top', 'bottom', 'left', 'right' ], '0' );
				$value['bottom'] = (string) $value['size'];
				unset( $value['size'], $value['sizes'] );
				$value['isLinked'] = false;
				$this->set( $gap_setting, $value );
			}
		}

		// Update filter switch. It was non-responsive.
		if ( $this->get( 'show_categories_filter' ) === 'y' ) {
			$this->set( 'show_categories_filter', 'show' );
		}
	}
}
