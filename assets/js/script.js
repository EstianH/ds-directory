jQuery( document ).ready( function() {
	// Directory abbreviated as "di".
	const $sd_nav_container = jQuery( '.dsdi-directory-list-nav-container' );

	// Close the directory navigation on non-nav clicks.
	jQuery( document ).mouseup( e => {
		if (
			  !$sd_nav_container.is(  e.target ) // if the target of the click isn't the container...
			&& $sd_nav_container.has( e.target ).length === 0 // ... nor a descendant of the container
		)
			dsdi_nav_menu_toggle(
				jQuery( '.dsdi-directory-list-nav-container .ds-button' ),
				jQuery( '.dsdi-directory-list-nav-container .ds-button' ).siblings( '.dsdi-directory-list-nav' ),
				true
			);
	} );

	// Close the directory navigation on specific keyboard key presses.
	jQuery( document ).keyup( function( e ) {
		// Escape key.
		if ( 27 === e.keyCode )
			dsdi_nav_menu_toggle(
				jQuery( '.dsdi-directory-list-nav-container .ds-button' ),
				jQuery( '.dsdi-directory-list-nav-container .ds-button' ).siblings( '.dsdi-directory-list-nav' ),
				true
			);
	});

	// Toggle the directory navigation on nav button clicks.
 	jQuery( '.dsdi-directory-list-nav-container .ds-button' ).on( 'click', e => {
		// Check for .closest() prior to checking for .siblings() to avoice issues with children elements being targeted.
		dsdi_nav_menu_toggle(
			jQuery( e.target ).closest( '.ds-button' ),
			jQuery( e.target ).closest( '.ds-button' ).siblings( '.dsdi-directory-list-nav' )
		);
 	} );
} );

function dsdi_nav_menu_toggle( nav_button, nav_container, force_close ) {
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
