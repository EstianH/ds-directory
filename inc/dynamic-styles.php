<?php
$dsdi = DS_DIRECTORY::get_instance();

$styles = '';
// ================ General ================
if ( !empty( $dsdi->settings['general']['single'] ) )
	$styles .= '@media ( min-width: 992px ) {
	              .dsdi-number { width: 10% !important; }
	              .dsdi-title { width: 25% !important; }
	              .dsdi-directory { width: 35% !important; }
	              .dsdi-contact-number { width: 15% !important; }
	              .dsdi-view-details{ width: 15% !important; }
	            }';

// ================ Design: Paddings ================
$paddings = '';

// Setting values may be empty, but the array will never be considered "empty" since it contains the top-right-bottom-left keys.
foreach ( $dsdi->settings['design']['padding'] as $side => $padding )
	if ( !empty( $padding ) )
		$paddings .= 'padding-' . $side . ': ' . $padding . ( false === strpos( $padding, 'px' ) ? 'px' : '' ) . ';';

if ( $paddings )
	$styles .= 'body #dsdi-wrapper { ' . $paddings . ' }';

//	================ Design: Max-width ================
if ( !empty( $dsdi->settings['design']['max_width'] ) )
	$styles .= 'body #dsdi-wrapper > .taxonomy-description,
	            body #dsdi-wrapper > .dsdi-directory-container{
	              max-width: ' . $dsdi->settings['design']['max_width'] . '
	            }';

// ================ Design: Button colors ================
if ( !empty( $dsdi->settings['design']['button_color_bg'] ) )
	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"],
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button,
	            body #dsdi-wrapper > .dsdi-directory-container button {
	              background-color: ' . $dsdi->settings['design']['button_color_bg'] . ';
	            }';

if ( !empty( $dsdi->settings['design']['button_color_bg_hover'] ) ) {
	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"]:hover,
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button:hover,
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button.active,
	            body #dsdi-wrapper > .dsdi-directory-container button:hover {
	              background-color: ' . $dsdi->settings['design']['button_color_bg_hover'] . ';
	            }';

	// Pagination styling to match button styling.
	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container .ds-pagination > a.page-numbers:hover:after,
	            body #dsdi-wrapper > .dsdi-directory-container .ds-pagination > .current:after {
	              border-color: ' . $dsdi->settings['design']['button_color_bg_hover'] . ';
	            }';
}

if ( !empty( $dsdi->settings['design']['button_color_text'] ) )
	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"],
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button,
	            body #dsdi-wrapper > .dsdi-directory-container button {
	              color: ' . $dsdi->settings['design']['button_color_text'] . ';
	            }';

if ( !empty( $dsdi->settings['design']['button_color_text_hover'] ) ) {
	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"]:hover,
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button:hover,
	            body #dsdi-wrapper > .dsdi-directory-container .ds-button.active,
	            body #dsdi-wrapper > .dsdi-directory-container button:hover {
	              color: ' . $dsdi->settings['design']['button_color_text_hover'] . ';
	            }';

	$styles .= 'body #dsdi-wrapper > .dsdi-directory-container button.active .ds-icon-arrow-down:before,
	            body #dsdi-wrapper > .dsdi-directory-container button.active .ds-icon-arrow-down:after,
	            body #dsdi-wrapper > .dsdi-directory-container button:hover .ds-icon-arrow-down:before,
	            body #dsdi-wrapper > .dsdi-directory-container button:hover .ds-icon-arrow-down:after {
	              background: ' . $dsdi->settings['design']['button_color_text_hover'] . ';
	            }';
}

if ( !empty( $dsdi->settings['design']['text_color'] ) )
	$styles .= 'body #dsdi-wrapper * {
	              color: ' . $dsdi->settings['design']['text_color'] . ';
	            }';

return $styles;
