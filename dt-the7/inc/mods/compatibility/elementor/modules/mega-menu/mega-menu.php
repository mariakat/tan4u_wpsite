<?php
/**
 * The7 elementor mega menu front class.
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Modules\Mega_Menu;

use Elementor\Plugin;
use stdClass;
use The7_Admin_Dashboard_Settings;
use The7_Option_Field_Spacing;
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Class Mega_Menu
 */
class Mega_Menu {

	const SUB_MENU_CLASS = "the7-e-sub-nav";

	/**
	 * Is mega menu enabled.
	 * @var bool
	 */
	protected $e_mega_menu_enabled = false;
	protected $e_mega_menu_mobile_empty = false;
	protected $e_mega_menu_mobile_content = '';

	/**
	 * Add mega menu hooks.
	 */
	public function add_hooks() {
		add_action( 'presscore_nav_menu_start_el', array( $this, 'detect_mega_menu_action' ), 10, 3 );
		add_filter( 'presscore_nav_menu_css_class', array( $this, 'mega_menu_class_filter' ), 10, 4 );
		add_filter( 'presscore_nav_menu_start_lvl', array( $this, 'start_row' ), 10, 3 );
		add_filter( 'presscore_nav_menu_end_lvl', array( $this, 'end_row' ), 10, 2 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'append_megamenu' ), 10, 4 );
		add_filter( 'presscore_nav_menu_item', array( $this, 'menu_item' ), 10, 5 );
		add_filter( 'presscore_nav_menu_start_el_li', array( $this, 'adjust_li_wrapper' ), 10, 4 );
		add_filter( 'presscore_nav_menu_end_el_li', array( $this, 'adjust_li_wrapper' ), 10, 4 );
	}


	/**
	 * Remove mega menu hooks.
	 */
	public function remove_hooks() {
		remove_action( 'presscore_nav_menu_start_el', array( $this, 'detect_mega_menu_action' ) );
		remove_filter( 'presscore_nav_menu_css_class', array( $this, 'mega_menu_class_filter' ) );
		remove_filter( 'presscore_nav_menu_start_lvl', array( $this, 'start_row' ) );
		remove_filter( 'presscore_nav_menu_end_lvl', array( $this, 'end_row' ) );
		remove_filter( 'walker_nav_menu_start_el', array( $this, 'append_megamenu' ) );
		remove_filter( 'presscore_nav_menu_item', array( $this, 'menu_item' ) );
		remove_action( 'presscore_nav_menu_start_el_li', array( $this, 'adjust_li_wrapper' ) );
		remove_filter( 'presscore_nav_menu_end_el_li', array( $this, 'adjust_li_wrapper' ) );
	}

	/**
	 * Filter menu item. Add icon/image.
	 *
	 * @param string  $menu_item   Menu item code.
	 * @param string  $title       Menu item title.
	 * @param string  $description Menu item description.
	 * @param WP_Post $item        Menu item data object.
	 * @param int     $depth       Menu item depth.
	 *
	 * @return string
	 */
	public function menu_item( $menu_item, $title, $description, $item, $depth ) {
		if ( $menu_item ) {
			return $menu_item;
		}

		$icon = $this->dt_get_item_icon( $item );
		if ( isset( $item->the7_mega_menu['menu-item-image-position'], $item->the7_mega_menu['menu-item-icon-type'] ) && in_array( $item->the7_mega_menu['menu-item-icon-type'], array(
				'image',
				'icon',
			), true ) && in_array( $item->the7_mega_menu['menu-item-image-position'], array(
				'right_top',
				'left_top',
			), true ) ) {
			$menu_item = '<span class="menu-item-text">' . $icon . '<span class="menu-text">' . $title . '</span></span>' . $description;
		} else {
			$menu_item = $icon . '<span class="menu-item-text"><span class="menu-text">' . $title . '</span>' . $description . '</span>';
		}

		return $menu_item;
	}

	/**
	 * Return menu item icon if any.
	 *
	 * @param WP_Post $item Page data object.
	 *
	 * @return string
	 */
	protected function dt_get_item_icon( $item ) {
		$image_html = '';
		$deprecated_mega_menu = The7_Admin_Dashboard_Settings::get( 'deprecated_mega_menu_settings' );
		if ( isset( $item->the7_mega_menu['menu-item-icon-type'] ) ) {
			switch ( $item->the7_mega_menu['menu-item-icon-type'] ) {
				case 'html':
					$image_html = $item->the7_mega_menu['menu-item-icon-html'];
					break;
				case 'icon':
					$inline_style = '';
					if ( $deprecated_mega_menu ) {
						$style = $this->dt_get_icon_padding_inline_style( $item );
						if ( $style ) {
							$inline_style = 'style="' . esc_attr( $style ) . '"';
						}
					}
					$image_html = '<i class="fa-fw ' . esc_attr( $item->the7_mega_menu['menu-item-icon'] ) . '" ' . $inline_style . ' ></i>';
					break;
				case 'image':
					if ( empty( $item->the7_mega_menu['menu-item-image'][1] ) ) {
						break;
					}

					$width = 50;
					$height = 50;
					$image_style = '';

					if ( $deprecated_mega_menu ) {
						if ( isset( $item->the7_mega_menu['menu-item-image-size'] ) ) {
							$size_option_value = (array) The7_Option_Field_Spacing::decode( $item->the7_mega_menu['menu-item-image-size'] );
							if ( count( $size_option_value ) === 2 ) {
								list( $width, $height ) = array_map( 'absint', wp_list_pluck( $size_option_value, 'val' ) );
							}
						}

						if ( isset( $item->the7_mega_menu['menu-item-image-border-radius'] ) ) {
							$image_style .= 'border-radius: ' . $item->the7_mega_menu['menu-item-image-border-radius'] . ';';
						}
						$image_style .= $this->dt_get_icon_padding_inline_style( $item );
						if ( $image_style ) {
							$image_style = 'style="' . esc_attr( $image_style ) . '"';
						}
					}
					$image_html = dt_get_thumb_img( array(
						'class'   => 'rollover',
						'img_id'  => $item->the7_mega_menu['menu-item-image'][1],
						'alt'     => 'Menu icon',
						'options' => array(
							'w' => $width,
							'h' => $height,
						),
						'wrap'    => '<img %IMG_CLASS% %SRC% %ALT% %SIZE% %CUSTOM% />',
						'custom'  => $image_style,
						'echo'    => false,
					) );
					break;
			}
		}

		return $image_html;
	}

	/**
	 * Return menu item padding inline style.
	 *
	 * @param WP_Post $item Page data object.
	 *
	 * @return string
	 */
	protected function dt_get_icon_padding_inline_style( $item ) {
		return $this->dt_get_spacing_inline_style( $item, 'menu-item-image-padding', 'margin' );
	}

	/**
	 * Return padding inline style.
	 *
	 * @param WP_Post $item       Page data object.
	 * @param string  $prop       Mega menu property.
	 * @param string  $style_type CSS property.
	 *
	 * @return string
	 */
	protected function dt_get_spacing_inline_style( $item, $prop, $style_type ) {
		$style = '';
		if ( empty( $style_type ) ) {
			$style_type = 'padding';
		}
		if ( isset( $item->the7_mega_menu[ $prop ] ) ) {
			$padding_option_value = The7_Option_Field_Spacing::decode( $item->the7_mega_menu[ $prop ] );
			if ( $padding_option_value ) {
				$padding_style = '';
				foreach ( $padding_option_value as $padding ) {
					$padding_style .= "{$padding['val']}{$padding['units']} ";
				}
				$style = $style_type . ': ' . trim( $padding_style ) . ';';
			}
		}

		return $style;
	}

	/**
	 * Early mega menu setup.
	 * Find out if mega menu enabled, set second level columns, is current item clickable.
	 *
	 * @param WP_Post  $item  Menu item data object.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item.
	 */
	public function detect_mega_menu_action( $item, $args, $depth ) {
		if ( 0 === $depth ) {
			$this->e_mega_menu_mobile_empty = false;
			$this->e_mega_menu_mobile_content = '';
			if ( isset( $item->the7_mega_menu['mega-menu-elementor'] ) && $item->the7_mega_menu['mega-menu-elementor'] === 'on' ) {
				$this->e_mega_menu_enabled = true;
				if ( isset( $item->the7_mega_menu['mega-menu-elementor-mobile-content'] ) && $item->the7_mega_menu['mega-menu-elementor-mobile-content'] && ! $item->dt_is_parent ) {
					$this->e_mega_menu_mobile_empty = true;
				}
				$item->dt_is_parent = true;
				$item->dt_is_clickable = $args->parent_is_clickable;
			} else {
				$this->e_mega_menu_enabled = false;
			}
			if ( $this->is_mega_menu_enabled() ) {
				if ( isset( $item->the7_mega_menu['mega-menu-elementor-mobile-content'] ) && $item->the7_mega_menu['mega-menu-elementor-mobile-content'] ) {
					$this->e_mega_menu_mobile_content = $item->the7_mega_menu['mega-menu-elementor-mobile-content'];
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	protected function is_mega_menu_enabled() {
		return $this->e_mega_menu_enabled;
	}

	/**
	 * Setup menu item classes.
	 *
	 * @param array    $classes Menu item classes.
	 * @param WP_Post  $item    Menu item data object.
	 * @param stdClass $args    An object of wp_nav_menu() arguments.
	 * @param int      $depth   Depth of menu item.
	 *
	 * @return array
	 */
	public function mega_menu_class_filter( $classes, $item, $args, $depth ) {
		if ( $this->is_mega_menu_enabled() && $depth == 0 ) {
			$classes[] = 'the7-e-mega-menu';
			if ( $this->e_mega_menu_mobile_content === 'wp_mobile_menu' ) {
				$classes[] = 'the7-e-mega-menu-mobile';
			}
			if ( $this->e_mega_menu_mobile_empty ) {
				$classes[] = 'the7-e-mega-menu-mobile-empty';
			}
		}

		return $classes;
	}

	/**
	 * Append row wrap open tag to $output if mega menu enabled.
	 *
	 * @param string $output Menu item html.
	 * @param int    $depth  Depth of menu item.
	 *
	 * @return string
	 */
	public function start_row( $output, $depth, $args ) {
		if ( $this->is_mega_menu_enabled() && $this->e_mega_menu_mobile_content !== 'wp_mobile_menu' ) {
			$output = '';
		} else {
			$output = '<ul class="' . self::SUB_MENU_CLASS . ' ' . esc_attr( $args->submenu_class ) . '" role="menubar">';
		}

		return $output;
	}

	public function end_row( $output, $depth ) {
		if ( $this->is_mega_menu_enabled() && $this->e_mega_menu_mobile_content !== 'wp_mobile_menu' ) {
			$output = '';
		}

		return $output;
	}


	/**
	 * Append widgets based on mega menu settings.
	 *
	 * @param string  $item_html Item HTML.
	 * @param WP_Post $item      Menu item data object.
	 *
	 * @return string
	 */
	public function append_megamenu( $item_html, $item, $depth, $args ) {
		if ( $this->is_mega_menu_enabled() ) {
			if ( $depth == 0 ) {
				$item_html .= '<ul class="' . self::SUB_MENU_CLASS . '  the7-e-mega-menu-sub-nav" role="menubar"><li>';
				$local_html = '';
				$document_id = '';
				if ( isset( $item->the7_mega_menu['mega-menu-elementor-template'] ) && $item->the7_mega_menu['mega-menu-elementor-template'] ) {
					$document_id = $item->the7_mega_menu['mega-menu-elementor-template'];
				}
				$document = Plugin::instance()->documents->get( $document_id );
				if ( $document && Document::STATUS_PUBLISH === $document->get_post()->post_status ) {
					//$local_html .= $document->get_content();
					$local_html .= Plugin::instance()->frontend->get_builder_content_for_display( $document->get_id() );
				}
				if ( empty( $local_html ) ) {
					$item_html .= esc_html__( 'No content found', 'the7mk2' );
				} else {
					$item_html .= $local_html;
				}
				$item_html .= '</li></ul>';
			} elseif ( $this->e_mega_menu_mobile_content !== 'wp_mobile_menu' ) {
				$item_html = ''; //remove normal menu items
			}
		}

		return $item_html;
	}

	public function adjust_li_wrapper( $item_html, $item, $args, $depth ) {
		if ( $this->is_mega_menu_enabled() && $depth >= 1 && $this->e_mega_menu_mobile_content !== 'wp_mobile_menu' ) {
			$item_html = '';
		}

		return $item_html;
	}
}
