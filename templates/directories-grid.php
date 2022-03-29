<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();
?>
<div class="dsdi-directory-container">
	<?php do_action( 'directories_header' ); ?>
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
									echo '<a href="' . get_permalink() . '" class="dsdi-featured-image ds-d-block"><div style="background-image: url(' . get_the_post_thumbnail_url( null, 'thumbnail' ) . ');"></div></a>';
								else
									echo '<a href="' . get_permalink() . '" class="dsdi-featured-image ds-d-flex ds-flex-align-center ds-justify-content-center"><small>' . __( 'No preview available.', DSDI_SLUG ) . '</small></a>';

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
														<a class="ds-button" href="' . get_permalink() . '">' . __( 'Details', DSDI_SLUG ) . '</a>
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
