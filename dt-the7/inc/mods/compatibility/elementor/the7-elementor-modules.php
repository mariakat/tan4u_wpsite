<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

defined( 'ABSPATH' ) || exit;

class The7_Elementor_Modules {

	private $modules = [];

	/**
	 * Hold the module list.
	 * @since  1.0.0
	 * @access public
	 * @static
	 */
	public function __construct() {
		$modules_namespace_prefix = $this->get_modules_namespace();

		foreach ( $this->get_modules_names() as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );
			//$class_name = $module_name;
			$class_name = str_replace( ' ', '_', ucwords( $class_name ) );
			/** @var The7_Elementor_Module_Base $class_name */
			$class_name = $modules_namespace_prefix . '\\Modules\\' . $class_name . '\Module';
			if ( class_exists( $class_name ) && $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}
	}

	/**
	 * Get modules.
	 *
	 * Retrieve all the registered modules or a specific module.
	 *
	 * @access public
	 *
	 * @param string $module_name The7_Elementor_Module_Base name.
	 *
	 * @return null|The7_Elementor_Module_Base|The7_Elementor_Module_Base[] All the registered modules or a specific module.
	 */
	public function get_modules( $module_name ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}

		return $this->modules;
	}

	protected function get_modules_namespace() {
		return __NAMESPACE__;
	}

	public function get_modules_names() {
		return [
			'mega-menu',
		];
	}
}
