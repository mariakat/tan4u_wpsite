<?php
namespace The7\Mods\Compatibility\Elementor;

defined( 'ABSPATH' ) || exit;

abstract class The7_Elementor_Module_Base {
	/**
	 * Module instance.
	 *
	 * Holds the module instance.
	 *
	 * @access protected
	 *
	 * @var The7_Elementor_Module_Base
	 */
	protected static $_instances = [];

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @access public
	 * @abstract
	 *
	 * @return string Module name.
	 */
	abstract public function get_name();

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the module class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return The7_Elementor_Module_Base An instance of the class.
	 */
	public static function instance() {
		$class_name = static::class_name();

		if ( empty( static::$_instances[ $class_name ] ) ) {
			static::$_instances[ $class_name ] = new static();
		}

		return static::$_instances[ $class_name ];
	}

	/**
	 * @access public
	 * @static
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * Class name.
	 *
	 * Retrieve the name of the class.
	 *
	 * @access public
	 * @static
	 */
	public static function class_name() {
		return get_called_class();
	}
}