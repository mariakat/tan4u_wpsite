<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Mega_Menu;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Utils;
use Elementor\Plugin as Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Document extends Library_Document {


	public function __construct( array $data = [] ) {
		if ( $data && the7_elementor_pro_is_active()) {
			add_filter( 'body_class', [ $this, 'filter_body_classes' ] );
		}

		parent::__construct( $data );
	}

	/**
	 * Add body classes.
	 *
	 * Add the body classes for the `style` controls selector.
	 *
	 * @param $body_classes
	 *
	 * @return array
	 */
	public function filter_body_classes( $body_classes ) {
        if ( get_the_ID() === $this->get_main_id() || Elementor::$instance->preview->is_preview_mode( $this->get_main_id() )) {
			$body_classes[] = $this->get_name() . '-template';
		}

		return $body_classes;
	}

	public static function get_properties() {
		$properties = parent::get_properties();
		$properties['support_site_editor'] = false; //in order to hide doc on "website" builder
		$properties['support_wp_page_templates'] = true; // in order to use canvas template
		$properties['support_conditions'] = false;
		$properties['support_kit'] = true;
		$properties['show_in_finder'] = false;
		return $properties;
	}

	/**
	 * Get document title.
	 * Retrieve the document title.
	 * @return string Document title.
	 * @access public
	 * @static
	 */
	public static function get_title() {
		return esc_html__( 'The7 Mega Menu', 'the7mk2' );
	}

	public static function get_plural_title() {
		return __( 'The7 Mega Menus', 'the7mk2' );
	}

	/**
	 * Get CSS wrapper selector.
	 * Retrieve the wrapper selector for the current menu.
	 * @since  1.6.0
	 * @access public
	 * @abstract
	 */
	public function get_css_wrapper_selector() {
		return '.elementor-' . $this->get_main_id();
	}

	/**
	 * Get element name.
	 * Retrieve the element name.
	 * @return string The name.
	 * @since  1.4.0
	 * @access public
	 */
	public function get_name() {
		return $this->get_type();
	}

	public static function get_type() {
		return Module::DOCUMENT_TYPE;
	}

	public function save( $data ) {
		$data['settings']['post_status'] = Document::STATUS_PUBLISH;

		return parent::save( $data );
	}

	public function print_elements_with_wrapper( $elements_data = null ) {
		if ( ! $elements_data ) {
			$elements_data = $this->get_elements_data();
		}

		?>
        <div <?php Utils::print_html_attributes( $this->get_container_attributes() ); ?>>
            <div class="elementor-section-wrap">
				<?php $this->print_elements( $elements_data ); ?>
            </div>
        </div>
		<?php
	}

	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();
		$settings = $this->get_settings_for_display();
		$attributes['class'] .= ' the7-e-mega-menu-content the7-e-mega-menu-width-' . $settings['the7_mega_menu_width'];

		return $attributes;
	}

	/**
	 * Override original `get_content` to prevent recursion
	 *
	 * @return string Megamenu HTML
	 */
	public function get_content( $with_css = false ) {
		if ( get_the_ID() === $this->get_main_id()) {
			return '';
		}
		return parent::get_content();
	}

	protected function register_controls() {

		$this->start_controls_section( 'the7_mega_menu_setting', [
			'label' => esc_html__( 'Menu Item Settings', 'the7mk2' ),
			'tab'   => Controls_Manager::TAB_SETTINGS,
		] );
		//handle optimized and not optimized menu template html, because preview html are generated via js
		$selector = "{{WRAPPER}}  > .elementor-section-wrap, {{WRAPPER}} > .elementor-inner > .elementor-section-wrap";

		$this->add_control( 'the7_mega_menu_bg_color', [
			'label'     => __( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'the7_mega_menu_border',
			'label'    => __( 'Border', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_responsive_control( 'the7_mega_menu_border_radius', [
			'label'      => __( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );


		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'the7_mega_menu_shadow',
			'selector' => $selector,
		] );

		$this->add_responsive_control(
			'the7_mega_menu_margin',
			[
				'label'      => __( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control( 'the7_mega_menu_width', [
			'label'        => __( 'Force Full Width', 'the7mk2' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'On', 'the7mk2' ),
			'label_off'    => __( 'Off', 'the7mk2' ),
			'return_value' => 'full',
			'default'      => 'auto',
		] );

		$this->end_controls_section();

		parent::register_controls();
	}
}
