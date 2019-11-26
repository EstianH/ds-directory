<?php
/**
 * Template used for stores.
 *
 * @package DS Store Directory
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( !defined( 'ABSPATH' ) ) exit();
?>
<?php get_header(); ?>
<section id="store-directory-wrapper">
	<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
	<?php include DSSD_ROOT_PATH . 'templates/categories-list.php'; ?>
</section>
<?php get_footer(); ?>
