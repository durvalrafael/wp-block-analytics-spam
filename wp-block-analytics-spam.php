<?php
/*
 * Plugin Name: WordPress Block Analytics Spam
 * Plugin URI: https://github.com/devimweb/wp-block-analytics-spam
 * Description: Stop the Spam bot as semalt.com and buttons-for-website.com that appear on Google Analytics and have real reports.
 * Version: 1.0.0
 * Author: Devim - Agência Web
 * Author URI: http://www.devim.com.br/
 * License: GPLv3 License
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-block-analytics-spam
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__, array( 'WP_Block_analytics_Spam', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Block_analytics_Spam', 'plugin_deactivate' ) );

if ( ! class_exists( 'WP_Block_analytics_Spam' ) ) :

/**
 * Plugin Main Class
 */
class WP_Block_analytics_Spam
{
	/**
	 * @var object
	 */
	private static $instance = null;

	/**
	 * @return object A single instance of this class
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin public actions
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		$this->includes();

		if ( is_admin() ) {
			$this->admin_includes();
		}
	}

	/**
	 * Run when the plugin is activated
	 */
	static function plugin_activate() {
		$activate_options = array(
			'update_frequency'   => '1',
			'extra_domains'      => '',
		);

		add_option( 'wp_block_analytics_spam_settings', $activate_options );

	}

	/**
	 * Run when the plugin is deactivated
	 */
	static function plugin_deactivate() {
		delete_option( 'wp_block_analytics_spam_settings' );
		delete_transient( 'wp_block_analytics_spam_list' );
	}

	/**
	 * Load the plugin text domain for translation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-block-analytics-spam', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Includes
	 */
	private function includes() {
		include_once 'includes/wp-block-analytics-spam-helpful.php';
		include_once 'includes/wp-block-analytics-spam-block.php';
	}

	/**
	 * Admin includes
	 */
	private function admin_includes() {
		include_once 'includes/admin/wp-block-analytics-spam-plugin.php';
		include_once 'includes/admin/wp-block-analytics-spam-settings.php';
	}
}
add_action( 'plugins_loaded', array( 'WP_Block_analytics_Spam', 'get_instance' ), 0 );

endif;
