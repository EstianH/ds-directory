<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();

$cat_all           = get_term_by( 'slug', 'all', 'ds_directory' );
$current_dir_obj   = $wp_query->queried_object; // If this is empty it means the root "ds-directory" is active.
$current_permalink = esc_url( ( !empty( $current_dir_obj ) ? get_term_link( $current_dir_obj ) : home_url() . '/directory' ) );

$args = array(
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
	'taxonomy'   => 'ds_directory'
);
$directories  = get_terms( $args );

$navigation_active = (
	empty( $current_dir_obj->name ) // Root "dsdi-directory".
	? 'All directory items'
	: $current_dir_obj->name
);

$sort_order = ( !empty( $_GET['order'] ) && 'DESC' === $_GET['order'] ? $_GET['order'] : 'ASC' );
$sort_order_link_addon = ( 'DESC' === $sort_order ? '?sort=name&order=' . $sort_order : '' );
?>
<div class="dsdi-directory-container">
	<div class="dsdi-directory-header">
		<div class="dsdi-directory-list-nav-container">
			<button class="ds-button ds-d-flex ds-flex-align-center ds-justify-content-center" type="button">
				<?php echo $navigation_active; ?>
				<span class="ds-icon-arrow-down ds-ml-1"></span>
			</button>
			<ul class="dsdi-directory-list-nav" style="display: none;">
				<li><a href="<?php echo esc_url( get_term_link( $cat_all ) . $sort_order_link_addon ); ?>"><?php _e( $cat_all->name, DSDI_SLUG ); ?></a></li>
				<?php foreach ( $directories as $directory ) {
					if ( $cat_all->term_id === $directory->term_id )
						continue;

					echo '<li><a href="' . esc_url( get_term_link( $directory ) . $sort_order_link_addon ) . '">' . $directory->name . ' <span>(' . $directory->count . ')</span></a></li>';
				} ?>
			</ul>
		</div>
		<div class="dsdi-directory-list-search-container">
			<?php
			if ( !empty( get_search_query() ) )
				echo '<a href="' . $current_permalink . '">' . __( 'Clear Search', DSDI_SLUG ) . '</a>';
			?>
			<form class="dsdi-directory-search-form" method="get" action="<?php echo $current_permalink; ?>">
				<input type="text" name="s" value="<?php echo get_search_query(); ?>" class="dsdi-directory-search" size="15" placeholder="<?php echo $navigation_active; ?>" />
				<input type="hidden" name="post_type" class="" value="dsdi_item" />
				<input type="submit" value="Search" class="<?php echo ( !empty( get_search_query() ) ? ' active' : '' ); ?>" />
			</form>
		</div>
	</div>
	<div class="dsdi-directory-body">
		<div class="dsdi-directory-grid">
			<?php
			echo '<div class="ds-row">';

			if ( have_posts() )
				while( have_posts() ) {
					the_post();

					$directories = get_the_terms( get_the_ID(), 'ds_directory' );
					    $options = get_post_meta( get_the_ID(), 'dsdi_options', true );

					if ( empty( $options ) )
						continue;

					switch ( ( int )$dsdi->settings['general']['grid']['columns'] ) {
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
						<div class="ds-block" title="' . get_the_title() . '" alt="' . get_the_title() . '">';

							if ( !empty( $dsdi->settings['general']['grid']['featured_images'] ) )
								if ( has_post_thumbnail() )
									echo '<div class="dsdi-featured-image"><div style="background-image: url(' . get_the_post_thumbnail_url() . ');"></div></div>';
								else
									echo '<div class="dsdi-featured-image ds-d-flex ds-flex-align-center ds-justify-content-center"><small>No Preview Available.</small></div>';

							echo '<div class="ds-block-title ds-p-2">
								<strong>' . get_the_title() . '</strong>
							</div>
							<div class="ds-block-body ds-p-2">
								<div class="ds-container">
									<div class="ds-row ds-bb ds-pb-1 ds-mb-1">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-shop ds-mr-1"></span> ' . ( !empty( $options['number'] ) ? $options['number'] : '-' ) . '
										</div>
									</div>
									<div class="ds-row">
										<div class="ds-col-12 ds-p-0">
											<span class="ds-icon-phone ds-mr-1"></span> ' . ( !empty( $options['contact_number'] ) ? $options['contact_number'] : '-' ) . '
										</div>
									</div>' .
									(
										   !empty( $dsdi->settings['general']['single'] )
										&&  empty( $options['single_excl'] )
											? '<div class="ds-row ds-bt ds-pt-1 ds-mt-1">
													<div class="ds-col-12 ds-p-0">
														<a class="ds-button" href="' . get_permalink() . '">Details</a>
													</div>
												</div>'
											: ''
									) . '
								</div><!-- .ds-container -->
							</div><!-- .ds-block-body -->
						</div><!-- .ds-block -->
					</div><!-- .$column_classes -->';
				}
			else
				echo '<div class="dsdi-directory-grid-empty ds-pt-3">' . __( 'No directory items found.', DSDI_SLUG ) . '</div>';

			echo '</div><!-- .ds-row -->';
			?>
		</div>
		<?php
		if (
			   empty( $dsdi->settings['general']['load_condition'] )
			|| 'all' !== $dsdi->settings['general']['load_condition']
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
