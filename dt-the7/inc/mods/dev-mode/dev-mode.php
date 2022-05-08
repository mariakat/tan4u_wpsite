<?php
/**
 * @package The7
 */

use The7\Mods\Dev_Mode\Tools;

/**
 * Development mode module.
 */

defined( 'ABSPATH' ) || exit;

class The7_Dev_Mode_Module {

	/**
	 * Execute module.
	 */
	public static function execute() {
		\The7\Mods\Dev_Mode\Admin_Page::init();
		\The7\Mods\Dev_Mode\Theme_Installer::init();

		// Use dev tools.
		add_action( 'admin_post_the7_use_dev_tool', [ Tools::class, 'use_tool' ] );
	}
}

The7_Dev_Mode_Module::execute();
