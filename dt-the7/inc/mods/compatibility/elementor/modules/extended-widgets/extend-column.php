<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Extended_Widgets;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Extend_Column {

	public function __construct() {
		// inject controls
		add_action( 'elementor/element/before_section_end', [ $this, 'update_controls' ], 20, 3 );
	}

	/**
	 * Before section end.
	 * Fires before Elementor section ends in the editor panel.
	 *
	 * @param Controls_Stack $widget     The control.
	 * @param string         $section_id Section ID.
	 * @param array          $args       Section arguments.
	 *
	 * @since 1.4.0
	 */
	public function update_controls( $widget, $section_id, $args ) {
		$widgets = [
			'column' => [
				'section_name' => [ 'layout' ],
			],
		];

		if ( ! array_key_exists( $widget->get_name(), $widgets ) ) {
			return;
		}

		$curr_section = $widgets[ $widget->get_name() ]['section_name'];
		if ( ! in_array( $section_id, $curr_section ) ) {
			return;
		}

		$widget->start_injection( [
			'of' => 'content_position',
			'at' => 'before',
		] );

		$consditions = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'the7_auto_width',
					'operator' => 'in',
					'value'    => [ 'minimize', 'maximize', 'fit-content' ],
				],
				[
					'name'     => 'the7_auto_width_tablet',
					'operator' => 'in',
					'value'    => [ 'minimize', 'maximize', 'fit-content' ],
				],
				[
					'name'     => 'the7_auto_width_mobile',
					'operator' => 'in',
					'value'    => [ 'minimize', 'maximize', 'fit-content' ],
				],
			],
		];

		$widget->add_responsive_control( 'the7_auto_width_notice', [
			'raw'             => esc_html__( 'When "Column Stretching" is enabled, "Column Width" setting will be ignored.', 'the7mk2' ),
			'type'            => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			'conditions'      => $consditions,
			'device_args'     => [
				Controls_Stack::RESPONSIVE_TABLET => [
					'conditions' => $consditions,
				],
				Controls_Stack::RESPONSIVE_MOBILE => [
					'conditions' => $consditions,
				],
			],
		] );

		$widget->add_responsive_control( 'the7_auto_width', [
			'label'                => esc_html__( 'Column Stretching', 'the7mk2' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => '',
			'tablet_default'       => '',
			'mobile_default'       => '',
			'options'              => [
				''            => esc_html__( 'Default', 'the7mk2' ),
				'none'        => esc_html__( 'None', 'the7mk2' ),
				'fit-content' => esc_html__( 'Fit Content', 'the7mk2' ),
				'maximize'    => esc_html__( 'Maximize', 'the7mk2' ),
				'minimize'    => esc_html__( 'Fixed Width', 'the7mk2' ),
			],
			'selectors'            => [
				'div{{WRAPPER}}' => '{{VALUE}}',
			],
			'selectors_dictionary' => [
				'none'        => 'max-width: initial; flex: none; min-width:25px;',
				'fit-content' => 'max-width: fit-content; flex: 0 1 fit-content; min-width:initial;',
				'minimize'    => 'max-width: var(--the7-target-width, fit-content); flex: 0 1 var(--the7-target-width, fit-content); min-width:initial;',
				'maximize'    => 'max-width: initial; flex: 1 0 0; min-width:25px;',
			],
			'classes'              => 'the7-control',
		] );


		$consditions = [
			'relation' => 'or',
			'terms'    => [
				[
					'name'     => 'the7_auto_width',
					'operator' => 'in',
					'value'    => [ 'minimize' ],
				],
				[
					'name'     => 'the7_auto_width_tablet',
					'operator' => 'in',
					'value'    => [ 'minimize' ],
				],
				[
					'name'     => 'the7_auto_width_mobile',
					'operator' => 'in',
					'value'    => [ 'minimize' ],
				],
			],
		];

		$widget->add_responsive_control( 'the7_target_width', [
			'label'       => esc_html__( 'Target Width', 'the7mk2' ) . ' (px)',
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ 'px' ],
			'range'       => [
				'px' => [
					'min'  => 1,
					'max'  => 2000,
					'step' => 1,
				],
			],
			'selectors'   => [
				'div{{WRAPPER}}' => '--the7-target-width:{{SIZE}}{{UNIT}}',
			],
			'classes'     => 'the7-control',
			'conditions'  => $consditions,
			'device_args' => [
				Controls_Stack::RESPONSIVE_TABLET => [
					'conditions' => $consditions,
				],
				Controls_Stack::RESPONSIVE_MOBILE => [
					'conditions' => $consditions,
				],
			],
		] );
		$widget->end_injection();
	}
}
