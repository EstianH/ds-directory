<?php
/**
 * Template used for store single pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

$item_options = get_post_meta( get_the_ID(), 'dsdi_options', true );

// Redirect pages that are set to exclude single.
if ( !empty( $item_options['single_excl'] ) )
	DS_DIRECTORY::get_instance()->redirect_to_root();

get_header();
?>
<main id="content" role="main" style="width: 100%;">
	<div id="dsdi-wrapper" class="entry dsdi-single-container">
		<div class="entry-header">
			<div class="entry-header-inner section-inner ds-text-center">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</div>
		</div>
		<div class="entry-content">
			<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();

					$categories = get_the_terms( get_the_ID(), 'ds_directory' );

					if ( !empty( $categories[0]->name ) ) {
					?>
						<div class="ds-container ds-mb-5">
							<div class="ds-row">
								<div class="ds-col-12 ds-col-lg-6 ds-p-0 ds-pr-lg-3 ds-mb-5 ds-mb-lg-0">
									<img class="dsdi-featured-image" src="<?php echo get_the_post_thumbnail_url( null, 'full-size' ); ?>" />
								</div>
								<div class="ds-col-12 ds-col-lg-6 ds-p-0 ds-pr-lg-2 ds-mb-5 ds-mb-lg-0">
									<?php
									if ( isset( $item_options['dynamic'] ) ) :
										$foreach_count = 0;

										foreach ( $item_options['dynamic'] as $option => $value ) : ?>
											<div class="ds-row ds-ml-auto ds-mr-auto ds-bt ds-pt-1<?php echo ( $foreach_count++ ? ' ds-mt-1' : '' ); ?>">
												<div class="ds-col-12 ds-p-0">
													<!-- <span class="ds-icon-shop ds-mr-1"></span> -->
													<span><?php _e( ucfirst( str_replace( '_', ' ', $option ) ), DSDI_SLUG ); ?>:</span>
													<span><?php echo ( !empty( $value ) ? $value : '-' ); ?></span>
												</div>
											</div>
										<?php endforeach; ?>
										<div class="ds-col-12 ds-p-0">
											<a class="ds-button ds-p-1 ds-mt-1" href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
												<?php echo __( 'Back to', DSDI_SLUG ) . ' ' . $categories[0]->name; ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php
					}
					the_content();
				}
			}
			?>
		</div>
	</div>
</main><!-- #site-content -->
<?php get_footer(); ?>
