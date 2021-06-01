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

<?php
// Here we register our "send_form" function to handle our AJAX request, do you remember the "superhypermega" hidden field? Yes, this is what it refers, the "send_form" action.
// add_action('wp_ajax_send_form', 'send_form'); // This is for authenticated users
// add_action('wp_ajax_nopriv_send_form', 'send_form'); // This is for unauthenticated users.

?>

<!-- <input type="button" value="Sync Calendar" class="button primary-button" id="getCalendarData" class="button primary-button" /> -->
<form action="<?php echo admin_url('admin-ajax.php'); ?>" id="calendarForm">
    <input type="hidden" name="action" value="calendar_call" />
    <input type="submit" value="Sync Calendar" class="primary" name="submit" id="submit" class="button button-primary" />
</form>
<div class="result_area"></div>
<!-- <script>
    jQuery("#getCalendarData").click(function() {
        jQuery.post(ajaxurl, {
                'action': 'calendar_call'
            },
            function(msg) {
                jQuery(".result_area").html(msg);
            });
    });
</script> -->
<?php
