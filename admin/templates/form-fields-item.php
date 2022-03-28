<?php if( !defined( 'ABSPATH' ) ) exit;

$dsdi = DS_DIRECTORY::get_instance();
$item_options = get_post_meta( $post->ID, 'dsdi_options', true );

// Render nonce field.
wp_nonce_field( 'dsdi_save_post', 'dsdi_options_save_nonce' );
?>
<div class="ds-container ds-p-2">
	<?php
	if ( isset( $dsdi->settings['directory']['enabled_options'] ) ) :
		foreach ( $dsdi->settings['directory']['enabled_options'] as $option => $enabled ) : ?>
			<div class="ds-row ds-flex-align-center ds-mt-1">
				<div class="ds-col-12 ds-col-lg-3">
					<?php _e( ucfirst( str_replace( '_', ' ', $option ) ), DSDI_SLUG ); ?>:
				</div>
				<div class="ds-col-12 ds-col-lg-9">
					<input
						class="ds-input-box"
						name="dsdi_options[dynamic][<?php echo $option; ?>]"
						type="text"
						value="<?php echo ( isset( $item_options['dynamic'][$option] ) ? $item_options['dynamic'][$option] : '' ); ?>" />
				</div><!-- .ds-col -->
			</div><!-- .ds-row -->
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if ( !empty( $dsdi->settings['general']['single'] ) ) : ?>
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
						<?php echo ( !empty( $item_options['single_excl'] ) ? ' checked="checked"' : '' ); ?> />
					<span></span>
				</label>
			</div><!-- .ds-col -->
		</div><!-- .ds-row -->
	<?php endif; ?>
</div><!-- .ds-container -->
