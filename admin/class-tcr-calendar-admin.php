<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/admin
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class TCR_Calendar_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', array($this, 'addPluginAdminMenu'), 9);
		add_action('admin_init', array($this, 'registerAndBuildFields'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TCR_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TCR_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tcr-calendar-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TCR_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TCR_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tcr-calendar-admin.js', array('jquery'), $this->version, false);

		wp_register_script('ajax-calendar-script', plugin_dir_url(__FILE__) . 'js/ajax-calendar-script.js', array('jquery'));
		wp_enqueue_script('ajax-calendar-script');
		wp_localize_script('ajax-calendar-script', 
			'ajax_calendar_object', 
			array(
				'ajaxurl' => admin_url('admin-ajax.php'), 
				'nonce' => wp_create_nonce('ajax-nonce'),
				'redirecturl' => admin_url('admin.php?page=tcr-calendar'), 
				'loadingmessage' => __('Getting calendar data, please wait...'))
			);
	}

	// calendar_call
	public function calendar_call() {

		// TODO: Make requests HERE to google calendar. We have it working and wired up now! ALSO, code cleanup.
		if (empty($_POST['data'])) {
			wp_die('Missing data');
		}
	
		// $post_data = wp_unslash($_POST['data']);
		// $post_data['command'] = wp_unslash($_POST['command']);
		// $post_data['request_token_id'] = wp_unslash($_POST['request_token_id']);
	
		$api_url = 'https://jsonplaceholder.typicode.com/todos/1';
		$response = wp_remote_get($api_url, array(
			// Set the Content-Type header.
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			// Set the request payload/body.
			// 'body'    => json_encode($post_data),
		));
	
		$res_body = wp_remote_retrieve_body($response);
		echo $res_body;
	
		wp_die();
	}

	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page($this->plugin_name, 'TCR Calendar', 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'), 'dashicons-schedule', 26);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->plugin_name, 'TCR Calendar Settings', 'Settings', 'administrator', $this->plugin_name . '-settings', array($this, 'displayPluginAdminSettings'));
	}
	public function displayPluginAdminDashboard() {
		require_once 'partials/' . $this->plugin_name . '-admin-display.php';
	}
	public function displayPluginAdminSettings() {
		// set this var to be used in the settings-display view
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', array($this, 'settingsPageSettingsMessages'));
			do_action('admin_notices', $_GET['error_message']);
		}
		require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
	}
	public function settingsPageSettingsMessages($error_message) {
		switch ($error_message) {
			case '1':
				$message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain');
				$err_code = esc_attr('tcr_calendar_setting');
				$setting_field = 'tcr_calendar_setting';
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}
	public function registerAndBuildFields() {
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'tcr_calendar_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			array($this, 'tcr_calendar_display_general_account'),
			// Page on which to add this section of options
			'tcr_calendar_settings'
		);
		unset($args);

		// Google Calendar API Key
		add_settings_field(
			'tcr_calendar_google_calendar_api_key',
			'Google Calendar API Key',
			array($this, 'tcr_calendar_render_settings_field'),
			'tcr_calendar_settings',
			'tcr_calendar_general_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'tcr_calendar_google_calendar_api_key',
				'name'      => 'tcr_calendar_google_calendar_api_key',
				'required' => 'true',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);
		register_setting(
			'tcr_calendar_settings',
			'tcr_calendar_google_calendar_api_key'
		);

		// Enable automatic calendar sync
		add_settings_field(
			'tcr_calendar_enable_automatic_calendar_sync',
			'Enable automatic calendar sync',
			array($this, 'tcr_calendar_render_settings_field'),
			'tcr_calendar_settings',
			'tcr_calendar_general_section',
			array(
				'label_for' => 'tcr_calendar_enable_automatic_calendar_sync',
				'type'      => 'input',
				'subtype'   => 'checkbox',
				'id'    => 'tcr_calendar_enable_automatic_calendar_sync',
				'name'      => 'tcr_calendar_enable_automatic_calendar_sync',
				'required' => 'true',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option',
			)
		);
		register_setting(
			'tcr_calendar_settings',
			'tcr_calendar_enable_automatic_calendar_sync'
		);
	}
	public function tcr_calendar_display_general_account() {
		echo '<p>These settings apply to all TCR Calendar functionality.</p>';
	}
	public function tcr_calendar_render_settings_field($args) {
		/* EXAMPLE INPUT
			'type'      => 'input',
			'subtype'   => '',
			'id'    => $this->plugin_name.'_example_setting',
			'name'      => $this->plugin_name.'_example_setting',
			'required' => 'required="required"',
			'get_option_list' => "",
				'value_type' = serialized OR normal,
			'wp_data'=>(option or post_meta),
			'post_id' =>
		*/

		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}


		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if ($args['subtype'] != 'checkbox') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
					$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
					$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
					if (isset($args['disabled'])) {
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					} else {
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
				}
				break;
			default:
				# code...
				break;
		}
	}
}
