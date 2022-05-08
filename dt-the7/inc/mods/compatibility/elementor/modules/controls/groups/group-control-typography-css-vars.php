<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Controls\Groups;

use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor typography control for css vars.
 * A base control for creating typography control. Displays input fields to define
 * the content typography including font size, font family, font weight, text
 * transform, font style, line height and letter spacing.
 */
class Group_Control_Typography_CSS_Vars extends Group_Control_Typography {

	public static function get_type() {
		return 'typography-css-vars';
	}

	/**
	 * Prepare fields.
	 * Process typography control fields before adding them to `add_control()`.
	 *
	 * @param array $fields Typography control fields.
	 *
	 * @return array Processed fields.
	 * @access protected
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();
		$css_name = $args['name'];
		if ( isset( $args['css_name'] ) ) {
			$css_name = $args['css_name'];
		}
		array_walk( $fields, function ( &$field, $field_name ) use ( $css_name ) {
			if ( $field_name === 'font_family' ) {
				$selector = str_replace( 'font-family: ', '', $field['selector_value'] );
			} elseif ( empty( $field['selector_value'] ) ) {
				$selector = '{{VALUE}}';
			} else {
				$selector = '{{SIZE}}{{UNIT}}';
			}
			$selector_value = '';
			if (is_array($css_name)) {
				foreach ( $css_name as $val ) {
					$selector_value .= '--' . $val . '-' . str_replace( '_', '-', $field_name ) . ': ' . $selector . ';';
				}
			}
			else{
				$selector_value .= '--' . $css_name . '-' . str_replace( '_', '-', $field_name ) . ': ' . $selector . ';';
			}
			$field['selector_value'] = $selector_value;
		} );
		return parent::prepare_fields( $fields );
	}
}