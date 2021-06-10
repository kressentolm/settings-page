<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wplauncher.com
 * @since      1.0.0
 *
 * @package    TCR_Calendar
 * @subpackage TCR_Calendar/admin/partials
 */
?>

<form action="<?php echo admin_url('admin-ajax.php'); ?>" id="calendarForm">
    <h2>Syncing your Calendar</h2>
    <p>Click the button below in order to sync with your Google Calendar. If you need to update the calendar to sync with, you can update your settings.</p>
    <input type="hidden" name="action" value="calendar_call" />
    <input type="submit" value="Manually Sync Calendar" name="submit" id="submit" class="button button-primary button-large" />
</form>
<div id="loader-area"></div>
<!-- <div class="result_area"></div> -->
<pre class="result_area" style="white-space: pre-wrap;">
</pre>
<?php 
