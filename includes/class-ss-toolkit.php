<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.g2techsoft.com
 * @since      1.0.0
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
 * @since      1.0.0
 * @package    Ss_Toolkit
 * @subpackage Ss_Toolkit/includes
 * @author     G2 TechSoft <lingesh@g2techsoft.com>
 */
class Ss_Toolkit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ss_Toolkit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SS_TOOLKIT_VERSION' ) ) {
			$this->version = SS_TOOLKIT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ss-toolkit';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Hook the function to the admin_menu action to add the submenu page
		add_action('admin_menu', array($this,'ss_toolkit_add_submenu_page'));

		//Hook function to Show the Spotlight Dashboard Widget
		add_action('wp_dashboard_setup', array($this,'ss_toolkit_add_dashboard_widgets'));

		//Function for listing the details of shortcode of SS Toolkit plugin
		add_action('init', array($this,'ss_toolkit_shortcode_listing'));

		//Hook functions to call Ajax 
		add_action('wp_ajax_ss_toolkit_ajax_request', array( $this, 'ss_toolkit_ajax'));

        add_action('wp_ajax_nopriv_ss_toolkit_ajax_request',array( $this, 'ss_toolkit_ajax' ));

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
	 * @since    1.0.0
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

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ss-toolkit-public.php';

		$this->loader = new Ss_Toolkit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ss_Toolkit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ss_Toolkit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ss_Toolkit_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ss_Toolkit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
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
			array($this,'ss_toolkit_menu_page') // Callback function to display the page content
		);
	}

	
	// Add the submenu page under the "Tools" menu
	function ss_toolkit_menu_page() {
		// Page content goes here (you can put your HTML and PHP code for the custom tools)
		echo '<h1>SS Toolskit 2.0</h1>';
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'tools';
		?>
		<form id="ss_toolkit_form" action="" enctype="multipart/form-data">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h2 class="nav-tab-wrapper">
							<a href="?page=ss-toolkit&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
							<a href="?page=ss-toolkit&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
						</h2>

						<?php if($active_tab == 'tools'){?>
							<div id="tab1 tools" style="display:block;">
								<table class="widefat" border="0">
									<tr>
										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>SpotLight Login</h3>
													<p>Enables the Spotlight Studios Login Screen</p>

													<div class="ss-toolkit-card-bottom">
														<a href="?page=ss-toolkit&tab=settings" class="page-title-action">Settings</a>

														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_login') == 1)?'checked ':""; ?> name="ss_login" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
										</td>

										<td>
											<div class="ss-toolkit-card">
												<div class="ss-toolkit-card-content">
													<h3>Dashboard Widget</h3>
													<p>Dispalys a Spotlight studios widget with useful links and removes useless widgets</p>
													
													<div class="ss-toolkit-card-bottom">
														<div></div>
														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_dashboard_widget') == 1)?'checked':''; ?> name="ss_dsahboardwidget" class="ss-form-input">
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
														<a href="#TB_inline?width=600&height=400&inlineId=my-thickbox-content" class="thickbox page-title-action">View</a>
													
														<label class="toggle-switch">
															<input type="checkbox" <?php echo (get_option('ss_shortcodes') == 1)?'checked':''; ?> name="ss_shortcode" class="ss-form-input">
															<span class="slider"></span>
														</label>
													</div>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						<?php } ?>
						<?php if($active_tab == 'settings'){?>
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
											<p><span>GA 4:</span> <input type="text" name="ss_api_key" id="ss_api_key" value="<?php echo (get_option('ss_api') != null)? get_option('ss_api') :""; ?>"></p>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</form>
		<?php

	}

	function ss_toolkit_add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'ss_toolkit_dashboard_widget_id',
			'Spotlight Studiios | Support Details',
			array($this,'ss_toolkit_dashboard_widget'),
			'high'
		);
	}

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
	
	function ss_toolkit_shortcode_listing(){
		add_thickbox(); ?>
		<div id="my-thickbox-content" style="display:none;">
			<!-- Your content goes here -->
			<h3>SS ToolKit ShortCode's</h3>
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
				</tbody>
			</table>
		</div>
	<?php
	}

	public function ss_toolkit_ajax() { 
		echo "hai";
		die;
	}
}	
