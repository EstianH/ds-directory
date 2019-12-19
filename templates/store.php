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

					if ( !empty( $store_categories[0]->name ) )
						echo '<a class="ds-button" href="' . esc_url( get_term_link( $store_categories[0] ) ) . '">Back to ' . $store_categories[0]->name . '</a>';
					// Pending development.
				}
			}
			?>
		</div>
	</div>
</main><!-- #site-content -->
<?php get_footer(); ?>
