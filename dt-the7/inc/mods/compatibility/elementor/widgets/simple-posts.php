<?php
/**
 * The7 Simple Posts widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\Style\Posts_Masonry_Style;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use The7\Mods\Compatibility\Elementor\With_Post_Excerpt;
use The7_Categorization_Request;
use The7_Query_Builder;
use The7_Related_Query_Builder;

defined( 'ABSPATH' ) || exit;

class Simple_Posts extends Simple_Widget_Base {

	use With_Post_Excerpt;
	use Posts_Masonry_Style;

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-simple-posts';
	}

	protected function the7_title() {
		return __( 'Simple Posts', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-posts-grid';
	}

	protected function get_less_file_name() {
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-simple-posts.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			THE7_ELEMENTOR_CSS_URI . '/the7-simple-posts.css',
			[ 'the7-filter-decorations-base', 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			THE7_ELEMENTOR_JS_URI . '/the7-simple-posts.js',
			[ 'dt-main' ]
		);

		// Preview script.
		the7_register_script_in_footer(
			$this->get_name() . '-preview',
			THE7_ELEMENTOR_ADMIN_JS_URI . '/the7-simple-posts-preview.js',
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
		$this->template( Pagination::class )->add_content_controls( 'post_type' );

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
		$this->template( Pagination::class )->add_style_controls( 'post_type' );
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
				'render_type' => 'template',
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
					'{{WRAPPER}} .dt-css-grid' => 'grid-template-columns: repeat({{SIZE}},1fr)',
					'{{WRAPPER}}'              => '--wide-desktop-columns: {{SIZE}};',
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
			'posts_offset'   => $settings['posts_offset'],
			'post_type'      => $post_type,
			'order'          => $settings['order'],
			'orderby'        => $settings['orderby'],
			'paged'          => $this->template( Pagination::class )->get_paged(),
			'posts_per_page' => $this->template( Pagination::class )->get_posts_per_page(),
		];

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

		// Force disable pagination for related posts.
		if ( $settings['post_type'] === 'related' ) {
			$this->template( Pagination::class )->set_loading_mode( 'disabled' );
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

		$this->template( Pagination::class )->add_containter_attributes( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['widget_title_tag'] );
		}

		$columns = [
			'd'  => $settings['widget_columns'],
			't'  => $settings['widget_columns_tablet'],
			'p'  => $settings['widget_columns_mobile'],
			'wd' => $settings['widget_columns_wide_desktop'],
		];

			$post_limit = $this->template( Pagination::class )->get_post_limit();
		?>
			<div class="dt-css-grid custom-pagination-handler" data-columns="<?php echo esc_attr( wp_json_encode( $columns ) ); ?>">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();

					$visibility = 'visible';
					if ( $post_limit >= 0 && $query->current_post >= $post_limit ) {
						$visibility = 'hidden';
					}

					$link_class = '';
					if ( 'button' !== $settings['link_click'] ) {
						$link_class = 'box-hover';
					}

					$this->remove_render_attribute( 'wrapper' );
					$this->add_render_attribute(
						'wrapper',
						'class',
						[
							'wf-cell',
							$visibility,
							$link_class,
						]
					);

					$post_class_array = [
						'post',
						'visible',
						'wrapper',
					];

					if ( ! has_post_thumbnail() ) {
						$post_class_array[] = 'no-img';
					}

					if ( 'button' === $settings['link_click'] ) {
						$wrapper_tag = 'div';
					} else {
						$wrapper_tag = 'a';
						$this->add_render_attribute( 'wrapper', $this->get_link_attributes( $settings ) );
					}
					?>
					<?php echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; ?>

						<article class="<?php echo esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ); ?>">

							<div class="post-content-wrapper">
								<?php
								$post_media = $this->get_post_image( $settings );
								if ( $settings['show_post_image'] && $post_media ) {
									?>
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
										echo $this->get_details_btn( $settings );
									}
									?>
								</div>
							</div>
						</article>
					<?php echo '</' . $wrapper_tag . '>'; ?>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		<?php

		$this->template( Pagination::class )->render( $query->max_num_pages );

		echo '</div>';
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

		if ( has_post_thumbnail() ) {
			$link_class = [ 'post-thumbnail-rollover', 'img-ratio-wrapper' ];
			if ( ! $link_attridutes['href'] ) {
				$link_class[] = 'not-clickable-item';
			}
			$icons_html = $this->get_hover_icons_html_template( $settings );

			if ( $settings['link_click'] == 'button' ) {
				$thumb_args = [
					'img_id'       => get_post_thumbnail_id(),
					'class'        => implode( ' ', $link_class ),
					'img_class'    => 'preload-me',
					'href'         => $link_attridutes['href'],
					'lazy_loading' => false,
					'custom'       => the7_get_html_attributes_string(
						[
							'aria-label' => __( 'Post image', 'the7mk2' ),
							'target'     => $link_attridutes['target'],
						]
					),
					'wrap'         => '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>',
					'echo'         => false,
				];
			} else {
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
					'wrap'         => '<div %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</div>',
					'echo'         => false,
				];
			}

			$post_media = dt_get_thumb_img( $thumb_args );
		}

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

	protected function get_post_excerpt() {
		$settings = $this->get_settings_for_display();

		$excerpt = get_the_excerpt();
		if ( $settings['excerpt_words_limit'] && $settings['description_width'] === 'normal' ) {
			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_words_limit'] );
		}

		$output  = '<p class="short-description">';
		$output .= wp_kses_post( $excerpt );
		$output .= '</p>';

		return $output;
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

	protected function display_widget_title( $text, $tag = 'h3' ) {

		$tag = esc_html( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function get_post_title( $settings, $tag = 'h3' ) {
		$link_attributes = [];

		if ( 'button' === $settings['link_click'] ) {
			$link_tag        = 'a';
			$link_attributes = $this->get_link_attributes( $settings );
			if ( ! empty( $link_attributes['href'] ) ) {
				$link_attributes['rel'] = 'bookmark';
			}
		} else {
			$link_tag = 'span';
		}

		$link_attributes['title'] = the_title_attribute( 'echo=0' );
		$link_attributes['class'] = 'post-title';

		$tag   = esc_html( $tag );
		$title = get_the_title();
		if ( $settings['title_words_limit'] && $settings['title_width'] === 'normal' ) {
			$title = wp_trim_words( $title, $settings['title_words_limit'] );
		}

		$output  = '';
		$output .= '<' . $tag . ' class="heading">';
		$output .= '<' . $link_tag . ' ' . the7_get_html_attributes_string( $link_attributes ) . '>';
		$output .= esc_html( $title );
		$output .= '</' . $link_tag . '>';
		$output .= '</' . $tag . '>';

		return $output;
	}

	protected function add_main_wrapper_class_render_attribute_for( $element ) {

		$class = [
			'the7-simple-widget-posts',
			'the7-elementor-widget',
			// 'loading-effect-none',
		];

		// Unique class.
		$class[] = $this->get_unique_class();

		$settings = $this->get_settings_for_display();

		$loading_mode = $settings['loading_mode'];
		if ( 'standard' !== $loading_mode ) {
			$class[] = 'jquery-filter';
		}

		if ( 'js_lazy_loading' === $loading_mode ) {
			$class[]  = 'lazy-loading-mode';
			$class[] .= 'loading-effect-none';
		}

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
					'{{WRAPPER}} a.wf-cell:hover .post-title' => 'color: {{VALUE}};',
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
				'selector'       => '{{WRAPPER}} .the7-simple-widget-posts .entry-meta',
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
					'{{WRAPPER}} a.wf-cell:hover .entry-meta .meta-item, {{WRAPPER}} a.wf-cell:hover .entry-meta .meta-item *' => 'color: {{VALUE}}',

					'{{WRAPPER}} a.wf-cell:hover .entry-meta .meta-item:before'                  => 'color: {{VALUE}}; ',
					'{{WRAPPER}} .entry-meta .meta-item:hover:before'  => 'color: {{VALUE}};',

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
					'{{WRAPPER}} a.wf-cell:hover .short-description' => 'color: {{VALUE}}',
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
		$less_vars->add_keyword( 'grid-wide-columns', $settings['widget_columns_wide_desktop'] );

		foreach ( Responsive::get_breakpoints() as $size => $value ) {
			$less_vars->add_pixel_number( "elementor-{$size}-breakpoint", $value );
		}
	}
}
