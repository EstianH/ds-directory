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

$store_options = get_post_meta( get_the_ID(), 'store_options', true );

// Redirect store pages that are set to exclude single pages.
if (
		 !empty( $store_options )
	&& !empty( $store_options['store_single_excl'] )
) {
	wp_safe_redirect( esc_url( home_url() . '/store-directory/all-stores/' ) );
	exit;
}

get_header();
?>
<main id="site-content" role="main">
	<div id="dssd-wrapper" class="entry store-single-container">
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

					$store_categories = get_the_terms( get_the_ID(), 'store_directory_category' );

					if ( !empty( $store_categories[0]->name ) ) {
					?>
						<div class="ds-container ds-mb-5">
							<div class="ds-row ds-flex-align-center">
								<div class="ds-col-12 ds-col-lg-6 ds-p-0 ds-pr-lg-3 ds-mb-5 ds-mb-lg-0">
									<div class="ds-row ds-ml-auto ds-mr-auto ds-bb ds-pb-1 ds-mb-1">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-shop ds-mr-1"></span>
											<span><?php echo ( !empty( $store_options['store_number'] ) ? $store_options['store_number'] : '-' ); ?></span>
										</div>
									</div>
									<div class="ds-row ds-ml-auto ds-mr-auto">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-phone ds-mr-1"></span>
											<span><?php echo ( !empty( $store_options['contact_number'] ) ? $store_options['contact_number'] : '-' ); ?></span>
										</div>
									</div>
								</div>
								<div class="ds-col-12 ds-col-lg-6 ds-p-0">
									<a class="ds-button" href="<?php echo esc_url( get_term_link( $store_categories[0] ) ); ?>">
										<?php echo __( 'Back to', DSSD_SLUG ) . ' ' . $store_categories[0]->name; ?>
									</a>
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
