<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var The7_Demo $demo
 */
?>

<div class="dt-dummy-controls-block">
	<hr>
	<p><strong><?php echo esc_html_x( 'Want to import a particular page(s)?', 'admin', 'the7mk2' ); ?></strong></p>
</div>
<form action="<?php echo esc_url( add_query_arg( 'step', '2', the7_demo_content()->admin_url() ) ); ?>" method="post">
	<input type="hidden" name="import_type" value="post_import">
	<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo->id ); ?>">

	<div class="dt-dummy-controls-block dt-dummy-control-buttons dt-dummy-controls-block-import-one-page">
		<div class="dt-dummy-button-wrap">
			<button class="button button-primary dt-dummy-button-import-one-page"><?php echo esc_html_x( 'Select page(s) to import', 'admin', 'the7mk2' ); ?></button>
			<span class="spinner"></span>
		</div>
	</div>
</form>
