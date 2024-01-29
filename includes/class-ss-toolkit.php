<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://spotlightstudios.co.uk/
 * @since      2.0.0
 *
 * @package    Ss_Toolkit
 * @subpackage Ss_Toolkit/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Ss_Toolkit
 * @subpackage Ss_Toolkit/includes
 * @author     Spotlight <info@spotlightstudios.co.uk>
 */
class Ss_Toolkit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Ss_Toolkit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'SS_TOOLKIT_VERSION' ) ) {
			$this->version = SS_TOOLKIT_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'ss-toolkit';

		$this->plugin_folder = basename( plugin_dir_path( dirname( __FILE__ )) ) .'/ss-toolkit.php';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		add_action('admin_enqueue_scripts', array($this,'ss_toolkit_enqueueAdmin'));

		// Hook the function to the admin_menu action to add the submenu page
		add_action('admin_menu', array($this,'ss_toolkit_add_submenu_page'));

		// Hook to check Robots.txt file is present in the site
		add_action('admin_init', array($this,'check_robots_txt'),999);

		//Hook function to Show the Spotlight Dashboard Widget
		if(get_option('ss_dashboard_widget') == 1){
			add_action('wp_dashboard_setup', array($this,'ss_toolkit_add_dashboard_widgets'));
		}

		//Hook functions to call Ajax 
		add_action('wp_ajax_ss_toolkit_ajax_request', array($this,'ss_toolkit_ajax_request'));
        add_action('wp_ajax_nopriv_ss_toolkit_ajax_request',array($this,'ss_toolkit_ajax_request') );

		//Hook function to add Google Analytics tag to Header
		add_action('wp_head', array($this,'add_googleanalytics_header'));
		
		//Hook functions to remove deactivation permission for plugins
		if(get_option('ss_removal_prevent') == 1){

			//Hook to remove plugin actions links
			add_filter('plugin_action_links', array($this,'hide_plugin_deactivation'), 10, 4);
			//Hook to remove Bulk actions links
			add_filter('bulk_actions-plugins', array($this,'remove_bulk_actions_for_plugins'), 10, 4);
		}

		//Hook function to custom login page
		if(get_option('ss_login') == 1){
			add_action( 'login_enqueue_scripts',array($this,'ss_custom_login_scripts') );
			add_action( 'login_init', array($this,'custom_login_page_template'), 10,1);
		}

		//Hook to shortcodes function
		if(get_option('ss_shortcodes') == 1){
			add_action('init', array($this,'ss_plugin_shortcodes'));
		}

		//Hook To custom Admin Footer Text
		add_filter('admin_footer_text', array($this,'custom_admin_footer_text'));

		if(get_option('ss_default_email_settings') == 1){
			//Hook to change wp from mail id
			add_filter( 'wp_mail_from', array($this,'custom_wp_mail_from'));
		}
		//Hook to add custom header content
		add_action('wp_head', array($this,'custom_header_content'));

		//Hook to add custom footer content
		add_action('wp_footer', array($this,'custom_footer_content'));

		if(get_option('ss_duplicate_post_page') == 1){
			//Hook to clone button for Posts and Pages
			add_filter( 'post_row_actions', array($this,'clone_custom_post_link'), 10, 2 ); // For Posts and CPTs
			add_filter( 'page_row_actions', array($this,'clone_custom_post_link'), 10, 2 ); // For Pages

			//Hook to clone Posts and Pages content
			add_action('admin_action_clone_custom_post', array($this,'clone_custom_post'));
		}

		//Hook to change yoo_theme name
		add_action('admin_head', array($this,'spotlight_builder'));

		add_filter('gettext',  array($this,'spot_translate'));
		add_filter('ngettext', array($this,'spot_translate'));

		add_action('customize_controls_print_styles', array($this,'ss_customizer_styles'), 999 );

		//Hook to change Google map API key
		if(get_option('ss_google_map_api') != ""){
			add_filter('acf/fields/google_map/api', array($this,'my_acf_google_map_api'));
		}

		//Hook to custom function
		if(get_option('ss_custom_functions') == 1){
			add_action('init', array($this,'include_custom_functions'));
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ss_Toolkit_Loader. Orchestrates the hooks of the plugin.
	 * - Ss_Toolkit_i18n. Defines internationalization functionality.
	 * - Ss_Toolkit_Admin. Defines all hooks for the admin area.
	 * - Ss_Toolkit_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ss-toolkit-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ss-toolkit-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ss-toolkit-admin.php';

		$this->loader = new Ss_Toolkit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ss_Toolkit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ss_Toolkit_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ss_Toolkit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

	}

	function ss_toolkit_enqueueAdmin() {
	
		wp_enqueue_script( $this->get_plugin_name(), plugin_dir_url( dirname( __FILE__ ) ) . '/admin/js/ss-toolkit-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script('ss-toolkit', 'ss_toolkit_ajax_url',array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ));
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Ss_Toolkit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
		
	/**
	 * Creating Menu for the plugin
	 * 
	 */
	function ss_toolkit_add_submenu_page() {
		add_submenu_page(
			'tools.php',        // Parent slug (the "Tools" menu slug)
			'SS Toolkit 2.0',     // Page title
			'SS Toolkit 2.0',     // Menu title
			'manage_options',   // Capability required to access the page
			'ss-toolkit',     // Menu slug (should be unique)
			array($this,'ss_toolkit_admin_page') // Callback function to display the page content
		);
	}
	
	/**
	 * Function to Plugin Admin page
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_toolkit_admin_page() {
		// Page content goes here (you can put your HTML and PHP code for the custom tools)
		echo '<h1>SS Toolkit 2.0</h1>';
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'tools';
		$current_user = wp_get_current_user();
		$user_id = $current_user->user_login;
		if((strtolower($current_user->user_login) != 'spotlight' && get_option('ss_access_toolkit') == 0) || strtolower($current_user->user_login) == 'spotlight' && get_option('ss_access_toolkit') == 0 ||
		    strtolower($current_user->user_login) == 'spotlight' && get_option('ss_access_toolkit') == 1){
		?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2 class="nav-tab-wrapper">
						<a href="?page=ss-toolkit&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
						<a href="?page=ss-toolkit&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
						<span class="ss_toolkit_message"></span>
					</h2>
					
					<?php if($active_tab == 'tools'){?>
						<form id="ss_toolkit_tools_form" action="">
							<input type="hidden" name="from_toolkit_form"  id="from_toolkit_form" value="tools_form"> 
							<div id="tab1 tools" style="display:block;overflow:scroll" class="tab1">
								<table class="widefat" border="0">
									<tr>
										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>SpotLight Login</h3>
													<p>Enables the Spotlight Studios Login Screen</p>

													<div class="ss-toolkit-card-bottom">
														<a href="#" class="page-title-action popup" id="ss-login-setting-popup-btn">Settings</a>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_login') == 1)?'checked ':""; ?> name="ss_login" id="ss_login" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div id="ss-login-setting-popup" class="ss-popup">
												<div class="ss-popup-content"  style="overflow:hidden"> 
													<!-- Your content goes here -->
													<div class="ss-pop-header">
														<h3>SS Login Page Settings</h3>
														<a id="ss-login-setting-close-btn" class="ss-close-btn">X</a>
													</div>
													<div id="ss-toolkit-tab2 settings" class="ss-toolkit-tab2" style="border:none">
														<span class="ss_toolkit_message"></span>
														<div class="container">
															<div class="row">
																<div class="col-md-12 ss-toolkit-card2">
																	<div><b>RSS Feeds : </b></div>
																		<div class="checkboxes">
																			<p></p>
																			<input type="checkbox" <?php echo (get_option('ss_rss_feed_link') == 1)?'checked ':""; ?> name="ss_rss_feed_link" id="ss_rss_feed_link" > <label>Featured news</label> 
																			<input type="checkbox" <?php echo (get_option('ss_rss_feed_link_promotion') == 1)?'checked ':""; ?> name="ss_rss_feed_link_promotion" id="ss_rss_feed_link_promotion"> <label>Promotions</label>
																		</div>
																	<p></p>
																	<label for="ss-rss-feed-link"><b>Background Image URL : </b></label>
																	<p></p>
																	<textarea type="text" name="ss-backgroud-image" id="ss-backgroud-image" placeholder="Background Image URL"><?php echo (get_option('ss_background_image') != null)?get_option('ss_background_image'):""; ?></textarea>
																</div>
															</div>
														</div>
														<div class="save-btn-div">
															<a href="#" class="ss-save-btn page-title-action popup" id="save-btn">Save</a>
														</div>
													</div>
												</div>
											<div>
										</td>

										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>Dashboard Widget</h3>
													<p>Dispalys a Spotlight studios widget with useful links and removes useless widgets</p>
													
													<div class="ss-toolkit-card-bottom">
														<div></div>
														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_dashboard_widget') == 1)?'checked':''; ?> name="ss_dashboardwidget" id="ss_dashboardwidget" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
										</td>

										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>SS Shortcodes</h3>
													<p>Enables common, useful shortcuts</p>

													<div class="ss-toolkit-card-bottom">
														<a href="#" class="page-title-action popup" id="ss-shortcode-popup-btn">View</a>
														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_shortcodes') == 1)?'checked':''; ?> name="ss_shortcode" id="ss_shortcode" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div id="ss-shortcode-popup" class="ss-popup">
  												<div class="ss-popup-content">
													<!-- Your content goes here -->
													<div class="ss-pop-header">
														<h3>SS ToolKit ShortCode's</h3>
														<a id="ss-shortcode-close-btn" class="ss-close-btn">X</a>
													</div>
													<table class="widefat" border="1">
														<thead>
															<tr>
																<th>Shortcode</th>   
																<th>Description</th>  
																<th>Variables</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><p>[5_star]</p></td>
																<td><p>Displays a number of stars out of 5</p></td>
																<td><p>colour, icon, number</p></td>
															</tr>
															<tr>
																<td><p>[ss_footer]</p></td>
																<td><p>Spotlight Footer Text</p></td>
																<td><p>company(site title), prefix (default: Powered by), name(of designer), link, developer(if displayed), developer_link, line_end</p></td>
															</tr>
															<tr>
																<td><p>[ss_logout]</p></td>
																<td><p>Logout button</p></td>
																<td><p>No variable</p></td>
															</tr>
															<tr>
																<td><p>[ss_lorum]</p></td>
																<td><p>Lorum ipsum generator</p></td>
																<td><p>p (paragraph), l (lines)</p></td>
															</tr>
															<tr>
																<td><p>[ss_placeholder]</p></td>
																<td>Places a placeholder image</td>
																<td><p>width, height, bg(999), text_colour(555), text, ext</p></td>
															</tr>
															<tr>
																<td><p>[ss_placekitten]</p></td>
																<td><p>Places a stock image of kittens </p></td>
																<td><p>width, height</p></td>
															</tr>
															<tr>
																<td><p>[ss_progressbar]</p></td>
																<td><p>Shows a progress bar</p></td>
																<td><p>class(success), percent, display</p></td>
															</tr>
															<tr>
																<td><p>[ss_sitemap]</p></td>
																<td><p>Creates a html site-map</p></td>
																<td><p>list_class, box_class</p></td>
															</tr>
															<tr>
																<td><p>[ss-icon]</p></td>
																<td><p>Lord Icon Licence</p></td>
																<td><p>id, width, trigger, delay, stroke, primary, secondary</p></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>SpotLight Header/Footer</h3>
													<p>Enables custom code for the header/footer for the website.</p>

													<div class="ss-toolkit-card-bottom">
														<a href="#" class="page-title-action popup" id="ss-custom-header-popup-btn">Content</a>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_head_foot_content') == 1)?'checked ':""; ?> name="ss_head_foot_content" id="ss_head_foot_content" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div id="ss-custom-header-popup" class="ss-popup">
												<div class="ss-popup-content"  style="overflow:auto"> 
													<!-- Your content goes here -->
													<div class="ss-pop-header">
														<h3>SS Header/Footer Contents</h3>
														<a id="ss-custom-header-close-btn" class="ss-close-btn">X</a>
													</div>
													<div id="ss-toolkit-tab2 content-settings" class="ss-toolkit-tab2" style="border:none">
														<span class="ss_toolkit_message"></span>
														<div class="container">
															<div class="row">
																<div class="col-md-12 ss-toolkit-card2">
																	<p></p>
																	<label for="ss-rss-feed-link"><b>Header Content: </b></label>
																	<p></p>
																	<textarea rows="6" name="ss-header-content" id="ss-header-content" placeholder="Header Content"><?php echo (get_option('ss_header_content') != null)?get_option('ss_header_content'):""; ?></textarea>
																	<p></p>
																	<label for="ss-rss-feed-link"><b>Footer Content: </b></label>
																	<p></p>
																	<textarea rows="6" name="ss-footer-content" id="ss-footer-content" placeholder="Footer Content"><?php echo (get_option('ss_footer_content') != null)?get_option('ss_footer_content'):""; ?></textarea>
																</div>
															</div>
														</div>
														<div class="save-btn-div">
															<a href="#" class="ss-content-save-btn ss-btn page-title-action popup" id="content-save-btn">Save</a>
														</div>
													</div>
												</div>
											<div>
										</td>

										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>SpotLight Custom Functions</h3>
													<p>Enables custom code to a specific website without editing functions.php.</p>

													<div class="ss-toolkit-card-bottom">
														<a href="#" class="page-title-action popup" id="ss-custom-function-btn">Functions</a>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_custom_functions') == 1)?'checked ':""; ?> name="ss_custom_functions" id="ss_custom_functions" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div id="ss-custom-function-popup" class="ss-popup">
												<div class="ss-popup-content"  style="overflow:scroll"> 
													<!-- Your content goes here -->
													<div class="ss-pop-header">
														<h3>Custom Functions</h3>
														<a id="ss-custom-function-close-btn" class="ss-close-btn">X</a>
													</div>
													<div id="ss-toolkit-tab2 settings" class="ss-toolkit-tab2" style="border:none">
														<span class="ss_toolkit_message"></span>
														<div class="container">
															<div class="row">
																<div class="col-md-12 ss-toolkit-card2">
																	<div id="textarea-wrapper">
																		<div class="textarea-group">
																			<?php
																				$textareaDetails = get_option('ss_custom_functions_value');
																				if (!empty($textareaDetails)) {
																					$outputArray = array();
																					foreach ($textareaDetails as $innerArray) {
																						if (isset($innerArray['is_checked'])) {
																							foreach ($innerArray['is_checked'] as $index => $isChecked) {
																								$outputArray[$index + 1] = array(
																									'is_checked' => $isChecked,
																									'function_data' => isset($innerArray['function_data'][$index]) ? $innerArray['function_data'][$index] : null
																								);
																							}
																						}
																					}

																					foreach ($outputArray as $key => $textarea) {
																						
																						$checked = '';
																						if($textarea['is_checked']){
																							$checked = 'checked';
																						}

																						echo '<label for="custom_function_' .$key. '"><b>Custom Function #'.$key.'</b></label>';
																						echo '<label class="custom-function-switch">';
																						echo '<input type="checkbox" id="custom_function_switch_'.$key.'" class="custom-function-switch" name="custom_function_switch[]" '.$checked.'>';
																						echo '<span class="slider"></span>';
																						echo '</label>';
																						echo '<textarea id="custom_function_'.$key. '" name="custom_functions[]" data-id="'.$key.'" cols="30" rows="6" placeholder="Custom Fucntions #1">' . esc_textarea($textarea['function_data']) . '</textarea>';
																						echo '<p></p>';
																					}
																				} else {
																					echo '<label for="custom_function_1"><b>Custom Function #1</b></label>';
																					echo '<label class="custom-function-switch">';
																					echo '<input type="checkbox" id="custom_function_switch_1" class="custom-function-switch" name="custom_function_switch[]" checked>';
																					echo '<span class="slider"></span>';
																					echo '</label>';
																					echo '<textarea id="custom_function_1" name="custom_functions[]" cols="30" rows="6" data-id="1" placeholder="Custom Fucntions #1"></textarea>';																		
																				}
																			?>
																		</div>
																	</div>
																	<button id="add-textarea">+</button>
																</div>
															</div>
														</div>
														<div class="save-btn-div">
															<a href="#" class="ss-custom-function-btn ss-btn page-title-action popup" id="save-btn">Save</a>
														</div>
													</div>
												</div>
											<div>
										</td>
										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>Duplicate Page/Post</h3>
													<p>Enables options for duplicating Page/Post</p>

													<div class="ss-toolkit-card-bottom">
														<div></div>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_duplicate_post_page') == 1)?'checked ':""; ?> name="ss_duplicate_post_page" id="ss_duplicate_post_page" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>Default Mail Change</h3>
													<p>Change default "From" email address</p>

													<div class="ss-toolkit-card-bottom">
														<a href="#" class="page-title-action popup" id="ss-default-mail-btn">Mail</a>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_default_email_settings') == 1)?'checked ':""; ?> name="ss_default_email_settings" id="ss_default_email_settings" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
											<div id="ss-default-mail-popup" class="ss-popup">
												<div class="ss-popup-content"  style="overflow:auto"> 
													<!-- Your content goes here -->
													<div class="ss-pop-header">
														<h3>SS Header/Footer Contents</h3>
														<a id="ss-default-mail-close-btn" class="ss-close-btn">X</a>
													</div>
													<div id="ss-toolkit-tab2 content-settings" class="ss-toolkit-tab2" style="border:none">
														<span class="ss_toolkit_message"></span>
														<div class="container">
															<div class="row">
																<div class="col-md-12 ss-toolkit-card2">
																	<p></p>
																	<label for="ss-rss-feed-link"><b>Mail : </b></label>
																	<p></p>
																	<input type="text" name="ss_default_mail" id="ss_default_mail" value="<?php echo (get_option('ss_default_email_value') != null)? get_option('ss_default_email_value') :""; ?>">
																</div>
															</div>
														</div>
														<div class="save-btn-div">
															<a href="#" class="ss-email-save-btn ss-btn page-title-action popup" id="email-save-btn">Save</a>
														</div>
													</div>
												</div>
											<div>
										</td>
									</tr>
								</table>
							</div>
						</form>
					<?php } ?>
					<?php if($active_tab == 'settings'){?>
						<form action="" id="ss_toolkit_settings_form">
							<input type="hidden" name="from_toolkit_form" id="from_toolkit_form" value="settings_form"> 
							<div id="ss-toolkit-tab2 settings" class="ss-toolkit-tab2">
								<div class="container">
									<div class="row">
										<div class="col-md-12 ss-toolkit-card2">
											<h3>General</h3>
											<input type="checkbox" name="ss_removal_prevent" id="sstoolkit-removal" <?php echo (get_option('ss_removal_prevent') == 1)? 'checked':''; ?> class="ss-form-input"> Prevent deactivation/removal of SS Toolkit </br>
											<input type="checkbox" name="ss_access_toolkit" id="spotlight-access"  <?php echo (get_option('ss_access_toolkit') == 1)? 'checked':""; ?> class="ss-form-input"> Prevent access if user is not "Spotlight"
										</div>
										<div class="col-md-12 ss-toolkit-card2">
											<h3>API Keys</h3>
											<p><span>GA 4:</span><input type="text" name="ss_api_key" id="ss_api_key" class="ss-form-input" value="<?php echo (get_option('ss_api') != null)? get_option('ss_api') :""; ?>"></p>

											<h3>Google Map API</h3>
											<p><span>API :</span><input type="text" name="ss_map_api_key" id="ss_map_api_key" class="ss-form-input" value="<?php echo (get_option('ss_google_map_api') != null)? get_option('ss_google_map_api') :""; ?>"></p>
										</div>
									</div>
								</div>
							</div>
						</form>
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php }else{
			$html = '<h2 style="padding: 10px;text-align: center;">Access to the plugin page is not granted. Please reach out to the administrator for authorization.</h2>';
			echo $html;
			exit;
		}

	}

	/**
	 * Function to add Wordpress Dashboard Widget
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_toolkit_add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'ss_toolkit_dashboard_widget_id',
			'Spotlight Studiios | Support Details',
			array($this,'ss_toolkit_dashboard_widget'),
			'high'
		);
	}

	/**
	 * Function to create Wordpress Dashboard Widget
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_toolkit_dashboard_widget() {
		?>
		<div class="main">
			<ul>
				<li><span class='dashicons dashicons-admin-site'></span> <strong>Website:</strong> <a href='https://spotlightstudios.co.uk' target='_blank'>spotlightstudios.co.uk</a></li>
				<li><span class='dashicons dashicons-businessman'></span> <strong>Client Portal:</strong> <a href='https://portal.spotlightstudios.co.uk/' target='_blank'>Log in</a></li>
				<li><span class='dashicons dashicons-book-alt'></span> <strong>Spotlight Knowledgebase:</strong> <a href='http://projects.spotlightstudios.co.uk/' target='_blank'>Browse</a></li>  
				<li><span class='dashicons dashicons-email-alt'></span> <strong>Contact:</strong> <a href='mailto:support@spotlightstudios.co.uk'>support@spotlightstudios.co.uk</a></li>
			</ul>
		</div>
		<?php
	}
	
	/**
	 * Function to AJAX request
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_toolkit_ajax_request() { 

		$message = '';
		if($_POST['from_toolkit_form'] == 'tools_form'){
			
			if(get_option('ss_login') != $_POST['ss_login']){
				update_option('ss_login',$_POST['ss_login']);
				$message = "Login Page settings updated";
			}

			if(get_option('ss_dashboard_widget') != $_POST['ss_dashboardwidget']){
				update_option('ss_dashboard_widget',$_POST['ss_dashboardwidget']);
				$message = "Dashboard Widgets settings updated";
			}

			if(get_option('ss_shortcodes') != $_POST['ss_shortcode']){
				update_option('ss_shortcodes',$_POST['ss_shortcode']);
				$message = "Shortcode settings updated";
			}

			if(get_option('ss_rss_feed_link') != $_POST['ss_rss_feed_link']){

				update_option('ss_rss_feed_link',$_POST['ss_rss_feed_link']);
				$message = "RSS News Feed option updated";
			}

			if(get_option('ss_rss_feed_link_promotion') != $_POST['ss_rss_feed_link_promotion']){

				update_option('ss_rss_feed_link_promotion',$_POST['ss_rss_feed_link_promotion']);
				$message = "RSS Promotion Feed option updated";
			}

			if(get_option('ss_background_image') != $_POST['ss_background_image']){
				update_option('ss_background_image',$_POST['ss_background_image']);
				$message = "Login Custom background image updated";
			}

			if(get_option('ss_head_foot_content') != $_POST['ss_head_foot_content']){

				update_option('ss_head_foot_content',$_POST['ss_head_foot_content']);
				$message = "Custom Header/Footer option updated";
			}

			if(get_option('ss_custom_functions') != $_POST['ss_custom_functions']){
				update_option('ss_custom_functions',$_POST['ss_custom_functions']);
				$message = "Custom function settings updated";
			}

			if(get_option('ss_custom_functions_value') != $_POST['ss_custom_functions_value']){

				$textarea_id = sanitize_text_field($_POST['ss_custom_function_id']);
				$is_checked = $_POST['ss_custom_function_switch_value'];
				$textarea_data = $_POST['ss_custom_functions_value'];
				
				// Get existing options from wp_options
				$existing_options = get_option('ss_custom_functions_value', array());
				
				// Update or add the new option
				$existing_options[$textarea_id] = array(
					'is_checked' => $is_checked,
					'function_data' => wp_unslash($textarea_data),
				);

				update_option('ss_custom_functions_value',$existing_options);

				$functions = get_option('ss_custom_functions_value');
				$functionDataArray = array();

				// Check if 'function_data' key exists in the original array
				if (isset($functions['']['is_checked']) && is_array($functions['']['is_checked']) && isset($functions['']['function_data']) && is_array($functions['']['function_data'])) {
					foreach ($functions['']['is_checked'] as $index => $isChecked) {
						// Check if 'is_checked' is 1 for the current index
						if ($isChecked == 1) {
							// Add the corresponding 'function_data' to the new array
							$functionDataArray[] = $functions['']['function_data'][$index];
						}
					}
				}

				// Specify the file path
				$file_path =  plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom_functions.php';

				$content = "<?php\n\n";

				foreach ($functionDataArray as $function) {
					$content .= $function . "\n\n";
				}

				$content .= "?>";
			
				// Write content to the file
				$result = file_put_contents($file_path, $content);
			
				if ($result !== false) {
					// Success message
					$message =  'Content successfully written to the file.';
				} else {
					// Error message
					$message =  'Error writing content to the file.';
				}
			
				// $message = "Custom function saved successfully";
			}

			if(get_option('ss_header_content') != $_POST['ss_header_content']){

				update_option('ss_header_content',wp_unslash(trim($_POST['ss_header_content'])));
				$message = "Custom header content updated";
			}

			if(get_option('ss_footer_content') != $_POST['ss_footer_content']){

				update_option('ss_footer_content',wp_unslash(trim($_POST['ss_footer_content'])));
				$message = "Custom footer content updated";
			}

			if(get_option('ss_default_email_settings') != $_POST['ss_default_email_settings']){

				update_option('ss_default_email_settings', $_POST['ss_default_email_settings']);
				$message = "Default mail options updated";
			}

			if(get_option('ss_default_email_value') != $_POST['ss_default_mail']){

				update_option('ss_default_email_value', $_POST['ss_default_mail']);
				$message = "Default Email Id Updated";
			}

			if(get_option('ss_duplicate_post_page') != $_POST['ss_duplicate_post_page']){

				update_option('ss_duplicate_post_page', $_POST['ss_duplicate_post_page']);
				$message = "Duplicate Post/Page options updated";
			}

		}

		if($_POST['from_toolkit_form'] == 'settings_form'){
			if(get_option('ss_removal_prevent') != $_POST['sstoolkit_removal']){
				update_option('ss_removal_prevent',$_POST['sstoolkit_removal']);
				$message = "Plugin Deactivation prevention settings updated";
			}

			if(get_option('ss_access_toolkit') != $_POST['spotlight_access']){
				update_option('ss_access_toolkit',$_POST['spotlight_access']);
				$message = "Spotlight user plugin access settings updated";
			}

			if(get_option('ss_api') != $_POST['ss_api_key']){
				update_option('ss_api',$_POST['ss_api_key']);
				$message = "Google Analytics API key updated";
			}
		}

		$return = array(
			'message' => __( $message, 'SSToolkit' ),
			'status'      => true
		);
		wp_send_json_success( $return );       
	}

	/**
	 * Function to add Google Analytics tag
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function add_googleanalytics_header(){ 
		
		$ga_id = (get_option('ss_api')) ? get_option('ss_api') : '';

		$ga_url = 'https://www.googletagmanager.com/gtag/js?id='.$ga_id;
		?>
		<!-- Global site tag (gtag.js) - Google Analytics -->

		<script async src="<?php echo $ga_url; ?>"></script>

		<script>
			window.dataLayer = window.dataLayer || [];
		
			function gtag(){
				dataLayer.push(arguments);
			}

			gtag('js', new Date());

			gtag('config', '<?php echo $ga_id ?>');
		</script>
	<?php
	}

	/**
	 * Function to remove plugin deactivation permission
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function hide_plugin_deactivation($actions, $plugin_file, $plugin_data, $context) {
		// Specify the plugin file(s) you want to hide the deactivation link for
		$plugins_to_hide = array(
			$this->plugin_folder,
		);
	
		if (array_key_exists( 'deactivate', $actions ) && in_array($plugin_file, $plugins_to_hide) ) {
		
			// Remove the 'Deactivate' action from the plugin's actions
			unset($actions['deactivate']);
		}
	
		return $actions;
	}

	/**
	 * Function to add custom login CSS and JS files 
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_custom_login_scripts() {
		wp_enqueue_style( 'custom-login', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/ss-custom-login.css' );
		wp_enqueue_style( 'custom-login-uikit', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/uikit.min.css' );

		// wp_enqueue_script( 'login-jquery' , 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' , array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'custom-login-js', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/ss-custom-login.js', array( 'jquery' ), $this->version, false );  
		wp_enqueue_script( 'custom-login-uikitjs', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/uikit.min.js', array( 'jquery' ), $this->version, false );  
		wp_enqueue_script( 'custom-login-uikitminjs', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/uikit-icons.min.js', array( 'jquery' ), $this->version, false );   
	}
	
	/**
	 * Function to redirect custom login page template
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_login_page_template() {
		// Load your custom login template file
		if(isset($_REQUEST['action']) && $_REQUEST['action'] === 'rp'){?>

			<style>
				.login-page{
					display : none;
				}
			</style>
		<?php 
		}else if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword') {
			require_once(dirname(__FILE__) . '/custom-forgot-password.php');
		}
		else{
			require_once(dirname(__FILE__) . '/custom-login-page.php');
		}
		// Check if the login form is submitted
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_REQUEST['action']) ) {
			// ob_start();
			// Handle the form submission and authentication process
			$credentials = array(
				'user_login'    => $_POST['log'],
				'user_password' => $_POST['pwd'],
				'remember'      => true,
			);
	
			$user = wp_signon($credentials);
			if (is_wp_error($user)) {
				// Failed login, display an error message
				// echo '<p class="error">Invalid username or password.</p>';?>
				<script>
					jQuery('#login-message').html('Invalid username or password.').css('display','block');
					jQuery('#login-message').addClass("error");
					setTimeout(function() {
						jQuery('#login-message').css('display','none');
					},  5000);
				</script><?php
			} else {
				// Successful login, redirect the user
				wp_redirect(admin_url());
				exit;
			}
			// ob_end_clean();
		}else if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword')){
			// ob_start();
			if (isset($_POST['user_login']) ) {
				$user_login = sanitize_text_field($_POST['user_login']);
				$user_data = get_user_by('login', $user_login);

				if (!$user_data) {
					echo '<div>User not found. Please enter a <br/>valid username or email.</div>';?>
					<script>
						jQuery('#login-message').html('User not found. Please enter a  <br/>valid username or email.').css('display','block');
						jQuery('#login-message').addClass("error");
						setTimeout(function() {
							jQuery('#login-message').css('display','none');
						},  5000);
					</script><?php
				} else {
					$user_email = $user_data->user_email;
					$reset_key = get_password_reset_key($user_data);
			
					if (is_wp_error($reset_key)) {
						echo '<div>Error generating the password reset link.  <br/>Please try again later.</div>';?>
						<script>
							jQuery('#login-message').html('Error generating the password reset link.  <br/>Please try again later.').css('display','block');
							jQuery('#login-message').addClass("error");
							setTimeout(function() {
								jQuery('#login-message').css('display','none');
							},  5000);
						</script><?php
					} else {
						$reset_link = site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user_login));
					}
				}
			}
			// ob_end_clean();
		} else {
			// Remove the default login form
			add_filter('login_form', '__return_empty_string');
		}
		add_filter('login_form', '__return_empty_string');
	}

	/**
	 * Function to add shortcodes related to plugin
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_plugin_shortcodes() {

		/**
		 * Function to add shortcode for showing stars
		 * 
		 * @Params
		 * color,icon,number
		 * 
		 * @since    2.0.0
		 * @access   public
		 */
		function ss_5star($atts) {
			extract(shortcode_atts(array(
				'color' => 'yellow',
				'icon' => 'star',
				'number' => 5
			), $atts));
			
			$html = "";
			if($number <= 5){
				for($i=1; $i<=$number; $i++){
					$html .= '<i class="uk-icon-' . $icon . '"  uk-icon="' . $icon . '" style="color: ' . $color . ';"></i>';
				}
			}else{
				$html .= "Please provide a number that is five or lower.";
			}

			return $html;
		}
		add_shortcode('5_star', 'ss_5star');
		
		/**
		 * Function to add shortcode for adding footers
		 * 
		 * @Params
		 * company,name,link,prefix,developer,developer_link,line_end
		 * 
		 * @since    2.0.0
	 	 * @access   public
		 */
		function ss_footer($atts) {
			$site_title = get_bloginfo( 'name' );
			extract(shortcode_atts(array(
				'company' => $site_title,
				'name' => 'Spotlight Studios',
				'link' => 'https://spotlightstudios.co.uk',
				'prefix' => 'Powered By',
				'developer' => '',
				'developer_link' => 'https://spotlightstudios.co.uk',
				'line_end' => '<br />',
			), $atts));

			$developer_text = '';
			if($developer != ''){
				$developer_text = $line_end.'
				Developed by <a href="'.$developer_link.'" title="Developed by '.$developer.'">'.$developer.'</a>';
			}

			$footer = '<p>
					Copyright &copy; 
					<script type="text/javascript">
						document.write(new Date().getFullYear());
					</script>
					<a href="/" title="'.$company.'">'.$company.'</a>
					'.$line_end.'
					'.$prefix.' <a href="'.$link.'" target="_blank" title="Web Design by '.$name.'">'.$name.'</a>'
					.$developer_text.
				'</p>';

			return $footer;
		}
		add_shortcode('ss_footer', 'ss_footer');

		
		/**
		 * Function to add shortcode for adding logout button
		 * 
		 * @Params
		 * no params
		 * 
		 * @since    2.0.0
	     * @access   public
		 */
		function ss_logout() {
			$html = '<form action="'.esc_url(wp_logout_url()).'" method="post" class="logout">';
			$html .= '<input type="submit" value="Logout" />';
			$html .= '</form>';
			return $html;
		}
		add_shortcode( 'ss_logout', 'ss_logout' );


		/**
		 * Function to add shortcode for adding placeholder image
		 * 
		 * @Params
		 * width,height,bg,text_colour,text,ext
		 * 
		 * @since    2.0.0
	     * @access   public
		 */
		function ss_placeholder($atts) {
			extract(shortcode_atts(array(
				'width' => 300,
				'height' => 300,
				'bg' => 999,
				'text_colour' => 555,
				'text' => 'Placeholder',
				'ext' => 'jpg',
			), $atts));

			$html = '<img src="https://placehold.co/'. $width . 'x' . $height . '/'. $bg . '/'. $text_colour . '/'. $ext .'?text=' . $text . '" />';
			return $html;
		}
		add_shortcode('ss_placeholder', 'ss_placeholder');

	
		/**
		 * Function to add shortcode for adding placekitten image
		 * 
		 * @Params
		 * width,height
		 * 
		 * @since    2.0.0
	 	 * @access   public
		 * 
		 */
		function ss_placekitten($atts) {
			extract(shortcode_atts(array(
				'width' => 300,
				'height' => 300,
			), $atts));

			$html = '<img src="http://placekitten.com/g/'. $width . '/'. $height . '" />';

			return $html;
		}
		add_shortcode('ss_placekitten', 'ss_placekitten');

		
		/**
		 * Function to add shortcode for adding progress
		 * 
		 * @Params
		 * class,percent,display
		 * 
		 * 
		 * @since    2.0.0
	     * @access   public
		 */
		function ss_progressbar($atts) {
			extract(shortcode_atts(array(
				'class' => 'success',
				'percent' => 50,
				'display' => 50,
			), $atts));

			$html = '<progress  id="js-progressbar" class="uk-progress uk-progress-'. $class .'" value="'.$percent.'" max="100"></progress>';
			$html .= '<p>Progress: <span id="progress-number">'.$display.'%</span></p>';

			return $html;
		}
		add_shortcode('ss_progressbar', 'ss_progressbar');

	
		/**
		 * Function to add shortcode for adding lorem ipsum contents
		 * 
		 * @Params
		 * p(paragraph),l(lines)
		 * 
		 * @since    2.0.0
	 	 * @access   public
		 */
		function ss_lorum($atts) {
			// Extract shortcode attributes
			extract(shortcode_atts(
				array(
					'p' => 2,
					'l' => 100
				),
				$atts
			));
		
			// Generate the lorem ipsum content
			$words = array(
				'Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
				'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
				'magna', 'aliqua', 'Ut', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
				'exercitation', 'ullamco', 'laboris', 'nisi', 'ut', 'aliquip', 'ex', 'ea',
				'commodo', 'consequat', 'Duis', 'aute', 'irure', 'dolor', 'in', 'reprehenderit',
				'in', 'voluptate', 'velit', 'esse', 'cillum', 'dolore', 'eu', 'fugiat', 'nulla',
				'pariatur', 'Excepteur', 'sint', 'occaecat', 'cupidatat', 'non', 'proident',
				'sunt', 'in', 'culpa', 'qui', 'officia', 'deserunt', 'mollit', 'anim', 'id', 'est',
				'laborum'
			);
		
			shuffle($words);
			
			$sentence = implode(' ', array_slice($words, 0, $l));
			$content = '';
			for ($i = 0; $i < $p; $i++) {
				$content .= '<p>'.$sentence.'</p>';
			}
		
			// Return the generated content
			return $content;
		}
		add_shortcode('ss_lorum', 'ss_lorum');

		/**
		 * Function to add shortcode for adding sitemap
		 * 
		 * @Params
		 * list_class,box_class
		 * 
		 * @since    2.0.0
	     * @access   public
		 */
		function ss_sitemap($atts) {
			// Shortcode attributes
			$atts = shortcode_atts(
				array(
					'list_class' => 'site-map-list',
					'box_class' => 'site-map-box',
				),
				$atts
			);
		
			// Get all published pages
			$pages = get_pages();
		
			// Initialize output variable
			$output = '<div class="' . esc_attr($atts['box_class']) . '">';
			$output .= '<ul class="' . esc_attr($atts['list_class']) . '">';
		
			// Loop through pages
			foreach ($pages as $page) {
				$output .= '<li><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></li>';
			}
		
			$output .= '</ul>';
			$output .= '</div>';
		
			return $output;
		}
		add_shortcode('ss_sitemap', 'ss_sitemap');

		/**
		 * Function to Spotlight Icons Shortcode
		 * 
		 * @Params
		 * id, width, trigger, delay, stroke, primary, secondary
		 * 
		 * @since    2.0.0
	     * @access   public
		 */
		function ss_icon_shortcode($atts) {
			// Extract attributes from the shortcode and set default values
			$attributes = shortcode_atts(array(
				'id' => '', // icon ID
				'width' => '100px', // default width
				'trigger' => 'loop', // default trigger
				'delay' => '200', // default delay in milliseconds
				'stroke' => 'light', // default stroke
				'primary' => '#6a2998', // default primary color
				'secondary' => '#126bf3', // default secondary color
			), $atts);
		
			// Construct the icon tag with delay attribute
			$icon_html = '<lord-icon
				src="https://cdn.lordicon.com/' . esc_attr($attributes['id']) . '.json"
				trigger="' . esc_attr($attributes['trigger']) . '"
				delay="' . esc_attr($attributes['delay']) . '"
				stroke="' . esc_attr($attributes['stroke']) . '"
				colors="primary:' . esc_attr($attributes['primary']) . ',secondary:' . esc_attr($attributes['secondary']) . '"
				style="width:' . esc_attr($attributes['width']) . ';height:' . esc_attr($attributes['width']) . '">
			</lord-icon>';
		
			return $icon_html;
		}
		add_shortcode('ss-icon', 'ss_icon_shortcode');
		
	}

	/**
	 * Function to remove deactivation option from bulk action
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function remove_bulk_actions_for_plugins($actions) {

		$current_user = wp_get_current_user();
		$user_id = $current_user->user_login;
		$user_email = $current_user->user_email;
		if(((strtolower($current_user->user_login) != 'spotlight' || !str_contains(strtolower($user_email), 'spotlight')) && get_option('ss_access_toolkit') == 1)){
			//remove deactivation option from bulk action
			unset($actions['deactivate-selected']);
		}
		return $actions;
	}

	/**
	 * Function to add Robots Blocked Custom text in Admin Bar
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_admin_bar_text( $wp_admin_bar ) {
		$args = array(
			'id'    => 'custom-text',
			'title' => 'Robots Blocked',
			'meta'  => array( 'class' => 'custom-text-class' ),
			'parent'=> 'top-secondary',
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Function to add CSS for Robots Blocked Custom text 
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_admin_bar_css() {
		echo '<style>
			#wpadminbar .quicklinks .ab-top-secondary>li.custom-text-class{
				background-color:red !important;
			}
		</style>';
	}

	/**
	 * Function to check Robots.txt file is present in the site
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function check_robots_txt() {
		// Set the URL of the robots.txt file
		$robots_url = home_url('/robots.txt');  // Assuming your WordPress is installed in the root directory
	
		// Send an HTTP request to the robots.txt file
		$response = wp_safe_remote_get($robots_url);
	
		// Get the HTTP response code
		$response_code = wp_remote_retrieve_response_code($response);
	
		// Check if the robots.txt file is accessible
		if ($response_code !== 200) {
			// echo 'robots.txt is not accessible and blocked.';
			add_action( 'admin_bar_menu', array($this,'custom_admin_bar_text'), 999 );
			add_action('wp_before_admin_bar_render', array($this,'custom_admin_bar_css'));
		}
	}

	/**
	 * Function to change admin footer text
	 * 
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_admin_footer_text() {
		$Footer_text = 'Fueled by <a href="https://wordpress.org/" target="_blank">WordPress</a> | Powered by <a href="https://spotlightstudios.co.uk/" target="_blank">Spotlight Studios</a>';
		echo $Footer_text;
	}

	/**
	 * Function to change the default mail 
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_wp_mail_from( $original_email_address ) {
		$emailid = get_option('ss_default_email_value');
		return $emailid;
	}

	/**
	 * Function to change the default mail 
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_header_content(){

		echo '<script src="https://cdn.lordicon.com/lordicon.js"></script>';
		//Code comes from Plugin Settings 
		if(get_option('ss_header_content') != ""){
			echo '<div class="custom-code-from-toolkit-plugin">';
			esc_attr(get_option('ss_header_content'));
			echo '</div>';
		}
	}
	
	/**
	 * Function to change the default mail 
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function custom_footer_content(){
		//Code comes from Plugin Settings Page
		if(get_option('ss_footer_content') != ""){
			echo '<div class="custom-code-from-toolkit-plugin">';
			esc_attr(get_option('ss_footer_content'));
			echo '</div>';
		}
	}

	/**
	 * Copy Page/Post Button
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function clone_custom_post_link( $actions, $post ) {
		$actions['clone'] = '<a href="' . wp_nonce_url('admin.php?action=clone_custom_post&post=' . $post->ID, basename(__FILE__), 'clone_nonce' ) . '" title="Clone this item">Copy</a>';
		return $actions;
	}

	/**
	 * Clone the contents of a Page/Post
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function clone_custom_post() {
		global $wpdb;

		if (!( isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'clone_custom_post' == $_REQUEST['action']) )) {
			wp_die('No post to clone has been supplied!');
		}

		if (!isset($_GET['clone_nonce']) || !wp_verify_nonce($_GET['clone_nonce'], basename(__FILE__))) 
			return;

		$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
		$post = get_post($post_id);

		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;

		if (isset($post) && $post != null) {
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status' => $post->ping_status,
				'post_author' => $new_post_author,
				'post_content' => $post->post_content,
				'post_excerpt' => $post->post_excerpt,
				'post_name' => $post->post_name,
				'post_parent' => $post->post_parent,
				'post_password' => $post->post_password,
				'post_status' => 'draft',
				'post_title' => $post->post_title . ' (Copy)',
				'post_type' => $post->post_type,
				'to_ping' => $post->to_ping,
				'menu_order' => $post->menu_order
			);

			$new_post_id = wp_insert_post($args);

			// Copy the post meta
			$post_meta = get_post_meta($post_id);
			if (!empty($post_meta)) {
				foreach ($post_meta as $key => $values) {
					foreach ($values as $value) {
						add_post_meta($new_post_id, $key, $value);
					}
				}
			}

			// Copy the taxonomies
			$taxonomies = get_object_taxonomies($post->post_type); // get all the taxonomies of the post type
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs')); // get all terms
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}

			wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}
	}

	/**
	 * Renames YT Theme
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function spotlight_builder() {
		echo '<style>
			#yootheme-name {font-size:0;}
			#yootheme-name:after {content: "Spotlight Pro Parent Theme"; font-size:16px}
			.parent-theme {display: none;}
			[data-slug="yootheme"] {display: none;}
		</style>';
	}

	/**
	 * Re-brands key terms
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function spot_translate( $translated ) {
		$words = array(
			// 'word to translate' => 'translation'
			'YOOtheme' => 'Spotlight',
			'Formidable' => 'Spotlight',
			'WP Media folder' => 'Spotlight Media Folder',
			'Bot Protection' => 'Spotlight WAF',
			'managewp.com' => 'spotlightstudios.co.uk/services/wordpress-webmaster/',
			'godaddy.com' => 'spotlightstudios.co.uk',
			'GoDaddy Pro' => 'Spotlight Webmaster',
			'ManageWP - Worker' => 'Spotlight Webmaster',
			'MalCare Security' => 'Spotlight Webmaster',
			'malcare.com' => 'spotlightstudios.co.uk/services/web-application-firewalls/',
			'ManageWP' => 'Spotlight',
			'Godaddy' => 'Spotlight',
			'Rank' => 'Spotlight'
		);
		$translated = str_ireplace(  array_keys($words),  $words,  $translated );
		return $translated;
	}

	/**
	 * Adds restrictions into the WordPress Customiser IF user isn't spotlight
	 *  
	 * 
	 * @since    2.0.0
	 * @access   public
	 */
	function ss_customizer_styles() {
		$user = wp_get_current_user();
	
		if($user && isset($user->user_login) && 'spotlight' != $user->user_login) { ?>
			<style>
				.yo-builder {display: none;} /* Hides Page Builder in Customiser */
				h2:first-of-type:after {content: " (\26A0  No Access to Builder)"; font-size:16px} /* Warning */
				iframe {display: none;} /* Hides preview On Page element */
				.customize-help-toggle {visibility: hidden;} /* Hides Help section in Customiser */
				.yo-wp-builder > div > .uk-button {visibility: hidden;} /* Hides Library */
				.yo-wp-nav-list > li:nth-child(5) {display: none;} /** Hides Settings from main customiser menu */
			</style>
		<?php
		}
	}

	/**
	 * Function to change Google map API
	 * 
	 * 
	 * @since 	2.0.0
	 * @access  public
	 */

	 function my_acf_google_map_api( $api ){
		$api['key'] = get_action('ss_google_map_api');
		return $api;
	}		
		
	/**
	 * Function to include custom function file
	 */
	function include_custom_functions() {
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom_functions.php';
	}
}	
