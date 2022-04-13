<?php
global $wp_query;

$dir_all           = get_term_by( 'slug', 'all', 'ds_directory' );
$current_dir_obj   = $wp_query->queried_object; // If this is empty it means the root "ds-directory" is active.
$current_permalink = ( !empty( $current_dir_obj ) ? get_term_link( $current_dir_obj ) : home_url() . '/directory' );

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

$sort_order = ( !empty( $_GET['order'] ) && 'DESC' === $_GET['order'] ? sanitize_text_field( $_GET['order'] ) : 'ASC' );
$sort_order_link_addon = ( 'DESC' === $sort_order ? '?sort=name&order=' . $sort_order : '' );
?>
<div class="dsdi-directory-header">
  <div class="dsdi-directory-list-nav-container">
    <button class="ds-button ds-d-flex ds-flex-align-center ds-justify-content-center" type="button">
      <?php echo esc_html( $navigation_active ); ?>
      <span class="ds-icon-arrow-down ds-ml-1"></span>
    </button>
    <ul class="dsdi-directory-list-nav" style="display: none;">
      <li><a href="<?php echo esc_url( get_term_link( $dir_all ) . $sort_order_link_addon ); ?>"><?php _e( $dir_all->name, DSDI_SLUG ); ?></a></li>
      <?php foreach ( $directories as $directory ) {
        if ( $dir_all->term_id === $directory->term_id )
          continue;

        echo '<li><a href="' . esc_url( get_term_link( $directory ) . $sort_order_link_addon ) . '">' . esc_html( $directory->name ) . ' <span>(' . ( int )$directory->count . ')</span></a></li>';
      } ?>
    </ul>
  </div>
  <div class="dsdi-directory-list-search-container">
    <?php
    if ( !empty( get_search_query() ) )
      echo '<a href="' . esc_url( $current_permalink ) . '">' . __( 'Clear Search', DSDI_SLUG ) . '</a>';
    ?>
    <form class="dsdi-directory-search-form" method="get" action="<?php echo esc_url( $current_permalink ); ?>">
      <input type="text" name="s" value="<?php echo get_search_query(); ?>" class="dsdi-directory-search" size="15" placeholder="<?php echo esc_html( $navigation_active ); ?>" />
      <input type="hidden" name="post_type" class="" value="dsdi_item" />
      <input type="submit" value="Search" class="<?php echo ( !empty( get_search_query() ) ? ' active' : '' ); ?>" />
    </form>
  </div>
</div>
