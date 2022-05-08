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
use Elementor\Utils;
use The7\Inc\Mods\Compatibility\WooCommerce\Front\Recently_Viewed_Products;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Query_Control\The7_Group_Control_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\Query_Adapters\Products_Query;
use The7\Mods\Compatibility\Elementor\Shortcode_Adapters\The7_Shortcode_Adapter_Interface;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Less_Vars_Decorator_Interface;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Add_To_Cart_Button;
use The7\Mods\Compatibility\Elementor\Widget_Templates\Pagination;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Simple products class.
 */
class Simple_Products extends Simple_Widget_Base {

	/**
	 * Get element name.
	 * Retrieve the element name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return 'the7-elements-woo-simple-products';
	}

	/**
	 * @return string
	 */
	protected function the7_title() {
		return __( 'Simple Products', 'the7mk2' );
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
		return PRESSCORE_THEME_DIR . '/css/dynamic-less/elementor/the7-woocommerce-simple-products.less';
	}

	/**
	 * Register widget assets.
	 *
	 * @see The7_Elementor_Widget_Base::__construct()
	 */
	protected function register_assets() {
		the7_register_style(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/css/compatibility/elementor/the7-woocommerce-simple-products.css',
			[ 'the7-filter-decorations-base', 'the7-simple-common' ]
		);

		the7_register_script_in_footer(
			$this->get_name(),
			PRESSCORE_THEME_URI . '/js/compatibility/elementor/woocommerce-simple-products.js',
			[ 'dt-main', 'the7-cookies' ]
		);

		// Preview script.
		the7_register_script_in_footer(
			$this->get_name() . '-preview',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/woocommerce-simple-products-preview.js',
			[ 'the7-elementor-editor-common' ]
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
		$this->register_query_controls();
		$this->add_layout_content_controls();
		$this->add_product_content_controls();

		$this->template( Pagination::class )->add_content_controls( 'query_post_type' );

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
				'show_product_image' => 'y',
			]
		);

		$this->add_content_style_controls();
		$this->add_title_style_controls();
		$this->add_price_style_controls();
		$this->add_rating_style_controls();
		$this->add_product_description_style_contros();

		$this->template( Add_To_Cart_Button::class )->add_style_controls(
			Add_To_Cart_Button::ICON_MANAGER,
			[
				'show_add_to_cart' => 'yes',
			]
		);

		$this->template( Pagination::class )->add_style_controls( 'query_post_type' );
	}

	/**
	 * Add query controls.
	 */
	protected function register_query_controls() {

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

		$this->end_controls_section();
	}

	/**
	 * Add layout content controls.
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

	/**
	 * Add products content controls.
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
				'label'     => __( 'Title Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_title' => 'y',
				],
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
				'label'     => __( 'Width', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'normal'      => __( 'Normal', 'the7mk2' ),
					'crp-to-line' => __( 'Crop to one line', 'the7mk2' ),
				],
				'default'   => 'normal',
				'condition' => [
					'show_description' => 'yes',
				],
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
	 * Render widget.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

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

		$this->add_main_wrapper_class_render_attribute_for( 'wrapper' );
		$this->template( Pagination::class )->add_containter_attributes( 'wrapper' );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $settings['show_widget_title'] === 'y' && $settings['widget_title_text'] ) {
			echo $this->display_widget_title( $settings['widget_title_text'], $settings['title_tag'] );
		}

		$posts_limit = $this->template( Pagination::class )->get_post_limit();
		$columns     = [
			'd'  => $settings['widget_columns'],
			't'  => $settings['widget_columns_tablet'],
			'p'  => $settings['widget_columns_mobile'],
			'wd' => $settings['widget_columns_wide_desktop'],
		];

		echo '<div class="dt-css-grid custom-pagination-handler" data-columns="' . esc_attr( wp_json_encode( $columns ) ) . '">';

		$index = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$index++;

			$product = wc_get_product();

			// It can be empty if query is not about products.
			if ( ! $product ) {
				continue;
			}

			$visibility = 'visible';
			if ( $posts_limit >= 0 && $query->current_post >= $posts_limit ) {
				$visibility = 'hidden';
			}
			$link_class = '';
			if ( 'button' !== $settings['link_click'] ) {
				$link_class = 'box-hover';
			}

			$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'link_wrapper', $index );

			$this->add_render_attribute(
				$repeater_setting_key,
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

			$link_key = 'link_' . $index;

			$link_attridutes = $this->get_custom_link_attributes( $settings );
			$this->add_link_attributes( $link_key, $link_attridutes, true );
			$btn_attributes = $this->get_render_attribute_string( $link_key );

			if ( 'button' === $settings['link_click'] ) {
				$wrapper       = '<div ' . $this->get_render_attribute_string( $repeater_setting_key ) . '>';
				$wrapper_close = '</div>';
			} else {
				$wrapper       = '<a ' . $btn_attributes . $this->get_render_attribute_string( $repeater_setting_key ) . '>';
				$wrapper_close = '</a>';
			}

			echo $wrapper;

			echo '<article class="' . esc_attr( implode( ' ', get_post_class( $post_class_array ) ) ) . '">';
			echo '<div class="post-content-wrapper">';

			if ( $settings['show_product_image'] ) {
				$post_media = $this->product_image( $settings, $product );
				echo '<div class="the7-simple-post-thumb"><div class="the7-product-thumb">';
				echo $post_media;
				echo '</div></div>';
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
			echo '</article>';

			echo $wrapper_close;
		}

		wp_reset_postdata();

		echo '</div>';

		$this->template( Pagination::class )->render( $query->max_num_pages );

		echo '</div>';
	}

	/**
	 * @param array $settings Widget settings.
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
	 * @param WC_Product $product Product object.
	 */
	protected function display_add_to_cart( $product ) {
		$settings = $this->get_settings_for_display();

		// Cleanup button render attributes.
		$this->remove_render_attribute( 'box-button' );

		$tag = 'div';
		if ( 'button' === $settings['link_click'] ) {
			$tag = 'a';
		}

		$this->template( Add_To_Cart_Button::class )->render_button( 'box-button', esc_html( $product->add_to_cart_text() ), $tag, $product );
	}

	/**
	 * @param WC_Product $product Product object.
	 *
	 * @return string
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

		$output  = '<p class="short-description">';
		$output .= wp_kses_post( $short_description );
		$output .= '</p>';

		return $output;
	}

	/**
	 * @param array      $settings Widget settings.
	 * @param WC_Product $product Product object.
	 *
	 * @return string
	 */
	protected function product_image( $settings, $product ) {
		if ( $product->get_image_id() ) {
			$icons_html = $this->get_hover_icons_html_template( $settings );

			$link_wrapper = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% />' . $icons_html . '</a>';
			$link         = $product->get_permalink();

			$thumb_args = [
				'img_id'       => $product->get_image_id(),
				'class'        => implode( ' ', [ 'post-thumbnail-rollover', 'img-ratio-wrapper' ] ),
				'lazy_loading' => false,
				'custom'       => the7_get_html_attributes_string(
					[
						'aria-label' => __( 'Product image', 'the7mk2' ),
					]
				),
				'wrap'         => $link_wrapper,
				'echo'         => false,
			];

			if ( $settings['link_click'] === 'box' ) {
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
	 * @param string $text Title text.
	 * @param  string $tag Title HTML tag.
	 *
	 * @return string
	 */
	protected function display_widget_title( $text, $tag = 'h3' ) {
		$tag = Utils::validate_html_tag( $tag );

		$output  = '<' . $tag . ' class="rp-heading">';
		$output .= esc_html( $text );
		$output .= '</' . $tag . '>';

		return $output;
	}

	/**
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	protected function get_hover_icons_html_template( $settings ) {
		$a_atts['class']      = 'the7-hover-icon';
		$a_atts['aria-label'] = __( 'Details link', 'the7mk2' );

		return sprintf(
			'<span %s>%s</span>',
			the7_get_html_attributes_string( $a_atts ),
			$this->get_elementor_icon_html( $settings['hover_icon'], 'i' )
		);
	}

	/**
	 * @param array      $settings Widget settings.
	 * @param string     $tag Title HTML tag.
	 * @param WC_Product $product Product object.
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
	 * Return container class attribute.
	 *
	 * @param string $element Element.
	 */
	protected function add_main_wrapper_class_render_attribute_for( $element ) {
		$class = [
			'the7-related-products',
			'the7-simple-widget-products',
			'the7-elementor-widget',
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

		if ( $loading_mode === 'js_pagination' || $loading_mode === 'js_more' ) {
			$class[] = 'loading-effect-none';
		}

		if ( $loading_mode === 'js_pagination' && $settings['show_all_pages'] ) {
			$class[] = 'show-all-pages';
		}

		if ( $settings['divider'] ) {
			$class[] = 'widget-divider-on';
		}

		if ( $settings['pagination_scroll'] === 'y' ) {
			$class[] = 'enable-pagination-scroll';
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

		$this->add_render_attribute( $element, 'class', $class );
	}

	/**
	 * @param  The7_Elementor_Less_Vars_Decorator_Interface $less_vars Less vars manager.
	 */
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

	/**
	 * Add widget title style controls.
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
	 * Add divider style controls.
	 */
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

	/**
	 * Add content style controls.
	 */
	protected function add_content_style_controls() {
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

	/**
	 * Add product title style controls.
	 */
	protected function add_title_style_controls() {
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
					'{{WRAPPER}} a.wf-cell:hover .product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Add price style controls.
	 */
	protected function add_price_style_controls() {
		$this->start_controls_section(
			'price_style',
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
	 * Add rating style controls.
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
	 * Add product desription style controls.
	 */
	protected function add_product_description_style_contros() {
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
}
