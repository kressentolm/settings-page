<?php

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
	 * Google API client
	 */
	private $gclient;

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
		add_action('admin_init', array($this, 'getGoogleClient'));
		add_filter('manage_tcr_event_posts_columns', array($this, 'add_admin_columns'));
		add_action('manage_tcr_event_posts_custom_column', array($this, 'update_admin_columns'), 10, 2);
	}

	public function getGoogleClient() {

		// Google Calendar OAuth API client setup
		try {

			// great, no exceptions where thrown while creating the object
			$this->gclient = new Google_Client();
			$this->gclient->setAuthConfig(plugin_dir_path(__DIR__) . '/includes/tx-ceo-test-calendar-a64897f369c6.json');
			$this->gclient->setScopes(
				"https://www.googleapis.com/auth/calendar.events.readonly"
			);
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$this->gclient->setRedirectUri($redirect_uri);

			return $this->gclient;
		} catch (\Exception $ex) {
			echo $ex->getMessage();
		}
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
		// TODO: Add progress bar using Pace.js - styles
		// wp_enqueue_style($this->plugin_name . '-pace-style', "https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css", array());
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

		// AJAX scripts
		wp_register_script('ajax-calendar-script', plugin_dir_url(__FILE__) . 'js/ajax-calendar-script.js', array('jquery'));
		wp_enqueue_script('ajax-calendar-script');
		wp_localize_script(
			'ajax-calendar-script',
			'ajax_calendar_object',
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ajax-nonce'),
				'redirecturl' => admin_url('admin.php?page=tcr-calendar'),
				'loadingmessage' => __('Getting calendar data, please wait...')
			)
		);

		// Pace.js
		// TODO: Add progress bar using Pace.js - script file
		// wp_enqueue_script($this->plugin_name . '-pace-script', "https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js", array('jquery'));
	}

	public function create_post_types() {
		$cap_type = 'post';
		$plural = 'Events';
		$single = 'Event';
		$cpt_name = 'tcr_event';
		$opts['can_export'] = TRUE;
		$opts['capability_type'] = $cap_type;
		$opts['description'] = '';
		$opts['exclude_from_search'] = FALSE;
		$opts['has_archive'] = FALSE;
		$opts['hierarchical'] = FALSE;
		$opts['map_meta_cap'] = TRUE;
		$opts['menu_icon'] = 'dashicons-calendar-alt';
		$opts['menu_position'] = 25;
		$opts['public'] = TRUE;
		$opts['publicly_querable'] = TRUE;
		$opts['query_var'] = TRUE;
		$opts['register_meta_box_cb'] = '';
		$opts['rewrite'] = FALSE;
		$opts['show_in_admin_bar'] = TRUE;
		$opts['show_in_menu'] = FALSE;
		$opts['show_in_nav_menu'] = FALSE;
		$opts['capabilities'] = array(
			'create_posts' => 'do_not_allow'
		);

		$opts['labels']['add_new'] = esc_html__("Add New {$single}", 'wisdom');
		$opts['labels']['add_new_item'] = esc_html__("Add New {$single}", 'wisdom');
		$opts['labels']['all_items'] = esc_html__($plural, 'wisdom');
		$opts['labels']['edit_item'] = esc_html__("Edit {$single}", 'wisdom');
		$opts['labels']['menu_name'] = esc_html__($plural, 'wisdom');
		$opts['labels']['name'] = esc_html__($plural, 'wisdom');
		$opts['labels']['name_admin_bar'] = esc_html__($single, 'wisdom');
		$opts['labels']['new_item'] = esc_html__("New {$single}", 'wisdom');
		$opts['labels']['not_found'] = esc_html__("No {$plural} Found", 'wisdom');
		$opts['labels']['not_found_in_trash'] = esc_html__("No {$plural} Found in Trash", 'wisdom');
		$opts['labels']['parent_item_colon'] = esc_html__("Parent {$plural} :", 'wisdom');
		$opts['labels']['search_items'] = esc_html__("Search {$plural}", 'wisdom');
		$opts['labels']['singular_name'] = esc_html__($single, 'wisdom');
		$opts['labels']['view_item'] = esc_html__("View {$single}", 'wisdom');

		register_post_type(strtolower($cpt_name), $opts);
	}

	public function move_post_types($parent_file) {
		global $submenu_file, $current_screen;

		// Set correct active/current menu and submenu in the WordPress Admin menu for the "example_cpt" Add-New/Edit/List
		if ($current_screen->post_type == 'example_cpt') {
			$submenu_file = 'edit.php?post_type=example_cpt';
			$parent_file = 'example_parent_page_id';
		}
		return $parent_file;
	}

	public function add_admin_columns($columns) {
		$columns['start'] = __('Start Date', 'tcr');
		$columns['end'] = __('End Date', 'tcr');
		return $columns;
	}

	function update_admin_columns($column, $post_id) {
		// Image column
		if ('start' === $column) {
			$start = get_post_meta($post_id, 'tcr_event_start', true);
			echo get_date_from_gmt($start, "m/d/Y");
		}
		if ('end' === $column) {
			$end = get_post_meta($post_id, 'tcr_event_end', true);
			echo get_date_from_gmt($end, "m/d/Y");
		}
	}

	// calendar_call
	public function calendar_call() {

		$incoming_events_array = [];

		// Bail early if AJAX post call not called in order to get here (or is empty for some reason)
		// echo json_encode($_REQUEST);
		if (!wp_verify_nonce($_REQUEST['nonce'], "ajax-nonce")) {
			exit("No naughty business please");
		}

		try {

			$client = $this->getGoogleClient();
			$calendarService = new Google_Service_Calendar($client);
			$return_to_js_script = [];

			// Take events array and create or update 'tcr_event' post type with posts

			// 1. Find all events that do not currently exist in DB and create them
			// 2. Update all events that do exist in DB
			// -- Check both by Id from event from Google Calendar

			$myCalendarID = get_option('tcr_calendar_google_calendar_id');

			$incoming_event_ids = [];
			$events = $calendarService->events
				->listEvents(
					$myCalendarID,
					array(
						'timeMin' => date(DATE_RFC3339), // Get all future events
					)
				)->getItems();

			$return_to_js_script['incoming_events_full'] = $events;

			foreach ($events as $event) {
				// If range of dates, comes through as `date` instead of `dateTime` from Google
				// 2021-07-08T19:30:00-05:00
				$start_date = isset($event->getStart()->dateTime) ?
					gmdate('y-m-d', strtotime($event->getStart()->dateTime)) :
					gmdate('y-m-d', strtotime($event->getStart()->date));

				$end_date = isset($event->getEnd()->dateTime) ?
					gmdate('y-m-d', strtotime($event->getEnd()->dateTime)) :
					gmdate('y-m-d', strtotime($event->getEnd()->date));

				$incoming_events_array[] = array(
					'id' => $event->getId(),
					'title' => $event->getSummary(),
					'start' => $start_date,
					'end' => $end_date
				);
				$incoming_event_ids[] = $event->getId();
			}

			$return_to_js_script['incoming_event_ids'] = $incoming_event_ids;

			$existing_posts_ids = get_posts(array(
				'posts_per_page' => -1,
				'post_type' => 'tcr_event',
				'fields' => 'ids',
			));

			$existing_gcal_ids = [];

			foreach ($existing_posts_ids as $id) {
				$gcal_id = get_post_meta($id, 'tcr_gcal_id', true);
				if (in_array($gcal_id, $incoming_event_ids)) {
					$existing_gcal_ids[] = $gcal_id;
				}
			}

			$posts_inserted = [];
			$posts_updated = [];
			$posts_skipped = [];

			$return_to_js_script['existing_gcal_ids'] = $existing_gcal_ids;
			$return_to_js_script['incoming_events_array'] = $incoming_events_array;
			$return_to_js_script['existing_posts_id'] = $existing_posts_ids;

			foreach ($incoming_events_array as $ev) {
				if (!in_array($ev['id'], $existing_gcal_ids)) {
					// create new event with title, start, and end being postmeta
					$new_post = wp_insert_post(array(
						'post_title' => $ev['title'],
						'post_type' => 'tcr_event',
						'post_status' => 'publish',
						'post_title' => $ev['title'],
						'meta_input' => array(
							'tcr_gcal_id' => $ev['id'],
							'tcr_event_start' => $ev['start'],
							'tcr_event_end' => $ev['end']
						)
					));
					$posts_inserted[] = $new_post;
				} else {
					$updateable_post_id = get_posts(array(
						'posts_per_page' => 1,
						'post_type' => 'tcr_event',
						'fields' => 'ids',
						'meta_key'   => 'tcr_gcal_id',
    					'meta_value' => $ev['id'],
						'post_status' => 'publish'
					))[0];

					$existing_start = get_post_meta($updateable_post_id, 'tcr_event_start', true);
					$existing_end = get_post_meta($updateable_post_id, 'tcr_event_end', true);
					$existing_gcal_id = get_post_meta($updateable_post_id, 'tcr_gcal_id', true);

					// Check if post meta has changed
					// TODO: Find good way to compare dates. Will be more performant if not having to update every post
					if (
						$existing_start !== $ev['start'] ||
						$existing_end !== $ev['end']
					) {

						// already exists, so just update
						$updated_post = wp_update_post(array(
							'ID' => $updateable_post_id,
							'post_type' => 'tcr_event',
							'post_title' => $ev['title'],
							'meta_input' => array(
								'tcr_gcal_id' => $existing_gcal_id,
								'tcr_event_start' => $ev['start'],
								'tcr_event_end' => $ev['end']
							)
						));
						$updated_array = array(
							'post_id' => $updated_post,
							'post_meta_start' => $existing_start,
							'post_meta_end' => $existing_end,
							'incoming_start' => $ev['start'],
							'incoming_end' => $ev['end'],
						);
						$posts_updated[] = $updated_array;
					} else {
						$skipped_array = array(
							'post_id' => $updateable_post_id,
							'start' => $existing_start,
							'end' => $existing_end
						);
						$posts_skipped[] = $skipped_array;
					}
				}
			}

			$return_to_js_script['posts_inserted_count'] = count($posts_inserted);
			$return_to_js_script['posts_updated_count'] = count($posts_updated);
			$return_to_js_script['posts_updated'] = $posts_updated;
			$return_to_js_script['posts_skipped'] = $posts_skipped;

			echo json_encode($return_to_js_script);
		} catch (\Exception $ex) {
			echo $ex->getMessage();
		}

		// AJAX cleanup
		wp_die();
	}

	public function addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page($this->plugin_name, 'TCR Calendar', 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'), 'dashicons-schedule', 26);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page($this->plugin_name, 'Events', 'Events', 'edit_pages', 'edit.php?post_type=tcr_event');

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

		// Google Calendar ID
		add_settings_field(
			'tcr_calendar_google_calendar_id',
			'Google Calendar ID',
			array($this, 'tcr_calendar_render_settings_field'),
			'tcr_calendar_settings',
			'tcr_calendar_general_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'tcr_calendar_google_calendar_id',
				'name'      => 'tcr_calendar_google_calendar_id',
				'required' => 'true',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);
		register_setting(
			'tcr_calendar_settings',
			'tcr_calendar_google_calendar_id'
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
