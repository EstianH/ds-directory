<?php if( !defined( 'ABSPATH' ) ) exit;

$dsdi = DS_DIRECTORY::get_instance();
// echo '<pre>'; var_dump( $dsdi ); echo '</pre>';
$tabs = array(
	'General',
	'Design'
);
$active_tab = ( !empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : 'general' );
?>

<div class="ds-wrapper">
	<h1><?php echo DSDI_TITLE; ?></h1>
	<div class="wrap mt-0">
		<div class="ds-container ds-p-0 ds-mb-2">
			<div class="ds-row">
				<div class="ds-col">
					<h2 id="ds-header-notices" class="pt-0 pb-0 ds-d-none"></h2><!-- WP Notices render after the first <h2> tag in class="wrap" -->
					<div id="dsdi-form-saved-notice" class="notice notice-success ds-m-0 ds-mb-2"><p>Settings saved</p></div>
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
				<div class="ds-col-12 ds-col-lg-9 ds-col-xl-8 ds-col-xxl-6 ds-mb-2">
					<form id="dsdi-form-main" method="post" action="admin-ajax.php">
						<div id="dsdi-form-loading-panel"></div>
						<?php wp_nonce_field( 'dsdi_settings_update', 'dsdi_settings_nonce' );
						/*
						████████  █████  ██████          ██████  ███████ ███    ██ ███████ ██████   █████  ██
						   ██    ██   ██ ██   ██ ██     ██       ██      ████   ██ ██      ██   ██ ██   ██ ██
						   ██    ███████ ██████         ██   ███ █████   ██ ██  ██ █████   ██████  ███████ ██
						   ██    ██   ██ ██   ██ ██     ██    ██ ██      ██  ██ ██ ██      ██   ██ ██   ██ ██
						   ██    ██   ██ ██████          ██████  ███████ ██   ████ ███████ ██   ██ ██   ██ ███████
						*/
						?>
						<input type="hidden" name="action" value="dsdi_settings_update" />
						<div id="tab-<?php echo sanitize_title( $tabs[0] ); ?>" class="ds-tab-content<?php echo ( $active_tab === sanitize_title( $tabs[0] ) ? ' active' : '' ); ?>">
							<div class="ds-row ds-mb-2">
								<div class="ds-col">
									<div class="ds-block">
										<div class="ds-block-body">
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-mt-1 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pr-lg-2">
													<?php _e( 'Enable Single Pages', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<label class="ds-toggler">
														<input
															name="dsdi_settings[general][single]"
															type="checkbox"
															value="1"
															<?php echo ( !empty( $dsdi->settings['general']['single'] ) ? ' checked="checked"' : ''); ?> />
															<span></span>
													</label>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-mt-1 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Category Template', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<?php $category_template = ( empty( $dsdi->settings['general']['category_template'] ) ? 'list' : $dsdi->settings['general']['category_template'] ); ?>
													<div class="ds-row">
														<div class="ds-col-12">
															<label class="ds-radio">
																<input
																	name="dsdi_settings[general][category_template]"
																	type="radio"
																	value="list"
																	<?php echo ( 'list' === $category_template ? ' checked="checked"' : ''); ?> />
																	<span>List View</span>
															</label>
														</div>
													</div>
													<div class="ds-row ds-mt-1">
														<div class="ds-col-12">
															<label class="ds-radio">
																<input
																	data-ds_block_toggler="category_template_grid"
																	name="dsdi_settings[general][category_template]"
																	type="radio"
																	value="grid"
																	<?php echo ( 'grid' === $category_template ? ' checked="checked"' : ''); ?> />
																	<span>Grid View</span>
															</label>
														</div>
													</div>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Load Count', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<?php $load_condition = ( empty( $dsdi->settings['general']['load_condition'] ) ? 'all' : $dsdi->settings['general']['load_condition'] ); ?>
													<div class="ds-row">
														<div class="ds-col-12">
															<label class="ds-radio">
																<input
																	name="dsdi_settings[general][load_condition]"
																	type="radio"
																	value="all"
																	<?php echo ( 'all' === $load_condition ? ' checked="checked"' : ''); ?> />
																	<span>Load All</span>
															</label>
														</div>
													</div>
													<div class="ds-row ds-mt-1">
														<div class="ds-col-12">
															<label class="ds-radio">
																<input
																	name="dsdi_settings[general][load_condition]"
																	type="radio"
																	value="paginated"
																	<?php echo ( 'paginated' === $load_condition ? ' checked="checked"' : ''); ?> />
																	<span>
																		<input
																			class="ds-input-box"
																			type="number"
																			name="dsdi_settings[general][load_count]"
																			value="<?php echo ( !empty( $dsdi->settings['general']['load_count'] ) ? $dsdi->settings['general']['load_count'] : '' ); ?>"
																			placeholder="15" />
																	</span>
															</label>
														</div>
													</div>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
										</div><!-- .ds-block-body -->
									</div><!-- .ds-block -->
								</div><!-- .ds-col -->
							</div><!-- .ds-row -->
							<div class="ds-row<?php echo ( 'grid' === $category_template ? ' active' : ''); ?>"
								data-ds_block_toggler_block="category_template_grid">
								<div class="ds-col ds-mb-2">
									<div class="ds-block">
										<div class="ds-block-title">
											<h2>
												<span class="dashicons dashicons-networking"></span>
												<?php _e( 'Grid Settings:', DSSM_SLUG ); ?>
											</h2>
										</div>
										<div class="ds-block-body">
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pr-lg-2">
													<?php _e( 'Enable Featured Images', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<label class="ds-toggler">
														<input
															name="dsdi_settings[general][grid][featured_images]"
															type="checkbox"
															value="1"
															<?php echo ( !empty( $dsdi->settings['general']['grid']['featured_images'] ) ? ' checked="checked"' : ''); ?> />
															<span></span>
													</label>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pr-lg-2">
													<?php _e( 'Enable Featured Images', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<select class="ds-input-box" name="dsdi_settings[general][grid][columns]">
														<?php
														$columns = array( 6, 4, 3, 2, 1 );

														foreach ( $columns as $column ) {
															echo '<option
																value="' . $column . '"' .
																( ( int )$dsdi->settings['general']['grid']['columns'] === $column ? ' selected="selected"' : '' ) . '>' .
																	$column . ' Column' . ( 1 !== $column ? 's' : '' ) .
															'</option>';
														}
														?>
													</select>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
										</div><!-- .ds-block-body -->
									</div><!-- .ds-block -->
								</div>
							</div>
						</div><!-- #tab-<?php echo sanitize_title( $tabs[0] ); ?> -->
						<?php
						/*
						████████  █████  ██████         ██████  ███████ ███████ ██  ██████  ███    ██
						   ██    ██   ██ ██   ██ ██     ██   ██ ██      ██      ██ ██       ████   ██
						   ██    ███████ ██████         ██   ██ █████   ███████ ██ ██   ███ ██ ██  ██
						   ██    ██   ██ ██   ██ ██     ██   ██ ██           ██ ██ ██    ██ ██  ██ ██
						   ██    ██   ██ ██████         ██████  ███████ ███████ ██  ██████  ██   ████
						*/
						?>
						<div id="tab-<?php echo sanitize_title( $tabs[1] ); ?>" class="ds-tab-content<?php echo ( $active_tab === sanitize_title( $tabs[1] ) ? ' active' : '' ); ?>">
							<div class="ds-row ds-mb-2">
								<div class="ds-col">
									<div class="ds-block">
										<div class="ds-block-body">
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pr-lg-2">
													<?php _e( 'Maximum Width', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="ds-input-box"
														name="dsdi_settings[design][max_width]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['max_width'] ) ? $dsdi->settings['design']['max_width'] : '' ); ?>"
														placeholder="1260px" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Wrapper Padding', DSDI_SLUG ); ?>:<br />
													<small>(Any valid CSS unit, e.g. px or %)</small>
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<div class="ds-row">
														<label class="ds-col-12 ds-col-lg-6">
															<div class="ds-row ds-flex-align-center">
																<div class="ds-col-3 ds-text-lg-right">Top:</div>
																<div class="ds-col-9">
																	<input
																		class="ds-input-box"
																		name="dsdi_settings[design][padding][top]"
																		type="text"
																		value="<?php echo ( !empty( $dsdi->settings['design']['padding']['top'] ) ? $dsdi->settings['design']['padding']['top'] : '' ); ?>"
																		placeholder="30px" />
																</div>
															</div>
														</label>
														<label class="ds-col-12 ds-col-lg-6 ds-mt-1 ds-mt-lg-0">
															<div class="ds-row ds-flex-align-center">
																<div class="ds-col-3 ds-text-lg-right">Right:</div>
																<div class="ds-col-9">
																	<input
																		class="ds-input-box"
																		name="dsdi_settings[design][padding][right]"
																		type="text"
																		value="<?php echo ( !empty( $dsdi->settings['design']['padding']['right'] ) ? $dsdi->settings['design']['padding']['right'] : '' ); ?>"
																		placeholder="30px" />
																</div>
															</div>
														</label>
													</div>
													<div class="ds-row ds-mt-1">
														<label class="ds-col-12 ds-col-lg-6">
															<div class="ds-row ds-flex-align-center">
																<div class="ds-col-3 ds-text-lg-right">Bottom:</div>
																<div class="ds-col-9">
																	<input
																		class="ds-input-box"
																		name="dsdi_settings[design][padding][bottom]"
																		type="text"
																		value="<?php echo ( !empty( $dsdi->settings['design']['padding']['bottom'] ) ? $dsdi->settings['design']['padding']['bottom'] : '' ); ?>"
																		placeholder="30px" />
																</div>
															</div>
														</label>
														<label class="ds-col-12 ds-col-lg-6 ds-mt-1 ds-mt-lg-0">
															<div class="ds-row ds-flex-align-center">
																<div class="ds-col-3 ds-text-lg-right">Left:</div>
																<div class="ds-col-9">
																	<input
																		class="ds-input-box"
																		name="dsdi_settings[design][padding][left]"
																		type="text"
																		value="<?php echo ( !empty( $dsdi->settings['design']['padding']['left'] ) ? $dsdi->settings['design']['padding']['left'] : '' ); ?>"
																		placeholder="30px" />
																</div>
															</div>
														</label>
													</div>
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-pb-1 ds-mb-1 ds-bb ds-mt-1 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Text Color', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="wp-color-picker"
														data-alpha="true"
														name="dsdi_settings[design][text_color]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['text_color'] ) ? $dsdi->settings['design']['text_color'] : '#515151' ); ?>"
														placeholder="#515151" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Button Color', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="wp-color-picker"
														data-alpha="true"
														name="dsdi_settings[design][button_color_bg]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['button_color_bg'] ) ? $dsdi->settings['design']['button_color_bg'] : '#fff' ); ?>"
														placeholder="#fff" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-mt-1 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Button Hover Color', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="wp-color-picker"
														data-alpha="true"
														name="dsdi_settings[design][button_color_bg_hover]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['button_color_bg_hover'] ) ? $dsdi->settings['design']['button_color_bg_hover'] : '#515151' ); ?>"
														placeholder="#515151" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-mt-5 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Button Text Color', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="wp-color-picker"
														data-alpha="true"
														name="dsdi_settings[design][button_color_text]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['button_color_text'] ) ? $dsdi->settings['design']['button_color_text'] : '#515151' ); ?>"
														placeholder="#515151" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
											<div class="ds-row ds-flex-align-center ds-mt-1 ds-ml-auto ds-mr-auto">
												<div class="ds-col-12 ds-col-lg-4 ds-p-0 ds-pb-1 ds-pb-lg-0 ds-pr-lg-2">
													<?php _e( 'Button Text Hover Color', DSDI_SLUG ); ?>:
												</div>
												<div class="ds-col-12 ds-col-lg-8 ds-p-0">
													<input
														class="wp-color-picker"
														data-alpha="true"
														name="dsdi_settings[design][button_color_text_hover]"
														type="text"
														value="<?php echo ( !empty( $dsdi->settings['design']['button_color_text_hover'] ) ? $dsdi->settings['design']['button_color_text_hover'] : '#fff' ); ?>"
														placeholder="#fff" />
												</div><!-- .ds-col -->
											</div><!-- .ds-row -->
										</div><!-- .ds-block-body -->
									</div><!-- .ds-block -->
								</div><!-- .ds-col -->
							</div><!-- .ds-row -->
						</div><!-- #tab-<?php echo sanitize_title( $tabs[0] ); ?> -->
						<div class="ds-row dsdi-sticky-bottom">
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
					</form><!-- #dsdi-form-main -->
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
										<?php _e( 'Support', DSDI_SLUG ); ?>
									</h2>
								</div>
								<div class="ds-block-body">
									<?php _e( 'If you require assistance please open a support ticket on the divSpot website by filling in the <a href="https://www.divspot.co.za/support" target="_blank">support form</a>.', DSDI_SLUG ); ?>
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
										<?php _e( 'Review', DSDI_SLUG ); ?>
									</h2>
								</div>
								<div class="ds-block-body">
									<?php _e( 'Thank you for using divSpot. If you like our plugins please support us by <a href="https://wordpress.org/plugins/ds-directory/#reviews" target="_blank">submitting a review</a>.', DSDI_SLUG ); ?>
								</div><!-- .ds-block-body -->
							</div><!-- .ds-block -->
						</div><!-- .ds-col -->
					</div><!-- .ds-row -->
				</div><!-- .ds-col -->
			</div><!-- .ds-row -->
		</div><!-- .ds-container -->
	</div><!-- .wrap -->
</div><!-- .ds-wrapper -->
