<?php
/**
 * The7 elements scroller widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\The7_Shortcode_Adapter_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;

defined( 'ABSPATH' ) || exit;

/**
 * Simple_Products_Carousel class.
 */
class Simple_Products_Carousel extends Simple_Products {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-woo-simple-products-carousel';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return __( 'Simple Products Carousel', 'the7mk2' );
	}

	/**
	 * @return string
	 */
	protected function the7_icon() {
		return 'eicon-products';
	}

	/**
	 * @return string
	 */
	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-simple-products-carousel.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-simple-products-carousel.css',
			[ 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-simple-products-carousel.js',
			[ 'dt-main', 'the7-cookies' ]
		);

		the7_register_script_in_footer(
			$this->get_name() . '-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/woocommerce-simple-products-carousel-preview.js',
			[ 'the7-elementor-editor-common', 'the7-elements-carousel-widget-preview' ]
		);
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	/**
	 * @return string[]
	 */
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
		$this->add_product_content_controls();
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
		$this->add_price_style_controls();
		$this->add_rating_style_controls();
		$this->add_excerpt_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_add_to_cart' => 'yes',
			]
		);
		$this->add_arrows_style_controls();
		$this->template( Bullets::class )->add_style_controls();
	}

	/**
	 * @return void
	 */
	protected function add_query_controls() {

		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'current_query_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __(
					'Note that the amount of posts per page is the product of "Products per row" and "Rows per page" settings from "Appearance"->"Customize"->"WooCommerce"->"Products Catalog".',
					'the7mk2'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'query_post_type' => 'current_query',
				],
			]
		);

		$this->add_group_control(
			The7_Group_Control_Query::get_type(),
			[
				'name'            => The7_Shortcode_Adapter_Interface::QUERY_CONTROL_NAME,
				'query_post_type' => 'product',
				'presets'         => [ 'include', 'exclude', 'order' ],
				'fields_options'  => [
					'post_type' => [
						'default' => 'product',
						'options' => [
							'current_query'   => __( 'Current Query', 'the7mk2' ),
							'product'         => __( 'Latest Products', 'the7mk2' ),
							'sale'            => __( 'Sale', 'the7mk2' ),
							'top'             => __( 'Top rated products', 'the7mk2' ),
							'best_selling'    => __( 'Best selling', 'the7mk2' ),
							'featured'        => __( 'Featured', 'the7mk2' ),
							'by_id'           => _x( 'Manual Selection', 'Posts Query Control', 'the7mk2' ),
							'related'         => __( 'Related Products', 'the7mk2' ),
							'recently_viewed' => __( 'Recently Viewed', 'the7mk2' ),
						],
					],
					'orderby'   => [
						'default' => 'date',
						'options' => [
							'date'       => __( 'Date', 'the7mk2' ),
							'title'      => __( 'Title', 'the7mk2' ),
							'price'      => __( 'Price', 'the7mk2' ),
							'popularity' => __( 'Popularity', 'the7mk2' ),
							'rating'     => __( 'Rating', 'the7mk2' ),
							'rand'       => __( 'Random', 'the7mk2' ),
							'menu_order' => __( 'Menu Order', 'the7mk2' ),
						],
					],
					'exclude'   => [
						'options' => [
							'current_post'     => __( 'Current Post', 'the7mk2' ),
							'manual_selection' => __( 'Manual Selection', 'the7mk2' ),
							'terms'            => __( 'Term', 'the7mk2' ),
						],
					],
					'include'   => [
						'options' => [
							'terms' => __( 'Term', 'the7mk2' ),
						],
					],
				],
				'exclude'         => [
					'posts_per_page',
					'exclude_authors',
					'authors',
					'offset',
					'related_fallback',
					'related_ids',
					'query_id',
					'avoid_duplicates',
					'ignore_sticky_posts',
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
					'query_post_type!' => 'current_query',
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
					'query_post_type!' => 'current_query',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
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
				'label'   => __( 'Transition Speed (ms)', 'the7mk2' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '600',
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
				'label'     => __( 'Autoplay Speed (ms)', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6000,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 10,
				'condition' => [
					'autoplay' => 'y',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
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

	/**
	 * @return void
	 */
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
					'{{WRAPPER}} .owl-stage-outer' => ' padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'render_type'        => 'template',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_product_content_controls() {

		$this->start_controls_section(
			'product_content_section',
			[
				'label' => __( 'Product Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'link_click',
			[
				'label'   => __( 'Apply Link & Hover', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'button',
				'options' => [
					'box'    => __( 'Whole box', 'the7mk2' ),
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
			'show_title',
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
			'product_title_tag',
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
				'default'   => 'h4',
				'condition' => [
					'show_title' => 'y',
				],
			]
		);

		$this->add_control(
			'title_width',
			[
				'label'       => __( 'Title Width', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'     => 'normal',
				'condition'   => [
					'show_title' => 'y',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'excerpt_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_title'  => 'y',
					'title_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'     => __( 'Rating', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'     => __( 'Short Description', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_width',
			[
				'label'       => __( 'Width', 'the7mk2' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'     => 'normal',
				'condition'   => [
					'show_description' => 'yes',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'description_words_limit',
			[
				'label'       => __( 'Maximum Number Of Words', 'the7mk2' ),
				'description' => __( 'Leave empty to show the entire title.', 'the7mk2' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 1,
				'max'         => 20,
				'condition'   => [
					'show_description'  => 'yes',
					'description_width' => 'normal',
				],
			]
		);

		$this->add_control(
			'show_add_to_cart',
			[
				'label'     => __( 'Add To Cart', 'the7mk2' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'the7mk2' ),
				'label_off' => __( 'Hide', 'the7mk2' ),
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
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
				],
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

	/**
	 * @return void
	 */
	protected function add_content_area_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'content_area_style',
			[
				'label' => __( 'Content Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
				'selectors'    => [
					'{{WRAPPER}} .post-entry-content' => 'text-align: {{VALUE}};',
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

	/**
	 * @return void
	 */
	protected function add_title_style_controls() {
		// Title Style.
		$this->start_controls_section(
			'title_style',
			[
				'label'     => __( 'Title', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'y',
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
					'{{WRAPPER}} .product-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .product-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.post.wrapper:hover .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_price_style_controls() {
		$this->start_controls_section(
			'product_price_style',
			[
				'label'     => __( 'Price', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'normal_price_heading',
			[
				'type'  => Controls_Manager::HEADING,
				'label' => __( 'Normal Price', 'the7mk2' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Normal Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price',
			]
		);

		$this->add_control(
			'normal_price_text_color',
			[
				'label'     => __( 'Normal Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Sale Price', 'the7mk2' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => __( 'Old Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price del',
			]
		);

		$this->add_control(
			'sale_price_text_color',
			[
				'label'     => __( 'Old Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'old_price_line_color',
			[
				'label'     => __( 'Old Price Line Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price del' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_new_price_typography',
				'label'    => __( 'New Price Typography', 'the7mk2' ),
				'selector' => '{{WRAPPER}} .price ins',
			]
		);

		$this->add_control(
			'sale_new_price_text_color',
			[
				'label'     => __( 'New Price Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .price ins span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_basic_responsive_control(
			'price_space',
			[
				'label'      => __( 'Price Spacing Above', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .price' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_rating_style_controls() {
		$this->start_controls_section(
			'show_rating_style',
			[
				'label'     => __( 'Rating', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'stars_size',
			[
				'label'     => __( 'Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .star-rating' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'empty_star_color',
			[
				'label'     => __( 'Empty Star Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .star-rating:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'full_star_color',
			[
				'label'     => __( 'Filled Star Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .star-rating span:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_basic_responsive_control(
			'gap_above_rating',
			[
				'label'      => __( 'Rating Spacing Above', 'the7mk2' ),
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
					'{{WRAPPER}} .star-rating-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function add_excerpt_style_controls() {
		$this->start_controls_section(
			'short_description',
			[
				'label'     => __( 'Short Description', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_description' => 'yes',
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

	/**
	 * @return void
	 */
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
							'name'     => 'arrows',
							'operator' => '!=',
							'value'    => 'never',
						],
						[
							'name'     => 'arrows_tablet',
							'operator' => '!=',
							'value'    => 'never',
						],
						[
							'name'     => 'arrows_mobile',
							'operator' => '!=',
							'value'    => 'never',
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
				'label'       => __( 'Next Arrow', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'icomoon-the7-font-the7-arrow-09',
					'library' => 'the7-icons',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'classes'     => [ 'elementor-control-icons-svg-uploader-hidden' ],
			]
		);

		$this->add_control(
			'prev_icon',
			[
				'label'       => __( 'Previous Arrow', 'the7mk2' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [
					'value'   => 'icomoon-the7-font-the7-arrow-08',
					'library' => 'the7-icons',
				],
				'skin'        => 'inline',
				'label_block' => false,
				'classes'     => [ 'elementor-control-icons-svg-uploader-hidden' ],
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
					'{{WRAPPER}} .owl-nav i'     => 'font-size: {{SIZE}}{{UNIT}}',
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
				'label'     => __( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-nav a i, {{WRAPPER}} .owl-nav a i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color',
			[
				'label'     => __( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
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
				'label'     => __( 'Icon Color Hover', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .owl-nav a:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a i:before { transition: color 0.3s; } {{WRAPPER}} .owl-nav a:hover i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav a:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_border_color_hover',
			[
				'label'     => __( 'Border Color Hover', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
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
						'icon'  => 'eicon-h-align-center',
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
						'icon'  => 'eicon-h-align-center',
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

		$this->end_controls_section();
	}

	/**
	 * @return string
	 */
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

	/**
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$settings['posts_per_page'] = $settings['dis_posts_total'];
		$settings['posts_offset']   = $settings['posts_offset'];

		if ( $settings['query_post_type'] === 'recently_viewed' && ! $this->is_preview_mode() ) {
			Recently_Viewed_Products::track_via_js();
		}

		$query_builder = new Products_Query( $settings, 'query_' );
		$query         = $query_builder->create();

		if ( ! $query->have_posts() ) {
			if ( $settings['query_post_type'] === 'current_query' ) {
				$this->render_nothing_found_message();
			}
			return;
		}

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'inner-wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

			echo '<div ' . $this->get_render_attribute_string( 'inner-wrapper' ) . $this->get_container_data_atts() . '>';

				$index = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$index++;

			$product = wc_get_product( get_the_ID() );

			if ( ! $product ) {
				continue;
			}

			$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'link_wrapper', $index );

			$post_class_array = [
				'post',
				'visible',
				'wrapper',
			];

			if ( ! has_post_thumbnail() ) {
				$post_class_array[] = 'no-img';
			}

			$link_key        = 'link_' . $index;
			$link_attridutes = $this->get_custom_link_attributes( $settings, $product );
			$this->add_link_attributes( $link_key, $link_attridutes, true );
			$btn_attributes = $this->get_render_attribute_string( $link_key );

			if ( 'button' === $settings['link_click'] ) {
				$parent_wrapper       = '<article class="' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
				$parent_wrapper_close = '</article>';
			} else {
				$parent_wrapper       = '<a ' . $btn_attributes . ' class="box-hover ' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
				$parent_wrapper_close = '</a>';
			}

			echo $parent_wrapper;
				echo '<div class="post-content-wrapper">';
			if ( $settings['show_product_image'] ) {
				echo '<div class="the7-simple-post-thumb"><div class="the7-product-thumb">' . $this->product_image( $settings, $product ) . '</div></div>';
			}

					echo '<div class="post-entry-content">';
			if ( $settings['show_title'] ) {
					echo $this->display_product_title( $settings, $settings['product_title_tag'], $product );
			}

			if ( $settings['show_price'] ) {
						echo '<span class="price">' . wp_kses_post( $product->get_price_html() ) . '</span>';
			}

			if ( $settings['show_rating'] && wc_review_ratings_enabled() ) {
				$price_html = wc_get_rating_html( $product->get_average_rating() );
				if ( $price_html ) {
					echo '<div class="star-rating-wrap">' . $price_html . '</div>'; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}

			if ( $settings['show_description'] ) {
				echo $this->get_short_description( $product );
			}

			if ( $settings['show_add_to_cart'] ) {
				echo '<div class="woo-buttons">';
				$this->display_add_to_cart( $product );
				echo '</div>';
			}
									echo '</div>';
									echo '</div>';
									echo $parent_wrapper_close;
		}

				wp_reset_postdata();

			echo '</div>';
			echo '<div class="owl-nav disabled">';
				echo '<a class="owl-prev" role="button">';
				Icons_Manager::render_icon( $settings['prev_icon'] );
				echo '</a>';
				echo '<a class="owl-next" role="button">';
				Icons_Manager::render_icon( $settings['next_icon'] );
				echo '</a>';
				echo '</div>';

		echo '</div>';
	}

	/**
	 * @param $settings
	 *
	 * @return array
	 */
	protected function get_custom_link_attributes( $settings ) {
		return [
			'url'    => get_the_permalink(),
			'target' => '',
		];
	}

	/**
	 * @param $product
	 *
	 * @return string|void
	 */
	protected function get_short_description( $product ) {

		$settings = $this->get_settings_for_display();

		$short_description = $product->get_short_description();
		if ( ! $short_description ) {
			return;
		}

		if ( $settings['description_words_limit'] && $settings['description_width'] === 'normal' ) {
			$short_description = wp_trim_words( $short_description, $settings['description_words_limit'] );
		}

		$output = '<p class="short-description">' . wp_kses_post( $short_description ) . '</p>';

		return $output;
	}

	/**
	 * @param $settings
	 * @param $product
	 *
	 * @return string
	 */
	protected function product_image( $settings, $product ) {

		if ( $product->get_image_id() ) {

			$icons_html = $this->get_hover_icons_html_template( $settings );

			$link_wrapper = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>';
			$link         = $product->get_permalink();
			$thumb_args   = [
				'img_id'       => $product->get_image_id(),
				'class'        => implode( ' ', [ 'product-thumb', 'img-ratio-wrapper' ] ),
				'lazy_loading' => false,
				'custom'       => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				'wrap'         => $link_wrapper,
				'echo'         => false,
			];

			if ( $settings['link_click'] == 'box' ) {
				$thumb_args['wrap'] = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</div>';
			} else {
				$thumb_args['href'] = $link;
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		} else {
			$image = sprintf(
				'<img class="%s" src="%s" width="%s" height="%s">',
				'preload-me',
				get_template_directory_uri() . '/images/gray-square.svg',
				1500,
				1500
			);

			$post_media = sprintf(
				'<a %s>%s</a>',
				the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				$image
			);
		}

		return $post_media;
	}

	/**
	 * @param $text
	 * @param $tag
	 *
	 * @return string
	 */
	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = Utils::validate_html_tag( $tag );

		$output = '<' . $tag . ' class="rp-heading">' . esc_html( $text ) . '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param $settings
	 *
	 * @return string
	 */
	protected function get_hover_icons_html_template( $settings ) {
		// if ( ! $settings['show_hover_icon'] ) {
		// return '';
		// }

		// $a_atts               = $this->get_link_attributes( $settings );
		$a_atts['class']      = 'the7-hover-icon';
		$a_atts['aria-label'] = __( 'Details link', 'the7mk2' );

		return sprintf(
			'<span %s>%s</span>',
			the7_get_html_attributes_string( $a_atts ),
			$this->get_elementor_icon_html( $settings['hover_icon'], 'i' )
		);
	}

	/**
	 * @param $settings
	 * @param $tag
	 * @param $product
	 *
	 * @return string
	 */
	protected function display_product_title( $settings, $tag, $product ) {

		$tag        = esc_html( $tag );
		$title_link = [
			'href'  => $product->get_permalink(),
			'class' => 'product-title',
		];
		$title      = $product->get_name();
		if ( $settings['excerpt_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['excerpt_words_limit'] );
		}

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper       = '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</a>';
		} else {
			$title_link['href']       = '';
			$title_link_wrapper       = '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</span>';
		}

		$output  = '<' . $tag . ' class="heading">';
		$output .= sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param $element
	 *
	 * @return void
	 */
	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-simple-widget-products-carousel',
			'elementor-owl-carousel-call',
			'loading-effect-none',
			'classic-layout-list',
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
				'never'  => 'carousel-nav-display-never',
				'always' => 'carousel-nav-display-always',
				'hover'  => 'carousel-nav-display-hover',
			]
		);
		$class[] = the7_array_match(
			$settings['arrows_tablet'],
			[
				'never'  => 'carousel-nav-display-tablet-never',
				'always' => 'carousel-nav-display-tablet-always',
				'hover'  => 'carousel-nav-display-tablet-hover',
			]
		);
		$class[] = the7_array_match(
			$settings['arrows_mobile'],
			[
				'never'  => 'carousel-nav-display-mobile-never',
				'always' => 'carousel-nav-display-mobile-always',
				'hover'  => 'carousel-nav-display-mobile-hover',
			]
		);

		$this->add_render_attribute( $element, 'class', $class );

		$this->add_render_attribute( 'wrapper', 'class', '' );
	}
	/**
	 * @param array $array Array of css vars like ['var' => 'value'].
	 *
	 * @return string
	 */
	protected function combine_to_css_vars_definition_string( $array ) {
		return implode( ' ', presscore_convert_indexed2numeric_array( ':', $array, '--', '%s;' ) );
	}

	/**
	 * @param  The7_Elementor_Less_Vars_Decorator_Interface  $less_vars
	 *
	 * @return void
	 */
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
