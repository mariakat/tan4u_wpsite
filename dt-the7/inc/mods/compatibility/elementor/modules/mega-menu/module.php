<?php

namespace The7\Mods\Compatibility\Elementor\Modules\Mega_Menu;

use Elementor\Core\Documents_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends The7_Elementor_Module_Base {

	const DOCUMENT_TYPE = 'the7-mega-menu';

	public function __construct() {
		new Options();
		add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
	}

	/**
	 * @param Documents_Manager $documents_manager
	 */
	public function register_document( $documents_manager ) {
		$documents_manager->register_document_type( self::DOCUMENT_TYPE, Document::get_class_full_name() );
	}

	public function get_posts() {
		$source = Plugin::$instance->templates_manager->get_source( 'local' );
		$templates = $source->get_items( [ 'type' => self::DOCUMENT_TYPE ] );

		return wp_list_pluck( $templates, 'title', 'template_id' );
	}

	/**
	 * Get module name.
	 * Retrieve the module name.
	 * @access public
	 * @return string Module name.
	 */
	public function get_name() {
		return 'mega-menu';
	}
}
