<?php
/**
 * @package The7
 */

namespace The7\Mods\Theme_Update\Migrations\v10_0_0;

use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Manager as SettingsPageManager;
use Elementor\Plugin;
use The7_Less_Vars_Value_Font;
use The7_Less_Vars_Value_Number;
use The7_Option_Field_Font_Sizes;
use The7_Option_Field_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Kit_Globals_Migration
 * migrate dynamical theme colors and typography to static values
 */
class Kit_Globals_Migration {

	const CUSTOM_COLORS = 'custom_colors';
	const CUSTOM_TYPOGRAPHY = 'custom_typography';
	const TRANSIENT_KEY = 'the7_global_kit_settings';

	public static function migrate() {
		$kit = self::get_kit();
		if ( ! $kit ) {
			return false;
		}
		$kit_raw_settings = self::get_settings($kit);
		self::migrate_the7_colors( $kit, $kit_raw_settings );
		self::migrate_the7_fonts( $kit, $kit_raw_settings );

		return true;
	}
	private static function get_kit() {
		if ( ! the7_elementor_is_active() ) {
			return false;
		}

		$kit_id = Plugin::$instance->kits_manager->get_active_id();
		if ( ! $kit_id ) {
			//'Active kit not found. nothing to do.
			return false;
		}

		return Plugin::$instance->documents->get( $kit_id );
	}

	private static function get_settings($kit){
		$meta_key = SettingsPageManager::META_KEY;
		return $kit->get_meta( $meta_key );
	}

	private static function migrate_the7_colors( $kit, $settings ) {
		$existing_color_ids = self::get_existing_global_ids( $settings, self::CUSTOM_COLORS );
		foreach ( self::get_the7_kit_colors() as $color ) {
			if ( ! in_array( $color['_id'], $existing_color_ids ) ) {
				$kit->add_repeater_row( self::CUSTOM_COLORS, $color );
			}
		}
	}

	private static function get_existing_global_ids( $settings, $field_name ) {
		$existing_ids = [];
		if ( isset( $settings[ $field_name ] ) ) {
			foreach ( $settings[ $field_name ] as $field ) {
				if ( isset( $field['_id'] ) ) {
					$existing_ids[] = $field['_id'];
				}
			}
		}

		return $existing_ids;
	}

	private static function get_the7_kit_colors() {
		$colors = [
			'the7-content-headers_color'           => __( 'Headings', 'the7mk2' ),
			'the7-content-primary_text_color'      => __( 'Primary text', 'the7mk2' ),
			'the7-content-secondary_text_color'    => __( 'Secondary text', 'the7mk2' ),
			'the7-content-links_color'             => __( 'Links color', 'the7mk2' ),
			'the7-accent'                          => __( 'Accent', 'the7mk2' ),
			'the7-buttons-color_mode'              => __( 'Button background normal', 'the7mk2' ),
			'the7-buttons-hover_color_mode'        => __( 'Button background hover', 'the7mk2' ),
			'the7-buttons-text_color_mode'         => __( 'Button text normal', 'the7mk2' ),
			'the7-buttons-text_hover_color_mode'   => __( 'Button text hover', 'the7mk2' ),
			'the7-buttons-border-color_mode'       => __( 'Button border normal', 'the7mk2' ),
			'the7-buttons-hover-border-color_mode' => __( 'Button border hover', 'the7mk2' ),
			'the7-dividers-color'                  => __( 'Dividers', 'the7mk2' ),
			'the7-general-content_boxes_bg_color'  => __( 'Content boxes background', 'the7mk2' ),
		];

		$result = [];
		foreach ( $colors as $key => $title ) {
			$key_filtered = str_replace( "-", "_", $key );
			$result[ $key_filtered ] = [
				'_id'   => $key_filtered,
				'title' => 'The7 ' . $title,
				'color' => the7_theme_get_color( str_replace( "the7-", "", $key ) ),
			];
		}

		return $result;
	}

	private static function migrate_the7_fonts( $kit, $settings ) {
		$existing_typography_ids = self::get_existing_global_ids( $settings, self::CUSTOM_TYPOGRAPHY );
		foreach ( self::get_the7_kit_typography() as $typography ) {
			if ( ! in_array( $typography['_id'], $existing_typography_ids ) ) {
				$kit->add_repeater_row( self::CUSTOM_TYPOGRAPHY, $typography );
			}
		}
	}

	private static function get_the7_kit_typography() {
		$typographys = [];

		for ( $id = 1; $id <= 6; $id ++ ) {
			$typographys["the7-fonts-h{$id}-typography"] = [
				'title' => __( "Headings {$id}", 'the7mk2' ),
				'id'    => "the7-h{$id}",
			];
		}
		$font_fields = array(
			'fonts-widget-title'   => array(
				'font_desc' => __( 'Widget title', 'the7mk2' ),
			),
			'fonts-widget-content' => array(
				'font_desc' => __( 'Widget content', 'the7mk2' ),
			),
			'fonts-woo-title'      => array(
				'font_desc' => __( 'Product title', 'the7mk2' ),
			),
			'fonts-woo-content'    => array(
				'font_desc' => __( 'Product content', 'the7mk2' ),
			),
		);
		foreach ( $font_fields as $id => $data ) {
			$typographys["the7-{$id}"] = [
				'title' => $data['font_desc'],
				'id'    => "the7-{$id}",
			];
		}

		//combine font sizes and main font
		$font_sizes = array(
			'big_size'    => array(
				'font_desc' => __( 'Large font', 'the7mk2' ),
			),
			'normal_size' => array(
				'font_desc' => __( 'Medium font', 'the7mk2' ),
			),
			'small_size'  => array(
				'font_desc' => __( 'Small font', 'the7mk2' ),
			),
		);

		foreach ( $font_sizes as $id => $data ) {
			$typographys["the7-fonts-{$id}"] = [
				'title'             => $data['font_desc'],
				'id'                => "the7-{$id}",
				'font-family'       => "fonts-font_family",
				'sizes-option-name' => "fonts-{$id}",
			];
		}
		$result = [];
		foreach ( $typographys as $key => $typography_val ) {
			$key_filtered = str_replace( "-", "_", $typography_val['id'] );

			$result[ $key_filtered ] = [
				'_id'   => $key_filtered,
				'title' => 'The7 ' . $typography_val['title'],
			];

			$arr_val = &$result[ $key_filtered ];

			$option_name = '';
			if ( isset( $typography_val['font-family'] ) ) {
				$option_name = $typography_val['font-family'];
			} else {
				$option_name = str_replace( "the7-", "", $key );
			}

			$option = of_get_option( $option_name );

			if ( ! is_array( $option ) ) {
				$option = [ 'font_family' => $option ];
			}
			if ( isset( $typography_val['sizes-option-name'] ) ) {
				$font_sizes = The7_Option_Field_Font_Sizes::sanitize( of_get_option( $typography_val['sizes-option-name'] ) );
				$option['responsive_font_size'] = [ 'desktop' => $font_sizes['font_size'] ];
				$option['responsive_line_height'] = [ 'desktop' => $font_sizes['line_height'] ];
			}

			$typography = The7_Option_Field_Typography::sanitize( $option );

			$the7_web_font = new The7_Less_Vars_Value_Font( $typography['font_family'] );

			$arr_val['typography_font_family'] = $the7_web_font->get_family();

			if ( $the7_web_font->get_weight() != '~""' ) {
				$arr_val['typography_font_weight'] = $the7_web_font->get_weight();
			}

			if ( $the7_web_font->get_style() != '~""' ) {
				$arr_val['typography_font_style'] = $the7_web_font->get_style();
			}

			if ( isset( $typography['text_transform'] ) && ! empty( $typography['text_transform'] ) ) {
				$arr_val["typography_text_transform"] = $typography['text_transform'];
			}

			foreach ( $typography['responsive_font_size'] as $device => $val ) {
				if ( $device === 'desktop' ) {
					$device = '';
				} else {
					$device = "_{$device}";
				}
				$var = new The7_Less_Vars_Value_Number( $val );
				$data = [
					'unit'  => $var->get_units(),
					'size'  => $var->get_val(),
					'sizes' => [],
				];

				$arr_val["typography_font_size{$device}"] = $data;
			}
			foreach ( $typography['responsive_line_height'] as $device => $val ) {
				if ( $device === 'desktop' ) {
					$device = '';
				} else {
					$device = "_{$device}";
				}
				$var = new The7_Less_Vars_Value_Number( $val );
				$data = [
					'unit'  => $var->get_units(),
					'size'  => $var->get_val(),
					'sizes' => [],
				];

				$arr_val["typography_line_height{$device}"] = $data;
			}

			$arr_val['typography_typography'] = 'custom';
		}

		return $result;
	}

	public static function remove_migration() {
		$kit = self::get_kit();
		if ( ! $kit ) {
			return false;
		}
		$document_settings = self::get_settings($kit);
		if ( !empty($document_settings) ) { //make sure we have some settings
			self::backup_the7_kit( $document_settings );

			$document_settings = self::clear_the7_fields( $document_settings, self::CUSTOM_COLORS, self::get_the7_kit_colors() );
			$document_settings = self::clear_the7_fields( $document_settings, self::CUSTOM_TYPOGRAPHY, self::get_the7_kit_typography() );

			self::save_kit_settings( $kit, $document_settings );
		}
		delete_option('the7-theme-style-migrate-first');

		return true;
	}

	private static function clear_the7_fields( $settings, $field_name, $the7_kits ) {
		if ( ! isset( $settings[ $field_name ] ) ) {
			return $settings;
		}
		foreach ( $the7_kits as $the7_field ) {
			foreach ( $settings[ $field_name ] as $key => $field ) {
				if ( $field['_id'] == $the7_field['_id'] ) {
					unset( $settings[ $field_name ][ $key ] );
					break;
				}
			}
		}
		//reorder leftovers
		$settings[ $field_name ] = array_values( $settings[ $field_name ]);
		return $settings;
	}

	private static function save_kit_settings( $kit, $settings ) {
		if ( empty( $settings ) ) {
			return;
		}
		$page_settings_manager = SettingsManager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $settings, $kit->get_id() );
	}

	public static function restore() {
		$backup = self::get_backup();
		if ( $backup === false ) {
			return self::migrate();
		}
		$kit = self::get_kit();
		if ( ! $kit ) {
			return false;
		}
		$document_settings = self::get_settings($kit);
		foreach ( $backup as $field_name => $the7_kits ) {
			foreach ( $the7_kits as $the7_field ) {
				$exist_key = '';
				foreach ( $document_settings[ $field_name ] as $key => $field ) {
					if ( isset( $field['_id'] ) && $the7_field['_id'] === $field['_id']) {
						$exist_key = $key;
						break;
					}
				}
				if ( empty( $exist_key ) ) {
					$document_settings[ $field_name ][] = $the7_field;
				} else {
					$document_settings[ $field_name ][ $exist_key ] = $the7_field;
				}
			}
		}
		delete_transient( self::TRANSIENT_KEY );
		self::save_kit_settings( $kit, $document_settings );

		return true;
	}

	private static function backup_the7_kit( $settings ) {
		$the7_kit_fields = [];
		$the7_kit_fields[ self::CUSTOM_COLORS ] = self::get_the7_kit_colors();
		$the7_kit_fields[ self::CUSTOM_TYPOGRAPHY ] = self::get_the7_kit_typography();

		$the7_kit_settings = [];
		foreach ( $the7_kit_fields as $field_name => $the7_kits ) {
			if ( ! isset( $settings[ $field_name ] ) ) {
				continue;
			}
			foreach ( $the7_kits as $the7_field ) {
				foreach ( $settings[ $field_name ] as $key => $field ) {
					if ( $field['_id'] == $the7_field['_id'] ) {
						$the7_kit_settings[ $field_name ][] = $settings[ $field_name ][ $key ];
						break;
					}
				}
			}
		}
		set_transient( self::TRANSIENT_KEY, $the7_kit_settings, MONTH_IN_SECONDS );
	}

	public static function get_backup(){
		return get_transient( self::TRANSIENT_KEY );
	}
}
