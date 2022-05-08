<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Controls\Groups;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor border control.
 * A base control for creating border control. Displays input fields to define
 * border type, border width and border color.
 */
class Group_Control_Border_CSS_Vars extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the border control fields.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array Border control fields.
	 */
	protected static $fields;

	/**
	 * Get group control type.
	 *
	 * Retrieve the group control type.
	 *
	 * @access public
	 * @static
	 */
	public static function get_type() {
		return 'border-css-vars';
	}

	/**
	 * Init fields.
	 * Initialize border control fields.
	 *
	 * @return array Control fields.
	 * @access protected
	 */
	protected function init_fields() {
		$fields = [];

		$fields['style'] = [
			'label'     => __( 'Border Type', 'the7mk2' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				''       => __( 'Default', 'the7mk2' ),
				'none'   => __( 'None', 'the7mk2' ),
				'solid'  => __( 'Solid', 'the7mk2' ),
				'double' => __( 'Double', 'the7mk2' ),
				'dotted' => __( 'Dotted', 'the7mk2' ),
				'dashed' => __( 'Dashed', 'the7mk2' ),
				'groove' => __( 'Groove', 'the7mk2' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => '{{VALUE}};',
			],
		];

		$fields['width'] = [
			'label'      => __( 'Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'selectors'  => [
				'{{SELECTOR}}' => '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'style!' => [ '', 'none' ],
			],
			'responsive' => true,
		];

		$fields['color'] = [
			'label'     => __( 'Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{SELECTOR}}' => '{{VALUE}};',
			],
			'condition' => [
				'style!' => [ '', 'none' ],
			],
		];

		return $fields;
	}

	/**
	 * Get default options.
	 * Retrieve the default options of the border control. Used to return the
	 * default options while initializing the border control.
	 *
	 * @return array Default border control options.
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}

	/**
	 * Prepare fields.
	 * Process border  control fields before adding them to `add_control()`.
	 *
	 * @param array $fields Typography control fields.
	 *
	 * @return array Processed fields.
	 * @access protected
	 */
	protected function prepare_fields( $fields ) {
		$args     = $this->get_args();
		$css_name = $args['name'];
		if ( isset( $args['css_name'] ) ) {
			$css_name = $args['css_name'];
		}
		array_walk(
			$fields,
			function ( &$field, $field_name ) use ( $css_name ) {
				foreach ( $field['selectors'] as $key => $val ) {
					$field['selectors'][ $key ] = '--' . $css_name . '-' . str_replace( '_', '-', $field_name ) . ': ' . $val;
				}
			}
		);
		return parent::prepare_fields( $fields );
	}
}
