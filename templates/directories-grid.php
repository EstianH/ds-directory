<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();
?>
<div class="dsdi-directory-container">
	<?php do_action( 'directories_header' ); ?>
	<div class="dsdi-directory-body">
		<div class="dsdi-directory-grid">
			<div class="ds-row">
				<?php
				if ( have_posts() ) :
					while( have_posts() ) :
						the_post();

						$directories  = get_the_terms( get_the_ID(), 'ds_directory' );
						$item_options = get_post_meta( get_the_ID(), 'dsdi_options', true );

						if ( empty( $item_options ) )
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
						?>
						<div class="<?php echo $column_classes; ?> ds-mb-2">
							<div class="ds-block" title="<?php echo get_the_title(); ?>" alt="<?php echo get_the_title(); ?>">
								<?php if ( !empty( $dsdi->settings['general']['grid']['featured_images'] ) ) : ?>
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php echo get_permalink(); ?>" class="dsdi-featured-image ds-d-block">
											<div style="background-image: url(<?php echo get_the_post_thumbnail_url( null, 'thumbnail' ); ?>);"></div>
										</a>
									<?php else : ?>
										<a href="<?php echo get_permalink(); ?>" class="dsdi-featured-image ds-d-flex ds-flex-align-center ds-justify-content-center">
											<small><?php _e( 'No preview available.', DSDI_SLUG ); ?></small>
										</a>
									<?php endif; ?>
								<?php endif; ?>
								<div class="ds-block-title ds-p-2">
									<strong><?php echo get_the_title(); ?></strong>
								</div>
								<div class="ds-block-body ds-p-2">
									<div class="ds-container ds-p-0">
										<?php
										if ( isset( $item_options['labels'] ) ) :
											foreach ( $item_options['labels'] as $key => $value ) : ?>
												<div class="ds-row ds-ml-auto ds-mr-auto ds-bb ds-pt-1 ds-pb-1">
													<div class="ds-col-12 ds-p-0">
														<?php if (
															   !empty( $dsdi->settings['directory']['item_options']['load_fa'] )
															&& !empty( $dsdi->settings['directory']['item_options']['labels'][$key]['icon'] )
														) : ?>
															<i class="ds-mr-1 fa fa-<?php echo $dsdi->settings['directory']['item_options']['labels'][$key]['icon']; ?>"></i>
														<?php endif; ?>
														<?php if ( !empty( $dsdi->settings['directory']['item_options']['labels_show_text'] ) ) : ?>
															<span><?php echo $dsdi->settings['directory']['item_options']['labels'][$key]['label']; ?>:</span>
														<?php endif; ?>
														<span><?php echo ( !empty( $value ) ? $value : '-' ); ?></span>
													</div>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
										<?php
										if (
											   !empty( $dsdi->settings['general']['single'] )
											&& empty( $item_options['single_excl'] )
										) :
										?>
											<div class="ds-row ds-pt-2 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-p-0">
													<a class="ds-button" href="<?php echo get_permalink(); ?>"><?php _e( 'Details', DSDI_SLUG ); ?></a>
												</div>
											</div>
										<?php endif; ?>
									</div><!-- .ds-container -->
								</div><!-- .ds-block-body -->
							</div><!-- .ds-block -->
						</div><!-- .$column_classes -->
					<?php endwhile; ?>
				<?php else : ?>
					<div class="dsdi-directory-grid-empty ds-pt-3"><?php _e( 'No directory items found.', DSDI_SLUG ); ?></div>
				<?php endif; ?>
			</div><!-- .ds-row -->
		</div><!-- .dsdi-directory-grid -->
		<?php
		if (
			   empty( $dsdi->settings['general']['load_condition'] )
			|| 'all' !== $dsdi->settings['general']['load_condition']
		) :
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
					</div><!-- .ds-pagination -->
				</div><!-- .ds-col -->
			</div><!-- .ds-row -->
		<?php endif; ?>
	</div><!-- .dsdi-directory-body -->
</div><!-- .dsdi-directory-container -->
