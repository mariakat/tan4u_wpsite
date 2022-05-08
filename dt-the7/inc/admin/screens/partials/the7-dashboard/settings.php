<?php
/**
 * The7 dashboard settings.
 * @package The7\Admin
 */

use The7\Mods\Theme_Update\Migrations\v10_0_0\Kit_Globals_Migration;

defined( 'ABSPATH' ) || exit;

$hide_tr = 'class="hide-if-js"';
$elementor_theme_style = The7_Admin_Dashboard_Settings::get( 'elementor-theme-style' );
?>
<div class="the7-postbox the7-settings">
    <h2><?php esc_html_e( 'Settings', 'the7mk2' ); ?></h2>
    <form id="the7-settings" type="post">
        <input type="hidden" name="action" value="the7_save_dashboard_settings">
		<?php wp_nonce_field( The7_Admin_Dashboard_Settings::SETTINGS_ID . '-save' ); ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th><?php esc_html_e( 'General Settings', 'the7mk2' ); ?></th>
                <td>
                    <fieldset>
						<?php
						$db_auto_update_disabled = '';
						$description = '';
						if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
							$db_auto_update_disabled = 'disabled style="color: rgba(222, 222, 222, 0.75);"';
							$description = '<br><small>' . esc_html__( 'Auto update disabled because DISABLE_WP_CRON constant is set to true.', 'the7mk2' ) . '</small>';
						}
						?>
                        <label for="the7-db-auto-update" <?php echo $db_auto_update_disabled; ?>>
							<?php echo $description; ?>
                            <input type="checkbox" id="the7-db-auto-update"
                                   name="the7_dashboard_settings[db-auto-update]"<?php checked( The7_Admin_Dashboard_Settings::get( 'db-auto-update' ) ); ?> <?php echo $db_auto_update_disabled; ?>>
							<?php esc_html_e( 'DB auto update', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-enable-mega-menu">
                            <input type="checkbox" id="the7-enable-mega-menu"
                                   name="the7_dashboard_settings[mega-menu]"<?php checked( The7_Admin_Dashboard_Settings::get( 'mega-menu' ) ); ?>>
							<?php esc_html_e( 'Enable Mega Menu', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-web-fonts-display-swap">
                            <input type="checkbox" id="the7-web-fonts-display-swap"
                                   name="the7_dashboard_settings[web-fonts-display-swap]"<?php checked( The7_Admin_Dashboard_Settings::get( 'web-fonts-display-swap' ) ); ?>>
							<?php esc_html_e( 'Set display "swap" for google fonts', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
					<?php if ( the7_elementor_is_active() ) : ?>
                        <fieldset>
                            <label for="the7-elementor-buttons-integration">
                                <input type="checkbox" id="the7-elementor-buttons-integration"
                                       name="the7_dashboard_settings[elementor-buttons-integration]"<?php checked( The7_Admin_Dashboard_Settings::get( 'elementor-buttons-integration' ) ); ?>>
								<?php esc_html_e( 'Elementor buttons integration', 'the7mk2' ); ?>
                            </label>
                        </fieldset>
                        <fieldset>
                            <label for="the7-elementor-theme-style">
                                <input type="checkbox" id="the7-elementor-theme-style"
                                       name="the7_dashboard_settings[elementor-theme-style]"<?php checked( $elementor_theme_style ); ?>>
								<?php esc_html_e( 'Enable Elementor Theme Style (and disable Theme Options)', 'the7mk2' ); ?>
                            </label>
                        </fieldset>
						<?php if ( ! $elementor_theme_style ) : ?>
							<?php
							$sel_options = [
								'do_nothing' => esc_html__( 'No', 'the7mk2' ),
								'migrate'    => esc_html__( 'Yes', 'the7mk2' ),
							];
							$sel_default = 'do_nothing';

							$has_backup = Kit_Globals_Migration::get_backup();
							if ( $has_backup === false ) {
								$was_migration = get_option( 'the7-theme-style-migrate-first', false );
								if ( $was_migration ) {
									$sel_default = 'migrate';
								}
							} else {
								$sel_options['restore'] = esc_html__( 'Restore Previously Used', 'the7mk2' );
								$sel_default = 'restore';
							}
							?>
                            <fieldset <?php echo( $hide_tr ); ?>>
                                <label for="the7-elementor-theme-style-migrate"><?php _e( 'Would you like to generate Global Colors and Fonts based on Theme Options?', 'the7mk2' ); ?>
                                    <br>
                                    <select id="the7-elementor-theme-style-migrate"
                                            name="the7_dashboard_settings[elementor-theme-style-migrate]">
										<?php foreach ( $sel_options as $field_value => $field_name ) : ?>
                                            <option value="<?php echo esc_attr( $field_value ); ?>" <?php echo $sel_default === $field_value ? 'selected="selected"' : ''; ?>><?php echo esc_html( $field_name ); ?></option>
										<?php endforeach; ?>
                                    </select>
                                </label>
                            </fieldset>
						<?php endif; ?>
                        <fieldset>
                            <label for="elementor-zero-paragraph-last-spacing">
                                <input type="checkbox" id="elementor-zero-paragraph-last-spacing"
                                       name="the7_dashboard_settings[elementor-zero-paragraph-last-spacing]"<?php checked( The7_Admin_Dashboard_Settings::get( 'elementor-zero-paragraph-last-spacing' ) ); ?>>
								<?php esc_html_e( 'Elementor, make spacing in last paragraph = 0', 'the7mk2' ); ?>
                            </label>
                        </fieldset>
					<?php endif; ?>
                    <fieldset>
                        <label for="disable-gutenberg-styles">
                            <input type="checkbox" id="disable-gutenberg-styles"
                                   name="the7_dashboard_settings[disable-gutenberg-styles]"<?php checked( The7_Admin_Dashboard_Settings::get( 'disable-gutenberg-styles' ) ); ?>>
							<?php esc_html_e( 'Disable Gutenberg Block Editor CSS (experimental feature)', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
					<?php $critical_alerts = The7_Admin_Dashboard_Settings::get( 'critical-alerts' ); ?>
                    <fieldset>
                        <label for="the7-critical-alerts">
                            <input type="checkbox" id="the7-critical-alerts"
                                   name="the7_dashboard_settings[critical-alerts]"<?php checked( $critical_alerts ); ?>>
							<?php esc_html_e( 'Allow to send critical alerts by email', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset <?php echo( $critical_alerts ? '' : $hide_tr ); ?>>
                        <label for="the7-critical-alerts-email">
                            <input type="text" id="the7-critical-alerts-email"
                                   name="the7_dashboard_settings[critical-alerts-email]"
                                   placeholder="<?php echo esc_attr( get_site_option( 'admin_email' ) ); ?>"
                                   value="<?php echo esc_attr( The7_Admin_Dashboard_Settings::get( 'critical-alerts-email' ) ); ?>">
							<?php esc_html_e( 'An email to send alert to', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Legacy Features', 'the7mk2' ); ?></th>
                <td>
					<?php if ( the7_elementor_is_active() ) : ?>
                        <fieldset>
                            <label for="the7-legacy-deprecated-elementor-widgets">
                                <input type="checkbox" id="the7-legacy-deprecated-elementor-widgets"
                                       name="the7_dashboard_settings[deprecated_elementor_widgets]"<?php checked( The7_Admin_Dashboard_Settings::get( 'deprecated_elementor_widgets' ) ); ?>>
								<?php esc_html_e( 'Deprecated Elementor Widgets', 'the7mk2' ); ?>
                            </label>
                        </fieldset>
					<?php endif; ?>
                    <fieldset>
                        <label for="the7-deprecated_mega_menu_settings">
                            <input type="checkbox" id="the7-deprecated_mega_menu_settings"
                                   name="the7_dashboard_settings[deprecated_mega_menu_settings]"<?php checked( The7_Admin_Dashboard_Settings::get( 'deprecated_mega_menu_settings' ) ); ?>>
							<?php esc_html_e( 'Deprecated Mega-Menu Settings', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
					<?php if ( ! $elementor_theme_style ) : ?>
                        <fieldset>
                            <label for="the7-legacy-options-in-sidebar">
                                <input type="checkbox" id="the7-legacy-options-in-sidebar"
                                       name="the7_dashboard_settings[options-in-sidebar]"<?php checked( The7_Admin_Dashboard_Settings::get( 'options-in-sidebar' ) ); ?>>
								<?php esc_html_e( 'Show theme options in sidebar', 'the7mk2' ); ?>
                            </label>
                        </fieldset>
					<?php endif; ?>
                    <fieldset>
                        <label for="the7-legacy-rows">
                            <input type="checkbox" id="the7-legacy-rows"
                                   name="the7_dashboard_settings[rows]"<?php checked( The7_Admin_Dashboard_Settings::get( 'rows' ) ); ?>>
							<?php esc_html_e( 'The7 rows', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-legacy-icons-bar">
                            <input type="checkbox" id="the7-legacy-icons-bar"
                                   name="the7_dashboard_settings[admin-icons-bar]"<?php checked( The7_Admin_Dashboard_Settings::get( 'admin-icons-bar' ) ); ?>>
							<?php esc_html_e( 'Icons Bar', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-legacy-overlapping-headers">
                            <input type="checkbox" id="the7-legacy-overlapping-headers"
                                   name="the7_dashboard_settings[overlapping-headers]"<?php checked( The7_Admin_Dashboard_Settings::get( 'overlapping-headers' ) ); ?>>
							<?php esc_html_e( 'Overlapping Headers', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr <?php echo( dt_the7_core_is_enabled() ? '' : $hide_tr ); ?>>
                <th><?php esc_html_e( 'The7 Post Types and Elements', 'the7mk2' ); ?></th>
                <td>
					<?php $portfolio_setting = The7_Admin_Dashboard_Settings::get( 'portfolio' ); ?>
                    <fieldset>
                        <label for="the7-post-type-portfolio">
                            <input type="checkbox" id="the7-post-type-portfolio"
                                   name="the7_dashboard_settings[portfolio]"<?php checked( $portfolio_setting ); ?>>
							<?php esc_html_e( 'Portfolio', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset <?php echo( $portfolio_setting ? '' : $hide_tr ); ?>>
                        <label for="the7-post-type-portfolio-slug">
                            <input type="text" id="the7-post-type-portfolio-slug"
                                   name="the7_dashboard_settings[portfolio-slug]"
                                   value="<?php echo esc_attr( The7_Admin_Dashboard_Settings::get( 'portfolio-slug' ) ); ?>">
							<?php esc_html_e( 'Portfolio slug', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset <?php echo( $portfolio_setting ? '' : $hide_tr ); ?>>
                        <label for="the7-post-type-portfolio-breadcrumbs-text">
							<?php
							$portfolio_breadcrumbs_placeholder = '';
							$portfolio_post_type_object = get_post_type_object( 'dt_portfolio' );
							if ( isset( $portfolio_post_type_object->labels->singular_name ) ) {
								$portfolio_breadcrumbs_placeholder = $portfolio_post_type_object->labels->singular_name;
							}
							?>
                            <input type="text" id="the7-post-type-portfolio-breadcrumbs-text"
                                   name="the7_dashboard_settings[portfolio-breadcrumbs-text]"
                                   value="<?php echo esc_attr( The7_Admin_Dashboard_Settings::get( 'portfolio-breadcrumbs-text' ) ); ?>"
                                   placeholder="<?php echo esc_attr( $portfolio_breadcrumbs_placeholder ); ?>">
							<?php esc_html_e( 'Breadcrumbs text', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset <?php echo( $portfolio_setting ? '' : $hide_tr ); ?>>
                        <label for="the7-post-type-portfolio-layout">
                            <input type="checkbox" id="the7-post-type-portfolio-layout"
                                   name="the7_dashboard_settings[portfolio-layout]"<?php checked( The7_Admin_Dashboard_Settings::get( 'portfolio-layout' ) ); ?>>
							<?php esc_html_e( 'Project media', 'the7mk2' ); ?><?php esc_html_e( '(legacy feature)', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-post-type-testimonials">
                            <input type="checkbox" id="the7-post-type-testimonials"
                                   name="the7_dashboard_settings[testimonials]"<?php checked( The7_Admin_Dashboard_Settings::get( 'testimonials' ) ); ?>>
							<?php esc_html_e( 'Testimonials', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
					<?php $team_setting = The7_Admin_Dashboard_Settings::get( 'team' ); ?>
                    <fieldset>
                        <label for="the7-post-type-team">
                            <input type="checkbox" id="the7-post-type-team"
                                   name="the7_dashboard_settings[team]"<?php checked( $team_setting ); ?>>
							<?php esc_html_e( 'Team', 'the7mk2' ); ?></label>
                    </fieldset>
                    <fieldset <?php echo( $team_setting ? '' : $hide_tr ); ?>>
                        <label for="the7-post-type-team-slug">
                            <input type="text" id="the7-post-type-team-slug" name="the7_dashboard_settings[team-slug]"
                                   value="<?php echo esc_attr( The7_Admin_Dashboard_Settings::get( 'team-slug' ) ); ?>">
							<?php esc_html_e( 'Team slug', 'the7mk2' ); ?></label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-post-type-logos">
                            <input type="checkbox" id="the7-post-type-logos"
                                   name="the7_dashboard_settings[logos]"<?php checked( The7_Admin_Dashboard_Settings::get( 'logos' ) ); ?>>
							<?php esc_html_e( 'Partners, Clients, etc.', 'the7mk2' ); ?><?php esc_html_e( '(legacy feature)', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-post-type-benefits">
                            <input type="checkbox" id="the7-post-type-benefits"
                                   name="the7_dashboard_settings[benefits]"<?php checked( The7_Admin_Dashboard_Settings::get( 'benefits' ) ); ?>>
							<?php esc_html_e( 'Benefits', 'the7mk2' ); ?><?php esc_html_e( '(legacy feature)', 'the7mk2' ); ?>
                        </label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-post-type-albums">
                            <input type="checkbox" id="the7-post-type-albums"
                                   name="the7_dashboard_settings[albums]"<?php checked( The7_Admin_Dashboard_Settings::get( 'albums' ) ); ?>>
							<?php esc_html_e( 'Photo Albums', 'the7mk2' ); ?></label>
                    </fieldset>
					<?php $albums_setting = The7_Admin_Dashboard_Settings::get( 'albums' ); ?>
                    <fieldset <?php echo( $albums_setting ? '' : $hide_tr ); ?>>
                        <label for="the7-post-type-albums-slug">
                            <input type="text" id="the7-post-type-albums-slug"
                                   name="the7_dashboard_settings[albums-slug]"
                                   value="<?php echo esc_attr( The7_Admin_Dashboard_Settings::get( 'albums-slug' ) ); ?>">
							<?php esc_html_e( 'Photo Albums slug', 'the7mk2' ); ?></label>
                    </fieldset>
                    <fieldset>
                        <label for="the7-post-type-slideshow">
                            <input type="checkbox" id="the7-post-type-slideshow"
                                   name="the7_dashboard_settings[slideshow]"<?php checked( The7_Admin_Dashboard_Settings::get( 'slideshow' ) ); ?>>
							<?php esc_html_e( 'Slideshows', 'the7mk2' ); ?></label>
                    </fieldset>
                </td>
            </tr>
            </tbody>
        </table>
        <p>
            <button type="submit" class="button button-primary"><?php esc_html_e( 'Save', 'the7mk2' ); ?></button>
            <span class="spinner" style="float: none; margin: 4px 10px"></span>
        </p>
    </form>
</div>
