<?php

/**
 * Fired during plugin activation
 *
 * @link       https://spotlightstudios.co.uk/
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
 * @author     Spotlight <info@spotlightstudios.co.uk>
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
		add_option( 'ss_login', false );
		add_option( 'ss_dashboard_widget', false);
		add_option( 'ss_shortcodes', false);
		add_option( 'ss_removal_prevent', false);
		add_option( 'ss_access_toolkit', false);
		add_option( 'ss_api', '');
	}
}
