<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/includes
 * @author     Ben Shadle <benshadle@gmail.com>
 */
class TCR_Calendar_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	public static function activate() {
		if ( ! wp_next_scheduled( 'tcr_hourly_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'tcr_hourly_cron' ); // tcr_hourly_cron is a hook
	   }
	}

}
