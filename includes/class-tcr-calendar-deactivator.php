<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/includes
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class TCR_Calendar_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook('tcr_hourly_cron');
	}

}
