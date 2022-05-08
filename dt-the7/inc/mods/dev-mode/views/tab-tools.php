<?php
/**
 * Tools tab template.
 *
 * @package The7/Dev/Templates
 */

defined( 'ABSPATH' ) || exit;
?>
<h2>Tools</h2>
<?php
$tools_message = get_transient( 'the7-dev-tools-message' );
if ( $tools_message ) {
	echo '<div class="the7-dev-tools-message the7-dashboard-notice the7-notice notice inline notice-info">' . wp_kses_post( $tools_message ) . '</div>';
	delete_transient( 'the7-dev-tools-message' );
}
?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<?php wp_nonce_field( 'the7-dev-tools' ); ?>
	<input type="hidden" name="action" value="the7_use_dev_tool">

	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row">Regenerate shortcodes CSS</th>
			<td>
				<button type="submit" class="button" name="tool" value="regenerate_shortcodes_css">Regenerate CSS</button>
			</td>
		</tr>

		<tr><th scope="row"></th><td><div><div><hr></div></div></td></tr>

		<tr>
			<th scope="row">Run DB migration</th>
			<td>
				<?php
				$migrations = array_slice( array_reverse( array_keys( \The7_Install::get_update_callbacks() ) ), 0, 10 );
				?>
				<select name="migration">
					<?php
					foreach ( $migrations as $migration ) {
						printf( '<option value="%1$s">%1$s</option>', esc_attr( $migration ) );
					}
					?>
				</select>
				<button type="submit" class="button" name="tool" value="run_migration">Migrate</button>

				<p class="description">
					<span style="color: red">Warning: Please backup your database before migrating.</span>
				</p>
			</td>
		</tr>

		<tr><th scope="row"></th><td><div><div><hr></div></div></td></tr>

		<tr>
			<th scope="row">Restore theme options</th>
			<td>
				<button type="submit" class="button" name="tool" value="delete_all_theme_options_backups">Delete all backups</button>
				<select name="theme_options_backup">
					<option value="">--none--</option>
					<?php
					$backup_records = The7_Options_Backup::get_records();
					foreach ( $backup_records as $backup ) {
						$backup_name = str_replace( 'the7-theme-options-backup-', '', $backup );
						echo '<option value="' . esc_attr( $backup ) . '">' . esc_html( $backup_name ) . '</option>';
					}
					?>
				</select>
				<button type="submit" class="button" name="tool" value="restore_theme_options_from_backup">Restore options</button>
				<p class="description">
					<span style="color: red">Warning: This will reset your Theme Options.</span>
				</p>
			</td>
		</tr>

		<tr><th scope="row"></th><td><div><div><hr></div></div></td></tr>

		<tr>
			<th scope="row">Test The7 repository donwload speed</th>
			<td>
				<button type="submit" class="button" name="tool" value="download_speed_test">Run test</button>
			</td>
		</tr>

		</tbody>
	</table>

</form>
