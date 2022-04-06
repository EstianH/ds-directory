<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();

$current_permalink = esc_url( get_term_link( get_queried_object()->term_id ) );
$sort_order = ( !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'ASC' );
?>
<div class="dsdi-directory-container">
	<?php do_action( 'directories_header' ); ?>
	<div class="dsdi-directory-body">
		<ul class="dsdi-directory-list">
			<li class="dsdi-directory-list-header ds-d-none ds-d-lg-flex">
				<div class="dsdi-title">
					<a href="<?php echo esc_url( $current_permalink . '?sort=name&order=' . ( 'DESC' === $sort_order ? 'ASC' : 'DESC' ) ); ?>"
						class="ds-d-flex ds-flex-align-center<?php echo ( 'DESC' !== $sort_order ? ' active' : '' ); ?>">
						<?php _e( 'Item', DSDI_SLUG ); ?>
						<span class="ds-icon-arrow-down ds-ml-1"></span>
					</a>
				</div>
				<div class="dsdi-directory"><?php _e( 'Directory', DSDI_SLUG ); ?></div>
				<?php if ( !empty( $dsdi->settings['general']['single'] ) ) : ?>
					<div class="dsdi-view-details"><?php _e( 'View Details',   DSDI_SLUG ); ?></div>
				<?php endif; ?>
			</li>
			<?php
			if ( have_posts() ) :
				while( have_posts() ) :
					the_post();

					$directories = get_the_terms( get_the_ID(), 'ds_directory' );
					$options     = get_post_meta( get_the_ID(), 'dsdi_options', true );

					if ( empty( $options ) )
						continue;
			?>
					<li class="dsdi-item">
						<div class="dsdi-title">
							<span class="ds-d-lg-none"><?php _e( 'Name', DSDI_SLUG ); ?></span>
							<span><?php echo get_the_title(); ?></span>
						</div>
						<div class="dsdi-directory">
							<span class="ds-d-lg-none"><?php _e( 'Directory', DSDI_SLUG ); ?></span>
							<span>
								<?php
								if ( !empty( $directories[0]->name ) ) {
									$directories_array = array();

									foreach ( $directories as $directory )
										if ( 'all' !== strtolower( $directory->name ) )
											$directories_array[] = $directory->name;

									echo implode( ', ', $directories_array );
								}
								?>
							</span>
						</div>
						<?php if ( !empty( $dsdi->settings['general']['single'] ) ) : ?>
							<?php if ( empty( $options['single_excl'] ) ) : ?>
								<div class="dsdi-view-details">
									<a class="ds-button" href="<?php echo get_permalink(); ?>"><?php _e( 'Details', DSDI_SLUG ); ?></a>
								</div>
							<?php else : ?>
								<div class="dsdi-view-details ds-justify-content-center">-</div>
							<?php endif; ?>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			<?php else : ?>
				<li class="dsdi-directory-list-empty ds-pt-3"><?php _e( 'No directory items found.', DSDI_SLUG ); ?></li>
			<?php endif; ?>
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
