<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.buildmood.com
 * @since             1.0.0
 * @package           TCR_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       TX CEO Ranch Calendar
 * Plugin URI:        https://www.wplauncher.com
 * Description:       Plugin that gives ability to manually or automatically sync calendars. Currently supports Google Calendar.
 * Version:           1.0.0
 * Author:            BuildMood
 * Author URI:        https://www.buildmood.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tcr-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require __DIR__ . '/vendor/autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TCR_Calendar_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tcr-calendar-activator.php
 */
function activate_tcr_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tcr-calendar-activator.php';
	TCR_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tcr-calendar-deactivator.php
 */
function deactivate_tcr_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tcr-calendar-deactivator.php';
	TCR_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tcr_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_tcr_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tcr-calendar.php';

// So we can use the calendar anywhere in the plugin (and extend it to the front-end)
require plugin_dir_path( __FILE__ ) . 'admin/TCR_Calendar_Display.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tcr_calendar() {

	$plugin = new TCR_Calendar();
	$plugin->run();

}
run_tcr_calendar();
