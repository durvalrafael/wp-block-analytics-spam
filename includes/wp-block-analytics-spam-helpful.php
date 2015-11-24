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
	protected $list_url = 'https://api.github.com/repos/devimweb/bot-public-list/contents/bot-public-list.txt';

	/**
	 *
	 */
	public function get_bot_list(){
		// Check update is necessary
		$array_remote_bots = $this->retrieve_remote_bots();

		// Get public domains list blocked
		$array_local_bots = $this->retrieve_local_bots();

		// Returns the list
		if ( is_array($array_remote_bots) && is_array($array_local_bots) ){
			return array_merge( $array_local_bots, $array_remote_bots );

		} elseif ( ! is_array($array_remote_bots) && is_array($array_local_bots) ) {
			return $array_local_bots;

		} elseif ( is_array($array_remote_bots) && ! is_array($array_local_bots) ) {
			return $array_remote_bots;

		} else {
			return false;
		}
	}


	/**
	 *
	 */
	public function retrieve_remote_bots(){
		// Checks if is necessary update the local version
		if ( ! get_transient( 'wp_block_analytics_spam_check_update' ) ) {
			$plugin_options = get_option( 'wp_block_analytics_spam_settings' );

			// Formulates the request
			$args = array(
			    'headers'     => array(
			    	'Accept' => 'application/vnd.github.VERSION.raw'
			    ),
			);
			$list_respose = wp_remote_get( $this->list_url, $args );

			// Responses of Request
			$list_respose_status = wp_remote_retrieve_response_code( $list_respose );
			$list_respose_data   = wp_remote_retrieve_body( $list_respose );

			// Checks for any errors
			if ( ( is_wp_error( $list_respose ) ) || ( $list_respose_data == 'Not Found' ) || ! in_array( $list_respose_status , array('200', '201') ) )
					return false;

			// Working up response
			$array_remote_bots = explode( PHP_EOL, $list_respose_data );
			$array_remote_bots = array_map( 'trim', $array_remote_bots );

			// Sets the next refresh cycle
			set_transient( 'wp_block_analytics_spam_check_update', 'yes', $plugin_options['update_frequency'] * DAY_IN_SECONDS );

			// Update local version of domains blocked
			update_option( 'wp_block_analytics_spam_remote_domains', $array_remote_bots );

			return $array_remote_bots;
		} else {
			return get_option( 'wp_block_analytics_spam_remote_domains' );
		}
	}


	/**
	 *
	 */
	public function retrieve_local_bots(){
		$plugin_options = get_option( 'wp_block_analytics_spam_settings' );

		$array_local_bots = $plugin_options['extra_domains'];
		if ( empty( $array_local_bots ) )
			return false;

		return explode( PHP_EOL, $array_local_bots );
	}


	/**
	 *
	 */
	public function update_bot_list(){
		delete_transient( 'wp_block_analytics_spam_check_update' );
		$this->get_bot_list();
	}
}
new WP_Block_analytics_Spam_Helpful();
