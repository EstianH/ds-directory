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

$options = get_post_meta( get_the_ID(), 'options', true );

// Redirect pages that are set to exclude single.
if ( !empty( $options['single_excl'] ) )
	directory_root_redirect();

get_header();
?>
<main id="site-content" role="main">
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

					$categories = get_the_terms( get_the_ID(), 'dsdi_category' );

					if ( !empty( $categories[0]->name ) ) {
					?>
						<div class="ds-container ds-mb-5">
							<div class="ds-row ds-flex-align-center">
								<div class="ds-col-12 ds-col-lg-6 ds-p-0 ds-pr-lg-3 ds-mb-5 ds-mb-lg-0">
									<div class="ds-row ds-ml-auto ds-mr-auto ds-bb ds-pb-1 ds-mb-1">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-shop ds-mr-1"></span>
											<span><?php echo ( !empty( $options['number'] ) ? $options['number'] : '-' ); ?></span>
										</div>
									</div>
									<div class="ds-row ds-ml-auto ds-mr-auto">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-phone ds-mr-1"></span>
											<span><?php echo ( !empty( $options['contact_number'] ) ? $options['contact_number'] : '-' ); ?></span>
										</div>
									</div>
								</div>
								<div class="ds-col-12 ds-col-lg-6 ds-p-0">
									<a class="ds-button" href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
										<?php echo __( 'Back to', DSDI_SLUG ) . ' ' . $categories[0]->name; ?>
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
