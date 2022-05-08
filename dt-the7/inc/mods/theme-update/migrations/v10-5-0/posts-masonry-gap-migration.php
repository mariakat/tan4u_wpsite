<?php
/**
 * Masonry gap migration for posts widget.
 *
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_5_0;

use The7\Mods\Compatibility\Elementor\Upgrade\The7_Elementor_Widget_Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Posts_Masonry_Gap_Migration class.
 */
class Posts_Masonry_Gap_Migration extends The7_Elementor_Widget_Migrations {

	/**
	 * @return string
	 */
	public static function get_widget_name() {
		return 'the7_elements';
	}

	/**
	 * Default widget migration logic here.
	 *
	 * @see The7_Elementor_Widget_Migrations::migrate()
	 */
	public function do_apply() {
		if ( $this->exists( 'rows_gap' ) ) {
			return;
		}

		$gap = $this->get( 'gap_between_posts' );
		if ( ! isset( $gap['size'] ) ) {
			$gap = [
				'unit' => 'px',
				'size' => 15,
			];
		}
		$gap['size'] = 2 * (int) $gap['size'];
		$this->set( 'gap_between_posts', $gap );
		$this->set( 'rows_gap', $gap );
	}

}
