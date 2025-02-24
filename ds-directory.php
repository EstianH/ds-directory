<?php
/*
Plugin Name:  DS Directory
Plugin URI:   https://www.divspot.co.za/plugin-ds-floated-menu/
Description:  DS Directory adds to your WordPress installation a clean and flexible directory (e.g. Shops, Services, Products, Cars).
Version:      1.1.2
Author:       divSpot
Author URI:   https://www.divspot.co.za
License:      GPLv3 or later
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
*/

if( !defined( 'ABSPATH' ) ) exit;


/*
██████  ███████ ███████ ██ ███    ██ ██ ████████ ██  ██████  ███    ██ ███████
██   ██ ██      ██      ██ ████   ██ ██    ██    ██ ██    ██ ████   ██ ██
██   ██ █████   █████   ██ ██ ██  ██ ██    ██    ██ ██    ██ ██ ██  ██ ███████
██   ██ ██      ██      ██ ██  ██ ██ ██    ██    ██ ██    ██ ██  ██ ██      ██
██████  ███████ ██      ██ ██   ████ ██    ██    ██  ██████  ██   ████ ███████
*/
if( !defined( 'DIVSPOT_URL' ) )
	define( 'DIVSPOT_URL', 'https://www.divspot.co.za' );

define( 'DSDI_BASENAME'      , plugin_basename( __FILE__ ) );
define( 'DSDI_ROOT_URL'      , plugins_url( '', DSDI_BASENAME ) . '/' ); // User-Friendly URL
define( 'DSDI_ROOT_PATH'     , __DIR__        . '/' );                   // FTP Path
define( 'DSDI_ADMIN_URL'     , DSDI_ROOT_URL  . 'admin/' );              // User-Friendly URL
define( 'DSDI_ADMIN_PATH'    , DSDI_ROOT_PATH . 'admin/' );              // FTP Path
define( 'DSDI_ASSETS_URL'    , DSDI_ROOT_URL  . 'assets/' );             // User-Friendly URL
define( 'DSDI_ASSETS_PATH'   , DSDI_ROOT_PATH . 'assets/' );             // FTP Path
define( 'DSDI_TEMPLATES_URL' , DSDI_ROOT_URL  . 'templates/' );          // User-Friendly URL
define( 'DSDI_TEMPLATES_PATH', DSDI_ROOT_PATH . 'templates/' );          // FTP Path
define( 'DSDI_TITLE'         , 'DS Directory' );                         // DSDI Title
define( 'DSDI_SLUG'          , sanitize_title( DSDI_TITLE ) );           // Plugin slug.
define( 'DSDI_VERSION'       , '1.1.2' );


/*
██████  ███████ ██████  ██
██   ██ ██      ██   ██ ██
██   ██ ███████ ██   ██ ██
██   ██      ██ ██   ██ ██
██████  ███████ ██████  ██
*/
class DS_DIRECTORY {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 * @var DS_DIRECTORY
	 */
	private static $instance;

	/**
	 * Saved settings.
	 *
	 * @access public
	 */
	public $settings;

	/**
	 * Returns the instance of the class.
	 *
	 * @access public
	 * @static
	 * @return DS_DIRECTORY $instance
	 */
	public static function get_instance() {
		if ( NULL === self::$instance )
			self::$instance = new DS_DIRECTORY();

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		$this->settings = get_option( 'dsdi_settings' );

		// Register the post type & taxonomies.
		add_action( 'init', array( $this, 'register_post_type' )      , 10 );
		add_action( 'init', array( $this, 'register_post_taxonomies' ), 10 );

		// Render the dsdi_item & ds_directory templates.
		add_filter( 'template_include', array( $this, 'get_directory_template' ), 0, 1 );
		add_filter( 'template_include', array( $this, 'get_directory_item_template' ), 0, 1 );

		add_action( 'directories_header', array( $this, 'get_directory_header' ), 10 );

		// Redirect root "/directory" to "all".
		add_action( 'template_redirect', array( $this, 'template_root_redirect' ) );

		// Alter the main WP_Query object to pull relevant dsdi_item's.
		add_action( 'pre_get_posts'  , array( $this, 'update_wp_query'     ), 0, 1 );
		add_action( 'parse_tax_query', array( $this, 'update_wp_tax_query' ), 0, 1 );

		// Enqueue plugin assets.
		add_action( 'wp_enqueue_scripts', function() {
			global $wp_query;

			// Continue only on relevant dsdi_item & ds_directory pages.
			if (
				   empty( $wp_query->query_vars['dsdi_root'] )
				&& empty( $wp_query->query_vars['ds_directory'] )
				&& empty( $wp_query->query_vars['dsdi_item'] )
			)
				return;

			wp_enqueue_script( 'dsdi-script', DSDI_ASSETS_URL . 'js/script.js'                        , array( 'jquery-core' ), DSDI_VERSION );
			 wp_enqueue_style( 'dsdi-style' , DSDI_ASSETS_URL . 'css/style.css'                       , array()               , DSDI_VERSION );
		   wp_enqueue_style( 'dsdi-core'  , DSDI_ADMIN_URL  . 'assets/vendors/ds-core/css/style.css', array( 'dsdi-style' ) , DSDI_VERSION );

			// Setting based styles.
			if ( $dynamic_styles = $this->get_dynamic_styles() )
				wp_add_inline_style( 'dsdi-style', $dynamic_styles );

			// Maybe load Font Awesome.
			if ( !empty( $this->settings['directory']['item_options']['load_fa'] ) )
				wp_enqueue_style( 'dsdi-font-awesome'  , DSDI_ASSETS_URL  . 'vendors/font-awesome/css/all.min.css', array( 'dsdi-style' ) , DSDI_VERSION );
		} );

		// Register the directory shortcode.
		add_shortcode( 'ds_directory', array( $this, 'shortcode_directory' ), 10, 3 );
	}

	/**
	 * Handle plugin activation.
	 *
	 * @access public
	 */
	public function activate() {
		update_option( 'dsdi_version', DSDI_VERSION );

		if ( empty( get_option( 'dsdi_settings' ) ) )
			update_option( 'dsdi_settings', $this->get_default_settings() );

		self::register_post_type();
		self::register_post_taxonomies();
		flush_rewrite_rules();
	}

	/**
	 * Handle plugin deactivation.
	 *
	 * @access public
	 */
	public function deactivate() {
		unregister_post_type( 'dsdi_item' );
		 unregister_taxonomy( 'ds_directory' );
		 flush_rewrite_rules();
	}

	/**
	 * Return plugin default settings.
	 *
	 * @access public
	 */
	public function get_default_settings() {
		return include ( DSDI_ADMIN_PATH . 'default-settings.php' );
	}

	/**
	 * Return dynamic styles.
	 *
	 * @access public
	 */
	public function get_dynamic_styles() {
		return include ( DSDI_ROOT_PATH . 'inc/dynamic-styles.php' );
	}

	/**
	 * Directory shortcode handler.
	 *
	 * @access public
	 *
	 * @param array  $atts    An associative array of attributes, or an empty string if no attributes are given
	 * @param string $content The enclosed content (if the shortcode is used in its enclosing form)
	 * @param string $tag     The shortcode tag, useful for shared callback functions
	 */
	public function shortcode_directory( $atts, $content, $tag ) {
		// $atts_merged = shortcode_atts( array(
		// 	'directory_id' => -1
		// ), $atts );
		//
		// ob_start();
		// include DSDI_ROOT_PATH . 'templates/directory-list.php';
		// return ob_get_clean();
		return '';
	}

	/**
	 * Register the post type.
	 *
	 * @access public
	 */
	static public function register_post_type() {
		$args = array(
			'label'               => __( 'dsdi_item',  DSDI_SLUG ),
			'description'         => __( 'Directory items', DSDI_SLUG ),
			'labels'              => array(
				'name'                => __( 'Directory items'       , DSDI_SLUG ),
				'singular_name'       => __( 'Directory item'        , DSDI_SLUG ),
				'menu_name'           => __( 'Directory items'       , DSDI_SLUG ),
				'parent_item_colon'   => __( 'Parent directory item' , DSDI_SLUG ),
				'all_items'           => __( 'All directory items'   , DSDI_SLUG ),
				'view_item'           => __( 'View directory items'  , DSDI_SLUG ),
				'add_new_item'        => __( 'Add New directory item', DSDI_SLUG ),
				'add_new'             => __( 'Add New'               , DSDI_SLUG ),
				'edit_item'           => __( 'Edit directory item'   , DSDI_SLUG ),
				'update_item'         => __( 'Update directory item' , DSDI_SLUG ),
				'search_items'        => __( 'Search directory item' , DSDI_SLUG ),
				'not_found'           => __( 'Not Found'             , DSDI_SLUG ),
				'not_found_in_trash'  => __( 'Not found in Trash'    , DSDI_SLUG )
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
			'capability_type'     => 'post',
			'menu_icon'           => DSDI_ASSETS_URL . 'images/icon-xs.png',
			'rewrite'             => array(
				'slug'            => 'directory-item'
			)
		);

		register_post_type( 'dsdi_item', $args );
	}

	/**
	 * Register the post taxonomies.
	 *
	 * @access public
	 */
	static public function register_post_taxonomies() {
		register_taxonomy( 'ds_directory', array( 'dsdi_item' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Directories', 'taxonomy general name'        , DSDI_SLUG ),
				'singular_name'     => _x( 'Directory', 'taxonomy singular name', DSDI_SLUG ),
				'search_items'      => __( 'Search directories'           , DSDI_SLUG ),
				'not_found'         => __( 'No directories found'         , DSDI_SLUG ),
				'all_items'         => __( 'All directories'              , DSDI_SLUG ),
				'parent_item'       => __( 'Parent directory'    , DSDI_SLUG ),
				'parent_item_colon' => __( 'Parent directory:'   , DSDI_SLUG ),
				'edit_item'         => __( 'Edit directory'      , DSDI_SLUG ),
				'update_item'       => __( 'Update directory'    , DSDI_SLUG ),
				'add_new_item'      => __( 'Add new directory'   , DSDI_SLUG ),
				'view_item'         => __( 'View directory'      , DSDI_SLUG ),
				'new_item_name'     => __( 'New directory name'  , DSDI_SLUG ),
				'menu_name'         => __( 'Directories'                  , DSDI_SLUG ),
		  ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug' => 'directory'
			)
		) );

		register_taxonomy_for_object_type( 'ds_directory', 'dsdi_item' );

		if ( !term_exists( 'all', 'ds_directory' ) )
			$ds_directory_all = get_term( wp_insert_term( 'All', 'ds_directory', ['slug' => 'all'] )['term_id'] );
		else
			$ds_directory_all = get_term_by( 'slug', 'all', 'ds_directory' );

		if ( empty( get_option( 'default_ds_directory' ) ) )
			update_option( 'default_ds_directory', $ds_directory_all->term_id );
	}

	/**
	 * Register dsdi_item & ds_directory query vars.
	 *
	 * @access public
	 */
	public function get_directory_template( $template ) {
		global $wp_query;

		if (
			   !empty( $wp_query->query_vars['ds_directory'] )
			&& term_exists( $wp_query->query_vars['ds_directory'] )
		)
			return DSDI_ROOT_PATH . 'templates/archive.php';

		return $template;
	}

	/**
	 * Register dsdi_item & ds_directory query vars.
	 *
	 * @access public
	 */
	public function get_directory_item_template( $template ) {
		global $wp_query;

		if ( empty( $wp_query->query_vars['dsdi_item'] ) )
			return $template;

		return DSDI_ROOT_PATH . 'templates/dsdi_item.php';
	}

	/**
	 * Register dsdi_item & ds_directory query vars.
	 *
	 * @access public
	 */
	public function get_directory_header() {
		include ( DSDI_ROOT_PATH . 'templates/directories-header.php' );
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & ds_directory data.
	 *
	 * @access public
	 */
	public function update_wp_query( $query ) {
		if ( is_admin() )
			return;

		if ( !$query->is_main_query() )
			return;

		if ( !empty( $query->query_vars['ds_directory'] ) ) {
			if (
				   !empty( $this->settings['general']['load_condition'] )
				&& 'all' !== $this->settings['general']['load_condition']
			)
				$query->set( 'posts_per_page', $this->settings['general']['load_count'] );
			else
				$query->set( 'posts_per_page', -1 );

			$query->set( 'post_status', 'publish' );
			$query->set( 'post_type'  , 'dsdi_item' );

			if (
				   !empty( $_GET['sort'] )
				&& 'name' === $_GET['sort']
			) {
				$query->set( 'orderby', sanitize_text_field( $_GET['sort'] ) );
				$query->set( 'order'  , ( !empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'ASC' ) );
			} else {
				$query->set( 'orderby', 'name'  );
				$query->set( 'order'  , 'ASC' );
			}
		}
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & ds_directory data.
	 * Handle directory root query (Fetch all directories).
	 *
	 * @access public
	 */
	public function update_wp_tax_query( $query ) {
		if ( is_admin() )
			return;

		if ( !$query->is_main_query() )
			return;

		if (
			   !empty( $query->query_vars['ds_directory'] )
			&& 'all' === $query->query_vars['ds_directory']
		) {
			$terms    = get_terms( array( 'taxonomy' => 'ds_directory' ) );
			$term_ids = array();

			foreach ( $terms as $term )
				$term_ids[] = $term->term_id;

			$tax_query = array(
				'taxonomy'         => 'ds_directory',
				'field'            => 'term_id',
				'terms'            => $term_ids,
				'operator'         => 'IN',
				'include_children' => true
			);
			$query->tax_query->queries[0] = $tax_query;
		}
	}

	/**
	 * // Redirect root "/directory" to "/all".
	 *
	 * @access public
	 */
	public function template_root_redirect() {
		global $wp_query;

		if (
			   !empty( $wp_query->query['name'] )
			&& 'ds_directory' === $wp_query->query['name']
		)
			redirect_to_root();
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & ds_directory data.
	 *
	 * @access public
	 */
	public function redirect_to_root() {
		wp_safe_redirect( esc_url( home_url() . '/directory/all/' ) );
		exit;
	}
}

add_action( 'plugins_loaded', array( 'DS_DIRECTORY', 'get_instance' ) );

/*
 █████  ██████  ███    ███ ██ ███    ██
██   ██ ██   ██ ████  ████ ██ ████   ██
███████ ██   ██ ██ ████ ██ ██ ██ ██  ██
██   ██ ██   ██ ██  ██  ██ ██ ██  ██ ██
██   ██ ██████  ██      ██ ██ ██   ████
*/
if ( is_admin() ) {
	require_once DSDI_ROOT_PATH . 'admin/inc/class-admin.php';
	add_action( 'plugins_loaded', array( 'DS_DIRECTORY_ADMIN', 'get_instance' ) );
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
$dsdi = DS_DIRECTORY::get_instance();
register_activation_hook( __FILE__, array( $dsdi, 'activate' ) );

/**
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, array( $dsdi, 'deactivate' ) );
