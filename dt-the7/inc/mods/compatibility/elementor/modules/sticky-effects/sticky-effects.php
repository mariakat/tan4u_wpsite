<?php
/**
 * The7 extension which brings Sticky Effects to elementor sections.
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Sticky_Effects;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin as Elementor;
use The7_Elementor_Compatibility;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;


defined( 'ABSPATH' ) || exit;

class Sticky_Effects {

	const JS_STICKY_SRC = PRESSCORE_THEME_URI . '/lib/jquery-sticky/jquery-sticky';
	const JS_SRC = PRESSCORE_THEME_URI . '/js/compatibility/elementor/sticky-effects';
	const CSS_SRC = PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-sticky-effects';

	public function __construct() {
		$this->add_actions();
	}

	private function add_actions() {
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			add_action( 'elementor/element/section/section_effects/after_section_end', [ $this, 'register_controls' ] );
		} else {
			add_action( 'elementor/element/section/section_advanced/after_section_end', [
				$this,
				'register_controls',
			] );
		}

		//add_action( 'elementor/element/before_section_end', [ $this, 'register_advanced_controls' ], 10, 3 );

		if ( ! The7_Elementor_Compatibility::is_assets_loader_exist() ) {
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles' ] );
		}
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function register_scripts() {
		the7_register_script_in_footer( 'the7-e-sticky', self::JS_STICKY_SRC, [ 'jquery' ] );
		the7_register_script_in_footer( 'the7-e-sticky-effect', self::JS_SRC, [
			'the7-elementor-frontend-common',
			'the7-e-sticky',
		] );
	}

	public function enqueue_scripts() {
		if ( The7_Elementor_Compatibility::is_assets_loader_exist() ) {
			$this->register_assets();
		} else {
			wp_enqueue_script( 'the7-e-sticky-effect' );
		}
	}

	private function register_assets() {
		$assets = $this->get_assets();

		if ( $assets ) {
			Elementor::$instance->assets_loader->add_assets( $assets );
		}
	}

	private function get_assets() {
		return [
			'scripts' => [
				'the7-e-sticky-effect' => [
					'src'          => the7_add_asset_suffix( self::JS_SRC, '.js' ),
					'version'      => THE7_VERSION,
					'dependencies' => [
						'the7-elementor-frontend-common',
						'the7-e-sticky',
					],
				],
			],
			'styles'  => [
				'the7-e-sticky-effect' => [
					'src'          => the7_add_asset_suffix( self::CSS_SRC, '.css' ),
					'version'      => THE7_VERSION,
					'dependencies' => [],
				],
			],
		];
	}

	public function enqueue_styles() {
		the7_register_style( 'the7-e-sticky-effect', self::CSS_SRC );
		wp_enqueue_style( 'the7-e-sticky-effect' );
	}

	public function register_controls( Element_Base $element ) {

		$devices_options = The7_Elementor_Widget_Base::get_device_options();

		$e_sticky_cond = [];
		$e_overlap_cond = [];
		// Target only main sections.
		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$e_sticky_cond['isInner'] = false;
			$e_overlap_cond['isInner'] = false;
		}

		if ( the7_elementor_pro_is_active() ) {
			$e_sticky_cond['sticky'] = '';
		}

		$element->start_controls_section( 'the7_section_sticky_row', [
			'label' => __( 'Sticky Section & Overlap<i></i>', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_ADVANCED,
			'classes'              => 'the7-control',
		] );

		if ( Elementor::$instance->editor->is_edit_mode() && $element->get_control_index( 'isInner' ) === false ) {
			$element->add_control(
				'isInner',
				[
					'label'        => '',
					'type'         => Controls_Manager::HIDDEN,
					'default'      => false,
					'return_value' => true,
				]
			);
		}

		$element->add_control( 'the7_sticky_row_overlap', [
			'label'              => __( 'Overlap', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => __( 'On', 'the7mk2' ),
			'label_off'          => __( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
			'condition'          => $e_overlap_cond,
			'prefix_class'       => 'the7-e-sticky-overlap-',
			'description'        => __( 'When enabled, the row will not take any vertical space on the page and will overlap the content that comes after it.', 'the7mk2' ),
		] );

		$element->add_control( 'the7_sticky_row', [
			'label'              => esc_html__( 'Make Row Sticky', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'default'            => '',
			'frontend_available' => true,
			'assets'             => $this->get_asset_conditions_data(),
			'prefix_class'       => 'the7-e-sticky-row-',
			'condition'          => $e_sticky_cond,
			'separator' => 'before'
		] );

		if ( the7_elementor_pro_is_active() ) {
			$element->add_control( 'the7_sticky_row_notice', [
				'raw'             => __( 'The7 Sticky Row settings not available while Sticky option is enabled in Motion Effects panel', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'sticky!' => '',
				],
			] );
		}
		$condition = array_merge( $e_sticky_cond, [ 'the7_sticky_row!' => '' ] );

		$element->add_control( 'the7_sticky_row_devices', [
			'label'              => esc_html__( 'Sticky On', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT2,
			'multiple'           => true,
			'label_block'        => true,
			'default'            => $devices_options['active_devices'],
			'options'            => $devices_options['devices_options'],
			'condition'          => $condition,
			'render_type'        => 'none',
			'frontend_available' => true,
		] );

		$element->add_responsive_control( 'the7_sticky_row_offset', [
			'label'              => esc_html__( 'Offset (px)', 'the7mk2' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 0,
			'min'                => 0,
			'max'                => 500,
			'required'           => true,
			'condition'          => $condition,
			'render_type'        => 'none',
			'frontend_available' => true,
			'description'        => __( 'Offset is a minimal distance (in pixels) that the row will maintain from the top of the browser window.', 'the7mk2' ),
			'separator' => 'after'
		] );

		$e_sticky_effects = [
			'relation' => 'or',
			'terms'    => [
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_sticky_row',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			],
		];

		if ( the7_elementor_pro_is_active() ) {
			$e_sticky_effects['terms'][0]['terms'][] = [
				'name'     => 'sticky',
				'operator' => '==',
				'value'    => '',
			];
		}

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$e_sticky_effects['terms'][] = [
				'relation' => 'and',
				'terms'    => [
					[
						'name'     => 'isInner',
						'operator' => '==',
						'value'    => true,
					],
				],
			];
		}else{
			$e_sticky_effects['terms'][] = [
				'terms'    => [
					[
						'name'     => 'the7_sticky_row',
						'operator' => '!=',
						'value'    => '100',
					],
				],
			];
		}

		$element->add_control( 'the7_sticky_effects', [
			'label'              => __( 'Change Styles When Sticky', 'the7mk2' ),
			'type'               => Controls_Manager::SWITCHER,
			'label_on'           => __( 'On', 'the7mk2' ),
			'label_off'          => __( 'Off', 'the7mk2' ),
			'default'            => '',
			'frontend_available' => true,
			'assets'             => $this->get_asset_conditions_data(),
			'prefix_class'       => 'the7-e-sticky-effect-',
			'conditions'         => $e_sticky_effects,
		] );

		$condition = [
			'relation' => 'or',
			'terms'    => [
				[
					'relation' => 'and',
					'terms'    => [
						[
							'name'     => 'the7_sticky_row',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'the7_sticky_effects',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			],
		];

		if ( the7_elementor_pro_is_active() ) {
			$condition['terms'][0]['terms'][] = [
				'name'     => 'sticky',
				'operator' => '==',
				'value'    => '',
			];
		}

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$condition['terms'][] = [
				'relation' => 'and',
				'terms'    => [
					[
						'name'     => 'isInner',
						'operator' => '==',
						'value'    => true,
					],
					[
						'name'     => 'the7_sticky_effects',
						'operator' => '!==',
						'value'    => '',
					],
				],
			];
		}else{
			$condition['terms'][] = [
				'terms'    => [
					[
						'name'     => 'the7_sticky_effects',
						'operator' => '!==',
						'value'    => '',
					],
				],
			];
		}

		$condition_device = $condition;

		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$condition_device['terms'][1]['terms'][] = [
				'name'     => 'the7_sticky_row',
				'operator' => '!==',
				'value'    => '',
			];

			if ( the7_elementor_pro_is_active() ) {
				$condition_device['terms'][1]['terms'][]  = [
					'name'     => 'sticky',
					'operator' => '==',
					'value'    => '',
				];
			}
		}

		$element->add_control( 'the7_sticky_effects_devices', [
			'label'              => __( 'Change Styles On', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT2,
			'multiple'           => true,
			'label_block'        => true,
			'default'            => $devices_options['active_devices'],
			'options'            => $devices_options['devices_options'],
			'conditions'          => $condition_device,
			'render_type'        => 'none',
			'frontend_available' => true,
		] );

		$element->add_responsive_control( 'the7_sticky_effects_offset', [
			'label'              => esc_html__( 'Scroll Offset (px)', 'the7mk2' ),
			'type'               => Controls_Manager::NUMBER,
			'default'            => 0,
			'min'                => 0,
			'max'                => 1000,
			'required'           => true,
			'conditions'          => $condition_device,
			'render_type'        => 'none',
			'frontend_available' => true,
			'description'        => __( 'Scroll offset is a distance (in pixels) a page has to be scrolled before the style changes will be applied.', 'the7mk2' ),
		] );

		$selector = '{{WRAPPER}}.the7-e-sticky-effects, .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}:not(.fix)';
		$element->add_responsive_control( 'the7_sticky_effects_height', [
			'label'       => __( 'Row Height (px)', 'the7mk2' ),
			'type'        => Controls_Manager::SLIDER,
			'range'       => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'size_units'  => [ 'px' ],
			'selectors'   => [
				'{{WRAPPER}}:not(.the7-e-sticky-spacer).the7-e-sticky-effects > .elementor-container, .the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}:not(.fix) > .elementor-container' => 'min-height: {{SIZE}}{{UNIT}};',
				'.elementor-element-{{ID}} > .elementor-container' => 'min-height: 0;',
			],
			'description' => __( 'Note that the row height will not get smaller than the elements inside of it.', 'the7mk2' ),
			'conditions'   => $condition,
		] );

		$element->add_control( 'the7_sticky_effects_background', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'conditions' => $condition,
			'selectors' => [
				$selector . ', {{WRAPPER}}.the7-e-sticky-effects > .elementor-motion-effects-container > .elementor-motion-effects-layer,
				.the7-e-sticky-effects .elementor-element.elementor-element-{{ID}}:not(.fix) > .elementor-motion-effects-container > .elementor-motion-effects-layer' => 'background-color: {{VALUE}}; background-image: none;',
			],
		] );

		$element->add_control( 'the7_sticky_effects_border_color', [
			'label'     => __( 'Border Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'conditions' => $condition,
			'selectors' => [
				$selector => 'border-color: {{VALUE}}',
			],
		] );


		$element->add_control( 'the7_sticky_effects_border_width', [
			'label'      => __( 'Border Width', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'conditions'  => $condition,
			'selectors'  => [
				$selector => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$element->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'      => 'the7_sticky_effects_shadow',
			'selector'  => $selector,
			'conditions' => $condition,
		] );

		$conditions = [];
		// Target only inner sections.
		// Checking for `$element->get_data( 'isInner' )` in both editor & frontend causes it to work properly on the frontend but
		// break on the editor, because the inner section is created in JS and not rendered in PHP.
		// So this is a hack to force the editor to show the `sticky_parent` control, and still make it work properly on the frontend.
		if ( Elementor::$instance->editor->is_edit_mode() ) {
			$conditions['isInner'] = true;
		}

		$element->add_responsive_control( 'the7_hide_on_sticky_effect', [
			'label'              => esc_html__( 'Visibility', 'the7mk2' ),
			'type'               => Controls_Manager::SELECT,
			'default'            => '',
			'options'            => [
				''     => esc_html__( 'Do Nothing', 'the7mk2' ),
				'hide' => esc_html__( 'Hide When Sticky', 'the7mk2' ),
				'show' => esc_html__( 'Show When Sticky', 'the7mk2' ),
			],
			'render_type'        => 'none',
			'frontend_available' => true,
			'description'        => sprintf( esc_html__( 'When "Sticky" and "Transitions On Scroll" are ON for the parent section.', 'the7mk2' ) ),
			'condition'          => $conditions,
			'separator' => 'before'
		] );


		$element->end_controls_section();
	}


	private function get_asset_conditions_data() {
		return [
			'scripts' => [
				[
					'name'       => 'the7-e-sticky-effect',
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'     => 'the7_sticky_effects',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row',
								'operator' => '!==',
								'value'    => '',
							],
						],
					],
				],
			],
			'styles'  => [
				[
					'name'       => 'the7-e-sticky-effect',
					'conditions' => [
						'relation' => 'or',
						'terms'    => [
							[
								'name'     => 'the7_sticky_effects',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row',
								'operator' => '!==',
								'value'    => '',
							],
							[
								'name'     => 'the7_sticky_row_overlap',
								'operator' => '!==',
								'value'    => '',
							],
						],
					],
				],
			],
		];
	}
}
