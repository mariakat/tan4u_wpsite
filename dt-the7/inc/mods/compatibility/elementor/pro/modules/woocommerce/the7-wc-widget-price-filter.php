<?php
/**
 * WooCommerce WC_Widget_Price_Filter class proxy with some usefull access tweaks.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Widget price filter class.
 */
class The7_WC_Widget_Price_Filter extends \WC_Widget_Price_Filter {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Do nothing since we do not need to bootstrap the widget.
	}

	/**
	 * Get filtered min price for current products.
	 *
	 * Proxy for the protected parent method.
	 *
	 * @return int
	 */
	public function get_filtered_price() {
		return parent::get_filtered_price();
	}

}
