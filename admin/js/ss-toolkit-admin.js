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

jQuery(document).ready(function($) {

	
	$("#ss_login").on('change', function() {
		if ($(this).is(':checked')) {
			$(this).attr('value', 'true');
		}
		else {
			$(this).attr('value', 'false');
		}
	});

// AJAX Function for saving details
	jQuery('body').on('click blur','.ss-form-input', function() {

		jQuery('.ss_toolkit_message').text('Please wait...');

		var from_toolkit_form = jQuery("#from_toolkit_form").val();

		var ss_login = (jQuery('#ss_login').is(":checked"))?1:0;
		var ss_dashboardwidget = (jQuery('#ss_dashboardwidget').is(":checked"))?1:0;
		var ss_shortcode = (jQuery('#ss_shortcode').is(":checked"))?1:0;

		var sstoolkit_removal = (jQuery('#sstoolkit-removal').is(":checked"))?1:0;
		var spotlight_access = (jQuery('#spotlight-access').is(":checked"))?1:0;
		var ss_api_key = jQuery('#ss_api_key').val();

		jQuery.ajax({
			type: 'POST',
			url: ss_toolkit_ajax_url.ajaxurl, // Replace with your AJAX handler URL
			data:{
				'action' : 'ss_toolkit_ajax_request',
				'ss_login': ss_login, 
				'ss_dashboardwidget': ss_dashboardwidget, 
				'ss_shortcode': ss_shortcode, 
				'sstoolkit_removal': sstoolkit_removal, 
				'spotlight_access': spotlight_access, 
				'ss_api_key' : ss_api_key,
				'from_toolkit_form' : from_toolkit_form
			},
			success: function(response) {
				jQuery('.ss_toolkit_message').text('Settings Saved');
				console.log("Working");
			},
			error: function(xhr, textStatus, errorThrown) {
				// Handle any error that occurs during the AJAX request
				$('.ss_toolkit_message').text('Settings Saved');
				console.log("Not Working");
			}
		});
	});
});