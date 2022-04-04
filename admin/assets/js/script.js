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


/*
██████  ██ ██████  ███████  ██████ ████████  ██████  ██████  ██    ██      ██████  ██████  ████████ ██  ██████  ███    ██ ███████
██   ██ ██ ██   ██ ██      ██         ██    ██    ██ ██   ██  ██  ██      ██    ██ ██   ██    ██    ██ ██    ██ ████   ██ ██
██   ██ ██ ██████  █████   ██         ██    ██    ██ ██████    ████       ██    ██ ██████     ██    ██ ██    ██ ██ ██  ██ ███████
██   ██ ██ ██   ██ ██      ██         ██    ██    ██ ██   ██    ██        ██    ██ ██         ██    ██ ██    ██ ██  ██ ██      ██
██████  ██ ██   ██ ███████  ██████    ██     ██████  ██   ██    ██         ██████  ██         ██    ██  ██████  ██   ████ ███████
*/
jQuery( document ).ready( function() {
	jQuery( document ).on( 'click', '#dsdi-directory-add-custom-label', function() {
		var count = (
			jQuery( '#dsdi-directory-options [name*="[labels]"][name*="[enabled]"]' ).length
				? parseInt( jQuery( '#dsdi-directory-options > .ds-row.item-label:last [name*="[labels]"][name*="[enabled]"]' ).data( 'label_key' ) )
				: 0
		);

		var $html = '<div class="ds-row ds-flex-align-center ds-pt-1 ds-pb-1 ds-bb ds-ml-auto ds-mr-auto item-label">' +
			'<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pr-lg-2">' +
				'<input ' +
					'name="dsdi_settings[directory][item_options][labels][' + ( count + 1 ) + '][label]" ' +
					'type="text" ' +
					'class="ds-input-box" ' +
					'placeholder="Option label" ' +
					'value="" />' +
			'</div>' +
			'<div class="ds-col-12 ds-col-lg-8 ds-p-0 ds-d-flex ds-flex-align-center">' +
				'<label class="ds-toggler">' +
					'<input ' +
						'name="dsdi_settings[directory][item_options][labels][' + ( count + 1 ) + '][enabled]" ' +
						'type="checkbox" ' +
						'value="1" ' +
						'checked="checked" ' +
						'data-label_key="' + ( count + 1 ) + '" />' +
						'<span></span>' +
				'</label>' +
				'<div class="' + ( jQuery( '[name="dsdi_settings[directory][item_options][load_fa]"]' ).is( ':checked' ) ? ' active' : '' ) + '"' +
					'data-ds_block_toggler_block="directory_load_fa">' +
					'<input ' +
						'name="dsdi_settings[directory][item_options][labels][' + ( count + 1 ) + '][icon]" ' +
						'type="text" ' +
						'class="ds-ml-2 ds-input-box" ' +
						'placeholder="e.g. fa-arrow-right" ' +
						'value="" /> ' +
				'</div>' +
				'<span class="ds-pl-2 ds-ml-2 dashicons dashicons-dismiss ds-float-right"></span>' +
			'</div><!-- .ds-col -->' +
		'</div><!-- .ds-row -->';

		jQuery( this ).closest( '.ds-row' ).before( $html );
	} );

	jQuery( document ).on( 'click', '#dsdi-directory-options .dashicons-dismiss', function() {
		jQuery( this ).closest( '.ds-row' ).fadeOut( 200, function() {
			jQuery( this ).remove();
		} );
	} );
} );


/*
██████  ███████ ██████  ██     ██  ██████  ██████  ███    ██ ███████
██   ██ ██      ██   ██ ██     ██ ██      ██    ██ ████   ██ ██
██   ██ ███████ ██   ██ ██     ██ ██      ██    ██ ██ ██  ██ ███████
██   ██      ██ ██   ██ ██     ██ ██      ██    ██ ██  ██ ██      ██
██████  ███████ ██████  ██     ██  ██████  ██████  ██   ████ ███████
*/
jQuery( document ).ready( function() {
	var key_timer = 0,
	    dsdi_icons_wrapper = 'dsdi-popup-icon-suggestions-wrapper';

	jQuery( document ).on( 'focus', '[name*="labels"][name*="icon"]', function( e ) {
		if ( '' !== jQuery( this ).val() )
			jQuery( this ).trigger( 'keyup' );
	} );

	jQuery( document ).on( 'keyup', '[name*="labels"][name*="icon"]', function( e ) {
		dsdi_icons_popup_close();

		// Proceed only with alphanumberic keys.
		if (
			   90 <= e.which
			&& 48 >= e.which
		)
			return;

		clearTimeout( key_timer );

		var $icon_input  = jQuery( this ),
		    fa_api_url   = 'https://api.fontawesome.com',
		    fa_api_query = '?query=query{search(version:"6.1.1",query:"' + $icon_input.val() + '",first:100){id membership{free}}}';

		key_timer = setTimeout( function() {
			jQuery.get( fa_api_url + fa_api_query, function( json, status ) {
				dsdi_icons_popup_open( $icon_input, json.data.search );
			} );
		}, 300 );
	} );

	// Close & remove the dsdi icons popup.
	jQuery( document ).on( 'click', 'body', function( e ) {
		if ( 0 !== jQuery( e.target ).closest( '.' + dsdi_icons_wrapper ).length )
			return false;

		dsdi_icons_popup_close();
	} );

	// Close & remove the dsdi icons popup.
	jQuery( document ).on( 'click', '.dsdi-icon-suggestion', function( e ) {
		jQuery( this ).closest( '.dsdi-icon-input-wrapper' ).children( '.ds-input-box' ).val( jQuery( this ).data( 'icon_id' ) );

		dsdi_icons_popup_close();
	} );

	function dsdi_icons_popup_open( $icon_input, icons ) {
		var icons_html = '<div id="' + dsdi_icons_wrapper + '" class="ds-p-2 ds-b ' + dsdi_icons_wrapper + '" style="display: none;"';

		jQuery.each( icons, function( index, icon ) {
			if ( 1 <= icon.membership.free.length )
				icons_html += '<i class="dsdi-icon-suggestion ds-text-center fa fa-' + icon.id + ' ds-p-1 ds-b" data-icon_id="' + icon.id + '"></i>';
		} );

		icons_html += '</div>';

		$icon_input.after( icons_html );
		jQuery( '.' + dsdi_icons_wrapper ).slideDown( 200 );
	}

	function dsdi_icons_popup_close() {
		jQuery( '.' + dsdi_icons_wrapper ).slideUp( 200, function() {
			jQuery( this ).remove();
		} );
	}
} );
