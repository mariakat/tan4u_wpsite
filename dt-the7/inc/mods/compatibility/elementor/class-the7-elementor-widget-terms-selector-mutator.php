<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Widget terms mutator class.
 */
class The7_Elementor_Widget_Terms_Selector_Mutator {

	/**
	 * Bootstrap.
	 */
	public function bootstrap() {
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'wp_ajax_the7_elements_get_widget_taxonomies', [ $this, 'ajax_return_taxonomies' ] );
	}

	/**
	 * Enqueue editor scripts.
	 */
	public function enqueue_editor_scripts() {
		the7_register_script(
			'the7-elements-widget-settings',
			PRESSCORE_ADMIN_URI . '/assets/js/elementor/elements-widget-settings.js'
		);
		wp_enqueue_script( 'the7-elements-widget-settings' );
		wp_localize_script(
			'the7-elements-widget-settings',
			'the7ElementsWidget',
			[
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'_wpnonce' => wp_create_nonce( 'the7-elements-ajax' ),
			]
		);
	}

	/**
	 * Ajax handler. Returns taxonomies and terms to be used in query selector.
	 */
	public function ajax_return_taxonomies() {
		check_admin_referer( 'the7-elements-ajax' );

		if ( empty( $_POST['post_types'] ) ) {
			$post_types = array_keys( the7_elementor_elements_widget_post_types() );
		} else {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$post_types = array_filter( (array) wp_unslash( $_POST['post_types'] ) );
		}

		$taxonomies = [];
		$terms      = [];
		foreach ( $post_types as $post_type ) {
			$taxonomies[ $post_type ] = [];

			if ( $post_type === 'related' ) {
				$tax_objects                = get_taxonomies( [ 'public' => true ], 'objects' );
				$taxonomies[ $post_type ][] = [
					'value' => '',
					'label' => esc_html__( 'Entire Post Type', 'the7mk2' ),
				];
			} else {
				$tax_objects = get_object_taxonomies( $post_type, 'objects' );
			}
			foreach ( $tax_objects as $tax ) {
				if ( $tax->name === 'post_format' || ! $tax->public ) {
					continue;
				}

				$taxonomies[ $post_type ][] = [
					'value' => $tax->name,
					'label' => $tax->label,
				];

				if ( $post_type === 'related' ) {
					continue;
				}

				$terms_objects       = get_terms(
					[
						'taxonomy'   => $tax->name,
						'hide_empty' => false,
					]
				);
				$terms[ $tax->name ] = [];
				foreach ( $terms_objects as $term ) {
					$terms[ $tax->name ][] = [
						'value' => (string) $term->term_id,
						'label' => $term->name,
					];
				}
			}
		}

		wp_send_json( compact( 'taxonomies', 'terms' ) );
	}

}
