<?php
/**
 * Horizontak menu gap migration.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_2_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal_Menu_Gap_Migration class.
 */
class Horizontal_Menu_Gap_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_horizontal-menu';
	}

	/**
	 * Apply migration.
	 */
	public function do_apply() {
		$gap_settings = [
			'sub_menu_gap',
			'sub_menu_gap_tablet',
			'sub_menu_gap_mobile',
		];

		foreach ( $gap_settings as $gap_setting ) {
			$value = $this->get( $gap_setting );

			if ( isset( $value['size'] ) ) {
				$value       += array_fill_keys( [ 'top', 'bottom', 'left', 'right' ], '0' );
				$value['top'] = $value['size'];
				unset( $value['size'], $value['sizes'] );
				$value['isLinked'] = false;
				$this->set( $gap_setting, $value );
			}
		}
	}
}
