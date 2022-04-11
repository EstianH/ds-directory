<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Add functionality to delete all settings & options.

delete_metadata(
	'dsdi_item',     // $meta_type
	0,               // $object_id (Will be ignored)
	'dsdi_options',  // $meta_key
	false,           // $meta_value,
	true             // $delete_all (Ignores $object_id)
);

/**
 * Options.
 *
 * @since 1.0
 */
delete_option( 'dsdi_version' );
delete_option( 'dsdi_settings' );
