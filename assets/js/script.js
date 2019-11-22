jQuery( document ).ready( function() {
	// Store Directory abbreviated as "sd".
	const $sd_nav_container = jQuery( '.store-directory-list-nav-container' );

	// Close the Store Directory navigation on non-nav clicks.
	jQuery( document ).mouseup( e => {
		if (
			  !$sd_nav_container.is(  e.target ) // if the target of the click isn't the container...
			&& $sd_nav_container.has( e.target ).length === 0 // ... nor a descendant of the container
		)
			$sd_nav_container.find( '.store-directory-list-nav' ).stop( true ).slideUp();
	} );

	// Close the Store Directory navigation on specific keyboard key presses.
	jQuery( document ).keyup(function( e ) {
		if ( 27 === e.keyCode ) // Escape key.
			jQuery( ".store-directory-list-nav" ).stop( true ).slideUp();
	});

	// Toggle the Store Directory navigation on nav button clicks.
 	jQuery( '.store-directory-button' ).on( 'click', e => {
		// Check for .closest() prior to checking for .siblings() to avoice issues with children elements being targeted.
		jQuery( e.target ).closest( '.store-directory-button' ).siblings( '.store-directory-list-nav' ).stop( true ).slideToggle();
 	} );
} );
