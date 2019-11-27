<?php
/**
 * Template used for store categories.
 *
 * @package DS Store Directory
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( !defined( 'ABSPATH' ) ) exit();
?>
<?php get_header(); ?>
<main id="site-content" role="main">
	<section id="dssd-wrapper">
		<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
		<?php include DSSD_ROOT_PATH . 'templates/categories-list.php'; ?>
	</section>
</main>
<?php get_footer(); ?>
