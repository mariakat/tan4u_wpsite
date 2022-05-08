<?php
/**
 * Migration that changes submenu visibility default for "The7 Vertical Menu" widget.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Submenu_Visibility_Migration class.
 */
class Submenu_Visibility_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_nav-menu';
	}

	/**
	 * Default widget migration logic here.
	 *
	 * @see The7_Elementor_Widget_Migrations::migrate()
	 */
	public function do_apply() {
		$this->add( 'submenu_display', 'on_click' );
	}

}
