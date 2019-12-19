<?php
if ( !defined( 'ABSPATH' ) ) exit;

// Add functionality to delete all stores & store categories.

delete_metadata(
	'store',         // $meta_type
	0,               // $object_id (Will be ignored)
	'store_options', // $meta_key
	false,           // $meta_value,
	true             // $delete_all (Ignores $object_id)
);

/**
 * Options.
 *
 * @since 1.0
 */
delete_option( 'dssd_version' );
delete_option( 'dssd_settings' );
