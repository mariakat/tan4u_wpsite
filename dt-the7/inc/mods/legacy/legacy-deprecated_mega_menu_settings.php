<?php

class Presscore_Modules_Legacy_Deprecated_Mega_Menu_Settings {

	public static function launch() {
		add_filter( 'the7_mega_menu_options', array( __CLASS__, 'alter_theme_options' ) );
	}

	public static function alter_theme_options( $options ) {
		$excludes = [
			'menu-item-image-border-radius',
			'menu-item-image-padding',
			'menu-item-hide-icon-on-mobile',
			'menu-item-image-position',
			'menu-item-image-size',
			'mega-menu',
		];
		foreach ( $options as $key => $opt ) {
			if ( isset( $opt['id'] ) && in_array( $opt['id'], $excludes ) ) {
				unset( $options[ $key ] );
			}
		}

		return $options;
	}
}