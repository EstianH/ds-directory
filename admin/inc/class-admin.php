<?php
if( !defined( 'ABSPATH' ) ) exit;


/*
██████  ███████ ███████ ██████       █████  ██████  ███    ███ ██ ███    ██
██   ██ ██      ██      ██   ██     ██   ██ ██   ██ ████  ████ ██ ████   ██
██   ██ ███████ ███████ ██   ██     ███████ ██   ██ ██ ████ ██ ██ ██ ██  ██
██   ██      ██      ██ ██   ██     ██   ██ ██   ██ ██  ██  ██ ██ ██  ██ ██
██████  ███████ ███████ ██████      ██   ██ ██████  ██      ██ ██ ██   ████
*/
class DS_STORE_DIRECTORY_ADMIN {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 * @var DS_STORE_DIRECTORY_ADMIN
	 */
	private static $instance;

	/**
	 * Returns the instance of the class.
	 *
	 * @access public
	 * @static
	 * @return DS_STORE_DIRECTORY_ADMIN $instance
	 */
	public static function get_instance() {
		if ( NULL === self::$instance )
			self::$instance = new DS_STORE_DIRECTORY_ADMIN();

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

		// Handle setting fields on edit/add-new stores.
		add_action( 'add_meta_boxes',  array( $this, 'store_edit_form_fields' ), 10 );
		add_action( 'save_post_store', array( $this, 'store_save_form_fields' ), 10, 3 );

		// Handle setting fields on edit/add-new categories.
		add_action( 'store_directory_category_add_form_fields',  array( $this, 'category_add_form_fields' ),  10, 1 );
		add_action( 'store_directory_category_edit_form_fields', array( $this, 'category_edit_form_fields' ), 10, 2 );
		add_action( 'edited_store_directory_category', array( $this, 'category_save_form_fields' ), 10, 2 );
		add_action( 'create_store_directory_category', array( $this, 'category_save_form_fields' ), 10, 2 );

		/**
		 * Enqueue admin assets.
		 *
		 * @param string $hook_suffix The current admin page.
		 */
		add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
			global $post;

			// Return early if an irrelevant page was opened.
			if (
				'store_page_' . DSSD_SLUG !== $hook_suffix
				&& (
					empty( $post )
					|| 'store' !== $post->post_type
				)
			)
				return;

			// WP assets.
			wp_enqueue_media();                    // WP Media
			wp_enqueue_script( 'jquery-form' );    // WP jQuery for forms handling.
			wp_enqueue_style( 'wp-color-picker' ); // WP Color Picker.

			// Plugin assets.
			wp_enqueue_script( 'dssd-script', DSSD_ROOT_URL . 'admin/assets/js/script.js',  array( 'jquery-core', 'wp-color-picker-alpha' ), DSSD_VERSION );
			 wp_enqueue_style( 'dssd-style',  DSSD_ROOT_URL . 'admin/assets/css/style.css', array(),                                         DSSD_VERSION );

			// Vendor assets.
			wp_enqueue_script( 'dsc-script', DSSD_ROOT_URL . 'admin/assets/vendors/ds-core/js/script.js',  array( 'jquery-core' ), DSSD_VERSION );
			 wp_enqueue_style(  'dsc-style', DSSD_ROOT_URL . 'admin/assets/vendors/ds-core/css/style.css', array(),                DSSD_VERSION );
			wp_enqueue_script(
				'wp-color-picker-alpha',
				DSSD_ROOT_URL . 'admin/assets/vendors/wp-color-picker-alpha/wp-color-picker-alpha.min.js',
				array( 'wp-color-picker' ),
				DSSD_VERSION
			); // Overriden/Extended WP Color Picker
		} );

		// Filters
		add_filter( 'plugin_action_links_' . DSSD_BASENAME, array( $this, 'register_plugin_action_links' ), 10, 1 ); // Add plugin list settings link.

		// Handle plugin setting updates.
		add_action( 'wp_ajax_dssd_settings_update', array( $this, 'dssd_settings_update' ), 10 );
	}

	/**
	 * Add plugin admin menu items.
	 *
	 * @access public
	 * @uses $GLOBALS
	 */
	public function render_admin_menu() {
		// Return early if the slug already exists.
		if ( !empty( $GLOBALS['admin_page_hooks'][DSSD_SLUG] ) )
			return;

		add_submenu_page(
			'edit.php?post_type=store',                 // $parent_slug
			DSSD_TITLE,                                 // $page_title
			'Settings',                                 // $menu_title
			'edit_plugins',                             // $capability
			DSSD_SLUG,                                  // $menu_slug
			function() {                                // $function
				include DSSD_ADMIN_PATH . 'templates/settings.php';
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
		if ( version_compare( get_option( 'dssd_version' ), DSSD_VERSION, '<' ) ) {
			$dssd = DS_STORE_DIRECTORY::get_instance();
			$dssd->activate();
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
		$settings_link = '<a href="' . esc_url( admin_url( '/edit.php' ) ) . '?page=' . DSSD_SLUG . '&post_type=store">' . __( 'Settings', DSSD_SLUG ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}

	/**
	 * Add field settings to the category add new page.
	 *
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function category_add_form_fields( $taxonomy ) {
		// echo '<div class="form-field ds-mt-5">';
		// 	include DSSD_ADMIN_PATH . 'templates/form-fields-category.php';
		// echo '</div>';
	}

	/**
	 * Add field settings to the category edit page.
	 *
	 * @param WP_Term $term     Current taxonomy term object.
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function category_edit_form_fields( $term, $taxonomy ) {
		// echo '<tr class="form-field"><td class="ds-pl-0 ds-pr-0" colspan="2">';
		// 	include DSSD_ADMIN_PATH . 'templates/form-fields-category.php';
		// echo '</td></tr>';
	}

	/**
	 * Save category field settings.
	 *
	 * @param int    $term_id Term ID.
	 * @param string $tt_id   Term taxonomy ID.
	 */
	public function category_save_form_fields( $term_id, $tt_id ) {
		// if ( !empty( $_POST['option_1'] ) )
		// 	update_term_meta( $term_id, 'option_1', sanitize_text_field( $_POST['option_1'] ) );
	}

	/**
	 * Add field settings to the store edit/add-new page.
	 *
	 * @uses store_edit_form_fields_template() Fetches the HTML fields.
	 */
	public function store_edit_form_fields() {
		add_meta_box(
			'dssd_store_options', // Unique ID
			'Store Options', // Box title
			array( $this, 'store_edit_form_fields_template' ),
			'store'          // Post type
		);
	}

	/**
	 * Render store field settings HTML.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function store_edit_form_fields_template( $post ) {
		include DSSD_ADMIN_PATH . 'templates/form-fields-store.php';
	}

	/**
	 * Save store field settings.
	 *
	 * @param int     $post_id Post id.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function store_save_form_fields( $post_id, $post, $update ) {
		// Return early on auto saves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Return early if the post_type does not equal store.
		if ( 'store' !== $post->post_type )
			return;

		// Return early for unauthorized user requests.
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		// Return early if the metabox nonce fails.
		if (
			empty( $_POST['store_options_save_nonce'] )
			|| !wp_verify_nonce( $_POST['store_options_save_nonce'], 'save_post' )
		)
			return;

		if ( array_key_exists( 'store_options', $_POST ) ) {
			foreach( $_POST['store_options'] as &$store_option ) {
				$store_option = sanitize_text_field( $store_option );
			}

			update_post_meta(
				$post_id,
				'store_options',
				$_POST['store_options']
			);
		}
	}

	/**
	 * Handle plugin setting updates.
	 */
	public function dssd_settings_update() {
		// Return early if no settings have been posted.
		if ( empty( $_POST['dssd_settings'] ) )
			return;

		// Return early if the form nonce fails.
		if (
			empty( $_POST['dssd_settings_nonce'] )
			|| !wp_verify_nonce( $_POST['dssd_settings_nonce'], 'dssd_settings_update' )
		)
			return;

		// Add the default CSS unit to relevant fields.
		foreach ( $_POST['dssd_settings']['design']['padding'] as $side => &$padding )
			if ( '0' === $padding )
				$padding .= 'px';

		// If this string value contains only numbers.
		if ( ctype_digit( $_POST['dssd_settings']['design']['max_width'] ) )
			$_POST['dssd_settings']['design']['max_width'] .= 'px';

		update_option( 'dssd_settings', $_POST['dssd_settings'] );
	}
}
