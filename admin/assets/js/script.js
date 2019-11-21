/*
██     ██ ██████       ██████  ██████  ██       ██████  ██████      ██████  ██  ██████ ██   ██ ███████ ██████
██     ██ ██   ██     ██      ██    ██ ██      ██    ██ ██   ██     ██   ██ ██ ██      ██  ██  ██      ██   ██
██  █  ██ ██████      ██      ██    ██ ██      ██    ██ ██████      ██████  ██ ██      █████   █████   ██████
██ ███ ██ ██          ██      ██    ██ ██      ██    ██ ██   ██     ██      ██ ██      ██  ██  ██      ██   ██
 ███ ███  ██           ██████  ██████  ███████  ██████  ██   ██     ██      ██  ██████ ██   ██ ███████ ██   ██
*/
jQuery( document ).ready( function() {
	jQuery( '.wp-color-picker' ).wpColorPicker( {
		// you can declare a default color here,
		// or in the data-default-color attribute on the input
		// defaultColor: '#fff',
		// a callback to fire whenever the color changes to a valid color
		// change: function( event, ui ) {},
		// a callback to fire when the input is emptied or an invalid color
		// clear: function() {
		// 	jQuery( this ).closest( '.wp-picker-container' )
		// 		.find( '.color-alpha' ).css( 'background', '#fff' )
		// 		.find( '.wp-color-picker' ).val( '#fff' );
		// },
		// hide the color picker controls on load
		// hide: true,
		// show a group of common colors beneath the square
		// or, supply an array of colors to customize further
		// palettes: true
	} );
} );


/*
███    ███ ███████ ██████  ██  █████
████  ████ ██      ██   ██ ██ ██   ██
██ ████ ██ █████   ██   ██ ██ ███████
██  ██  ██ ██      ██   ██ ██ ██   ██
██      ██ ███████ ██████  ██ ██   ██
*/
jQuery( document ).ready( function() {
	/**
	 * WP Media uploader.
	 */
	var media_uploader = wp.media( {
		frame: "post",
		state: "insert",
		multiple: false
	} );

	// Event element (container) to process.
	var element_processing = '';

	// Adding images.
	jQuery( document ).on( 'click', '.ds-image-add', function() {
		element_processing = jQuery( this ).closest( '.ds-image-container' );
		media_uploader.open();
	} );

	// WP Media event.
	media_uploader.on( 'insert', function() {
		var json = media_uploader.state().get( "selection" ).first().toJSON();

		element_processing.find( '.ds-image-load, button' ).toggleClass( 'ds-d-none' );
		element_processing.find( 'input' ).val( json.url );
		element_processing.find( 'img' ).prop( 'src', json.url );
	} );

	// Removing images.
	jQuery( document ).on( 'click', '.ds-image-remove', function() {
		element_processing = jQuery( this ).closest( '.ds-image-container' );

		element_processing.find( '.ds-image-load, button' ).toggleClass( 'ds-d-none' );
		element_processing.find( 'input' ).val( '' );
		element_processing.find( 'img' ).prop( 'src', '' );
	} );
} );


/*
 █████       ██  █████  ██   ██     ███████  ██████  ██████  ███    ███
██   ██      ██ ██   ██  ██ ██      ██      ██    ██ ██   ██ ████  ████
███████      ██ ███████   ███       █████   ██    ██ ██████  ██ ████ ██
██   ██ ██   ██ ██   ██  ██ ██      ██      ██    ██ ██   ██ ██  ██  ██
██   ██  █████  ██   ██ ██   ██     ██       ██████  ██   ██ ██      ██
*/
jQuery( document ).ready( function() {
	// Convert form submission to Ajax submission.
	jQuery( '[id*="form-main"]' ).submit( function( e ) {
		e.preventDefault();

		jQuery( this ).ajaxSubmit( {
			beforeSend: function() {
				jQuery( '[id*="form-loading-panel"]' ).addClass( 'active' );
			},
			success: function() {
				jQuery( '[id*="form-saved-notice"]' ).addClass( 'active' );
			},
			complete: function() {
				jQuery( '[id*="form-loading-panel"]' ).removeClass( 'active' );

				setTimeout(
					function() {
						jQuery( '[id*="form-saved-notice"]' ).removeClass( 'active' );
					},
					5000
				);
			},
			timeout: 5000
		} );

		return false;
	} );
} );
