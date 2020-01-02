<?php if( !defined( 'ABSPATH' ) ) exit;

$dssd = DS_STORE_DIRECTORY::get_instance();
$store_options = get_post_meta( $post->ID, 'store_options', true );

// Render nonce field.
wp_nonce_field( 'save_post', 'store_options_save_nonce' );
?>
<div class="ds-container ds-p-2">
	<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb">
		<div class="ds-col-12 ds-col-lg-3">
			<?php _e( 'Store Number', DSSD_SLUG ); ?>:
		</div>
		<div class="ds-col-12 ds-col-lg-9">
			<input
				class="ds-input-box"
				name="store_options[store_number]"
				type="text"
				value="<?php echo ( !empty( $store_options['store_number'] ) ? $store_options['store_number'] : ''); ?>"
				placeholder="eg. 1A" />
		</div><!-- .ds-col -->
	</div><!-- .ds-row -->
	<div class="ds-row ds-flex-align-center">
		<div class="ds-col-12 ds-col-lg-3">
			<?php _e( 'Contact Number', DSSD_SLUG ); ?>:
		</div>
		<div class="ds-col-12 ds-col-lg-9">
			<input
				class="ds-input-box"
				name="store_options[contact_number]"
				type="text"
				value="<?php echo ( !empty( $store_options['contact_number'] ) ? $store_options['contact_number'] : ''); ?>"
				placeholder="eg. 000 111 2222" />
		</div><!-- .ds-col -->
	</div><!-- .ds-row -->
	<?php if ( !empty( $dssd->settings['general']['store_single'] ) ) { ?>
		<div class="ds-row ds-flex-align-center ds-pt-1 ds-mt-1 ds-bt">
			<div class="ds-col-12 ds-col-lg-3">
				<?php _e( 'Disable Store Page', DSSD_SLUG ); ?>:
			</div>
			<div class="ds-col-12 ds-col-lg-9">
				<label class="ds-toggler">
					<input
						name="store_options[store_single_excl]"
						type="checkbox"
						value="1"
						<?php echo ( !empty( $store_options['store_single_excl'] ) ? ' checked="checked"' : '' ); ?> />
					<span></span>
				</label>
			</div><!-- .ds-col -->
		</div><!-- .ds-row -->
	<?php } ?>
</div><!-- .ds-container -->
