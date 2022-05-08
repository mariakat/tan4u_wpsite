<?php
/**
 * The7 Simple Posts widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use stdClass;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

class Simple_Product_Categories_Carousel extends Simple_Product_Categories {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-simple-product-categories-carousel';
	}

	protected function the7_title() {
		return __( 'Product categories carousel', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-products';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-product-categories-carousel.less';
	}

	public function get_style_depends() {
		return [ 'the7-simple-product-categories-carousel', 'the7-simple-common' ];
	}

	public function get_script_depends() {
		$scripts = [
			'the7-simple-product-categories-carousel',
		];

		if ( $this->is_preview_mode() ) {
			$scripts[] = 'the7-elements-carousel-widget-preview';
			$scripts[] = 'the7-simple-product-categories-carousel-preview';
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
		$this->add_scrolling_controls();
		$this->add_arrows_controls();
		$this->template( Bullets::class )->add_content_controls();

		// Style.
		$this->add_widget_title_style_controls();

		/**
		 * Common simple box style settings.
		 *
		 * @see Simple_Widget_Base::add_box_content_style_controls()
		 */
		$this->add_box_content_style_controls();

		/**
		 * Common simple image style settings.
		 *
		 * @see Simple_Widget_Base::add_image_style_controls()
		 */
		$this->add_image_style_controls(
			[
				'show_product_image' => 'y',
			]
		);

		$this->add_content_area_style_controls();
		$this->add_title_style_controls();
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
		$this->add_arrows_style_controls();
		$this->template( Bullets::class )->add_style_controls();
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


		$this->add_control(
			'dis_posts_total',
			[
				'label'       => __( 'Total Number Of Posts', 'the7mk2' ),
				'description' => __( 'Leave empty to display all posts.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '12',
				'condition'   => [
					'source!' => 'current_subcategories',
				],
			]
		);

		$this->add_control(
			'posts_offset',
			[
				'label'       => __( 'Posts Offset', 'the7mk2' ),
				'description' => __(
					'Offset for posts query (i.e. 2 means, posts will be displayed starting from the third post).',
					'the7mk2'
				),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'condition'   => [
					'source!' => 'current_subcategories',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_scrolling_controls() {

		$this->start_controls_section(
			'scrolling_section',
			[
				'label' => __( 'Scrolling', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slide_to_scroll',
			[
				'label'   => __( 'Scroll Mode', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => [
					'single' => 'One slide at a time',
					'all'    => 'All slides',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'       => __( 'Transition Speed (ms)', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '600',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay Slides', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'y',
				'default'      => '',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'       => __( 'Autoplay Speed (ms)', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6000,
				'min'         => 100,
				'max'         => 10000,
				'step'        => 10,
				'condition'   => [
					'autoplay' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_arrows_controls() {

		$this->start_controls_section(
			'arrows_section',
			[
				'label' => __( 'Arrows', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$arrow_options            = [
			'never'  => __( 'Never', 'the7mk2' ),
			'always' => __( 'Always', 'the7mk2' ),
			'hover'  => __( 'On Hover', 'the7mk2' ),
		];
		$arrow_options_on_devices = [
			'' => __( 'Default', 'the7mk2' ),
		] + $arrow_options;

		$this->add_basic_responsive_control(
			'arrows',
			[
				'label'       => __( 'Show Arrows', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $arrow_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $arrow_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $arrow_options_on_devices,
					],
				],
				'default'     => 'always',
			]
		);
		$arrow_position_options            = [
			'box_area' => 'Box Area',
			'image'         => 'Image',
		];
		$arrow_position_options_on_devices = [
			'' => __( 'Default', 'the7mk2' ),
		] + $arrow_position_options;
		$this->add_basic_responsive_control(
			'arrows_position',
			[
				'label'   => __( 'Vertically aligned to', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'box_area',
				'options'     => $arrow_position_options,
				'device_args'          => [
					'tablet' => [
						'default' => '',
						'options' => $arrow_position_options_on_devices,
					],
					'mobile' => [
						'default' => '',
						'options' => $arrow_position_options_on_devices,
					],
				],
				// 'options' => [
				// 	'box_area' => 'Box Area',
				// 	'image'         => 'Image',
				// ],
				'selectors_dictionary' => [
					'image' => $this->combine_to_css_vars_definition_string(
						[
							
							'offset-v-t-img'                  => 'var(--stage-top-gap) + var(--box-padding-top)',
							'offset-v-m-img'                  => 'calc(var(--stage-top-gap) + var(--box-padding-top) + var(--arrow-height)/2)',
							'arrow-height'         => 'var(--dynamic-img-height)',
							'top-b-img' => '0px',
							'offset-v-b-img'                  => 'calc(var(--stage-top-gap) + var(--box-padding-top) + var(--arrow-height) - var(--arrow-bg-height, var(--arrow-icon-size)))',
						]
					),
					'box_area' => $this->combine_to_css_vars_definition_string(
						[
							
							'offset-v-t-img' => '0px',
							'offset-v-m-img'                  => '50%',
							'top-b-img' => '100%',
							'offset-v-b-img'                  => '0px',
						]
					),
				],
				'selectors'            => [
					'{{WRAPPER}} .owl-carousel' => '{{VALUE}}',
				],
				'prefix_class' => 'arrows%s-relative-to-',

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
			'title_tag',
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
			]
		);

		$this->add_basic_responsive_control(
			'gap_between_posts',
			[
				'label'      => __( 'Columns Gap (px)', 'the7mk2' ),
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
			]
		);
		$this->add_basic_responsive_control(
			'carousel_margin',
			[
				'label'      => __( 'outer gaps', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-stage, {{WRAPPER}} .owl-carousel' => '--stage-top-gap:{{TOP}}{{UNIT}}; --stage-right-gap:{{RIGHT}}{{UNIT}};  --stage-left-gap:{{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .owl-stage-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'render_type'        => 'template',
			]
		);

		$this->end_controls_section();
	}

	protected function add_image_controls() {

		$this->start_controls_section(
			'featured_image',
			[
				'label' => __( 'Featured Image', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
			'show_product_image',
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
			'show_product_title',
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
					'show_product_title' => 'y',
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
					'show_product_title' => 'y',
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
					'show_product_title' => 'y',
					'title_width'     => 'normal',
				],
			]
		);

		$this->add_control(
			'show_term_description',
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
					'show_term_description' => 'show_excerpt',
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
					'show_term_description'      => 'show_excerpt',
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
					'show_product_title' => 'y',
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
					'{{WRAPPER}} a.post.wrapper:hover .product-name' => 'color: {{VALUE}};',
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
                'selector'       => '{{WRAPPER}} .entry-meta',
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
					'{{WRAPPER}} a.post.wrapper:hover .entry-meta' => 'color: {{VALUE}}',
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
					'show_term_description' => 'show_excerpt',
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
					'{{WRAPPER}} a.post.wrapper:hover .short-description' => 'color: {{VALUE}}',
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

	protected function add_arrows_style_controls() {
		$this->start_controls_section(
			'arrows_style',
			[
				'label'      => __( 'Arrows', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'arrows',
							'operator' => '!=',
							'value' => 'never',
						],
						[
							'name'  => 'arrows_tablet',
							'operator' => '!=',
							'value' => 'never',
						],
						[
							'name'  => 'arrows_mobile',
							'operator' => '!=',
							'value' => 'never',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __( 'Arrow Icon', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'next_icon',
			[
				'label'   => __( 'Next Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icomoon-the7-font-the7-arrow-09',
					'library' => 'the7-icons',
				],
				'skin' => 'inline',
				'label_block' => false,
				'classes' => [ 'elementor-control-icons-svg-uploader-hidden' ],
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label'   => __( 'Previous Arrow', 'the7mk2' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'icomoon-the7-font-the7-arrow-08',
					'library' => 'the7-icons',
				],
				'skin' => 'inline',
				'label_block' => false,
				'classes' => [ 'elementor-control-icons-svg-uploader-hidden' ],
			]
		);

		$this->add_basic_responsive_control(
			'arrow_icon_size',
			[
				'label'      => __( 'Arrow Icon Size', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 16,
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
					'{{WRAPPER}} .owl-carousel' => '--arrow-icon-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .owl-nav i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .owl-nav a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrows_background_heading',
			[
				'label'     => __( 'Arrow style', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'arrow_bg_width',
			[
				'label'      => __( 'Background Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 30,
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
					'{{WRAPPER}} .owl-nav a' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'arrow_bg_height',
			[
				'label'      => __( 'Background Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 30,
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
					'{{WRAPPER}} .owl-carousel' => '--arrow-bg-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .owl-nav a' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Arrow Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 500,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-nav a' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrow_border_width',
			[
				'label'      => __( 'Arrow Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 2,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 25,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .owl-nav a' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid',
				],
			]
		);

		$this->start_controls_tabs( 'arrows_style_tabs' );

		$this->start_controls_tab(
			'arrows_colors',
			[
				'label' => __( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_color',
			[
				'label'       => __( 'Icon Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a i, {{WRAPPER}} .owl-nav a i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'       => __( 'Border Color', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a'       => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-nav a'       => 'background: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover_colors',
			[
				'label' => __( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'arrow_icon_color_hover',
			[
				'label'       => __( 'Icon Color Hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'{{WRAPPER}} .owl-nav a:hover i'                                                                             => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a i:before { transition: color 0.3s; } {{WRAPPER}} .owl-nav a:hover i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color_hover',
			[
				'label'       => __( 'Border Color Hover', 'the7mk2' ),
				'type'        => Controls_Manager::COLOR,
				'alpha'       => true,
				'default'     => '',
				'selectors'   => [
					'
					{{WRAPPER}} .owl-nav a { transition: all 0.3s; }
					{{WRAPPER}} .owl-nav a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_bg_color_hover',
			[
				'label'     => __( 'Background Hover Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'
					{{WRAPPER}} .owl-nav a { transition: all 0.3s; }
					{{WRAPPER}} .owl-nav a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'left_arrow_position_heading',
			[
				'label'     => __( 'Left Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'l_arrow_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'center',
			]
		);

		$this->add_basic_responsive_control(
			'l_arrow_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'left',
			]
		);

		$this->add_basic_responsive_control(
			'l_arrow_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_basic_responsive_control(
			'l_arrow_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -15,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'right_arrow_position_heading',
			[
				'label'     => __( 'Right Arrow Position', 'the7mk2' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_basic_responsive_control(
			'r_arrow_v_position',
			[
				'label'       => __( 'Vertical Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top'    => [
						'title' => __( 'Top', 'the7mk2' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'the7mk2' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'center',
			]
		);

		$this->add_basic_responsive_control(
			'r_arrow_h_position',
			[
				'label'       => __( 'Horizontal Position', 'the7mk2' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left'   => [
						'title' => __( 'Left', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'the7mk2' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'right'  => [
						'title' => __( 'Right', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'right',
			]
		);

		$this->add_basic_responsive_control(
			'r_arrow_v_offset',
			[
				'label'      => __( 'Vertical Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_basic_responsive_control(
			'r_arrow_h_offset',
			[
				'label'      => __( 'Horizontal Offset', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'unit' => 'px',
					'size' => -15,
				],
				'size_units' => [ 'px'],
				'range'      => [
					'px' => [
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_container_data_atts() {
		$settings = $this->get_settings_for_display();

		$data_atts = [
			'scroll-mode'          => $settings['slide_to_scroll'] === 'all' ? 'page' : '1',
			'col-num'              => $settings['widget_columns'],
			'wide-col-num'         => $settings['widget_columns_wide_desktop'] ?: $settings['widget_columns'],
			'laptop-col'           => $settings['widget_columns_tablet'],
			'h-tablet-columns-num' => $settings['widget_columns_tablet'],
			'v-tablet-columns-num' => $settings['widget_columns_tablet'],
			'phone-columns-num'    => $settings['widget_columns_mobile'],
			'auto-height'          => $settings['adaptive_height'] ? 'true' : 'false',
			'col-gap'              => $settings['gap_between_posts']['size'],
			'col-gap-tablet'       => $settings['gap_between_posts_tablet']['size'],
			'col-gap-mobile'       => $settings['gap_between_posts_mobile']['size'],
			'speed'                => $settings['speed'],
			'autoplay'             => $settings['autoplay'] ? 'true' : 'false',
			'autoplay_speed'       => $settings['autoplay_speed'],
			'arrows'               => $settings['arrows'] !== 'never' ? 'true' : 'false',
			'arrows_tablet'        => $settings['arrows_tablet'] !== 'never' ? 'true' : 'false',
			'arrows_mobile'        => $settings['arrows_mobile'] !== 'never' ? 'true' : 'false',
			'bullet'               => $settings['show_bullets'],
			'bullet_tablet'        => $settings['show_bullets_tablet'],
			'bullet_mobile'        => $settings['show_bullets_mobile'],
		];

		return ' ' . presscore_get_inlide_data_attr( $data_atts );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$product_categories = $this->product_categories();
		if ( empty( $product_categories->terms ) ) {
			return false;
		}

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'inner-wrapper' );

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		echo '<div ' . $this->get_render_attribute_string( 'inner-wrapper' ) . $this->get_container_data_atts() . '>';

			$index = 0;
			foreach ( $product_categories->terms as $category ) {
				$index++;

				$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'link_wrapper', $index );

				$post_class_array = [
					'post',
					'visible',
					'wrapper'
				];

				if ( ! get_term_meta( $category->term_id, 'thumbnail_id', true ) ) {
					$post_class_array[] = 'no-img';
				}

				$link_key = 'link_' . $index;
				$link_attridutes        = $this->get_custom_link_attributes( $category );
				$this->add_link_attributes( $link_key, $link_attridutes, true );
				$btn_attributes = $this->get_render_attribute_string( $link_key );

				if ( 'button' === $settings['link_click'] ) {
					$parent_wrapper       	= '<article class="dt-owl-item-wrap '. esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) .'">';
					$parent_wrapper_close 	= '</article>';
				} else {
					$parent_wrapper       	= '<a '. $btn_attributes .' class="dt-owl-item-wrap box-hover '. esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) .'">';
					$parent_wrapper_close 	= '</a>';
				}

				echo $parent_wrapper;
					echo '<div class="post-content-wrapper">';
						if ( $settings['show_product_image'] ) {
							$post_media = $this->get_category_image( $settings, $category );

							if ( $post_media ) {
								echo '<div class="the7-simple-post-thumb">' . $post_media .'</div>';
							}
						}

						echo '<div class="post-entry-content">';
							if ( $settings['show_product_title'] ) {
								echo $this->get_category_title( $settings, $settings['post_title_tag'], $category );
							}

							if ( $settings['products_count'] ) {
								echo $this->get_category_count( $settings, $category );
							}

							if ( $settings['show_term_description'] === 'show_excerpt' ) {
								echo $this->get_category_description( $category );
							}

							if ( $settings['show_read_more_button'] ) {
								$this->render_details_btn( $category );
							}
						echo '</div>';
					echo '</div>';
				echo $parent_wrapper_close;
			}

		echo '</div>';

		echo '<div class="owl-nav disabled">';
			echo '<a class="owl-prev" role="button">';
			Icons_Manager::render_icon( $settings['prev_icon'] );
			echo '</a>';
			echo '<a class="owl-next" role="button">';
			Icons_Manager::render_icon( $settings['next_icon'] );
			echo '</a>';
		echo '</div>';
	}

	protected function product_categories() {
		$settings = $this->get_settings_for_display();

		$attributes = [
			'number'     => $settings['dis_posts_total'] ? : 9999,
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

		$terms_query                = new stdClass();
		$terms_query->terms         = $terms;

		return $terms_query;
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

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = esc_html( $tag );

		$output = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
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

			$thumb_args = [
				'img_id' => $thumbnail_id,
				'class'  => implode( ' ', $link_class ),
				'custom' => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Category Image', 'the7mk2' ),
					]
				),
				'wrap'   => '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>',
				'echo'   => false,
			];

			if ( $settings['link_click'] == 'box' ) {
				$thumb_args['wrap'] = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</div>';
			} else {
				//$link_attridutes    = $this->get_custom_link_attributes( $category );
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

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper     	= '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</a>';
		} else {
			$title_link['href'] 		= '';
			$title_link_wrapper    		= '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close 	= '</span>';
		}

		$title      = $category->name;
		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
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

		return '<div class="entry-meta">' . wp_kses_post( $string ) . '</div>';
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

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-simple-widget-product-categories-carousel',
			'elementor-owl-carousel-call',
			'loading-effect-none',
			'classic-layout-list'
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$class[] = the7_array_match(
			$settings['bullets_style'],
			[
				'scale-up'         => 'bullets-scale-up',
				'stroke'           => 'bullets-stroke',
				'fill-in'          => 'bullets-fill-in',
				'small-dot-stroke' => 'bullets-small-dot-stroke',
				'ubax'             => 'bullets-ubax',
				'etefu'            => 'bullets-etefu',
			]
		);

		if ( $settings['arrow_bg_color'] === $settings['arrow_bg_color_hover'] ) {
			$class[] = 'disable-arrows-hover-bg';
		}

		if ( $settings['title_width'] === 'crp-to-line' ) {
			$class[] = 'title-to-line';
		}

		if ( $settings['description_width'] === 'crp-to-line' ) {
			$class[] = 'desc-to-line';
		}

		if ( ! $settings['show_product_image'] ) {
			$class[] = 'hide-product-image';
		}

		$class[] = the7_array_match(
			$settings['arrows'],
			[
				'never'         => 'carousel-nav-display-never',
				'always'        => 'carousel-nav-display-always',
				'hover'         => 'carousel-nav-display-hover',
			]
		);
		$class[] = the7_array_match(
			$settings['arrows_tablet'],
			[
				'never'         => 'carousel-nav-display-tablet-never',
				'always'        => 'carousel-nav-display-tablet-always',
				'hover'         => 'carousel-nav-display-tablet-hover',
			]
		);
		$class[] = the7_array_match(
			$settings['arrows_mobile'],
			[
				'never'         => 'carousel-nav-display-mobile-never',
				'always'        => 'carousel-nav-display-mobile-always',
				'hover'         => 'carousel-nav-display-mobile-hover',
			]
		);

		$this->add_render_attribute( $element, 'class', $class );
	}
		/**
	 * @param array $array Array of css vars like ['var' => 'value'].
	 *
	 * @return string
	 */
	protected function combine_to_css_vars_definition_string( $array ) {
		return implode( ' ', presscore_convert_indexed2numeric_array( ':', $array, '--', '%s;' ) );
	}

	protected function less_vars( The7_Elementor_Less_Vars_Decorator_Interface $less_vars ) {
		$settings = $this->get_settings_for_display();

		$less_vars->add_keyword(
			'unique-shortcode-class-name',
			$this->get_unique_class(),
			'~"%s"'
		);

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}

		if ( $settings['arrows'] !== 'never' || $settings['arrows_tablet'] !== 'never' || $settings['arrows_mobile'] !== 'never' ) {
			foreach ( $this->get_supported_devices() as $device => $dep ) {
				$less_vars->start_device_section( $device );

				$less_vars->add_keyword(
					'arrow-right-v-position',
					$this->get_responsive_setting( 'r_arrow_v_position' ) ?: 'center'
				);
				$less_vars->add_keyword(
					'arrow-right-h-position',
					$this->get_responsive_setting( 'r_arrow_h_position' ) ?: 'right'
				);
				$less_vars->add_unitized_number(
					'r-arrow-v-position',
					$this->get_responsive_setting( 'r_arrow_v_offset' )
				);
				$less_vars->add_unitized_number(
					'r-arrow-h-position',
					$this->get_responsive_setting( 'r_arrow_h_offset' )
				);

				$less_vars->add_keyword(
					'arrow-left-v-position',
					$this->get_responsive_setting( 'l_arrow_v_position' ) ?: 'center'
				);
				$less_vars->add_keyword(
					'arrow-left-h-position',
					$this->get_responsive_setting( 'l_arrow_h_position' ) ?: 'left'
				);
				$less_vars->add_unitized_number(
					'l-arrow-v-position',
					$this->get_responsive_setting( 'l_arrow_v_offset' )
				);
				$less_vars->add_unitized_number(
					'l-arrow-h-position',
					$this->get_responsive_setting( 'l_arrow_h_offset' )
				);

				$less_vars->close_device_section();
			}
		}

		$less_vars->add_rgba_color( 'bullet-color', $settings['bullet_color'] );
		$less_vars->add_rgba_color( 'bullet-color-hover', $settings['bullet_color_hover'] );
		$less_vars->add_keyword( 'bullets-v-position', $settings['bullets_v_position'] );
		$less_vars->add_keyword( 'bullets-h-position', $settings['bullets_h_position'] );
		$less_vars->add_pixel_number( 'bullet-v-position', $settings['bullets_v_offset'] );
		$less_vars->add_pixel_number( 'bullet-h-position', $settings['bullets_h_offset'] );

		$less_vars->add_pixel_number( 'arrow-bg-width', $settings['arrow_bg_width'] );
	}
}
