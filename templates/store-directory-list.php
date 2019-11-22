<?php
if ( ! defined( 'ABSPATH' ) ) exit( 'Direct script access denied.' );

$do_search = ( !empty( $_GET['store-directory-search'] ) ? sanitize_text_field( $_GET['store-directory-search'] ) : '' );

$store_categories = get_terms(
	'store_directory_category',
	array(
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC'
	)
);

$store_directory = get_queried_object();

$args = array(
	'post_type'   => 'store',
	'orderby'     => 'name',
	'order'       => 'ASC',
	'numberposts' => -1,
	'post_status' => 'publish'
);

if ( $do_search )
	$args['s'] = $do_search;

$stores = get_posts( $args );

$navigation_active = (
	'Store Directory' === $store_directory->name
	? 'All Stores'
	: $store_directory->name
);
?>
<div class="store-directory-container">
	<div class="store-directory-header">
		<div class="store-directory-list-nav-container">
			<button class="store-directory-button" type="button"><?php echo $navigation_active; ?><i class="fas fa-angle-down ds-ml-2"></i></button>
			<ul class="store-directory-list-nav" style="display: none;">
				<li><a href="<?php echo get_category_link( $store_directory_main_obj ); ?>"><?php _e( 'All Stores', 'store-directory' ); ?></a></li>
				<?php foreach ( $store_categories as $store_category ) {
					echo '<li><a href="' . get_category_link( $store_category ) . '">' . $store_category->name . ' (' . $store_category->count . ')</a></li>';
				} ?>
			</ul>
		</div>
		<div class="store-directory-list-search-container">
			<?php
			if ( $do_search )
				echo '<a href="' . get_category_link( $store_directory ) . '">' . __( 'Clear Search', 'store-directory' ) . '</a>';
			?>
			<form class="store-directory-search-form" method="get" action="<?php echo get_category_link( $store_directory ); ?>">
				<input type="text" name="store-directory-search" value="<?php echo $do_search; ?>" class="store-directory-search" size="15" placeholder="<?php echo $navigation_active; ?>" />
				<input type="hidden" name="cat" class="cat" value="<?php echo $store_directory_main_obj->term_id; ?>" />
				<!--<input type="hidden" name="test" class="test" value="this-will-show-in-url" />-->
				<input type="submit" value="Search" />
			</form>
		</div>
	</div>
	<div class="store-directory-body">
		<ul class="store-directory-list">
			<?php
			echo '<li class="store-directory-list-header ds-d-none ds-d-lg-flex">' .
				'<div class="store-number">'             . __( 'Store Number'  , 'store-directory' ) . '</div>' .
				'<div class="store-title">'              . __( 'Store Name'    , 'store-directory' ) . '</div>' .
				'<div class="store-category">'           . __( 'Store Category', 'store-directory' ) . '</div>' .
				'<div class="store-contact-number">'     . __( 'Contact Number', 'store-directory' ) . '</div>' .
				'<div class="store-view-details">'       . __( 'View Details', 'store-directory' ) . '</div>' .
			'</li>';

			if ( empty( $stores ) )
				echo '<li class="store-directory-list-empty ds-pt-3">' . __( 'No results found.', 'store-directory' ) . '</li>';
			else {
				foreach ( $stores as $store ) {
					echo '<li class="store-directory-list-item">';
						echo '<div class="store-number">' .
							'<span class="ds-d-lg-none">' . __( 'Store Number', 'store-directory' ) . '</span>' .
							'<span>' . sanitize_text_field( get_field( 'post--store_number', $store->ID ) ) . '</span>' .
						'</div>' .
						'<div class="store-title">' .
							'<span class="ds-d-lg-none">' . __( 'Store Name', 'store-directory' ) . '</span>' .
							'<span>' . sanitize_text_field( $store->post_title ) . '</span>' .
						'</div>';

						$store_categories = get_the_category( $store->ID );

						echo '<div class="store-category">' .
							'<span class="ds-d-lg-none">' . __( 'Store Category', 'store-directory' ) . '</span>' .
							'<span>';

						if ( !empty( $store_categories ) )
							foreach ( $store_categories as $store_category )
								if (
									sd_is_store_directory( $store_category->term_id )
									&& 'store-directory' !== $store_category->slug
								) {
									echo $store_category->name;
									break;
								}

						echo '</span>' .
						'</div>';

						echo '<div class="store-contact-number">' .
							'<span class="ds-d-lg-none">' . __( 'Contact Number', 'store-directory' ) . '</span>' .
							'<span>' . ( sanitize_text_field( get_field( 'post--store_contact_number', $store->ID ) ) ?: '-' ) . '</span>' .
						'</div>' .
						(
							get_field( 'post--store_view_details_link', $store->ID )
							? '<div class="store-view-details">' .
								do_shortcode(
									'[fusion_button
										link="' . esc_url( get_permalink( $store->ID ) ) . '"
										text_transform=""
										title="View Details"
										target="_self"
										link_attributes=""
										alignment="text-flow"
										modal=""
										hide_on_mobile="small-visibility,medium-visibility,large-visibility"
										class=""
										id=""
										color="default"
										button_gradient_top_color=""
										button_gradient_bottom_color=""
										button_gradient_top_color_hover=""
										button_gradient_bottom_color_hover=""
										accent_color=""
										accent_hover_color=""
										type=""
										bevel_color=""
										border_width=""
										size="medium"
										stretch="default"
										shape=""
										icon="fa-angle-double-right fas"
										icon_position="right"
										icon_divider="no"
										animation_type="fade"
										animation_direction="left"
										animation_speed="1.0"
										animation_offset=""]' .
											__( 'View Details', 'store-directory' ) .
									'[/fusion_button]'
								) .
							'</div>'
							: '<div class="store-view-details ds-d-none ds-d-lg-flex">-</div>'
						);

					echo '</li>';
				}
			} ?>
		</ul>
	</div>
</div>
