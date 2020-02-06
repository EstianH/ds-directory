<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dssd = DS_STORE_DIRECTORY::get_instance();

$store_cat_all = get_term_by( 'slug', 'all-stores', 'store_directory_category' );
$store_current_cat_obj = $wp_query->queried_object; // If this is empty it means the root "store-directory" is active.
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

$sort_order = ( !empty( $_GET['order'] ) && 'DESC' === $_GET['order'] ? $_GET['order'] : 'ASC' );
$sort_order_link_addon = ( 'DESC' === $sort_order ? '?sort=name&order=' . $sort_order : '' );
?>
<div class="store-directory-container">
	<div class="store-directory-header">
		<div class="store-directory-list-nav-container">
			<button class="ds-button ds-d-flex ds-flex-align-center ds-justify-content-center" type="button">
				<?php echo $navigation_active; ?>
				<span class="ds-icon-arrow-down ds-ml-1"></span>
			</button>
			<ul class="store-directory-list-nav" style="display: none;">
				<li><a href="<?php echo esc_url( get_term_link( $store_cat_all ) . $sort_order_link_addon ); ?>"><?php _e( $store_cat_all->name, DSSD_SLUG ); ?></a></li>
				<?php foreach ( $store_categories as $store_category ) {
					if ( $store_cat_all->term_id === $store_category->term_id )
						continue;

					echo '<li><a href="' . esc_url( get_term_link( $store_category ) . $sort_order_link_addon ) . '">' . $store_category->name . ' <span>(' . $store_category->count . ')</span></a></li>';
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
		<div class="store-directory-grid">
			<?php
			echo '<div class="ds-row">';

			if ( have_posts() )
				while( have_posts() ) {
					the_post();

					$store_categories = get_the_terms( get_the_ID(), 'store_directory_category' );
					   $store_options = get_post_meta( get_the_ID(), 'store_options', true );

					if ( empty( $store_options ) )
						continue;

					switch ( ( int )$dssd->settings['general']['store_grid']['columns'] ) {
						case 1:
							$column_classes = 'ds-col-12';
							break;
						case 2:
							$column_classes = 'ds-col-12 ds-col-md-6';
							break;
						case 3:
							$column_classes = 'ds-col-12 ds-col-md-6 ds-col-lg-4';
							break;
						case 4:
							$column_classes = 'ds-col-12 ds-col-md-6 ds-col-lg-3';
							break;
						case 6:
							$column_classes = 'ds-col-12 ds-col-md-6 ds-col-lg-3 ds-col-xl-2';
							break;
					}

					echo '<div class="' . $column_classes . ' ds-mb-2">
						<div class="store ds-block" title="' . get_the_title() . '" alt="' . get_the_title() . '">';

							if ( !empty( $dssd->settings['general']['store_grid']['featured_images'] ) )
								if ( has_post_thumbnail() )
									echo '<div class="ds-store-featured-image"><div style="background-image: url(' . get_the_post_thumbnail_url() . ');"></div></div>';
								else
									echo '<div class="ds-store-featured-image ds-d-flex ds-flex-align-center ds-justify-content-center"><small>No Preview Available.</small></div>';

							echo '<div class="ds-block-title ds-p-2">
								<strong>' . get_the_title() . '</strong>
							</div>
							<div class="ds-block-body ds-p-2">
								<div class="ds-container">
									<div class="ds-row ds-bb ds-pb-1 ds-mb-1">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-shop ds-mr-1"></span> ' . ( !empty( $store_options['store_number'] ) ? $store_options['store_number'] : '-' ) . '
										</div>
									</div>
									<div class="ds-row">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-phone ds-mr-1"></span> ' . ( !empty( $store_options['contact_number'] ) ? $store_options['contact_number'] : '-' ) . '
										</div>
									</div>' .
									(
										!empty( $dssd->settings['general']['store_single'] )
										? '<div class="ds-row ds-bt ds-pt-1 ds-mt-1">
												<div class="ds-col-12 ds-p-0">
													<a class="ds-button" href="' . get_permalink() . '">Details</a>
												</div>
											</div>'
										: ''
									) . '
								</div>
							</div>
						</div>
					</div><!-- .ds-block -->';
				}
			else
				echo '<div class="store-directory-grid-empty ds-pt-3">' . __( 'No stores found.', DSSD_SLUG ) . '</div>';

			echo '</div><!-- .ds-row -->';
			?>
		</div>
		<?php
		if (
			      empty( $dssd->settings['general']['store_load_condition'] )
			|| 'all' !== $dssd->settings['general']['store_load_condition']
		) {
		?>
		<div class="ds-row">
			<div class="ds-col-12">
				<div class="ds-pagination ds-mt-5">
					<?php
					$big = 999999999; // An unlikely integer.
					$args = array(
						'base'      => str_replace( $big, '%#%', get_pagenum_link( $big, false ) ),
						'format'    => '?paged=%#%',
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $wp_query->max_num_pages,
						'prev_text' => '«',
						'next_text' => '»',
						'end_size'  => 3,
						'mid_size'  => 3
					);
					echo paginate_links( $args );
					?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
