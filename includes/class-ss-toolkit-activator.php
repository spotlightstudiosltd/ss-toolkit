<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.g2techsoft.com
 * @since      1.0.0
 *
 * @package    Ss_Toolkit
 * @subpackage Ss_Toolkit/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ss_Toolkit
 * @subpackage Ss_Toolkit/includes
 * @author     G2 TechSoft <lingesh@g2techsoft.com>
 */
class Ss_Toolkit_Activator {

	public function __construct() {
		do_action( 'activate' );
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option( 'ss_login', true );
		add_option( 'ss_dashboard_widget', true);
		add_option( 'ss_shortcodes', true);
		add_option( 'ss_removal_prevent', true);
		add_option( 'ss_access_toolkit', true);
		add_option( 'ss_api', true);
	}
}
