<?php
if ( !defined( 'ABSPATH' ) ) exit();

global $wp_query;

$dsdi = DS_DIRECTORY::get_instance();

$current_permalink = esc_url( ( !empty( $current_dir_obj ) ? get_term_link( $current_dir_obj ) : home_url() . '/ds-directory' ) );
$sort_order = ( !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'ASC' );
?>
<div class="dsdi-directory-container">
	<?php do_action( 'directories_header' ); ?>
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
				'<div class="dsdi-directory">' . __( 'Directory', DSDI_SLUG ) . '</div>' .
				'<div class="dsdi-contact-number">' . __( 'Contact No.', DSDI_SLUG ) . '</div>';

			if ( !empty( $dsdi->settings['general']['single'] ) )
				echo '<div class="dsdi-view-details">'       . __( 'View Details',   DSDI_SLUG ) . '</div>';

			echo '</li>';

			if ( have_posts() )
				while( have_posts() ) {
					the_post();

					$directories = get_the_terms( get_the_ID(), 'ds_directory' );
					   $options = get_post_meta( get_the_ID(), 'dsdi_options', true );

					if ( empty( $options ) )
						continue;

					echo '<li class="dsdi-item">';
						echo '<div class="dsdi-number">' .
							'<span class="ds-d-lg-none">' . __( 'Number', DSDI_SLUG ) . '</span>' .
							'<span>' . ( !empty( $options['number'] ) ? $options['number'] : '-' ) . '</span>' .
						'</div>' .
						'<div class="dsdi-title">' .
							'<span class="ds-d-lg-none">' . __( 'Name', DSDI_SLUG ) . '</span>' .
							'<span>' . get_the_title() . '</span>' .
						'</div>';

						echo '<div class="dsdi-directory">' .
							'<span class="ds-d-lg-none">' . __( 'Directory', DSDI_SLUG ) . '</span>' .
							'<span>';

						if ( !empty( $directories[0]->name ) ) {
							$directories_array = array();

							foreach ( $directories as $directory )
								$directories_array[] = $directory->name;

							echo implode( ', ', $directories_array );
						}

						echo '</span>' .
						'</div>';

						echo '<div class="dsdi-contact-number">' .
							'<span class="ds-d-lg-none">' . __( 'Contact Number', DSDI_SLUG ) . '</span>' .
							'<span>' . ( !empty( $options['contact_number'] ) ? $options['contact_number'] : '-' ) . '</span>' .
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
