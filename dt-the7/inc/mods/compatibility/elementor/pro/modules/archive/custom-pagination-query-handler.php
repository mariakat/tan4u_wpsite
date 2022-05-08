<?php
/**
 * Custom pagination handler for an archive query.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Archive;

use ElementorPro\Modules\ThemeBuilder\Module;
use The7\Mods\Compatibility\Elementor\Widgets\Woocommerce\Products;

defined( 'ABSPATH' ) || exit;

/**
 * Custom_Pagination_Query_Handler class.
 */
class Custom_Pagination_Query_Handler {

	/**
	 * Construct and init handler.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			add_action(
				'pre_get_posts',
				[ $this, 'handle_archive_and_search_posts_per_page' ],
				11
			);
		}
	}

	/**
	 * Change archive and search posts_per_page value based on widget settings.
	 *
	 * @see Products::register_controls()
	 *
	 * @param  \WP_Query $query WP query.
	 */
	public function handle_archive_and_search_posts_per_page( \WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$widget_filter_callback = null;
		if ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) {
			$widget_filter_callback = [ $this, 'product_archive_widget_filter' ];
		} elseif ( $query->is_search() || $query->is_archive() ) {
			$widget_filter_callback = [ $this, 'archive_widget_filter' ];
		}

		if ( ! $widget_filter_callback ) {
			return;
		}

		$documents_by_conditions = Module::instance()->get_conditions_manager()->get_documents_for_location( 'archive' );

		foreach ( $documents_by_conditions as $document_id => $document ) {
			$widget = the7_elementor_find_the_first_element_recursive( $document->get_elements_raw_data(), $widget_filter_callback );

			if ( ! empty( $widget['settings']['archive_posts_per_page'] ) ) {
				$query->set( 'posts_per_page', (int) $widget['settings']['archive_posts_per_page'] );

				return;
			}
		}
	}

	/**
	 * @param array $element Elementor widget as array.
	 *
	 * @return bool
	 */
	public function product_archive_widget_filter( $element ) {
		if ( ! $this->is_widget( $element, 'the7-wc-products' ) ) {
			return false;
		}

		$settings = $element['settings'];

		return isset( $settings['query_post_type'] ) && $settings['query_post_type'] === 'current_query';
	}

	/**
	 * @param array $element Elementor widget as array.
	 *
	 * @return bool
	 */
	public function archive_widget_filter( $element ) {
		if ( ! $this->is_widget( $element, 'the7_elements' ) ) {
			return false;
		}

		$settings = $element['settings'];

		return isset( $settings['post_type'] ) && $settings['post_type'] === 'current_query';
	}

	/**
	 * @param array        $element Elementor widget as array.
	 * @param array|string $widget Widget/widgets to check for.
	 *
	 * @return bool
	 */
	protected function is_widget( $element, $widget ) {
		return isset( $element['widgetType'], $element['settings'] ) && in_array( $element['widgetType'], (array) $widget, true );
	}
}
