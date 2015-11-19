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
	protected $list_url = 'https://api.github.com/repos/devimweb/bot-public-list/contents/bot-public-list.php?client_id=%s&client_secret=%s';

	/**
	 * @var string
	 */
	protected $client_id = '0691a3989b54d3536d90';

	/**
	 * @var string
	 */
	protected $client_secret = '563b6c4d8af8f594f74e8b5dd877d0cda5d6a6e3';

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
			$url = sprintf( $this->list_url, $this->client_id, $this->client_secret );
			$args = array(
			    'headers'     => array(
			    	'Accept' => 'application/vnd.github.VERSION.raw'
			    ),
			);
			$list_respose = wp_remote_get( $url, $args );

			$list_respose_status = wp_remote_retrieve_response_code( $list_respose );
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
