<?php
/**
 * @package The7
 */

namespace The7\Mods\Dev_Mode;

use phpDocumentor\Reflection\Types\Object_;

defined( 'ABSPATH' ) || exit;

/**
 * Theme installer class.
 */
class Theme_Installer {

	/**
	 * Bootstrap.
	 */
	public static function init() {
		add_filter( 'site_transient_update_themes', [ __CLASS__, 'force_theme_re_install_filter' ] );
	}

	/**
	 * Fix update_theme transient so theme could be re installed.
	 *
	 * @param null|Object $transient Transient object.
	 *
	 * @return mixed
	 */
	public static function force_theme_re_install_filter( $transient ) {
		if ( ! isset( $_GET['the7-force-update'] ) || ! presscore_theme_is_activated() ) {
			return $transient;
		}

		if ( ! is_object( $transient ) ) {
			$transient           = new \stdClass();
			$transient->response = [];
		}

		$code                                   = presscore_get_purchase_code();
		$the7_remote_api                        = new \The7_Remote_API( $code );
		$theme_template                         = get_template();
		$transient->response[ $theme_template ] = array(
			'theme'       => $theme_template,
			'new_version' => THE7_VERSION,
			'url'         => presscore_theme_update_get_changelog_url(),
			'package'     => $the7_remote_api->get_theme_download_url( THE7_VERSION ),
		);

		return $transient;
	}

}
