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
		// Create post type
		if (!post_type_exists('tcr_events')) {
			register_post_type('tcr_events', array(
				'label' => 'TCR Events',
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array(
					'slug' => 'tcr-events',
					'with_front' => false
				),
				'query_var' => true,
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'trackbacks',
					'custom-fields',
					'revisions',
					'thumbnail',
					'author',
					'page-attributes'
				)
			));
		}
	}
}
