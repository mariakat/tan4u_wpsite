<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Mega_Menu;

use The7_Elementor_Compatibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Options {

	public function __construct() {
		//add_action( 'admin_print_footer_scripts-nav-menus.php', [ $this, 'options_iframe' ] ); possible would be used when modal interface would be implemented
		add_filter( 'the7_mega_menu_options', [ __CLASS__, 'alter_menu_options' ], 5, 1 );
	}

	public static function alter_menu_options( $options ) {
		$new_options = [];
		$new_options[] = [
			'name'       => _x( 'Use Elementor MegaMenu', 'admin', 'the7mk2' ),
			'id'         => 'mega-menu-elementor',
			'type'       => 'switch',
			'std'        => 'off',
			'options'    => [
				'on'  => _x( 'Yes', 'admin', 'the7mk2' ),
				'off' => _x( 'No', 'admin', 'the7mk2' ),
			],
			'dependency' => [
				[
					'field'    => 'item-depth',
					'operator' => '==',
					'value'    => '0',
				],
			],
			'show_hide' => array(
				'off' => 'mega-menu-elementor-off',
			),
			'divider'    => 'top',
		];

		/* @var Module $mega_menu_module */
		$mega_menu_module = The7_Elementor_Compatibility::instance()->modules->get_modules( 'mega-menu' );
		$list = $mega_menu_module->get_posts();
		$new_options[] = [
			'name'       => _x( 'Elementor menu template', 'admin', 'the7mk2' ),
			'id'         => 'mega-menu-elementor-template',
			'type'       => 'select',
			'std'        => 'none',
			'options'    => $list,
			'dependency' => [
				[
					'field'    => 'mega-menu-elementor',
					'operator' => '==',
					'value'    => 'on',
				],
				[
					'field'    => 'item-depth',
					'operator' => '==',
					'value'    => '0',
				],
			],
		];

		$new_options[] = [
			'name'       => _x( 'Use mobile menu as', 'admin', 'the7mk2' ),
			'id'         => 'mega-menu-elementor-mobile-content',
			'type'       => 'select',
			'std'        => 'none',
			'options'    => [
				''               => _x( 'Elementor content', 'admin', 'the7mk2' ),
				'wp_mobile_menu' => _x( 'WP Submenu', 'admin', 'the7mk2' ),
			],
			'dependency' => [
				[
					'field'    => 'mega-menu-elementor',
					'operator' => '==',
					'value'    => 'on',
				],
				[
					'field'    => 'item-depth',
					'operator' => '==',
					'value'    => '0',
				],
			],
		];

		$new_options[] = [
			'type'  => 'js_hide_begin',
			'id'    => 'mega-menu-wrap',
			'class' => 'mega-menu-elementor mega-menu-elementor-off',
		];

		$found = false;
		$insert_after = 'menu-item-icon-html';

		foreach ( $options as $key => $opt ) {
			if ( isset( $opt['id'] ) && $opt['id'] === $insert_after ) {
				$options = dt_array_push_after( $options, $new_options, $key );
				$found = true;
				break;
			}
		}

		if ( $found ) {
			$insert_after = 'mega-menu-hide-on-mobile';
			$new_options = [];
			$new_options[] = [
				'type' => 'js_hide_end',
			];
			foreach ( $options as $key => $opt ) {
				if ( isset( $opt['id'] ) && $opt['id'] === $insert_after ) {
					$options = dt_array_push_after( $options, $new_options, $key );
					break;
				}
			}
		}

		return $options;
	}

	public static function alter_option( $options, $alter_option, $option_id ) {
		foreach ( $options as $key => $opt ) {
			if ( isset( $opt['id'] ) && $opt['id'] === $option_id ) {
				$options[ $key ] = array_merge( $opt, $alter_option );
				break;
			}
		}

		return $options;
	}

	public static function options_iframe() {
		include 'views/options-iframe.php';
	}
}
