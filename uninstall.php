<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Options.
 *
 * @since 1.0
 */
delete_metadata(
	'store',         // $meta_type
	0,               // $object_id (Will be ignored)
	'store_options', // $meta_key
	false            // $meta_value,
	true             // $delete_all (Ignores $object_id)
);
delete_option( 'dssd_version' );
delete_option( 'dssd_settings' );
