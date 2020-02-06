/*
██████  ██       ██████   ██████ ██   ██   ████████  ██████   ██████   ██████  ██      ███████ ██████
██   ██ ██      ██    ██ ██      ██  ██       ██    ██    ██ ██       ██       ██      ██      ██   ██
██████  ██      ██    ██ ██      █████  █████ ██    ██    ██ ██   ███ ██   ███ ██      █████   ██████
██   ██ ██      ██    ██ ██      ██  ██       ██    ██    ██ ██    ██ ██    ██ ██      ██      ██   ██
██████  ███████  ██████   ██████ ██   ██      ██     ██████   ██████   ██████  ███████ ███████ ██   ██
*/
jQuery( document ).ready( function() {
	jQuery( '[data-ds_block_toggler]' ).on( 'change', function() {
		var ds_block_selector = '[data-ds_block_toggler_block="' + jQuery( this ).data( 'ds_block_toggler' ) + '"]';

		if ( jQuery( this ).is( ':checked' ) ) {
			jQuery( ds_block_selector ).stop( true ).slideDown( function() {
				jQuery( this ).removeAttr( 'style' ).addClass( 'active' );
			} );
		} else {
			jQuery( ds_block_selector ).stop( true ).slideUp( function() {
				jQuery( this ).removeAttr( 'style' ).removeClass( 'active' );
			} );
		}
	} );
} );


/*
████████  █████  ██████  ███████
   ██    ██   ██ ██   ██ ██
   ██    ███████ ██████  ███████
   ██    ██   ██ ██   ██      ██
   ██    ██   ██ ██████  ███████
*/
jQuery( document ).ready( function() {
	jQuery(document).on( 'click', '.ds-tab-nav:not( .ds-tab-nav-link )', function( e ) {
		e.preventDefault();

		if ( jQuery( this ).hasClass( 'active' ) )
			return;

		var clicked_nav        = jQuery( this ),
				clicked_nav_parent = jQuery( this ).parent( '.ds-tab-nav-wrapper' ),
				clicked_tab        = jQuery( this ).attr( 'href' ),
				active_nav         = jQuery( clicked_nav_parent ).children( '.active' )[0],
				active_tab         = jQuery( active_nav ).attr( 'href' ),
				animation_time     = (
					true === jQuery( clicked_nav_parent ).hasClass( 'ds-tab-nav-wrapper-animate' )
					? 200
					: 0
				);

		jQuery( active_nav ).removeClass( 'active' );
		jQuery( clicked_nav ).addClass( 'active' );

		jQuery( active_tab ).stop( true ).fadeOut( animation_time, function() {
			jQuery( this ).removeClass( 'active' ).removeAttr( 'style' );

			jQuery( clicked_tab ).stop( true ).fadeIn( animation_time, function() {
				jQuery( this ).addClass( 'active' ).removeAttr( 'style' );
			} );
		} );
	} );
} );


/*
██ ███    ██ ██████  ██    ██ ████████ ███████
██ ████   ██ ██   ██ ██    ██    ██    ██
██ ██ ██  ██ ██████  ██    ██    ██    ███████
██ ██  ██ ██ ██      ██    ██    ██         ██
██ ██   ████ ██       ██████     ██    ███████
*/
jQuery( document ).ready( function() {
	jQuery( document ).on( 'focus', '.ds-radio .ds-input-box', function() {
		jQuery( this ).closest( '.ds-radio' ).trigger( 'click' );
	} );

	// Manually trigger change events on all radio buttons with the same name (except for the target).
	jQuery( document ).on( 'click', '.ds-radio > input', function( e ) {
		var target = e.target;

		jQuery.each( jQuery( '[name="' + jQuery( this ).prop( 'name' ) + '"]' ), function() {
			if ( jQuery( target ).is( jQuery( this ) ) )
				return;

			jQuery( this ).trigger( 'change' );
		} );
	} );
} );
