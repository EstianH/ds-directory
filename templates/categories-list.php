<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();

$cat_all           = get_term_by( 'slug', 'all', 'dsdi_category' );
$current_cat_obj   = $wp_query->queried_object; // If this is empty it means the root directory is active.
$current_permalink = esc_url( ( !empty( $current_cat_obj ) ? get_term_link( $current_cat_obj ) : home_url() . '/ds-directory' ) );

$args = array(
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
	'taxonomy'   => 'dsdi_category'
);
$categories  = get_terms( $args );

$navigation_active = (
	empty( $current_cat_obj->name ) // Root "ds-directory".
		? 'All directory items'
		: $store_current_cat_obj->name
);

$sort_order = ( !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'ASC' );
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
				<?php foreach ( $categories as $category ) {
					if ( $cat_all->term_id === $category->term_id )
						continue;

					echo '<li><a href="' . esc_url( get_term_link( $category ) . $sort_order_link_addon ) . '">' . $category->name . ' <span>(' . $category->count . ')</span></a></li>';
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
				<!--<input type="hidden" name="test" class="test" value="this-will-show-in-url" />-->
				<input type="submit" value="Search" class="<?php echo ( !empty( get_search_query() ) ? ' active' : '' ); ?>" />
			</form>
		</div>
	</div>
	<div class="dsdi-directory-body">
		<ul class="dsdi-directory-list">
			<?php
			echo '<li class="dsdi-directory-list-header ds-d-none ds-d-lg-flex">' .
				'<div class="dsdi-number">' . __( 'Item No.', DSDI_SLUG ) . '</div>' .
				'<div class="dsdi-title">
					<a href="' . esc_url( $current_permalink . '?sort=name&order=' . ( 'DESC' === $sort_order ? 'ASC' : 'DESC' ) ) . '"
						class="ds-d-flex ds-flex-align-center' . ( 'DESC' !== $sort_order ? ' active' : '' ) . '">' .
						__( 'Name'    , DSDI_SLUG ) .
						'<span class="ds-icon-arrow-down ds-ml-1"></span>' .
					'</a>
				</div>' .
				'<div class="dsdi-category">' . __( 'Category', DSDI_SLUG ) . '</div>' .
				'<div class="dsdi-contact-number">' . __( 'Contact No.', DSDI_SLUG ) . '</div>';

			if ( !empty( $dsdi->settings['general']['single'] ) )
				echo '<div class="dsdi-view-details">'       . __( 'View Details',   DSDI_SLUG ) . '</div>';

			echo '</li>';

			if ( have_posts() )
				while( have_posts() ) {
					the_post();

					$categories = get_the_terms( get_the_ID(), 'dsdi_category' );
					   $options = get_post_meta( get_the_ID(), 'options', true );

					if ( empty( $options ) )
						continue;

					echo '<li class="dsdi-item">';
						echo '<div class="dsdi-number">' .
							'<span class="ds-d-lg-none">' . __( 'Number', DSDI_SLUG ) . '</span>' .
							'<span>' . ( ( int )$options['number'] ?: '-' ) . '</span>' .
						'</div>' .
						'<div class="dsdi-title">' .
							'<span class="ds-d-lg-none">' . __( 'Name', DSDI_SLUG ) . '</span>' .
							'<span>' . get_the_title() . '</span>' .
						'</div>';

						echo '<div class="dsdi-category">' .
							'<span class="ds-d-lg-none">' . __( 'Category', DSDI_SLUG ) . '</span>' .
							'<span>';

						if ( !empty( $categories[0]->name ) ) {
							$categories_array = array();

							foreach ( $categories as $category )
								$categories_array[] = $category->name;

							echo implode( ', ', $categories_array );
						}

						echo '</span>' .
						'</div>';

						echo '<div class="dsdi-contact-number">' .
							'<span class="ds-d-lg-none">' . __( 'Contact Number', DSDI_SLUG ) . '</span>' .
							'<span>' . ( ( int )$store_options['contact_number'] ?: '-' ) . '</span>' .
						'</div>';

						if ( !empty( $dsdi->settings['general']['single'] ) )
							if ( empty( $options['single_excl'] ) )
								echo '<div class="dsdi-view-details">' .
									'<a class="ds-button" href="' . get_permalink() . '">Details</a>' .
								'</div>';
							else
								echo '<div class="dsdi-view-details ds-justify-content-center">-</div>';

					echo '</li>';
				}
			else
				echo '<li class="dsdi-directory-list-empty ds-pt-3">' . __( 'No directory items found.', DSDI_SLUG ) . '</li>';
			?>
		</ul>
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
