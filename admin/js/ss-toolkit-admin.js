(function( $ ) {
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

})( jQuery );


// AJAX Function for saving details
jQuery('body').on('click change keyup keydown','.ss-form-input', function() {
	var form_data = jQuery("#ss_toolkit_form").serialize(); // Get form data
	var ajax_url = 'ss_toolkit_ajax_url.ajaxurl'; // Replace with your AJAX handler URL
	
	jQuery.ajax({
		type: 'POST',
		url: ss_toolkit_ajax_url.ajaxurl,
		data: form_data + '&action=ss_toolkit_ajax_request',
		contentType: false,
		processData: false,
		success: function(response) {
			// Handle the AJAX response
		},
		error: function(xhr, textStatus, errorThrown) {
			// Handle any error that occurs during the AJAX request
		}
	});
});