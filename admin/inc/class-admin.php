<?php
if( !defined( 'ABSPATH' ) ) exit;


/*
██████  ███████ ██████  ██      █████  ██████  ███    ███ ██ ███    ██
██   ██ ██      ██   ██ ██     ██   ██ ██   ██ ████  ████ ██ ████   ██
██   ██ ███████ ██   ██ ██     ███████ ██   ██ ██ ████ ██ ██ ██ ██  ██
██   ██      ██ ██   ██ ██     ██   ██ ██   ██ ██  ██  ██ ██ ██  ██ ██
██████  ███████ ██████  ██     ██   ██ ██████  ██      ██ ██ ██   ████
*/
class DS_DIRECTORY_ADMIN {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 * @var DS_DIRECTORY_ADMIN
	 */
	private static $instance;

	/**
	 * Returns the instance of the class.
	 *
	 * @access public
	 * @static
	 * @return DS_DIRECTORY_ADMIN $instance
	 */
	public static function get_instance() {
		if ( NULL === self::$instance )
			self::$instance = new DS_DIRECTORY_ADMIN();

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		// Version check settings.
		$this->update_settings_maybe();

		// Register the admin settings page.
		add_action( 'admin_menu', array( $this, 'render_admin_menu' ) );

		// Handle setting fields on edit/add-new directory items.
		add_action( 'add_meta_boxes'     , array( $this, 'edit_form_fields' ), 10 );
		add_action( 'save_post_dsdi_item', array( $this, 'save_form_fields' ), 10, 3 );

		// Handle setting fields on edit/add-new directories.
		add_action( 'ds_directory_add_form_fields',  array( $this, 'directory_add_form_fields' ),  10, 1 );
		add_action( 'ds_directory_edit_form_fields', array( $this, 'directory_edit_form_fields' ), 10, 2 );
		add_action( 'edited_ds_directory', array( $this, 'directory_save_form_fields' ), 10, 2 );
		add_action( 'create_ds_directory', array( $this, 'directory_save_form_fields' ), 10, 2 );

		// Add theme specific "Avada Fusion" options to our custom taxonomies.
		add_filter( 'fusion_tax_meta_allowed_screens', array( $this, 'directory_fusion_options' ), 0, 1 );

		/**
		 * Enqueue admin assets.
		 *
		 * @param string $hook_suffix The current admin page.
		 */
		add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
			global $post;

			// Return early if an irrelevant page was opened.
			if (
				   'dsdi_item_page_' . DSDI_SLUG !== $hook_suffix
				&& (
					   empty( $post )
					|| 'dsdi_item' !== $post->post_type
				)
			)
				return;

			// WP assets.
			wp_enqueue_media();                    // WP Media
			wp_enqueue_script( 'jquery-form' );    // WP jQuery for forms handling.
			wp_enqueue_style( 'wp-color-picker' ); // WP Color Picker.

			// Plugin assets.
			wp_enqueue_script( 'dsdi-script', DSDI_ROOT_URL . 'admin/assets/js/script.js',  array( 'jquery-core', 'wp-color-picker-alpha' ), DSDI_VERSION );
			 wp_enqueue_style( 'dsdi-style',  DSDI_ROOT_URL . 'admin/assets/css/style.css', array(),                                         DSDI_VERSION );

			// Vendor assets.
			wp_enqueue_script( 'dsc-script', DSDI_ROOT_URL . 'admin/assets/vendors/ds-core/js/script.js',  array( 'jquery-core' ), DSDI_VERSION );
			 wp_enqueue_style(  'dsc-style', DSDI_ROOT_URL . 'admin/assets/vendors/ds-core/css/style.css', array(),                DSDI_VERSION );
			wp_enqueue_script(
				'wp-color-picker-alpha',
				DSDI_ROOT_URL . 'admin/assets/vendors/wp-color-picker-alpha/wp-color-picker-alpha.min.js',
				array( 'wp-color-picker' ),
				DSDI_VERSION
			); // Overriden/Extended WP Color Picker
		} );

		// Filters
		add_filter( 'plugin_action_links_' . DSDI_BASENAME, array( $this, 'register_plugin_action_links' ), 10, 1 ); // Add plugin list settings link.

		// Handle plugin setting updates.
		add_action( 'wp_ajax_dsdi_settings_update', array( $this, 'settings_update' ), 10 );
	}

	/**
	 * Add plugin admin menu items.
	 *
	 * @access public
	 * @uses $GLOBALS
	 */
	public function render_admin_menu() {
		// Return early if the slug already exists.
		if ( !empty( $GLOBALS['admin_page_hooks'][DSDI_SLUG] ) )
			return;

		add_submenu_page(
			'edit.php?post_type=dsdi_item',             // $parent_slug
			DSDI_TITLE,                                 // $page_title
			'Settings',                                 // $menu_title
			'edit_plugins',                             // $capability
			DSDI_SLUG,                                  // $menu_slug
			function() {                                // $function
				include DSDI_ADMIN_PATH . 'templates/settings.php';
			},
			99                                          // $position
		);
	}

	/**
	 * Update database settings if versions differ.
	 *
	 * @access public
	 */
	public function update_settings_maybe() {
		if ( version_compare( get_option( 'dsdi_version' ), DSDI_VERSION, '<' ) ) {
			$dsdi = DS_DIRECTORY::get_instance();
			$dsdi->activate();
		}
	}

	/**
	 * Add plugin action links to the plugin page.
	 *
	 * @access public
	 * @param array  $links Plugin links.
	 * @return array $links Updated plugin links.
	 */
	public function register_plugin_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( '/edit.php' ) ) . '?page=' . DSDI_SLUG . '&post_type=dsdi_item">' . __( 'Settings', DSDI_SLUG ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}

	/**
	 * Add field settings to the directory add new page.
	 *
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function directory_add_form_fields( $taxonomy ) {
		// echo '<div class="form-field ds-mt-5">';
		// 	include DSDI_ADMIN_PATH . 'templates/form-fields-directory.php';
		// echo '</div>';
	}

	/**
	 * Add field settings to the directory edit page.
	 *
	 * @param WP_Term $term     Current taxonomy term object.
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function directory_edit_form_fields( $term, $taxonomy ) {
		// echo '<tr class="form-field"><td class="ds-pl-0 ds-pr-0" colspan="2">';
		// 	include DSDI_ADMIN_PATH . 'templates/form-fields-directory.php';
		// echo '</td></tr>';
	}

	/**
	 * Save directory field settings.
	 *
	 * @param int    $term_id Term ID.
	 * @param string $tt_id   Term taxonomy ID.
	 */
	public function directory_save_form_fields( $term_id, $tt_id ) {
		// if ( !empty( $_POST['option_1'] ) )
		// 	update_term_meta( $term_id, 'option_1', sanitize_text_field( $_POST['option_1'] ) );
	}

	/**
	 * Add field settings to the dsdi_item edit/add-new page.
	 *
	 * @uses edit_form_fields_template() Fetches the HTML fields.
	 */
	public function edit_form_fields() {
		add_meta_box(
			'dsdi_options',           // Unique ID
			'Directory Item Options', // Box title
			array( $this, 'edit_form_fields_template' ),
			'dsdi_item'                   // Post type
		);
	}

	/**
	 * Render field settings HTML.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function edit_form_fields_template( $post ) {
		include DSDI_ADMIN_PATH . 'templates/form-fields-item.php';
	}

	/**
	 * Save item field settings.
	 *
	 * @param int     $post_id Post id.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function save_form_fields( $post_id, $post, $update ) {
		// Return early on auto saves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Return early on irrelevant post_type.
		if ( 'dsdi_item' !== $post->post_type )
			return;

		// Return early for unauthorized user requests.
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		// Return early if the metabox nonce fails.
		if (
			   empty( $_POST['dsdi_options_save_nonce'] )
			|| !wp_verify_nonce( $_POST['dsdi_options_save_nonce'], 'dsdi_save_post' )
		)
			return;

		if ( array_key_exists( 'dsdi_options', $_POST ) ) {
			foreach ( $_POST['dsdi_options'] as &$store_option )
				$store_option = sanitize_text_field( $store_option );

			update_post_meta(
				$post_id,
				'dsdi_options',
				$_POST['dsdi_options']
			);
		}
	}

	/**
	 * Handle plugin setting updates.
	 */
	public function settings_update() {
		// Return early if no settings have been posted.
		if ( empty( $_POST['dsdi_settings'] ) )
			return;

		// Return early if the form nonce fails.
		if (
			   empty( $_POST['dsdi_settings_nonce'] )
			|| !wp_verify_nonce( $_POST['dsdi_settings_nonce'], 'dsdi_settings_update' )
		)
			return;

		// Add the default CSS unit to relevant fields.
		foreach ( $_POST['dsdi_settings']['design']['padding'] as $side => &$padding )
			if ( '0' === $padding )
				$padding .= 'px';

		// If this string value contains only numbers.
		if ( ctype_digit( $_POST['dsdi_settings']['design']['max_width'] ) )
			$_POST['dsdi_settings']['design']['max_width'] .= 'px';

		// If the paginated option is selected without adding a number, save 15 as the default.
		if (
			   'all' !== $_POST['dsdi_settings']['general']['load_condition']
			&& empty( $_POST['dsdi_settings']['general']['load_count'] )
		)
			$_POST['dsdi_settings']['general']['load_count'] = 15;

		update_option( 'dsdi_settings', $_POST['dsdi_settings'] );
	}



	/**
	 * Add theme specific "Avada Fusion" options to our custom taxonomies.
	 */
	public function directory_fusion_options( $taxonomies ) {
		$taxonomies[] = 'ds_directory';
		return $taxonomies;
	}
}
