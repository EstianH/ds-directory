<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dssd = DS_STORE_DIRECTORY::get_instance();

$store_current_cat_obj = $wp_query->queried_object; // If this empty it means the root "store-directory" is active.
$store_current_permalink = esc_url( ( !empty( $store_current_cat_obj ) ? get_term_link( $store_current_cat_obj ) : home_url() . '/store-directory' ) );

$args = array(
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
	'taxonomy'   => 'store_directory_category'
);
$store_categories  = get_terms( $args );

$navigation_active = (
	empty( $store_current_cat_obj->name ) // Root "store-directory".
	? 'All Stores'
	: $store_current_cat_obj->name
);
?>
<div class="store-directory-container">
	<div class="store-directory-header">
		<div class="store-directory-list-nav-container">
			<button class="ds-button" type="button"><?php echo $navigation_active; ?></button>
			<ul class="store-directory-list-nav" style="display: none;">
				<li><a href="<?php echo esc_url( home_url() . '/store-directory' ); ?>"><?php _e( 'All Stores', DSSD_SLUG ); ?></a></li>
				<?php foreach ( $store_categories as $store_category ) {
					echo '<li><a href="' . get_term_link( $store_category ) . '">' . $store_category->name . ' (' . $store_category->count . ')</a></li>';
				} ?>
			</ul>
		</div>
		<div class="store-directory-list-search-container">
			<?php
			if ( !empty( get_search_query() ) )
				echo '<a href="' . $store_current_permalink . '">' . __( 'Clear Search', DSSD_SLUG ) . '</a>';
			?>
			<form class="store-directory-search-form" method="get" action="<?php echo $store_current_permalink; ?>">
				<input type="text" name="s" value="<?php echo get_search_query(); ?>" class="store-directory-search" size="15" placeholder="<?php echo $navigation_active; ?>" />
				<!--<input type="hidden" name="test" class="test" value="this-will-show-in-url" />-->
				<input type="submit" value="Search" class="<?php echo ( !empty( get_search_query() ) ? ' active' : '' ); ?>" />
			</form>
		</div>
	</div>
	<div class="store-directory-body">
		<ul class="store-directory-list">
			<?php
			echo '<li class="store-directory-list-header ds-d-none ds-d-lg-flex">' .
				'<div class="store-number">'             . __( 'Store No.'  , DSSD_SLUG ) . '</div>' .
				'<div class="store-title">'              . __( 'Store Name'    , DSSD_SLUG ) . '</div>' .
				'<div class="store-category">'           . __( 'Store Category', DSSD_SLUG ) . '</div>' .
				'<div class="store-contact-number">'     . __( 'Contact No.', DSSD_SLUG ) . '</div>';

			if ( !empty( $dssd->settings['general']['read_more'] ) )
				echo '<div class="store-view-details">'       . __( 'View Details',   DSSD_SLUG ) . '</div>';

			echo '</li>';

			if ( have_posts() )
				while( have_posts() ) {
					the_post();

					$store_categories = get_the_terms( get_the_ID(), 'store_directory_category' );
					   $store_options = get_post_meta( get_the_ID(), 'store_options', true );

					if ( empty( $store_options ) )
						continue;

					echo '<li class="store-directory-list-item">';
						echo '<div class="store-number">' .
							'<span class="ds-d-lg-none">' . __( 'Store Number', DSSD_SLUG ) . '</span>' .
							'<span>' . ( sanitize_text_field( $store_options['store_number'] ) ?: '-' ) . '</span>' .
						'</div>' .
						'<div class="store-title">' .
							'<span class="ds-d-lg-none">' . __( 'Store Name', DSSD_SLUG ) . '</span>' .
							'<span>' . get_the_title() . '</span>' .
						'</div>';

						echo '<div class="store-category">' .
							'<span class="ds-d-lg-none">' . __( 'Store Category', DSSD_SLUG ) . '</span>' .
							'<span>';

						if ( !empty( $store_categories[0]->name ) ) {
							$categories_array = array();

							foreach ( $store_categories as $store_category )
								$categories_array[] = $store_category->name;

							echo implode( ', ', $categories_array );
						}

						echo '</span>' .
						'</div>';

						echo '<div class="store-contact-number">' .
							'<span class="ds-d-lg-none">' . __( 'Contact Number', DSSD_SLUG ) . '</span>' .
							'<span>' . ( sanitize_text_field( $store_options['contact_number'] ) ?: '-' ) . '</span>' .
						'</div>';

						if ( !empty( $dssd->settings['general']['read_more'] ) )
							if ( empty( $store_options['excl_read_more_col'] ) )
								echo '<div class="store-view-details">' .
									'<a class="ds-button" href="' . get_permalink() . '">Read More</a>' .
								'</div>';
							else
								echo '<div class="store-view-details ds-justify-content-center">-</div>';

					echo '</li>';
				}
			else
				echo '<li class="store-directory-list-empty ds-pt-3">' . __( 'No results found.', DSSD_SLUG ) . '</li>';
			?>
		</ul>
	</div>
</div>
