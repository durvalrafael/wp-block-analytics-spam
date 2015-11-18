<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WP_Block_analytics_Spam_Plugin
{
	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_wp-block-analytics-spam/wp-block-analytics-spam.php', array( &$this, 'plugin_action_links' ), 10, 5 );
	}

	/**
	 * Add the settings page.
	 */
	function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'wp-block-analytics-spam.php' ) !== false ) {
			$new_links = array(
				'<a href="http://wordpress.org/support/plugin/wp-block-analytics-spam/" target="_blank" title="'. __( 'Official Forum', 'wp-block-analytics-spam' ) .'">' . __( 'Get Help', 'wp-block-analytics-spam' ) . '</a>',
				'<a href="https://github.com/devimweb/wp-block-analytics-spam/" target="_blank" title="'. __( 'Official Repository', 'wp-block-analytics-spam' ) .'">' . __( 'Get Involved', 'wp-block-analytics-spam' ) . '</a>',
				'<a href="https://wordpress.org/support/view/plugin-reviews/wp-block-analytics-spam?rate=5#postform" target="_blank" title="'. __( 'Rate WordPress Block analytics Spam', 'wp-block-analytics-spam' ) .'">' . __( 'Rate WordPress Block analytics Spam', 'wp-block-analytics-spam' ) . '</a>'
			);
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Add the settings page.
	*/
	function plugin_action_links( $actions ) {
		$new_actions = array(
			'<a href="' . admin_url( 'tools.php?page=page-wp-block-analytics-spam' ) . '">'. __( 'Settings', 'wp-block-analytics-spam' ) .'</a>',
		);
		return array_merge( $new_actions, $actions );
	}
}
new WP_Block_analytics_Spam_Plugin();
