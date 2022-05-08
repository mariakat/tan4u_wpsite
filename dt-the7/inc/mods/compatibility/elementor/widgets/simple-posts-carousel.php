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
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Bullets;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7_Categorization_Request;
use The7_Query_Builder;
use The7_Related_Query_Builder;

defined( 'ABSPATH' ) || exit;

class Simple_Posts_Carousel extends Simple_Widget_Base {

	use Posts_Masonry_Style;

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-simple-posts-carousel';
	}

	protected function the7_title() {
		return __( 'Simple Posts Carousel', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-carousel';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-posts-carousel.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-simple-posts-carousel.css',
			[ 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/the7-simple-posts-carousel.js',
			[ 'dt-main' ]
		);

		the7_register_script_in_footer(
			$this->get_name() . '-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/the7-simple-posts-carousel-preview.js',
			[ 'the7-elementor-editor-common', 'the7-elements-carousel-widget-preview' ]
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
				'show_post_image' => 'y',
			]
		);

		$this->add_content_area_style_controls();
		$this->add_title_style_controls();
		$this->add_meta_style_controls();
		$this->add_excerpt_style_controls();
		$this->template( Button::class )->add_style_controls(
			Button::ICON_MANAGER,
			[
				'show_read_more_button' => 'y',
				'article_links'         => 'y',
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

		$this->add_control(
			'post_type',
			[
				'label'   => __( 'Source', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => 'post',
				'options' => the7_elementor_elements_widget_post_types() + [ 'related' => __( 'Related', 'the7mk2' ) ],
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
					'post_type!' => [ '', 'current_query' ],
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
					'taxonomy!'  => '',
					'post_type!' => [ 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'     => __( 'Order', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => [
					'asc'  => __( 'Ascending', 'the7mk2' ),
					'desc' => __( 'Descending', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'     => __( 'Order By', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => [
					'date'          => __( 'Date', 'the7mk2' ),
					'title'         => __( 'Name', 'the7mk2' ),
					'ID'            => __( 'ID', 'the7mk2' ),
					'modified'      => __( 'Modified', 'the7mk2' ),
					'comment_count' => __( 'Comment count', 'the7mk2' ),
					'menu_order'    => __( 'Menu order', 'the7mk2' ),
					'rand'          => __( 'Rand', 'the7mk2' ),
				],
				'condition' => [
					'post_type!' => 'current_query',
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
					'post_type!' => 'current_query',
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
					'post_type!' => 'current_query',
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

	protected function add_content_controls() {

		$this->start_controls_section(
			'post_content_section',
			[
				'label' => __( 'Content', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'article_links',
			[
				'label'        => __( 'Links To A Single Post', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
			]
		);

		$this->add_control(
			'article_links_goes_to',
			[
				'label'     => __( 'Links Lead To', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'posts',
				'options'   => [
					'posts'                => __( 'Posts', 'the7mk2' ),
					'external_or_posts'    => __( 'External links or posts', 'the7mk2' ),
					'external_or_disabled' => __( 'External links or disabled', 'the7mk2' ),
				],
				'condition' => [
					'post_type'     => 'dt_portfolio',
					'article_links' => 'y',
				],
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
			'show_post_image',
			[
				'label'        => __( 'Image', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'separator'    => 'before',
				'default'      => 'y',
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
				'label'        => __( 'Excerpt', 'the7mk2' ),
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
				'label'     => __( 'Width', 'the7mk2' ),
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
			'post_terms',
			[
				'label'        => __( 'Category', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_terms_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_terms' => 'y',
					'link_click' => 'button',
				],
			]
		);

		$this->add_control(
			'post_author',
			[
				'label'        => __( 'Author', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_author_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_author' => 'y',
					'link_click'  => 'button',
				],
			]
		);

		$this->add_control(
			'post_date',
			[
				'label'        => __( 'Date', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_date_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_date'  => 'y',
					'link_click' => 'button',
					'post_type'  => [ 'post', 'current_query', 'related' ],
				],
			]
		);

		$this->add_control(
			'post_comments',
			[
				'label'        => __( 'Comments Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'the7mk2' ),
				'label_off'    => __( 'Hide', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'post_comments_link',
			[
				'label'        => __( 'Link', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'the7mk2' ),
				'label_off'    => __( 'No', 'the7mk2' ),
				'return_value' => 'y',
				'default'      => 'y',
				'condition'    => [
					'post_comments' => 'y',
					'link_click'    => 'button',
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
				'condition'    => [
					'article_links' => 'y',
				],
			]
		);

		$this->add_control(
			'read_more_button_text',
			[
				'label'     => __( 'Button Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read more', 'the7mk2' ),
				'condition' => [
					'show_read_more_button' => 'y',
					'article_links'         => 'y',
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
				'label'                => __( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
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
				'prefix_class'         => 'slide-h-position%s-',
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'align-items: flex-start; text-align: left;',
					'center' => 'align-items: center; text-align: center;',
					'right'  => 'align-items: flex-end; text-align: right;',
				],
				'selectors'            => [
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
					'{{WRAPPER}} .post-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .post-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} a.post.wrapper:hover .post-title' => 'color: {{VALUE}};',
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
				'label'      => __( 'Meta Information', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'post_date',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_terms',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_author',
							'operator' => '!==',
							'value'    => '',
						],
						[
							'name'     => 'post_comments',
							'operator' => '!==',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'post_meta_separator',
			[
				'label'       => __( 'Separator Between', 'the7mk2' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '•',
				'placeholder' => '',
				'selectors'   => [
					'{{WRAPPER}} .entry-meta .meta-item:not(:first-child):before' => 'content: "{{VALUE}}";',
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
					'{{WRAPPER}} .entry-meta .meta-item, {{WRAPPER}} .entry-meta .meta-item *' => 'color: {{VALUE}}',

					'{{WRAPPER}} .entry-meta .meta-item:hover:before'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .entry-meta .meta-item:before'  => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .entry-meta .meta-item:hover, {{WRAPPER}} .entry-meta .meta-item:hover *' => 'color: {{VALUE}}',
					'{{WRAPPER}} a.post.wrapper:hover .entry-meta .meta-item, {{WRAPPER}} a.post.wrapper:hover .entry-meta .meta-item *' => 'color: {{VALUE}}',

					'{{WRAPPER}} a.post.wrapper:hover .entry-meta .meta-item:before, {{WRAPPER}} .entry-meta  .meta-item:hover:before'                  => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'post_meta_bottom_margin',
			[
				'label'      => __( 'Meta Spacing Above', 'the7mk2' ),
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

	protected function add_excerpt_style_controls() {
		$this->start_controls_section(
			'short_description',
			[
				'label'     => __( 'Excerpt', 'the7mk2' ),
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
					'{{WRAPPER}} a.post.wrapper:hover .short-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_basic_responsive_control(
			'gap_above_description',
			[
				'label'      => __( 'Excerpt Spacing Above', 'the7mk2' ),
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

	protected function get_query() {
		$settings  = $this->get_settings_for_display();
		$post_type = $settings['post_type'];

		if ( $post_type === 'current_query' ) {
			return $GLOBALS['wp_query'];
		}

		$taxonomy = $settings['taxonomy'];
		$terms    = $settings['terms'];

		// Loop query.
		$query_args = [
			'posts_offset' => $settings['posts_offset'],
			'post_type'    => $post_type,
			'order'        => $settings['order'],
			'orderby'      => $settings['orderby'],
		];

		$query_args['posts_per_page'] = $settings['dis_posts_total'];
		$query_args['posts_offset']   = $settings['posts_offset'];

		if ( $post_type === 'related' ) {
			$query_builder = new The7_Related_Query_Builder( $query_args );
		} else {
			$query_builder = new The7_Query_Builder( $query_args );
		}

		$query_builder->from_terms( $taxonomy, $terms );

		$request = new The7_Categorization_Request();
		if ( $request->taxonomy && $request->not_empty() ) {
			$query_builder->with_categorizaition( $request );
		}

		return $query_builder->query();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! in_array( $settings['post_type'], [ 'current_query', 'related' ] ) && ! post_type_exists( $settings['post_type'] ) ) {
			echo the7_elementor_get_message_about_disabled_post_type();
			return;
		}

		$query = $this->get_query();
		if ( ! $query->have_posts() ) {
			if ( $settings['post_type'] === 'current_query' ) {
				$this->render_nothing_found_message();
			}
			return;
		}

		$this->print_inline_css();

		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . $this->get_container_data_atts() . '>';

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_class_array = [
				'post',
				'visible',
				'wrapper',
				'dt-owl-item-wrap',
			];

			if ( ! has_post_thumbnail() ) {
				$post_class_array[] = 'no-img';
			}

			$this->remove_render_attribute( 'inner-wrapper' );
			$this->add_render_attribute( 'inner-wrapper', 'class', get_post_class( $post_class_array ) );

			if ( 'button' === $settings['link_click'] ) {
				$wrapper_tag = 'article';
			} else {
				$wrapper_tag = 'a';
				$this->add_render_attribute( 'inner-wrapper', 'class', 'box-hover' );
				$this->add_render_attribute( 'inner-wrapper', $this->get_link_attributes( $settings ) );
			}

			$post_media = $this->get_post_image( $settings );

			echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'inner-wrapper' ) . '>';
			?>

				<div class="post-content-wrapper">
					<?php if ( $settings['show_post_image'] && $post_media ) { ?>
						<div class="the7-simple-post-thumb">
							<?php echo $post_media; ?>
						</div>
					<?php } ?>

					<div class="post-entry-content">
						<?php
						if ( $settings['show_post_title'] ) {
							echo $this->get_post_title( $settings, $settings['post_title_tag'] );
						}

						echo $this->get_post_meta_html_based_on_settings( $settings );

						if ( $settings['post_content'] === 'show_excerpt' ) {
							echo $this->get_post_excerpt();
						}

						if ( $settings['show_read_more_button'] ) {
							echo '<div class="details-wrap">' . $this->get_details_btn( $settings ) . '</div>';
						}
						?>
					</div>
				</div>

			<?php
			echo '</' . $wrapper_tag . '>';
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
	}

	protected function get_details_btn( $settings ) {
		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );
		$btn_element = 'button';
		if ( 'button' === $settings['link_click'] ) {
			$btn_element = 'a';
		}

		$link_attributes               = $this->get_link_attributes( $settings );
		$link_attributes['aria-label'] = the7_get_read_more_aria_label();

		$this->add_render_attribute( 'box-button', $link_attributes );

		ob_start();
		$this->template( Button::class )->render_button( 'box-button', esc_html( $settings['read_more_button_text'] ), $btn_element );

		return ob_get_clean();
	}

	protected function get_post_meta_html_based_on_settings( $settings ) {
		$post_meta_types    = [
			'terms',
			'author',
			'date',
			'comments',
		];
		$required_post_meta = [];
		foreach ( $post_meta_types as $post_meta_type ) {
			if ( ! empty( $settings[ "post_{$post_meta_type}" ] ) ) {
				$link = '';
				if ( $settings['link_click'] == 'button' ) {
					$link = ! empty( $settings[ "post_{$post_meta_type}_link" ] );
				}

				$required_post_meta[] = [
					'type' => $post_meta_type,
					'link' => $link,
				];
			}
		}

		return $this->get_post_meta_html( $required_post_meta );
	}

	protected function get_post_image( $settings ) {

		$link_attridutes = $this->get_link_attributes( $settings );
		$post_media      = '';

		$link_class = [ 'post-thumbnail-rollover', 'img-ratio-wrapper' ];
		if ( ! $link_attridutes['href'] ) {
			$link_class[] = 'not-clickable-item';
		}
		$icons_html = $this->get_hover_icons_html_template( $settings );

		$link_wrapper = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>';

		$thumb_args = [
			'img_id'       => get_post_thumbnail_id(),
			'class'        => implode( ' ', $link_class ),
			'img_class'    => 'preload-me',
			'lazy_loading' => false,
			'custom'       => the7_get_html_attributes_string(
				[
					'aria-label' => __( 'Post image', 'the7mk2' ),
					'target'     => $link_attridutes['target'],
				]
			),
			'wrap'         => $link_wrapper,
			'echo'         => false,
		];

		if ( $settings['link_click'] == 'box' ) {
			$thumb_args['wrap'] = '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</div>';
		} else {
			$thumb_args['href'] = $link_attridutes['href'];
		}

		$post_media = dt_get_thumb_img( $thumb_args );

		return $post_media;
	}

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

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = esc_html( $tag );

		$output = '<' . $tag . ' class="rp-heading">' . esc_html( $text ) . '</' . $tag . '>';

		return $output;
	}

	protected function get_post_title( $settings, $tag = 'h3' ) {
		$title_link          = $this->get_link_attributes( $settings );
		$title_link['title'] = the_title_attribute( 'echo=0' );
		if ( ! empty( $title_link['href'] ) ) {
			$title_link['rel'] = 'bookmark';
		}

		$title_link['class'] = 'post-title';
		$tag                 = esc_html( $tag );
		$title               = get_the_title();
		if ( ! $title ) {
			return false;
		}

		if ( 'button' === $settings['link_click'] ) {
			$title_link_wrapper       = '<a ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</a>';
		} else {
			$title_link['href']       = '';
			$title_link_wrapper       = '<span ' . the7_get_html_attributes_string( $title_link ) . '>';
			$title_link_wrapper_close = '</span>';
		}

		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

		$output  = '';
		$output .= '<' . $tag . ' class="heading">';
		$output .= sprintf( '%s%s%s', $title_link_wrapper, $title, $title_link_wrapper_close );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_post_excerpt() {
		$settings = $this->get_settings_for_display();

		$excerpt = get_the_excerpt();
		if ( ! $excerpt ) {
			return false;
		}

		if ( $settings['excerpt_words_limit'] && $settings['description_width'] === 'normal' ) {
			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_words_limit'] );
		}

		$output  = '<p class="short-description">';
		$output .= wp_kses_post( $excerpt );
		$output .= '</p>';

		return $output;
	}

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'owl-carousel',
			'the7-elementor-widget',
			'the7-simple-widget-posts-carousel',
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

		if ( ! $settings['show_post_image'] ) {
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
			$this->get_unique_class() . '.the7-elementor-widget',
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
