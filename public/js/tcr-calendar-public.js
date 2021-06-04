(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

	 function setCookie(key, value, expiry) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
    }

    // function getCookie(key) {
    //     var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    //     return keyValue ? keyValue[2] : null;
    // }

    function eraseCookie(key) {
        // Set cookie to empty value and previous date to force expiration
		document.cookie = key + "= ; expires = Thu, 01 Jan 1970 00:00:00 GMT"
    }

	$(function() {
		// Set cookies for WP to consume
		$(".month-selector").on('click', function (e) {
			e.stopImmediatePropagation();
			eraseCookie('targetMonth');
			var target = $(this).data('month-target');
			setCookie('targetMonth', target, 1);
			location.reload();
		});
		
	});

})( jQuery );
