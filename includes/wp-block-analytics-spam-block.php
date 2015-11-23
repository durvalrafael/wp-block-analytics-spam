<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WP_Block_analytics_Spam_Block
{
	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		$this->helpful = new WP_Block_analytics_Spam_Helpful;

		add_action( 'parse_request', array( &$this, 'block_spam_bot' ) );
	}

	/**
	 * Add the settings page.
	 */
	function block_spam_bot() {
		if( isset( $_SERVER['HTTP_REFERER'] ) ){
			// HOST REFERENCE
			$reference = parse_url( $_SERVER['HTTP_REFERER'] );
			$reference = array_map( 'trim', $reference );
			$reference = $reference['host'];

			$array_of_bots = $this->helpful->get_bot_list();

			if ( is_array( $array_of_bots ) ) {
				foreach( $array_of_bots as $bot ){
					if ( strpos( $reference, $bot ) !== false ){
						wp_die( 'This is no place for bot', 'This is no place for bot', array( 'response'=> 403 ) );
						exit;
					}
				}
			}
		}
	}
}
new WP_Block_analytics_Spam_Block();
