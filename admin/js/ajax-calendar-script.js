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

        console.log('We found it!');
        console.log($('#calendarForm'));
        console.log(ajax_calendar_object);

        // var ajaxurl = ajax_calendar_object.ajaxurl;
        console.log(ajaxurl);
        var loadingMessage = ajax_calendar_object.loadingmessage;
        var nonce = ajax_calendar_object.nonce;
        var redirecturl = ajax_calendar_object.redirecturl;

        // Here is the ajax petition.
        $('#calendarForm').on('submit', function (e) {
            e.preventDefault();

            console.log('Submit correctly found');

            // var form = $('#calendarForm').serialize();

            $.ajax({
                type: "post",
                url: ajaxurl + "?action=calendar_call&nonce=" + nonce,
                url: ajaxurl,
                loadingMessage: loadingMessage,
                // data: $('#calendarForm').serialize() + '?action=calendar_call&nonce=' + nonce,
                data: {
                    action: 'calendar_call',
                    nonce: ajax_calendar_object.nonce
                },
                // processData: false,
                success: function (response, data) {
                    console.log('Submission was successful. Try to make simple call here to https://jsonplaceholder.typicode.com/todos/1');
                    console.log(data);
                    console.log(response);
                    $('.result_area').html(response);
                    let json_data = JSON.parse(response);
                    console.log({json_data});
                    // console.log(window.location.href);
                    // window.location.href = redirecturl;
                },
                error: function(xhr, status, error){
                    var errorMessage = xhr.status + ': ' + xhr.statusText
                    console.error('Error - ' + errorMessage);
                }
            });
        });

        // This return prevents the submit event to refresh the page.
        return false;
    });

})(jQuery);