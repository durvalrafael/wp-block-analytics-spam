<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WP_Block_analytics_Spam_Helpful
{
	/**
	 * @var string
	 */
	protected $bot_public_list = 'https://raw.githubusercontent.com/devimweb/bot-public-list/master/bot-public-list.php';

	/**
	 *
	 */
	public function get_bot_list(){
		if ( ! ( $array_of_bots = get_transient( 'wp_block_analytics_spam_list' ) ) ) {
			$plugin_options = get_option( 'wp_block_analytics_spam_settings' );

			// Get local domains list blocked
			$array_of_private_bots = $plugin_options['extra_domains'];
			$array_of_private_bots = explode( PHP_EOL, $array_of_private_bots );

			// Get public domains list blocked
			$list_respose = wp_remote_get( $this->bot_public_list );

			$list_respose_status =  wp_remote_retrieve_response_code( $list_respose );
			$list_respose_data   = wp_remote_retrieve_body( $list_respose );

			if ( ( is_wp_error( $list_respose ) ) || ( $list_respose_data == 'Not Found' ) || ! in_array( $list_respose_status , array('200', '201') ) )
				return false;

			$array_of_bots = explode( PHP_EOL, $list_respose_data );

			// Merge both list
			$array_of_bots = array_merge( $array_of_private_bots, $array_of_bots );

			// Clear list
			$array_of_bots = array_map( 'trim', $array_of_bots );

			set_transient( 'wp_block_analytics_spam_list', $array_of_bots, $plugin_options['update_frequency'] * DAY_IN_SECONDS );
		}

		return $array_of_bots;
	}

	/**
	 *
	 */
	public function update_bot_list(){
		delete_transient( 'wp_block_analytics_spam_list' );
		$this->get_bot_list();
	}
}
new WP_Block_analytics_Spam_Helpful();
