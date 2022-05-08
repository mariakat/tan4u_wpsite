<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WP_Query;
use WP_Term;

defined( 'ABSPATH' ) || exit;

/**
 * Class Taxonomy_Filter
 */
class Taxonomy_Filter extends The7_Elementor_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-taxonomy-filter';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	protected function the7_title() {
		return __( 'Taxonomy Filter', 'the7mk2' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-taxonomy-filter.css',
			[ 'the7-filter-decorations-base' ]
		);
	}

	/**
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$get_terms_args = [
			'taxonomy'   => $settings['taxonomy'],
			'hide_empty' => ! $settings['terms'],
		];

		if ( ! empty( $settings['terms'] ) ) {
			// Try to get rid empty ids.
			$get_terms_args['include'] = array_filter( array_map( 'intval', (array) $settings['terms'] ) );
		}

		$terms = get_terms( $get_terms_args );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return;
		}

		$this->add_render_attributes_for_the_wrapper( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="filter-categories">';

		$current_term = $this->get_current_term();

		if ( $settings['filter_show_all'] ) {
			$default_filter_item_class = 'filter-item' . ( $current_term ? '' : ' act' );
			echo '<a href="' . esc_url( remove_query_arg( [ 'taxonomy', 'term' ] ) ) . '" class="' . esc_attr( $default_filter_item_class ) . '">' . esc_html( $settings['filter_all_text'] ) . '</a>';
		}

		foreach ( $terms as $term_obj ) {
			$class = 'filter-item';
			if ( in_array( $current_term, [ (string) $term_obj->term_id, (string) $term_obj->slug ], true ) ) {
				$class .= ' act';
			}

			$filter_item_element = "filter-item-{$term_obj->slug}";

			$this->add_render_attribute(
				$filter_item_element,
				[
					'class'       => $class,
					'href'        => esc_url(
						add_query_arg(
							[
								'taxonomy' => $settings['taxonomy'],
								'term'     => $term_obj->slug,
							]
						)
					),
					'data-filter' => ".category-{$term_obj->term_id}",
				]
			);

			echo '<a ' . $this->get_render_attribute_string( $filter_item_element ) . '>' . esc_html( $term_obj->name ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->add_query_controls();
		$this->add_style_controls();
	}

	/**
	 * Query controls.
	 */
	protected function add_query_controls() {
		/**
		 * Must have section_id = query_section to work properly.
		 *
		 * @see elements-widget-settings.js:onEditSettings()
		 */
		$this->start_controls_section(
			'query_section',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$post_type_options = the7_elementor_elements_widget_post_types( [ 'product' ] );
		unset( $post_type_options['current_query'] );

		$this->add_control(
			'post_type',
			[
				'label'   => __( 'Source', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => $post_type_options,
				'classes' => 'select2-medium-width',
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'     => __( 'Select Taxonomy', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'post_type!' => '',
				],
			]
		);

		$this->add_control(
			'terms',
			[
				'label'     => __( 'Select Terms', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT2,
				'default'   => '',
				'multiple'  => true,
				'options'   => [],
				'classes'   => 'select2-medium-width',
				'condition' => [
					'taxonomy!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style controls.
	 */
	protected function add_style_controls() {
		$this->start_controls_section(
			'filter_bar_style_section',
			[
				'label' => __( 'Style', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'filter_show_all',
			[
				'label'        => __( '"All" Filter', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'filter_all_text',
			[
				'label'       => __( '"All" Filter Label', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'View all', 'the7mk2' ),
				'placeholder' => '',
				'condition'   => [
					'filter_show_all' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'filter_position',
			[
				'label'                => __( 'Align', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .filter'                => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .filter .filter-categories' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .filter .filter-extras' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'filter_style',
			[
				'label'          => __( 'Pointer', 'the7mk2' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => 'underline',
				'options'        => [
					'none'        => __( 'None', 'the7mk2' ),
					'underline'   => __( 'Underline', 'the7mk2' ),
					'overline'    => __( 'Overline', 'the7mk2' ),
					'double-line' => __( 'Double Line', 'the7mk2' ),
					'framed'      => __( 'Framed', 'the7mk2' ),
					'background'  => __( 'Background', 'the7mk2' ),
					'text'        => __( 'Text', 'the7mk2' ),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'animation_line',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'     => 'Fade',
					'slide'    => 'Slide',
					'grow'     => 'Grow',
					'drop-in'  => 'Drop In',
					'drop-out' => 'Drop Out',
					'none'     => 'None',
				],
				'condition' => [
					'filter_style' => [ 'underline', 'overline', 'double-line' ],
				],
			]
		);

		$this->add_control(
			'animation_framed',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'    => 'Fade',
					'grow'    => 'Grow',
					'shrink'  => 'Shrink',
					'draw'    => 'Draw',
					'corners' => 'Corners',
					'none'    => 'None',
				],
				'condition' => [
					'filter_style' => 'framed',
				],
			]
		);

		$this->add_control(
			'animation_background',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'fade'                   => 'Fade',
					'grow'                   => 'Grow',
					'shrink'                 => 'Shrink',
					'sweep-left'             => 'Sweep Left',
					'sweep-right'            => 'Sweep Right',
					'sweep-up'               => 'Sweep Up',
					'sweep-down'             => 'Sweep Down',
					'shutter-in-vertical'    => 'Shutter In Vertical',
					'shutter-out-vertical'   => 'Shutter Out Vertical',
					'shutter-in-horizontal'  => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none'                   => 'None',
				],
				'condition' => [
					'filter_style' => 'background',
				],
			]
		);

		$this->add_control(
			'animation_text',
			[
				'label'     => __( 'Animation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'grow',
				'options'   => [
					'grow'   => 'Grow',
					'shrink' => 'Shrink',
					'sink'   => 'Sink',
					'float'  => 'Float',
					'skew'   => 'Skew',
					'rotate' => 'Rotate',
					'none'   => 'None',
				],
				'condition' => [
					'filter_style' => 'text',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'filter_typography',
				'label'    => __( 'Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .filter a',
			]
		);

		$this->add_control(
			'filter_underline_height',
			[
				'label'      => __( 'Pointer Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => '',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-pointer-border-width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'filter_style!' => [ 'background', 'none', 'text', 'default' ],
				],
			]
		);

		$this->start_controls_tabs( 'filter_elemenets_style' );

		$this->start_controls_tab(
			'filter_normal_style',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'navigation_font_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-title-color-normal: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_hover_style',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'filter_hover_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-title-color-hover: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'filter_hover_pointer_color',
			[
				'label'     => __( 'Pointer Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-pointer-bg-color-hover: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'filter_active_style',
			[
				'label' => __( 'Active', 'the7mk2' ),
			]
		);

		$this->add_control(
			'filter_active_text_color',
			[
				'label'     => __( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-title-color-active: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'filter_active_pointer_color',
			[
				'label'     => __( 'Pointer Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-pointer-bg-color-active: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'filter_bg_border_radius',
			[
				'label'      => __( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter.filter-decorations *' => '--filter-pointer-bg-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'filter_style' => 'background',
				],
			]
		);

		$this->add_basic_responsive_control(
			'filter_element_padding',
			[
				'label'      => __( 'Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter .filter-categories a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .filter .filter-by'      => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .filter .filter-sorting' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'filter_element_margin',
			[
				'label'      => __( 'Margin', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter .filter-categories a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .filter .filter-by'      => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .filter .filter-sorting' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return string
	 */
	protected function get_current_term() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';

		if ( $taxonomy !== $this->get_settings_for_display( 'taxonomy' ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
	}

	/**
	 * @param string $element Element name.
	 */
	protected function add_render_attributes_for_the_wrapper( $element ) {
		$settings = $this->get_settings_for_display();

		$class = [
			'filter-decorations',
			'without-isotope',
			'filter',
		];

		if ( $settings['filter_style'] ) {
			$class[] = 'filter-pointer-' . $settings['filter_style'];

			foreach ( $settings as $key => $value ) {
				if ( $value && strpos( $key, 'animation' ) === 0 ) {
					$class[] = 'filter-animation-' . $value;
					break;
				}
			}
		}

		$this->add_render_attribute( $element, 'class', $class );
	}
}
