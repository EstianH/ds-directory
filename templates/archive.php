<?php
/**
 * Template used for store categories.
 *
 * @package DS Store Directory
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( !defined( 'ABSPATH' ) ) exit();

$dsdi = DS_DIRECTORY::get_instance();
?>
<?php get_header(); ?>
<div id="dsdi-wrapper">
	<?php the_archive_description( '<div class="taxonomy-description ds-bb ds-b-light ds-mb-4">', '</div>' ); ?>
	<?php
	if ( 'list' === $dsdi->settings['general']['directory_template'] )
		include DSDI_ROOT_PATH . 'templates/directories-list.php';
	else
		include DSDI_ROOT_PATH . 'templates/directories-grid.php';
	?>
</div>
<?php get_footer(); ?>
