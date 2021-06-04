(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    //  var form_data = $( this ).serializeArray();

    // Here we add our nonce (The one we created on our functions.php. WordPress needs this code to verify if the request comes from a valid source.

    
    $(function () {

        console.log(ajaxurl);
        var loadingMessage = ajax_calendar_object.loadingmessage;
        var nonce = ajax_calendar_object.nonce;

        // Here is the ajax petition.
        $('#calendarForm').on('submit', function (e) {
            e.preventDefault();
            var loader = $("#loader-area");
            loader.html("<strong>" + loadingMessage + "...</strong>");
               
                $.ajax({
                    type: "post",
                    url: ajaxurl + "?action=calendar_call&nonce=" + nonce,
                    loadingMessage: loadingMessage,
                    data: {
                        action: 'calendar_call',
                        nonce: ajax_calendar_object.nonce
                    },
                    // processData: false,
                    success: function (response, data) {
                        let json_data = JSON.parse(response);
                        console.log(json_data);
                        if (json_data) {
                            if (json_data.posts_inserted > 0) {
                                loader.html("<strong>Successfully created " + json_data.posts_inserted + " new events!</strong>"); 
                            } else {
                                loader.html("<strong>No new events were found, though some might have updated.</strong>"); 
                            }
                            // + "and updated " + json_data.posts_updated + " events from Google Calendar</strong>");
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.status + ': ' + xhr.statusText
                        loader.html("Error occurred: " + errorMessage);
                        console.error('Error - ' + errorMessage);
                    }
                });
            });


        // This return prevents the submit event to refresh the page.
        return false;
    });

})(jQuery);