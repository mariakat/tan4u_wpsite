<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Kits;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Modules\Kits\Tabs;

class The7_Kit extends Kit {

	protected $exclude_tabs = [
		'theme-style-typography',
		'theme-style-buttons',
		'theme-style-images',
		'theme-style-form-fields',
	];

	public function __construct( array $data = [] ) {
		$this->exclude_tabs = apply_filters( 'elementor/kit/the7_exclude_register_tabs', $this->exclude_tabs );
		parent::__construct( $data );
		if ( the7_is_elementor_theme_style_enabled() ) {
			$this->register_tabs();
		}
	}

	private function register_tabs() {
		$tabs = [
			'theme-style-general'    => Tabs\Theme_Style_General::class,
			'theme-style-buttons'    => Tabs\Theme_Style_Buttons::class,
			'theme-style-forms'    => Tabs\Theme_Style_Forms::class,
			'theme-style-typography' => Tabs\Theme_Style_Typography::class,
			'settings-advanced' => Tabs\Settings_Advanced::class,
		];

		foreach ( $tabs as $id => $class ) {
			parent::register_tab( $id, $class );
		}
	}

	/**
	 * Register a kit settings menu.
	 *
	 * @param $id
	 * @param $class
	 */
	public function register_tab( $id, $class ) {
		if ( ! in_array( $id, $this->exclude_tabs ) ) {
			parent::register_tab( $id, $class );
		}
	}

	/**
	 * Register a kit settings menu.
	 *
	 * @param $id
	 * @param $class
	 */
	public function register_kit_tab( $id, $class ) {
		parent::register_tab( $id, $class );
	}

	public function save( $data ) {
		$saved = parent::save( $data );
		if ( ! $saved ) {
			return false;
		}
		//here we can compile less if needed
		if ( isset( $data['settings'] ) ) {
			$the7_options = [];
			foreach ( $data['settings'] as $key => $val ) {
				$control = Plugin::instance()->controls_manager->get_control_from_stack( $this->get_unique_name(), $key );
				if ( ! is_wp_error( $control )
				     && isset( $control['the7_save'] ) && $control['the7_save']
				     && isset( $control['the7_option_name'] )) {
					$option_name = $control['the7_option_name'];

					if ( isset( $control['type'] ) && $control['type'] === Controls_Manager::SWITCHER && $val === '' ) {
						$val = isset( $control['empty_value'] ) ? $control['empty_value'] : $val;
					}
					unset($saved[$key]);
					$the7_options[ $option_name ] = $val;
				}
			}
			if ( ! empty( $the7_options ) ) {
				$optionsframework_settings = get_option( 'optionsframework' );
				$options_id = $optionsframework_settings['id'];
				update_option( $options_id, $the7_options );
				presscore_refresh_dynamic_css();
			}
		}

		return $saved;
	}

}
