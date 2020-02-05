<?php
/**
 * Template used for store categories.
 *
 * @package DS Store Directory
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( !defined( 'ABSPATH' ) ) exit();

$dssd = DS_STORE_DIRECTORY::get_instance();
?>
<?php get_header(); ?>
<div id="dssd-wrapper">
	<?php the_archive_description( '<div class="taxonomy-description ds-bb ds-b-light ds-mb-4">', '</div>' ); ?>
	<?php
	if ( 'list' === $dssd->settings['general']['store_category_template'] )
		include DSSD_ROOT_PATH . 'templates/categories-list.php';
	else
		include DSSD_ROOT_PATH . 'templates/categories-grid.php';
	?>
</div>
<?php get_footer(); ?>
