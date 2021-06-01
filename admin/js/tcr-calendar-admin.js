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

	// $(function () {

	// 	// Here is the ajax petition.
	// 	$('#getCalendarData').on('click', function (e) {
	// 		e.preventDefault();

	// 		// var form = $('#calendarForm').serialize();

	// 		$.ajax({
	// 			type: "POST",
	// 			url: scriptData.ajaxurl,
	// 			data: {
	// 				action: 'calendarCall'
	// 			},
	// 			success: function (data) {
	// 				console.log('Submission was successful. Try to make simple call here to https://jsonplaceholder.typicode.com/todos/1');
	// 				console.log(data);
	// 				console.log(window.location.href);
	// 				//window.location.href = '?page_id=7=' + data;
	// 			},
	// 			error: function (data) {
	// 				console.log('An error occurred.');
	// 				console.log(data);
	// 			},
	// 		});
	// 	});

	// 	// This return prevents the submit event to refresh the page.
	// 	return false;

	// });

})(jQuery);
