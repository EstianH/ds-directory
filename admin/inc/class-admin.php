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

		// Register the store post type & taxonomies.
		add_action( 'init', array( $this, 'register_store_post_type' ) );
		add_action( 'init', array( $this, 'register_store_post_taxonomies' ) );

		// Handle setting fields on edit/add-new pages.
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
			// Return early if an irrelevant page was opened.
			if (
				'store_page_' . DSSD_SLUG !== $hook_suffix
				&& (
					        empty( $_GET['post_type'] )
					|| 'store' !== $_GET['post_type']
				)
			)
				return;

			// WP assets.
			wp_enqueue_media();                    // WP Media
			wp_enqueue_script( 'jquery-form' );    // WP jQuery for forms handling.
			wp_enqueue_style( 'wp-color-picker' ); // WP Color Picker.

			// Plugin assets.
			wp_enqueue_script( 'dssd-script', DSSD_URL . 'admin/assets/js/script.js',  array( 'jquery-core', 'wp-color-picker-alpha' ), DSSD_VERSION );
			 wp_enqueue_style( 'dssd-style',  DSSD_URL . 'admin/assets/css/style.css', array(),                                         DSSD_VERSION );

			// Vendor assets.
			wp_enqueue_script( 'dsc-script', DSSD_URL . 'admin/assets/vendors/ds-core/js/script.js',  array( 'jquery-core' ), DSSD_VERSION );
			 wp_enqueue_style(  'dsc-style', DSSD_URL . 'admin/assets/vendors/ds-core/css/style.css', array(),                DSSD_VERSION );
			wp_enqueue_script(
				'wp-color-picker-alpha',
				DSSD_URL . 'admin/assets/vendors/wp-color-picker-alpha/wp-color-picker-alpha.min.js',
				array( 'wp-color-picker' ),
				DSSD_VERSION
			); // Overriden/Extended WP Color Picker
		} );

		// Filters
		add_filter( 'plugin_action_links_' . DSSD_BASENAME, array( $this, 'register_plugin_action_links' ), 10, 1 ); // Add plugin list settings link.

		// Register plugin settings.
		register_setting( 'dssd_settings', 'dssd_settings' );
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
				include DSSD_ADMIN . 'templates/settings.php';
			},
			99                                          // $position
		);
	}

	/**
	 * Handle plugin activation.
	 *
	 * @access public
	 */
	public function activate() {
		$dssd = DS_STORE_DIRECTORY::get_instance();
		include DSSD_ROOT . 'admin/default-settings.php'; // Fetch $default_settings.

		update_option( 'dssd_version', DSSD_VERSION );

		$db_settings = get_option( 'dssd_settings' );

		if ( empty( $db_settings ) ) {
			update_option( 'dssd_settings', $default_settings );
			$db_settings = $default_settings;
		}

		// Refresh cached settings.
		$dssd->settings = $db_settings;
	}

	/**
	 * Handle plugin deactivation.
	 *
	 * @access public
	 */
	public function deactivate() {

	}

	/**
	 * Update database settings if versions differ.
	 *
	 * @access public
	 */
	public function update_settings_maybe() {
		if ( version_compare( get_option( 'dssd_version' ), DSSD_VERSION, '<' ) )
			$this->activate();
	}

	/**
	 * Add plugin action links to the plugin page.
	 *
	 * @access public
	 * @param array  $links Plugin links.
	 * @return array $links Updated plugin links.
	 */
	public function register_plugin_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( '/edit.php' ) ) . '?page=' . DSSD_SLUG . '">' . __( 'Settings', DSSD_SLUG ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}

	/**
	 * Register the store post type.
	 *
	 * @access public
	 */
	public function register_store_post_type() {
		$args = array(
			'label'               => __( 'store',  DSSD_SLUG ),
			'description'         => __( 'Stores', DSSD_SLUG ),
			'labels'              => array(
				'name'                => __( 'Stores',             DSSD_SLUG ),
				'singular_name'       => __( 'Store',              DSSD_SLUG ),
				'menu_name'           => __( 'Stores',             DSSD_SLUG ),
				'parent_item_colon'   => __( 'Parent Store',       DSSD_SLUG ),
				'all_items'           => __( 'All Stores',         DSSD_SLUG ),
				'view_item'           => __( 'View Stores',        DSSD_SLUG ),
				'add_new_item'        => __( 'Add New Store',      DSSD_SLUG ),
				'add_new'             => __( 'Add New',            DSSD_SLUG ),
				'edit_item'           => __( 'Edit Store',         DSSD_SLUG ),
				'update_item'         => __( 'Update Store',       DSSD_SLUG ),
				'search_items'        => __( 'Search Store',       DSSD_SLUG ),
				'not_found'           => __( 'Not Found',          DSSD_SLUG ),
				'not_found_in_trash'  => __( 'Not found in Trash', DSSD_SLUG )
			),
			'supports'            => array(
				'title',
				'editor',
				// 'excerpt',
				// 'author',
				'thumbnail',
				'revisions'
				// 'custom-fields'
			),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => true,
			'can_export'          => true,
			'exclude_from_search' => false,
			'yarpp_support'       => true,
			// 'taxonomies' 	        => array( 'post_tag' ),
			'publicly_queryable'  => true,
			'capability_type'     => 'post'
		);

		register_post_type( 'store', $args );
	}

	/**
	 * Register the store post taxonomies.
	 *
	 * @access public
	 */
	public function register_store_post_taxonomies() {
		register_taxonomy( 'store_directory_category', array( 'store' ), array(
			'hierarchical'      => true,
			// 'labels'            => array(
			// 	'name'              => _x( 'Directories', 'taxonomy general name',  DSSD_SLUG ),
			// 	'singular_name'     => _x( 'Directory',   'taxonomy singular name', DSSD_SLUG ),
			// 	'search_items'      => __( 'Search Directories',   DSSD_SLUG ),
			// 	'not_found'         => __( 'No directories found', DSSD_SLUG ),
			// 	'all_items'         => __( 'All Directories',      DSSD_SLUG ),
			// 	'parent_item'       => __( 'Parent Directory',     DSSD_SLUG ),
			// 	'parent_item_colon' => __( 'Parent Directory:',    DSSD_SLUG ),
			// 	'edit_item'         => __( 'Edit Directory',       DSSD_SLUG ),
			// 	'update_item'       => __( 'Update Directory',     DSSD_SLUG ),
			// 	'add_new_item'      => __( 'Add New Directory',    DSSD_SLUG ),
			// 	'view_item'         => __( 'View Directory',       DSSD_SLUG ),
			// 	'new_item_name'     => __( 'New Directory Name',   DSSD_SLUG ),
			// 	'menu_name'         => __( 'Directories',          DSSD_SLUG ),
		  // ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'store-directory' ),
		) );
	}

	/**
	 * Add field settings to the category add new page.
	 *
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function category_add_form_fields( $taxonomy ) {
		// echo '<div class="form-field ds-mt-5">';
		// 	include DSSD_ADMIN . 'templates/form-fields-category.php';
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
		// 	include DSSD_ADMIN . 'templates/form-fields-category.php';
		// echo '</td></tr>';
	}

	/**
	 * Save category field settings.
	 *
	 * @param int    $term_id Term ID.
	 * @param string $tt_id   Term taxonomy ID.
	 */
	public function category_save_form_fields( $term_id, $tt_id ) {
		// if (
		// 				empty( $_POST['post_type'] )
		// 	|| 'store' !== $_POST['post_type']
		// )
		// 	return;
		//
		// if ( !empty( $_POST['option_1'] ) )
		// 	update_term_meta( $term_id, 'option_1', sanitize_text_field( $_POST['option_1'] ) );
	}
}


/*
 █████   ██████ ████████ ██ ██    ██  █████  ████████ ███████     ██ ██████  ███████  █████   ██████ ████████ ██ ██    ██  █████  ████████ ███████
██   ██ ██         ██    ██ ██    ██ ██   ██    ██    ██         ██  ██   ██ ██      ██   ██ ██         ██    ██ ██    ██ ██   ██    ██    ██
███████ ██         ██    ██ ██    ██ ███████    ██    █████     ██   ██   ██ █████   ███████ ██         ██    ██ ██    ██ ███████    ██    █████
██   ██ ██         ██    ██  ██  ██  ██   ██    ██    ██       ██    ██   ██ ██      ██   ██ ██         ██    ██  ██  ██  ██   ██    ██    ██
██   ██  ██████    ██    ██   ████   ██   ██    ██    ███████ ██     ██████  ███████ ██   ██  ██████    ██    ██   ████   ██   ██    ██    ███████
*/
/**
 * Register plugin activation hook.
 */
register_activation_hook( __FILE__, array( 'DS_STORE_DIRECTORY_ADMIN', 'activate' ) );

/**
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, array( 'DS_STORE_DIRECTORY_ADMIN', 'deactivate' ) );
