/*
██████  ██       ██████   ██████ ██   ██   ████████  ██████   ██████   ██████  ██      ███████ ██████
██   ██ ██      ██    ██ ██      ██  ██       ██    ██    ██ ██       ██       ██      ██      ██   ██
██████  ██      ██    ██ ██      █████  █████ ██    ██    ██ ██   ███ ██   ███ ██      █████   ██████
██   ██ ██      ██    ██ ██      ██  ██       ██    ██    ██ ██    ██ ██    ██ ██      ██      ██   ██
██████  ███████  ██████   ██████ ██   ██      ██     ██████   ██████   ██████  ███████ ███████ ██   ██
*/
jQuery( document ).ready( function() {
	jQuery( '.ds-block-toggler .ds-block-toggler-input' ).on( 'change', function() {
		var parent = jQuery( this ).closest( '.ds-block-toggler' );

		if ( jQuery( this ).is( ':checked' ) ) {
			parent.siblings( '.ds-block-toggler-block' ).stop( true ).slideDown( function() {
				jQuery( this ).removeAttr( 'style' );
				parent.addClass( 'active' );
			} );
		} else {
			parent.siblings( '.ds-block-toggler-block' ).stop( true ).slideUp( function() {
				jQuery( this ).removeAttr( 'style' );
				parent.removeClass( 'active' );
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
