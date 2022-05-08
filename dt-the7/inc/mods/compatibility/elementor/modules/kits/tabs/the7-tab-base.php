<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

abstract class The7_Tab_Base extends Tab_Base {

	public function get_group() {
		return 'theme-style';
	}

	/**
	 * Get self title.
	 * Retrieve the self title.
	 * @access public
	 */
	public function get_title() {
		return 'The7 ' . $this->the7_title();
	}

	abstract function the7_title();

	public function get_id() {
		return $this->get_group() . '-' . $this->the7_id();
	}

	abstract function the7_id();

	protected function get_wrapper() {
		return '#the7-body';
	}
}