<?php if( !defined( 'ABSPATH' ) ) exit;

$dssd = DS_STORE_DIRECTORY::get_instance();
// echo '<pre>'; print_r( $dssd ); echo '</pre>';
$tabs = array(
	'General'
);
$active_tab = ( !empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : 'general' );
?>

<div class="ds-wrapper">
	<h1><?php echo DSSD_TITLE; ?></h1>
	<div class="wrap mt-0">
		<div class="ds-container ds-p-0 ds-mb-2">
			<div class="ds-row">
				<div class="ds-col">
					<h2 id="ds-header-notices" class="pt-0 pb-0 ds-d-none"></h2><!-- WP Notices render after the first <h2> tag in class="wrap" -->
					<div id="dssd-form-saved-notice" class="notice notice-success ds-m-0 ds-mb-2"><p>Settings saved</p></div>
					<div class="ds-tab-nav-wrapper ds-tab-nav-wrapper-animate">
						<?php
						foreach( $tabs as $tab )
							echo '<a href="#tab-' . sanitize_title( $tab ) . '" class="ds-tab-nav' . ( $active_tab === sanitize_title( $tab ) ? ' active' : '' ) . '">' . ucfirst( $tab ) . '</a>';
						?>
					</div><!-- .ds-tab-nav-wrapper -->
				</div><!-- .ds-col -->
			</div><!-- .ds-row -->
		</div><!-- .ds-container -->
		<div class="ds-container ds-p-0">
			<div class="ds-row">
				<?php
				/*
				███    ███  █████  ██ ███    ██
				████  ████ ██   ██ ██ ████   ██
				██ ████ ██ ███████ ██ ██ ██  ██
				██  ██  ██ ██   ██ ██ ██  ██ ██
				██      ██ ██   ██ ██ ██   ████
				*/
				?>
				<div class="ds-col-12 ds-col-lg-9 ds-mb-2">
					<form id="dssd-form-main" method="post" action="options.php">
						<div id="dssd-form-loading-panel"></div>
						<?php settings_fields( 'dssd_settings' );
						/*
						████████  █████  ██████          ██████  ███████ ███    ██ ███████ ██████   █████  ██
						   ██    ██   ██ ██   ██ ██     ██       ██      ████   ██ ██      ██   ██ ██   ██ ██
						   ██    ███████ ██████         ██   ███ █████   ██ ██  ██ █████   ██████  ███████ ██
						   ██    ██   ██ ██   ██ ██     ██    ██ ██      ██  ██ ██ ██      ██   ██ ██   ██ ██
						   ██    ██   ██ ██████          ██████  ███████ ██   ████ ███████ ██   ██ ██   ██ ███████
						*/
						?>
						<div id="tab-<?php echo sanitize_title( $tabs[0] ); ?>" class="ds-tab-content<?php echo ( $active_tab === sanitize_title( $tabs[0] ) ? ' active' : '' ); ?>">
							<div class="ds-row ds-mb-2">
								<div class="ds-col">
									<div class="ds-block">
										<div class="ds-block-title">
											<h2>
												<span class="dashicons dashicons-admin-customizer"></span>
												<?php _e( 'Settings', DSSD_SLUG ); ?>
											</h2>
										</div>
										<div class="ds-block-body">
											<div class="ds-row ds-flex-align-center ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-3 ds-p-0 ds-pr-lg-2">
													<?php _e( 'Enable Read More', DSSD_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-9 ds-p-0">
													<label class="ds-toggler">
														<input
															name="dssd_settings[general][read_more]"
															type="checkbox"
															value="1"
															<?php echo ( !empty( $dssd->settings['general']['read_more'] ) ? ' checked="checked"' : ''); ?> />
															<span></span>
													</label>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
										</div><!-- .ds-block-body -->
									</div><!-- .ds-block -->
								</div><!-- .ds-col -->
							</div><!-- .ds-row -->
						</div><!-- #tab-<?php echo sanitize_title( $tabs[0] ); ?> -->
						<div class="ds-row dssd-sticky-bottom">
							<div class="ds-col">
								<div class="ds-block">
									<div class="ds-block-body ds-p-1">
										<?php
										submit_button('', 'button-primary button-hero', '', false );
										?>
									</div><!-- .ds-block-body -->
								</div><!-- .ds-block -->
							</div><!-- .ds-col -->
						</div><!-- .ds-row -->
					</form><!-- #dssd-form-main -->
				</div><!-- .ds-col -->
				<?php
				/*
				███████ ██ ██████  ███████ ██████   █████  ██████
				██      ██ ██   ██ ██      ██   ██ ██   ██ ██   ██
				███████ ██ ██   ██ █████   ██████  ███████ ██████
				     ██ ██ ██   ██ ██      ██   ██ ██   ██ ██   ██
				███████ ██ ██████  ███████ ██████  ██   ██ ██   ██
				*/
				?>
				<div class="ds-col-12 ds-col-lg-3">
					<div class="ds-row ds-mb-2">
						<div class="ds-col">
							<div class="ds-block">
								<div class="ds-block-title">
									<h2>
										<span class="dashicons dashicons-feedback"></span>
										<?php _e( 'Support', DSSD_SLUG ); ?>
									</h2>
								</div>
								<div class="ds-block-body">
									<?php _e( 'If you require assistance please open a support ticket on the divSpot website by filling in the <a href="https://www.divspot.co.za/support" target="_blank">support form</a>.', DSSD_SLUG ); ?>
								</div><!-- .ds-block-body -->
							</div><!-- .ds-block -->
						</div><!-- .ds-col -->
					</div><!-- .ds-row -->
					<div class="ds-row ds-mb-2">
						<div class="ds-col">
							<div class="ds-block">
								<div class="ds-block-title">
									<h2>
										<span class="dashicons dashicons-feedback"></span>
										<?php _e( 'Review', DSSD_SLUG ); ?>
									</h2>
								</div>
								<div class="ds-block-body">
									<?php _e( 'Thank you for using divSpot. If you like our plugins please support us by <a href="https://wordpress.org/plugins/ds-store-directory/#reviews" target="_blank">submitting a review</a>.', DSSD_SLUG ); ?>
								</div><!-- .ds-block-body -->
							</div><!-- .ds-block -->
						</div><!-- .ds-col -->
					</div><!-- .ds-row -->
				</div><!-- .ds-col -->
			</div><!-- .ds-row -->
		</div><!-- .ds-container -->
	</div><!-- .wrap -->
</div><!-- .ds-wrapper -->
