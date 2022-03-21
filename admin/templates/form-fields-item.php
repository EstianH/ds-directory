<?php if( !defined( 'ABSPATH' ) ) exit;

$dsdi = DS_DIRECTORY::get_instance();
$options = get_post_meta( $post->ID, 'options', true );

// Render nonce field.
wp_nonce_field( 'save_post', 'dsdi_options_save_nonce' );
?>
<div class="ds-container ds-p-2">
	<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb">
		<div class="ds-col-12 ds-col-lg-3">
			<?php _e( 'Number', DSDI_SLUG ); ?>:
		</div>
		<div class="ds-col-12 ds-col-lg-9">
			<input
				class="ds-input-box"
				name="dsdi_options[number]"
				type="text"
				value="<?php echo ( !empty( $options['number'] ) ? $options['number'] : ''); ?>"
				placeholder="eg. 1A" />
		</div><!-- .ds-col -->
	</div><!-- .ds-row -->
	<div class="ds-row ds-flex-align-center">
		<div class="ds-col-12 ds-col-lg-3">
			<?php _e( 'Contact Number', DSDI_SLUG ); ?>:
		</div>
		<div class="ds-col-12 ds-col-lg-9">
			<input
				class="ds-input-box"
				name="dsdi_options[contact_number]"
				type="text"
				value="<?php echo ( !empty( $options['contact_number'] ) ? $options['contact_number'] : ''); ?>"
				placeholder="eg. 000 111 2222" />
		</div><!-- .ds-col -->
	</div><!-- .ds-row -->
	<?php if ( !empty( $dsdi->settings['general']['single'] ) ) { ?>
		<div class="ds-row ds-flex-align-center ds-pt-1 ds-mt-1 ds-bt">
			<div class="ds-col-12 ds-col-lg-3">
				<?php _e( 'Disable Item Page', DSDI_SLUG ); ?>:
			</div>
			<div class="ds-col-12 ds-col-lg-9">
				<label class="ds-toggler">
					<input
						name="dsdi_options[single_excl]"
						type="checkbox"
						value="1"
						<?php echo ( !empty( $options['single_excl'] ) ? ' checked="checked"' : '' ); ?> />
					<span></span>
				</label>
			</div><!-- .ds-col -->
		</div><!-- .ds-row -->
	<?php } ?>
</div><!-- .ds-container -->
