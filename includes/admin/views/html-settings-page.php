<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div class="updated">
		<p><?php printf( __( 'Help us keep the %s plugin free making a rate %s on %s. Thank you in advance!', 'wp-block-analytics-spam' ), '<strong>' . __( 'WordPress Block analytics Spam', 'wp-block-analytics-spam' ) . '</strong>', '<a href="https://wordpress.org/support/view/plugin-reviews/wp-block-analytics-spam?rate=5#postform" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/wp-block-analytics-spam?rate=5#postform" target="_blank">' . __( 'WordPress.org', 'wp-block-analytics-spam' ) . '</a>' ); ?></p>
	</div>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php
			settings_fields( 'wp_block_analytics_spam_settings' );
			do_settings_sections( 'wp_block_analytics_spam_settings' );

			submit_button();
		?>

	</form>

</div>
