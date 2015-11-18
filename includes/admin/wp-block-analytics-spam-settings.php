<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WP_Block_analytics_Spam_Settings
{
	/**
	 * Initialize the settings.
	 */
	public function __construct() {
		$this->helpful = new WP_Block_analytics_Spam_Helpful;

		add_action( 'admin_menu', array( $this, 'settings_menu' ), 59 );
		add_action( 'admin_init', array( $this, 'plugin_settings' ) );

		add_action( 'update_option_wp_block_analytics_spam_settings', array( $this, 'update_option' ), 10, 2 );
	}

	/**
	 * Update option
	*/
	public function update_option( $old_value, $new_value ) {
		$this->helpful->update_bot_list();
	}

	/**
	 * Add the settings page.
	*/
	public function settings_menu() {
		add_submenu_page(
			'tools.php',
			__( 'Block analytics Spam', 'wp-block-analytics-spam' ),
			__( 'Block analytics Spam', 'wp-block-analytics-spam' ),
			'manage_options',
			'page-wp-block-analytics-spam',
			array( $this, 'html_settings_page' )
		);
	}

	/**
	 * Render the settings page for this plugin
	*/
	public function html_settings_page() {
		include_once 'views/html-settings-page.php';
	}

	/**
	 * Plugin settings form fields
	 */
	public function plugin_settings() {
		global $plugin_page;

		$option = 'wp_block_analytics_spam_settings';

		// Set Custom Fields cection.
		add_settings_section(
			'general_section',
			__( 'General Options:', 'wp-block-analytics-spam' ),
			array( $this, 'section_options_callback' ),
			$option
		);

		// Frequency of updating the bot list
		add_settings_field(
			'update_frequency',
			__( 'List update frequency', 'wp-block-analytics-spam' ),
			array( $this, 'select_element_callback' ),
			$option,
			'general_section',
			array(
				'menu'  => $option,
				'id'    => 'update_frequency',
				'options' => array(
					'1'  => '1 Day',
					'7'  => '7 Days',
					'15' => '15 Days',
					'30' => '30 Days',
				),
				'optional' => FALSE
			)
		);

		// Extra domains that should be blocked
		add_settings_field(
			'extra_domains',
			__( 'Block additional domains', 'wp-block-analytics-spam' ),
			array( $this, 'textarea_element_callback' ),
			$option,
			'general_section',
			array(
				'menu'  => $option,
				'id'    => 'extra_domains',
				'class' => 'large-text',
				'rows'  => '10',
				'description' => __( 'Domains must be entered one per line and be careful in the domains you want to block.', 'wp-block-analytics-spam' ),
			)
		);

		// Register settings.
		register_setting( $option, $option, array( $this, 'validate_options' ) );
	}

	/**
	 * Section null fallback
	 */
	public function section_options_callback() {

	}

	/**
	 * Input element fallback.
	 *
	 * @return string Input field
	 */
	public function textarea_element_callback( $args ) {
		$menu    = $args['menu'];
		$id      = $args['id'];
		$class   = $args['class'];
		$rows    = $args['rows'];
		$options = get_option( $menu );

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : '';
		}

		$html = '<textarea rows="'. $rows .'" class="'. $class .'" id="' . $id . '" name="' . $menu . '[' . $id . ']">'. $current .'</textarea>';

		if ( isset( $args['description'] ) ) {
			$html .= '<p class="description">' . $args['description'] . '</p>';
		}

		echo $html;
	}

	/**
	 * Select element fallback
	 *
	 * @return string Select field
	 */
	public function select_element_callback( $args ) {
		$menu     = $args['menu'];
		$id       = $args['id'];
		$options  = get_option( $menu );
		$optional = $args['optional'];

		if ( isset( $options[ $id ] ) ) {
			$current = $options[ $id ];
		} else {
			$current = isset( $args['default'] ) ? $args['default'] : 0;
		}

		$html = '<select id="' . $id . '" name="' . $menu . '[' . $id . ']">';
			if ($optional) {
				$html .= sprintf( '<option value="%s" %s>%s</option>', '0', selected( $current, '0', false ), __( '> Not Set', 'wp-block-analytics-spam' ) );
			}
			foreach ( $args['options'] as $key => $value ) {
				$html .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $current, $key, false ), $value );
			}
		$html .= '</select>';

		if ( isset( $args['description'] ) ) {
			$html .= '<p class="description">' . $args['description'] . '</p>';
		}

		echo $html;
	}

	/**
	 * Valid options
	 *
	 * @param  array $input options to valid
	 *
	 * @return array validated options
	 */
	public function validate_options( $input ) {
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {
				if ( $key == 'extra_domains' ) {
					$output[ $key ] = esc_textarea( $input[ $key ] );
				} else {
					$output[ $key ] = sanitize_text_field( $input[ $key ] );
				}
			}
		}

		return $output;
	}
}
new WP_Block_analytics_Spam_Settings();
