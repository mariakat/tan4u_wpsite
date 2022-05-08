<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var The7_Demo $demo
 */
?>

<?php if ( $demo->id === 'main' ) : ?>

	<div class="dt-dummy-controls-block dt-dummy-info-content">
		<?php
		echo wp_kses_post(
			_x(
				'<p><strong>Important!</strong> This demo is huge. Many servers will struggle importing it.<br><strong>Please make a full site backup</strong> before proceeding with the import. In case of emergency, you may have to restore your database (or the whole website) from it.</p>',
				'admin',
				'the7mk2'
			)
		);
		?>
	</div>

<?php endif; ?>

<?php if ( ! $demo->include_attachments ) : ?>

	<div class="dt-dummy-controls-block dt-dummy-info-content">
		<p><strong>
		<?php
			echo esc_html_x(
				'Please note that all copyrighted images were replaced with a placeholder pictures.',
				'admin',
				'the7mk2'
			);
		?>
		</strong></p>
	</div>

<?php endif; ?>
