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



// JavaScript function to switch tabs
function openTab(event, tabName) {
	var i, tabContent, tabLinks;

	// Hide all tab content
	tabContent = document.getElementsByClassName("tab-content");
	for (i = 0; i < tabContent.length; i++) {
	tabContent[i].style.display = "none";
	}

	// Remove the "active" class from all tab links
	tabLinks = document.getElementsByClassName("tab-link");
	for (i = 0; i < tabLinks.length; i++) {
	tabLinks[i].className = tabLinks[i].className.replace(" active", "");
	}

	// Show the specific tab content
	document.getElementById(tabName).style.display = "block";

	// Add the "active" class to the clicked tab link
	event.currentTarget.className += " active";
}