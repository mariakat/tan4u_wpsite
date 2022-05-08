<?php

use \The7\Mods\Theme_Update\Migrations as Migrations;

defined( 'ABSPATH' ) || exit;

/**
 * Class The7_Install
 */
class The7_Install {

	/**
	 * @var The7_Background_Updater
	 */
	private static $background_updater;

	/**
	 * @return array
	 */
	public static function get_update_callbacks() {
		return [
			'5.5.0'   => [
				'the7_update_550_fancy_titles_parallax',
				'the7_update_550_fancy_titles_font_size',
				'the7_update_550_fancy_subtitles_font_size',
			],
			'6.0.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.1.0'   => [],
			'6.1.1'   => [
				'the7_update_611_page_transparent_top_bar_migration',
			],
			'6.2.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.3.0'   => [
				'the7_update_630_microsite_content_visibility_settings_migration',
			],
			'6.4.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.4.1'   => [
				'the7_update_641_carousel_backward_compatibility',
			],
			'6.4.3'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.5.0'   => [
				'the7_update_650_disable_options_autoload',
			],
			'6.6.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.6.1'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.7.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.8.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.8.1'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'6.9.3'   => [
				'the7_update_693_migrate_custom_menu_widgets',
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.0.0'   => [
				'the7_update_700_shortcodes_gradient_backward_compatibility',
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.1.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.3.0'   => [
				'the7_update_730_set_fancy_title_zero_top_padding',
				'the7_update_730_fancy_title_responsiveness_settings',
			],
			'7.4.0'   => [
				'the7_update_740_fancy_title_uppercase_migration',
			],
			'7.4.3'   => [
				'the7_update_743_back_button_migration',
			],
			'7.5.0'   => [
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.6.0'   => [
				'the7_update_760_mega_menu_migration',
			],
			'7.6.2'   => [],
			'7.7.0'   => [
				'the7_update_770_shortcodes_blog_backward_compatibility',
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.7.1'   => [
				'the7_update_771_shortcodes_blog_backward_compatibility',
				'the7_update_771_shortcodes_button_backward_compatibility',
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'7.7.2'   => [],
			'7.7.5'   => [
				'the7_update_775_fontawesome_compatibility',
			],
			'7.7.6'   => [],
			'7.8.0'   => [
				'the7_update_780_shortcodes_backward_compatibility',
			],
			'7.9.0'   => [
				'the7_update_790_silence_plugins_purchase_notification',
			],
			'7.9.1'   => [
				'the7_regenerate_post_css',
			],
			'8.0.0'   => [],
			'8.1.0'   => [],
			'8.2.0'   => [],
			'8.3.0'   => [
				'the7_update_830_fix_post_padding_meta',
				'the7_update_830_migrate_post_mobile_padding',
				'the7_regenerate_post_css',
			],
			'8.4.0'   => [
				'the7_regenerate_post_css',
			],
			'8.5.0'   => [
				'the7_update_850_migrate_post_footer_visibility',
			],
			'8.5.0.2' => [
				'the7_update_8502_migrate_post_footer_source_for_elementor',
			],
			'8.6.0'   => [],
			'8.7.0'   => [
				'the7_update_purge_elementor_cache',
			],
			'8.9.0'   => [
				'the7_update_890_elementor_the7_elements',
			],
			'9.1.2'   => [
				'the7_update_912_elementor_the7_elements',
			],
			'9.2.0'   => [],
			'9.3.1'   => [
				'the7_update_931_elementor_the7_photo_scroller',
			],
			'9.4.0'   => [
				'the7_update_940_theme_options',
				'the7_update_940_elementor_the7_posts_masonry',
			],
			'9.4.0.2' => [
				'the7_update_9402_theme_options',
			],
			'9.6.0'   => [
				'the7_update_9600_theme_options',
				'the7_update_960_elementor_the7_posts_carousel',
			],
			'9.13.0'  => [
				'the7_update_9130_elementor_the7_posts_carousel',
				'the7_update_9130_elementor_the7_testimonials_carousel',
				'the7_update_9130_elementor_the7_text_and_icon_carousel',
			],
			'9.14.0'  => [
				'the7_update_9140_simple_posts_widget',
				'the7_update_9140_simple_posts_carousel_widget',
				'the7_update_9140_simple_products_widget',
				'the7_update_9140_simple_products_carousel_widget',
				'the7_update_9140_simple_product_category_widget',
				'the7_update_9140_simple_product_category_carousel_widget',
				'the7_elementor_flush_css_cache',
			],
			'9.14.2'  => [
				'the7_update_9142_theme_options',
			],
			'9.15.1'  => [
				'the7_update_9150_posts_carousel_widget',
				'the7_update_9150_multipurpose_carousel_widget',
				'the7_update_9150_testimonials_carousel_widget',
				'the7_update_91501_simple_posts_widget_border',
				'the7_update_91501_simple_posts_carousel_widget_border',
				'the7_update_91501_simple_products_categories_widget_border',
				'the7_update_91501_simple_products_categories_carousel_widget_border',
				'the7_update_91501_simple_products_widget_border',
				'the7_update_91501_simple_products_carousel_widget_border',
				'the7_elementor_flush_css_cache',
			],
			'9.16.0'  => [
				'the7_update_9160_set_buttons_integration_off',
				'the7_update_9160_posts_masonry_widget_buttons',
				'the7_update_9160_posts_carousel_widget_buttons',
				'the7_update_9160_icon_box_grid_widget_buttons',
				'the7_update_9160_simple_posts_carousel_widget_buttons',
				'the7_update_9160_simple_posts_widget_buttons',
				'the7_update_9160_simple_product_categories_widget_buttons',
				'the7_update_9160_simple_product_categories_carousel_widget_buttons',
				'the7_update_9160_simple_products_widget_buttons',
				'the7_update_9160_simple_products_carousel_widget_buttons',
				'the7_update_9160_products_widget_buttons',
				'the7_update_9160_testimonials_widget_buttons',
				'the7_update_9160_text_and_icon_carousel_widget_buttons',
				'the7_elementor_flush_css_cache',
			],
			'9.17.0'  => [
				'the7_update_9170_the7_nav_menu_widget',
				'the7_elementor_flush_css_cache',
				'the7_mass_regenerate_short_codes_inline_css',
			],
			'10.0.0'  => [
				'the7_update_10_0_0_manage_flags',
			],
			'10.1.0'  => [
				'the7_update_10_1_0_products_add_to_cart_icon_migration',
				'the7_update_10_1_0_products_carousel_add_to_cart_icon_migration',
				'the7_elementor_flush_css_cache',
			],
			'10.2.0'  => [
				'the7_update_10_2_0_horizontal_menu_gap_migration',
				'the7_elementor_flush_css_cache',
			],
			'10.3.0'  => [
				'the7_update_10_3_0_activate_deprecated_elementor_widgets',
				'the7_update_10_3_0_migrate_portfolio_bredcrumbs_text',
				'the7_update_10_3_0_activate_deprecated_mega_menu_settings',
			],
			'10.4.0'  => [
				'the7_update_10_4_0_posts_masonry_grid_filters_migration',
				'the7_update_10_4_0_replace_andale_mono_font_in_theme_options',
				'the7_elementor_flush_css_cache',
			],
			'10.4.3'  => [
				'the7_update_10_4_3_maybe_turn_on_elementor_custom_breakpoints',
				'the7_elementor_flush_css_cache',
			],
			'10.5.0'  => [
				'the7_update_10_5_0_turn_off_elementor_paragraph_last_spacing',
				[
					'callback'   => [ Migrations\v10_5_0\Bullets_Switch_Migration::class, 'migrate' ],
					'args_array' => Migrations\v10_5_0\Bullets_Switch_Migration::get_callback_args_array(),
				],
				[ Migrations\v10_5_0\Posts_Masonry_Gap_Migration::class, 'migrate' ],
				[ Migrations\v10_5_0\Submenu_Visibility_Migration::class, 'migrate' ],
				'the7_elementor_flush_css_cache',
			],
			'10.6.1'  => [
				[
					'callback'   => [ Migrations\v10_6_1\Carousel_Stage_Padding_Migration::class, 'migrate' ],
					'args_array' => Migrations\v10_6_1\Carousel_Stage_Padding_Migration::get_callback_args_array(),
				],
			],
			'10.7.0'  => [
				[
					'callback'   => [ Migrations\v10_7_0\Arrows_Visibility_Migration::class, 'migrate' ],
					'args_array' => Migrations\v10_7_0\Arrows_Visibility_Migration::get_callback_args_array(),
				],
			],
		];
	}

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'upgrade_stylesheets_action' ) );

		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) && ! wp_doing_ajax() && ! wp_doing_cron() ) {
			add_action( 'init', array( __CLASS__, 'install_actions' ) );
			add_action( 'init', array( __CLASS__, 'show_db_update_notices' ), 20 );
		}
	}

	/**
	 * @return The7_Background_Updater
	 */
	public static function get_updater() {
		if ( ! self::$background_updater ) {
			self::init_background_updater();
		}

		return self::$background_updater;
	}

	/**
	 * @param  The7_Background_Updater $updater Updater instance.
	 *
	 * @return void
	 */
	public static function set_updater( The7_Background_Updater $updater ) {
		self::$background_updater = $updater;
	}

	/**
	 * @return void
	 */
	public static function updater_dispatch() {
		self::$background_updater->save()->dispatch();
	}

	/**
	 * @return void
	 */
	public static function check_version() {
		$current_db_version = self::get_db_version();

		// No db version? New install.
		if ( is_null( $current_db_version ) ) {
			self::update_db_version();

			// Dismiss updated notice.
			the7_admin_notices()->dismiss_notice( 'the7_updated' );
		}
	}

	/**
	 * Init background updates
	 */
	public static function init_background_updater() {
		require_once __DIR__ . '/patches/interface-the7-db-patch.php';
		require_once __DIR__ . '/class-the7-background-updater.php';
		require_once __DIR__ . '/the7-update-functions.php';
		require_once PRESSCORE_MODS_DIR . '/compatibility/elementor/upgrade/class-the7-elementor-updater.php';
		require_once PRESSCORE_MODS_DIR . '/compatibility/elementor/upgrade/class-the7-elementor-widget-migrations.php';

		self::set_updater( new The7_Background_Updater() );
	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}

		if ( ! empty( $_GET['force_update_the7'] ) && is_admin() ) {
			do_action( 'wp_the7_updater_cron' );
			wp_safe_redirect( admin_url( 'admin.php?page=the7-dashboard' ) );
			exit;
		}

		if ( self::db_is_updating() ) {
			return;
		}

		if ( self::is_auto_update_db() ) {
			self::update();

			return;
		}

		if ( ! empty( $_GET['do_update_the7'] ) && is_admin() ) {
			self::update();
			wp_safe_redirect( add_query_arg( 'do_updating_the7', 'true', admin_url( 'admin.php?page=the7-dashboard' ) ) );
			exit;
		}
	}

	public static function update_notice() {
		include __DIR__ . '/views/html-notice-update.php';
	}

	public static function updating_notice() {
		include __DIR__ . '/views/html-notice-updating.php';
	}

	public static function updated_notice() {
		include __DIR__ . '/views/html-notice-updated.php';
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	public static function update() {
		$db_version = self::get_db_version();

		if ( version_compare( $db_version, PRESSCORE_DB_VERSION, '>=' ) ) {
			return;
		}

		$update_queued = false;

		// Update the7 options.
		self::$background_updater->push_to_queue( array( __CLASS__, 'update_theme_options' ) );

		$db_update_callbacks = self::get_update_callbacks();

		// Update db.
		foreach ( $db_update_callbacks as $version => $update_callbacks ) {
			if ( version_compare( $db_version, $version, '<' ) ) {

				// Automatically bump db version as a last batch task.
				$update_callbacks[] = 'bump_db_version_to_' . $version;

				self::register_update_callbacks( $update_callbacks );

				$update_queued = true;
			}
		}

		if ( $update_queued ) {
			self::updater_dispatch();
		}
	}

	/**
	 * @param array $update_callbacks Update callbacks array.
	 *
	 * @return void
	 */
	public static function register_update_callbacks( array $update_callbacks ) {
		foreach ( $update_callbacks as $update_callback ) {
			if ( isset( $update_callback['callback'], $update_callback['args_array'] ) ) {
				$args_array = (array) $update_callback['args_array'];
				foreach ( $args_array as $args ) {
					self::$background_updater->push_to_queue(
						[
							'callback' => $update_callback['callback'],
							'args'     => $args,
						]
					);
				}
			} else {
				self::$background_updater->push_to_queue( $update_callback );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function show_db_update_notices() {
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}

		$db_version = self::get_db_version();

		if ( version_compare( $db_version, PRESSCORE_DB_VERSION, '<' ) ) {
			the7_admin_notices()->reset( 'the7_updated' );
			if ( self::db_is_updating() ) {
				the7_admin_notices()->add( 'the7_updating', array( __CLASS__, 'updating_notice' ), 'the7-dashboard-notice' );
			} elseif ( ! self::is_auto_update_db() ) {
				the7_admin_notices()->add( 'the7_update', array( __CLASS__, 'update_notice' ), 'the7-dashboard-notice' );
			}
		} else {
			the7_admin_notices()->add( 'the7_updated', array( __CLASS__, 'updated_notice' ), 'the7-dashboard-notice updated is-dismissible' );
		}
	}

	/**
	 * @param string|null $version Theme db version.
	 *
	 * @return void
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'the7_db_version' );
		add_option( 'the7_db_version', is_null( $version ) ? PRESSCORE_DB_VERSION : $version );
	}

	/**
	 * @return mixed
	 */
	public static function is_auto_update_db() {
		return The7_Admin_Dashboard_Settings::get( 'db-auto-update' );
	}

	/**
	 * @return mixed
	 */
	public static function get_db_version() {
		return get_option( 'the7_db_version', null );
	}

	/**
	 * @return void
	 */
	public static function upgrade_stylesheets_action() {
		if ( version_compare( get_option( 'the7_style_version' ), PRESSCORE_STYLESHEETS_VERSION, '<' ) ) {
			_optionsframework_delete_defaults_cache();

			self::regenerate_stylesheets();

			update_option( 'the7_style_version', PRESSCORE_STYLESHEETS_VERSION );
		}
	}

	/**
	 * @return void
	 */
	public static function regenerate_stylesheets() {
		presscore_refresh_dynamic_css();
		the7_elementor_flush_css_cache();
	}

	/**
	 * @return bool
	 */
	public static function db_is_updating() {
		return self::$background_updater->is_updating();
	}

	/**
	 * @return bool
	 */
	public static function db_update_is_needed() {
		return version_compare( self::get_db_version(), PRESSCORE_DB_VERSION, '<' );
	}

	/**
	 * @return void
	 */
	public static function update_theme_options() {
		$cur_db_version = self::get_db_version();
		$options        = optionsframework_get_options();
		if ( ! $options ) {
			return;
		}

		$patches_dir = trailingslashit( trailingslashit( __DIR__ ) . 'patches' );

		$patches = array(
			'3.5.0' => 'The7_DB_Patch_030500',
			'4.0.0' => 'The7_DB_Patch_040000',
			'4.0.3' => 'The7_DB_Patch_040003',
			'5.0.3' => 'The7_DB_Patch_050003',
			'5.1.6' => 'The7_DB_Patch_050106',
			'5.2.0' => 'The7_DB_Patch_050200',
			'5.3.0' => 'The7_DB_Patch_050300',
			'5.4.0' => 'The7_DB_Patch_050400',
			'6.0.0' => 'The7_DB_Patch_060000',
			'6.1.0' => 'The7_DB_Patch_060100',
			'6.1.1' => 'The7_DB_Patch_060101',
			'6.6.0' => 'The7_DB_Patch_060600',
			'6.6.1' => 'The7_DB_Patch_060601',
			'7.0.0' => 'The7_DB_Patch_070000',
			'7.1.0' => 'The7_DB_Patch_070100',
			'7.3.0' => 'The7_DB_Patch_070300',
			'7.4.0' => 'The7_DB_Patch_070400',
			'7.4.3' => 'The7_DB_Patch_070403',
			'7.6.0' => 'The7_DB_Patch_070600',
			'7.6.2' => 'The7_DB_Patch_070602',
			'7.7.1' => 'The7_DB_Patch_070701',
			'7.7.2' => 'The7_DB_Patch_070702',
			'7.7.6' => 'The7_DB_Patch_070706',
			'7.8.0' => 'The7_DB_Patch_070800',
			'8.0.0' => 'The7_DB_Patch_080000',
			'8.1.0' => 'The7_DB_Patch_080100',
			'8.2.0' => 'The7_DB_Patch_080200',
			'8.6.0' => 'The7_DB_Patch_080600',
			'9.2.0' => 'The7_DB_Patch_090200',
		);

		$update_options = false;
		foreach ( $patches as $ver => $class_name ) {
			if ( version_compare( $ver, $cur_db_version ) <= 0 ) {
				continue;
			}

			if ( ! class_exists( $class_name ) ) {
				require_once $patches_dir . 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
			}

			$patch          = new $class_name();
			$options        = $patch->apply( $options );
			$update_options = true;
		}

		The7_Options_Backup::store_options();

		if ( $update_options ) {
			of_save_unsanitized_options( $options );
			_optionsframework_delete_defaults_cache();
		}
	}
}
