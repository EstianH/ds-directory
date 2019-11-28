jQuery( document ).ready( function() {
	// Store Directory abbreviated as "sd".
	const $sd_nav_container = jQuery( '.store-directory-list-nav-container' );

	// Close the Store Directory navigation on non-nav clicks.
	jQuery( document ).mouseup( e => {
		if (
			  !$sd_nav_container.is(  e.target ) // if the target of the click isn't the container...
			&& $sd_nav_container.has( e.target ).length === 0 // ... nor a descendant of the container
		)
			dssd_nav_menu_toggle(
				jQuery( '.store-directory-list-nav-container .ds-button' ),
				jQuery( '.store-directory-list-nav-container .ds-button' ).siblings( '.store-directory-list-nav' ),
				true
			);
	} );

	// Close the Store Directory navigation on specific keyboard key presses.
	jQuery( document ).keyup( function( e ) {
		// Escape key.
		if ( 27 === e.keyCode )
			dssd_nav_menu_toggle(
				jQuery( '.store-directory-list-nav-container .ds-button' ),
				jQuery( '.store-directory-list-nav-container .ds-button' ).siblings( '.store-directory-list-nav' ),
				true
			);
	});

	// Toggle the Store Directory navigation on nav button clicks.
 	jQuery( '.store-directory-list-nav-container .ds-button' ).on( 'click', e => {
		// Check for .closest() prior to checking for .siblings() to avoice issues with children elements being targeted.
		dssd_nav_menu_toggle(
			jQuery( e.target ).closest( '.ds-button' ),
			jQuery( e.target ).closest( '.ds-button' ).siblings( '.store-directory-list-nav' )
		);
 	} );
} );

function dssd_nav_menu_toggle( nav_button, nav_container, force_close ) {
	force_close = force_close || false;

	if (
		!nav_button.hasClass( 'active' )
		&& false === force_close
	) {
		nav_button.addClass( 'active' );
		nav_container.stop( true ).slideDown();
	} else {
		nav_container.stop( true ).slideUp( function() {
			nav_button.removeClass( 'active' );
		} );
	}
}
