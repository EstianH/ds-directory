<?php if( !defined( 'ABSPATH' ) ) exit;

$directory_options = array();

if ( !empty( $term ) ) {
	$directory_options['option_1'] = get_term_meta( $term->term_id, 'option_1', true );
}
?>
<div class="ds-row ds-mb-2">
	<div class="ds-col">
		<div class="ds-block">
			<div class="ds-block-title">
				<h2>
					<span class="dashicons dashicons-admin-generic"></span>
					<?php _e( 'Directory Options', DSSD_SLUG ); ?>
				</h2>
			</div>
			<div class="ds-block-body">
				<div class="ds-row ds-flex-align-center ds-ml-auto ds-mr-auto">
					<div class="ds-col-12 ds-col-lg-3 ds-p-0 ds-pr-lg-2">
						<?php _e( 'Option 1', DSSD_SLUG ); ?>:
					</div>
					<div class="ds-col-12 ds-col-lg-9 ds-p-0">
						<label class="ds-toggler">
							<input
								name="option_1"
								type="checkbox"
								value="1"
								<?php echo ( !empty( $directory_options['option_1'] ) ? ' checked="checked"' : '' ); ?> />
							<span></span>
						</label>
					</div><!-- .ds-col -->
				</div><!-- .ds-row -->
			</div><!-- .ds-block-body -->
		</div><!-- .ds-block -->
	</div><!-- .ds-col -->
</div><!-- .ds-row -->
