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

$dsdi         = DS_DIRECTORY::get_instance();
$item_options = get_post_meta( get_the_ID(), 'dsdi_options', true );

// Redirect pages that are set to exclude single.
if ( !empty( $item_options['single_excl'] ) )
	DS_DIRECTORY::get_instance()->redirect_to_root();

get_header();
?>
<main id="content" role="main" style="width: 100%;">
	<div id="dsdi-wrapper" class="entry dsdi-single-container">
		<?php if ( !empty( $dsdi->settings['directory']['item_options']['title_show'] ) ) : ?>
			<div class="entry-header">
				<div class="entry-header-inner section-inner ds-text-center">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</div>
			</div>
		<?php endif; ?>
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
									if ( isset( $item_options['labels'] ) ) :
										foreach ( $item_options['labels'] as $key => $value ) : ?>
											<div class="ds-row ds-ml-auto ds-mr-auto ds-bt ds-pt-1 ds-pb-1">
												<div class="ds-col-12 ds-p-0">
													<?php if (
														   !empty( $dsdi->settings['directory']['item_options']['load_fa'] )
														&& !empty( $dsdi->settings['directory']['item_options']['labels'][$key]['icon'] )
													) : ?>
														<i class="ds-mr-1 fa fa-<?php echo $dsdi->settings['directory']['item_options']['labels'][$key]['icon']; ?>"></i>
													<?php endif; ?>
													<?php if ( !empty( $dsdi->settings['directory']['item_options']['labels_show_text'] ) ) : ?>
														<span><?php echo $dsdi->settings['directory']['item_options']['labels'][$key]['label']; ?>:</span>
													<?php endif; ?>
													<span><?php echo ( !empty( $value ) ? $value : '-' ); ?></span>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
									<div class="ds-col-12 ds-p-0">
										<a class="ds-button ds-p-1" href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
											<?php echo __( 'Back to', DSDI_SLUG ) . ' ' . $categories[0]->name; ?>
										</a>
									</div>
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
