<?php
/**
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widget_Templates;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class Add_To_Cart_Button
 *
 * @package The7\Mods\Compatibility\Elementor\Widget_Templates
 */
class Add_To_Cart_Button extends Button {

	/**
	 * @var array
	 */
	protected $args_cache = [];

	/**
	 * Set button render attributes.
	 *
	 * @param string     $element Element.
	 * @param WC_Product $product Products object.
	 */
	public function add_render_attributes( $element, $product ) {
		$args = $this->get_args( $product );

		$attributes                   = isset( $args['attributes'] ) ? (array) $args['attributes'] : [];
		$attributes['href']           = esc_url( $product->add_to_cart_url() );
		$attributes['data-quantity']  = $args['quantity'];
		$attributes['data-widget-id'] = $this->widget->get_id();
		$attributes['class']          = $args['class'];

		$this->widget->add_render_attribute( $element, $attributes );
	}

	/**
	 * Output button HTML.
	 *
	 * @param string     $element Element name.
	 * @param string     $text    Button text. Should be escaped beforehand.
	 * @param string     $tag     Button HTML tag, 'a' by default.
	 * @param WC_Product $product WC product object.
	 */
	public function render_button( $element, $text = '', $tag = 'a', $product = null ) {
		$this->add_render_attributes( $element, $product );

		ob_start();
		parent::render_button( $element, $text, $tag );
		$html = ob_get_clean();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'woocommerce_loop_add_to_cart_link', $html, $product, $this->get_args( $product ) );
	}

	/**
	 * Returns filtered add to cart button args.
	 *
	 * @param WC_Product $product Products object.
	 *
	 * @return array
	 */
	protected function get_args( $product ) {
		if ( ! $product ) {
			return [];
		}

		$product_id = $product->get_id();

		if ( ! isset( $this->args_cache[ $product_id ] ) ) {
			$defaults = [
				'quantity'   => 1,
				'class'      => array_filter(
					[
						'product_type_' . $product->get_type(),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					]
				),
				'attributes' => [
					'data-product_id'  => $product_id,
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => wp_strip_all_tags( $product->add_to_cart_description() ),
					'rel'              => 'nofollow',
				],
			];

			$this->args_cache = [
				$product_id => (array) apply_filters( 'woocommerce_loop_add_to_cart_args', $defaults, $product ),
			];
		}

		return $this->args_cache[ $product_id ];
	}
}
