<?php
/**
 * The7 Simple Posts widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use stdClass;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use The7\Mods\Compatibility\Elementor\With_Post_Excerpt;

defined( 'ABSPATH' ) || exit;

class Simple_Product_Categories extends Simple_Widget_Base {

	use With_Post_Excerpt;
	use Posts_Masonry_Style;

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-simple-product-categories';
	}

	protected function the7_title() {
		return __( 'Product categories grid', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-grid';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-product-categories.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-product-categories.css',
			[ 'the7-filter-decorations-base', 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-product-categories.js',
			[ 'dt-main' ]
		);

		// Preview script.
		the7_register_script_in_footer(
			$this->get_name() . '-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-product-categories-preview.js',
			[ 'the7-elementor-editor-common' ]
		);
	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	public function get_script_depends() {
		$scripts = [
			$this->get_name(),
		];

		if ( $this->is_preview_mode() ) {
			$scripts[] = $this->get_name() . '-preview';
		}

		return $scripts;
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content.
		$this->add_query_controls();
		$this->add_layout_content_controls();
		$this->add_content_controls();

		$this->template( Pagination::class )->add_content_controls( 'source' );

		// Style.
		$this->add_widget_title_style_controls();

		/**
		 * Common simple box style settings.
		 *
		 * @see Simple_Widget_Base::add_box_content_style_controls()
		 */
		$this->add_box_content_style_controls();
		$this->add_divider_style_controls();

		/**
		 * Common simple image style settings.
		 *
		 * @see Simple_Widget_Base::add_image_style_controls()
		 */
		$this->add_image_style_controls(
			[
				'show_post_image' => 'y',
			]
		);

		$this->add_title_style_controls();
		$this->add_content_area_style_controls();
		$this->add_meta_style_controls();
		$this->add_description_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
			],
			[
				'button_icon' => [
					'default' => [
						'value'   => 'dt-icon-the7-arrow-552',
						'library' => 'the7-icons',
					],
				],
			]
		);

		$this->template( Pagination::class )->add_style_controls( 'source' );
	}

	protected function add_query_controls() {

		$this->start_controls_section(
			'query_section',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source',
			[
				'label'       => __( 'Source', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					''                      => __( 'Show All', 'the7mk2' ),
					'by_id'                 => __( 'Manual Selection', 'the7mk2' ),
					'by_parent'             => __( 'By Parent', 'the7mk2' ),
					'current_subcategories' => __( 'Current Subcategories', 'the7mk2' ),
				],
				'label_block' => true,
			]
		);

		$categories = get_terms( 'product_cat' );

		$options = [];
		foreach ( $categories as $category ) {
			$options[ $category->term_id ] = $category->name;
		}

		$this->add_control(
			'categories',
			[
				'label'       => __( 'Categories', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $options,
				'default'     => [],
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'source' => 'by_id',
				],
			]
		);

		$parent_options = [ '0' => __( 'Only Top Level', 'the7mk2' ) ] + $options;
		$this->add_control(
			'parent',
			[
				'label'     => __( 'Parent', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => $parent_options,
				'condition' => [
					'source' => 'by_parent',
				],
			]
		);

		$this->add_control(
			'hide_empty',
			[
				'label'     => __( 'Hide Empty', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'label_on'  => 'Hide',
				'label_off' => 'Show',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Order By', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name'        => __( 'Name', 'the7mk2' ),
					'slug'        => __( 'Slug', 'the7mk2' ),
					'description' => __( 'Description', 'the7mk2' ),
					'count'       => __( 'Count', 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => __( 'ASC', 'the7mk2' ),
					'desc' => __( 'DESC', 'the7mk2' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_layout_content_controls() {

		$this->start_controls_section(
			'layout_content_section',
			[
				'label' => __( 'Layout', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_widget_title',
			[
				'label'        => __( 'Widget Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'widget_title_text',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Widget title',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h3',
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_control(
			'widget_columns_wide_desktop',
			[
				'label'       => __( 'Columns On A Wide Desktop', 'the7mk2' ),
				'description' => sprintf(
				// translators: %s: elementor content width.
					__( 'Apply when browser width is bigger than %s ("Content Width" Elementor setting).', 'the7mk2' ),
					the7_elementor_get_content_width_string()
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 12,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .dt-css-grid' => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_basic_responsive_control(
			'widget_columns',
			[
				'label'          => __( 'Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'tablet_default' => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 12,
				'selectors'      => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},minmax(0, 1fr))',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}}',
				],
				'render_type'    => 'template',
			]
		);

		$this->add_basic_responsive_control(
			'gap_between_posts',
			[
				'label'      => __( 'Columns Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '40',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'rows_gap',
			[
				'label'      => __( 'Rows Gap', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => '20',
				],
				'range'      => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .dt-css-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}; --grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor' ),
				'label_on'  => __( 'On', 'elementor' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'     => __( 'Apply Link & Hover', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'button',
				'options'   => [
					'box'  => __( 'Whole box', 'the7mk2' ),
					'button' => __( "Separate element's", 'the7mk2' ),
				],
			]
		);

		$this->add_control(
			'show_post_image',
			[
				'label'        => __( 'Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'show_post_title',
			[
				'label'        => __( 'Title', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default'   => 'h5',
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_width',
			[
				'label'     => __( 'Title Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_post_title' => 'y',
					'title_width'     => 'normal',
				],
			]
		);

		$this->add_control(
			'post_content',
			[
				'label'        => __( 'Description', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'show_excerpt',
				'default'      => 'show_excerpt',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'description_width',
			[
				'label'     => __( 'Description Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire excerpt.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'condition'   => [
					'post_content'      => 'show_excerpt',
					'description_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'products_count',
			[
				'label'        => __( 'Products Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'products_custom_format',
			[
				'label'        => __( 'Custom Format', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => false,
				'return_value' => 'yes',
				'condition'    => [
					'products_count' => 'y',
				],
			]
		);

		$this->add_control(
			'string_no_products',
			[
				'label'       => __( 'No Products', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'No Products', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'string_one_product',
			[
				'label'       => __( 'One Product', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'One Product', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'string_products',
			[
				'label'       => __( 'Products', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( '%s Products', 'the7mk2' ),
				'condition'   => [
					'products_custom_format' => 'yes',
					'products_count'         => 'y',
				],
			]
		);

		$this->add_control(
			'show_read_more_button',
			[
				'label'        => __( 'Button', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => __( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'View Category', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_widget_title_style_controls() {
		$this->start_controls_section(
			'widget_style_section',
			[
				'label'     => __( 'Widget Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_widget_title' => 'y',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_title_align',
			[
				'label'     => __( 'Alignment', 'the7mk2' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
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
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'text-align: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widget_title_typography',
				'selector' => '{{WRAPPER}} .rp-heading',
			]
		);

		$this->add_control(
			'widget_title_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .rp-heading' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'widget_title_bottom_margin',
			[
				'label'      => __( 'Spacing Below Title', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rp-heading' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_divider_style_controls() {
		$this->start_controls_section(
			'widget_divider_section',
			[
				'label'     => __( 'Dividers', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'     => __( 'Style', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'the7mk2' ),
					'double' => __( 'Double', 'the7mk2' ),
					'dotted' => __( 'Dotted', 'the7mk2' ),
					'dashed' => __( 'Dashed', 'the7mk2' ),
				],
				'default'   => 'solid',
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on' => '--divider-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'divider' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .widget-divider-on .wf-cell:before' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_area_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'content_area_style',
			[
				'label'     => __( 'Content Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_basic_responsive_control(
			'content_alignment',
			[
				'label'        => __( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
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
				'prefix_class' => 'slide-h-position%s-',
				'default'      => 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'selectors'    => [
					'{{WRAPPER}} .post-entry-content' => '{{VALUE}}',
				],

			]
		);

		$this->add_basic_responsive_control(
			'content_area_padding',
			[
				'label'      => __( 'Content Area Padding', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .post-entry-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_title' => 'y',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .heading',
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => __( 'Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .product-name:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.wf-cell:hover .product-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function add_meta_style_controls() {
		$this->start_controls_section(
            'post_meta_style_section',
            [
                'label'     => __( 'Products Count', 'the7mk2' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'products_count' => 'y',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'           => 'post_meta',
                'label'          => __( 'Typography', 'the7mk2' ),
                'fields_options' => [
                    'font_family' => [
                        'default' => '',
                    ],
                    'font_size'   => [
                        'default' => [
                            'unit' => 'px',
                            'size' => '',
                        ],
                    ],
                    'font_weight' => [
                        'default' => '',
                    ],
                    'line_height' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => '',
                        ],
                    ],
                ],
                'selector'       => '{{WRAPPER}} .the7-simple-widget-product-categories .entry-meta',
            ]
        );

        $this->start_controls_tabs( 'tabs_post_meta_style' );

		$this->start_controls_tab(
			'tab_post_meta_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'tab_post_meta_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_post_meta_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'field_post_meta_color_hover',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .entry-meta:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.wf-cell:hover .entry-meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->add_basic_responsive_control(
            'post_meta_bottom_margin',
            [
                'label'      => __( 'Product Count Spacing Above', 'the7mk2' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 200,
                        'step' => 1,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .entry-meta' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->end_controls_section();
	}

	protected function add_description_style_controls() {

		$this->start_controls_section(
			'short_description',
			[
				'label'     => __( 'Description', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_content' => 'show_excerpt',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .short-description',
			]
		);

		$this->start_controls_tabs( 'tabs_description_style' );

		$this->start_controls_tab(
			'tab_desc_color_normal',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_desc_color_hover',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'short_desc_color_hover',
			[
				'label'     => __( 'Font Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .short-description:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.wf-cell:hover .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'gap_above_description',
			[
				'label'      => __( 'Description Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .short-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_categories = $this->product_categories();
		if ( empty( $product_categories->terms ) ) {
			return;
		}

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );

		$this->template( Pagination::class )->add_containter_attributes( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}
		$link_class = '';
		if ( 'button' !== $settings['link_click'] ) {
			$link_class = 'box-hover';
		}

		$columns = [
			'd'  => $settings['widget_columns'],
			't'  => $settings['widget_columns_tablet'],
			'p'  => $settings['widget_columns_mobile'],
			'wd' => $settings['widget_columns_wide_desktop'],
		];

		echo '<div class="dt-css-grid custom-pagination-handler"  data-columns="' . esc_attr( wp_json_encode( $columns ) ) . '">';

		foreach ( $product_categories->terms as $category ) {
			$this->remove_render_attribute( 'product_wrapper' );
			$this->add_render_attribute(
				'product_wrapper',
				'class',
				[
					'wf-cell',
					'visible',
					$link_class
				],
				true
			);

			$wrapper_tag = 'div';
			if ( 'button' !== $settings['link_click'] ) {
				$this->add_link_attributes( 'product_wrapper', $this->get_custom_link_attributes( $category ) );
				$wrapper_tag = 'a';
			}

			echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'product_wrapper' ) . '>';

			$post_class_array = [
				'post',
				'visible',
				'wrapper',
			];

			if ( ! get_term_meta( $category->term_id, 'thumbnail_id', true ) ) {
				$post_class_array[] = 'no-img';
			}
			echo '<article class="' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
				echo '<div class="post-content-wrapper">';
					if ( $settings['show_post_image'] ) {
						$post_media = $this->get_category_image( $settings, $category );

						if ( $post_media ) {
							echo '<div class="the7-simple-post-thumb">';
							echo $post_media;
							echo '</div>';
						}
					}

					echo '<div class="post-entry-content">';
						if ( $settings['show_post_title'] ) {
							echo $this->get_category_title( $settings, $settings['post_title_tag'], $category );
						}

						if ( $settings['products_count'] ) {
							echo $this->get_category_count( $settings, $category );
						}

						if ( $settings['post_content'] === 'show_excerpt' ) {
							echo $this->get_category_description( $category );
						}

						if ( $settings['show_read_more_button'] ) {
							$this->render_details_btn( $category );
						}
					echo '</div>';
				echo '</div>';
			echo '</article>';
			echo '</' . $wrapper_tag . '>';
		}

		echo '</div>';

		$this->template( Pagination::class )->render( $product_categories->max_num_pages );

		echo '</div>';
	}

	protected function get_custom_link_attributes( $category ) {
		$term_link = get_term_link( $category, 'product_cat' );

		if ( is_wp_error( $term_link ) ) {
			$term_link = '';
		}

		return [
			'url' => $term_link,
		];
	}

	protected function product_categories() {
		$settings = $this->get_settings_for_display();

		$posts_per_page = $this->template( Pagination::class )->get_posts_per_page();
		$number         = $posts_per_page;
		if ( $this->template( Pagination::class )->get_loading_mode() === 'standard' ) {
			$number = 9999;
		}

		$attributes = [
			'number'     => $number,
			'hide_empty' => 'yes' === $settings['hide_empty'],
			'orderby'    => $settings['orderby'],
			'order'      => $settings['order'],
			'parent'     => '',
			'include'    => [],
			'offset'     => $settings['posts_offset'],
		];

		if ( 'by_id' === $settings['source'] ) {
			$attributes['include'] = array_filter( (array) $settings['categories'] );
		} elseif ( 'by_parent' === $settings['source'] ) {
			$attributes['parent'] = $settings['parent'];
		} elseif ( 'current_subcategories' === $settings['source'] ) {
			$attributes['object_ids'] = get_queried_object_id();
		}

		$terms = get_terms( 'product_cat', $attributes );

		$max_num_pages = 1;
		if ( $settings['loading_mode'] === 'standard' ) {
			$terms_chunk   = array_chunk( $terms, $posts_per_page );
			$max_num_pages = count( $terms_chunk );
			$current_page  = the7_get_paged_var();
			if ( isset( $terms_chunk[ $current_page - 1 ] ) ) {
				$terms = $terms_chunk[ $current_page - 1 ];
			}
		}

		$terms_query                = new stdClass();
		$terms_query->terms         = $terms;
		$terms_query->max_num_pages = $max_num_pages;

		return $terms_query;
	}

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = esc_html( $tag );

		$output = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_hover_icons_html_template( $settings ) {
		// if ( ! $settings['show_hover_icon'] ) {
		// 	return '';
		// }

		//$a_atts               = $this->get_link_attributes( $settings );
		$a_atts['class']      = 'the7-hover-icon';
		$a_atts['aria-label'] = __( 'Details link', 'the7mk2' );

		return sprintf(
			'<span %s>%s</span>',
			the7_get_html_attributes_string( $a_atts ),
			$this->get_elementor_icon_html( $settings['hover_icon'], 'i' )
		);
	}

	protected function get_category_image( $settings, $category ) {
		$link         = get_term_link( $category, 'product_cat' );
		$post_media   = '';
		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$link_class = [ 'post-thumbnail-rollover', 'img-ratio-wrapper' ];
			if ( ! $link ) {
				$link_class[] = 'not-clickable-item';
			}
			$icons_html = $this->get_hover_icons_html_template( $settings );

			$link_wrapper = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>';

			$thumb_args = [
				'img_id' => $thumbnail_id,
				'class'  => implode( ' ', $link_class ),
				'custom' => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Category Image', 'the7mk2' ),
					]
				),
				'wrap'   => $link_wrapper,
				'echo'   => false,
			];

			if ( $settings['link_click'] == 'box' ) {
				$thumb_args['wrap'] = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</div>';
			} else {
				$thumb_args['href'] = $link;
			}

			$thumb_args['img_class'] = 'preload-me';

			if ( presscore_lazy_loading_enabled() ) {
				$thumb_args['lazy_loading'] = true;
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		}

		return $post_media;
	}

	protected function get_category_title( $settings, $tag, $category ) {
		$tag        = esc_html( $tag );
		$title_link = [
			'href'  => get_term_link( $category, 'product_cat' ),
			'class' => 'product-name',
		];
		$title      = $category->name;
		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper     	= '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</a>';
		} else {
			$title_link['href'] 		= '';
			$title_link_wrapper    		= '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</span>';
		}

		$output = '<' . $tag . ' class="heading">';
		$output .=  sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_category_count( $settings, $category ) {

		$default_strings = [
			'string_no_products' => __( 'No Products', 'the7mk2' ),
			'string_one_product' => __( 'One Product', 'the7mk2' ),
			'string_products'    => __( '%s Products', 'the7mk2' ),
		];

		if ( 'yes' === $settings['products_custom_format'] ) {
			if ( ! empty( $settings['string_no_products'] ) ) {
				$default_strings['string_no_products'] = $settings['string_no_products'];
			}

			if ( ! empty( $settings['string_one_product'] ) ) {
				$default_strings['string_one_product'] = $settings['string_one_product'];
			}

			if ( ! empty( $settings['string_products'] ) ) {
				$default_strings['string_products'] = $settings['string_products'];
			}
		}

		$num_products = (int) $category->count;

		if ( 0 === $num_products ) {
			$string = $default_strings['string_no_products'];
		} elseif ( 1 === $num_products ) {
			$string = $default_strings['string_one_product'];
		} else {
			$string = sprintf( $default_strings['string_products'], $num_products );
		}

		$output = '<div class="entry-meta">';
		$output .= wp_kses_post( $string );
		$output .= '</div>';

		return $output;
	}

	protected function get_category_description( $category ) {
		$settings = $this->get_settings_for_display();

		$excerpt = $category->description;
		if ( ! $excerpt ) {
			return;
		}

		if ( $settings['excerpt_words_limit'] && $settings['description_width'] === 'normal' ) {
			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_words_limit'] );
		}

		$output = '<p class="short-description">';
		$output .= wp_kses_post( $excerpt );
		$output .= '</p>';

		return $output;
	}

	protected function render_details_btn( $category ) {
		$settings = $this->get_settings_for_display();

		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$this->add_render_attribute( 'box-button', 'class', 'product-details' );

		$tag = 'div';
		if ( 'button' === $settings['link_click'] ) {
			$tag = 'a';
			$this->add_link_attributes( 'box-button', $this->get_custom_link_attributes( $category ) );
		}

		$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['read_more_button_text'] ), $tag );
	}

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'the7-simple-widget-product-categories',
			'the7-elementor-widget',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		if ( $settings['divider'] ) {
			$class[] = 'widget-divider-on';
		}

		if ( $settings['title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		if ( ! $settings['show_post_image'] ) {
			$class[] = 'hide-post-image';
		}

		$this->add_render_attribute( $element, 'class', $class );
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class(),
			'~"%s"'
		);
		foreach ( $this->get_supported_devices() as $device => $dep ) {
			$less_vars->start_device_section( $device );
			$less_vars->add_keyword(
				'grid-columns',
				$this->get_responsive_setting( 'widget_columns' ) ?: 3
			);
			$less_vars->close_device_section();
		}
		$less_vars->add_keyword('grid-wide-columns', $settings['widget_columns_wide_desktop']);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}
	}
}
