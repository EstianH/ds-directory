<?php
/*
Plugin Name:  DS Store Directory
Plugin URI:   https://www.divspot.co.za/plugin-ds-floated-menu/
Description:  Description.
Version:      1.0
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

define( 'DSSD_BASENAME'      , plugin_basename( __FILE__ ) );
define( 'DSSD_ROOT_URL'      , plugins_url( '', DSSD_BASENAME ) . '/' ); // User-Friendly URL
define( 'DSSD_ROOT_PATH'     , __DIR__        . '/' );                   // FTP Path
define( 'DSSD_ADMIN_URL'     , DSSD_ROOT_URL  . 'admin/' );              // User-Friendly URL
define( 'DSSD_ADMIN_PATH'    , DSSD_ROOT_PATH . 'admin/' );              // FTP Path
define( 'DSSD_ASSETS_URL'    , DSSD_ROOT_URL  . 'assets/' );             // User-Friendly URL
define( 'DSSD_ASSETS_PATH'   , DSSD_ROOT_PATH . 'assets/' );             // FTP Path
define( 'DSSD_TEMPLATES_URL' , DSSD_ROOT_URL  . 'templates/' );          // User-Friendly URL
define( 'DSSD_TEMPLATES_PATH', DSSD_ROOT_PATH . 'templates/' );          // FTP Path
define( 'DSSD_TITLE'         , 'DS Store Directory' );                   // DSSD Title
define( 'DSSD_SLUG'          , sanitize_title( DSSD_TITLE ) );           // Plugin slug.
define( 'DSSD_VERSION'       , '1.0' );


/*
██████  ███████ ███████ ██████
██   ██ ██      ██      ██   ██
██   ██ ███████ ███████ ██   ██
██   ██      ██      ██ ██   ██
██████  ███████ ███████ ██████
*/
class DS_STORE_DIRECTORY {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 * @var DS_STORE_DIRECTORY
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
	 * @return DS_STORE_DIRECTORY $instance
	 */
	public static function get_instance() {
		if ( NULL === self::$instance )
			self::$instance = new DS_STORE_DIRECTORY();

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		$this->settings = get_option( 'dssd_settings' );

		// Register the store post type & taxonomies.
		add_action( 'init', array( $this, 'register_store_post_type' )      , 10 );
		add_action( 'init', array( $this, 'register_store_post_taxonomies' ), 10 );

		// Register store & store category rewrite rules
		add_action( 'init'      , array( $this, 'add_rewrite_rules' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0, 1 );

		// Render the store & store category template.
		add_filter( 'template_include', array( $this, 'store_directory_render_template' ), 0, 1 );

		// Filter the root "store-directory" title.
		add_filter( 'document_title_parts', array( $this, 'store_directory_filter_title' ), 0, 1 );

		// Alter the main WP_Query object to pull all stores on the root "store-directory" page.
		add_action( 'pre_get_posts', array( $this, 'update_wp_query' ), 0, 1 );

		// Enqueue plugin assets.
		add_action( 'wp_enqueue_scripts', function() {
			global $wp_query;

			// Continue only on relevant store and store category pages.
			if (
				   empty( $wp_query->query_vars['store_directory_root'] )
				&& empty( $wp_query->query_vars['store_directory_category'] )
			)
				return;

			wp_enqueue_script( 'dssd-script', DSSD_ASSETS_URL . 'js/script.js' , array(), DSSD_VERSION );
		   wp_enqueue_style( 'dssd-style' , DSSD_ASSETS_URL . 'css/style.css', array(), DSSD_VERSION );

			// Setting based styles.
	 		if ( $dynamic_styles = $this->get_dynamic_styles() )
	 			wp_add_inline_style( 'dssd-style', $dynamic_styles );
		} );

		// Register the store directory shortcode.
		add_shortcode( 'store_directory', array( $this, 'shortcode_handler' ), 10, 3 );
	}

	/**
	 * Return dynamic styles.
	 *
	 * @access public
	 */
	public function get_dynamic_styles() {
		$styles = '';

		if ( empty( $this->settings ) )
			return $styles;

		if ( !empty( $this->settings['general']['read_more'] ) )
			$styles .= '@media ( min-width: 992px ) {
				.store-number { width: 10% !important; }
				.store-title { width: 25% !important; }
				.store-category { width: 25% !important; }
				.store-contact-number { width: 20% !important; }
				.store-view-details{ width: 20% !important; }
			}';

		return $styles;
	}

	/**
	 * Store Directory shortcode handler.
	 *
	 * @access public
	 *
	 * @param array  $atts    An associative array of attributes, or an empty string if no attributes are given
	 * @param string $content The enclosed content (if the shortcode is used in its enclosing form)
	 * @param string $tag     The shortcode tag, useful for shared callback functions
	 */
	public function shortcode_handler( $atts, $content, $tag ) {
		// $atts_merged = shortcode_atts( array(
		// 	'category_id' => -1
		// ), $atts );
		//
		// ob_start();
		// include DSSD_ROOT_PATH . 'templates/store-directory-list.php';
		// return ob_get_clean();
		return '';
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
			'rewrite'           => array( 'slug' => 'store-directory' )
		) );
	}

	/**
	 * Register store & store category rewrite rules.
	 *
	 * @access public
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule(
			'^store-directory/?$',
			'index.php?store_directory_root=store-directory',
			'top'
		);
	}

	/**
	 * Register store & store category query vars.
	 *
	 * @access public
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'store_directory_root';
		return $vars;
	}

	/**
	 * Register store & store category query vars.
	 *
	 * @access public
	 */
	public function store_directory_render_template( $template ) {
		global $wp_query;
// echo '<pre>'; print_r($wp_query->query_vars); echo '</pre>';
		if (
			(
				!empty( $wp_query->query_vars['store_directory_category'] )
				&& term_exists( $wp_query->query_vars['store_directory_category'] )
			)
			|| !empty( $wp_query->query_vars['store_directory_root'] )
		)
			return DSSD_ROOT_PATH . 'templates/archive-categories.php';

		return $template;
	}

	/**
	 * Alter the main WP_Query object to pull all stores on the root "store-directory" page.
	 *
	 * @access public
	 */
	public function update_wp_query( $query ) {
		if ( !$query->is_main_query() )
			return;

		if ( !empty( $query->query_vars['store_directory_root'] ) ) {
			$query->set( 'post_type', 'store' );
			$query->set( 'posts_per_page', -1 );
			$query->set( 'orderby', 'name' );
			$query->set( 'order', 'ASC' );
			$query->set( 'post_status', 'publish' );
		}
	}

	/**
	 * Filter the root "store-directory" page title.
	 *
	 * @access public
	 */
	public function store_directory_filter_title( $title_parts ) {
		global $wp_query;

		if ( !empty( $wp_query->query_vars['store_directory_root'] ) )
			$title_parts = array(
				'title' => 'Store Directory',
				'site' => get_bloginfo()
			);

		return $title_parts;
	}
}

add_action( 'plugins_loaded', array( 'DS_STORE_DIRECTORY', 'get_instance' ) );

/*
 █████  ██████  ███    ███ ██ ███    ██
██   ██ ██   ██ ████  ████ ██ ████   ██
███████ ██   ██ ██ ████ ██ ██ ██ ██  ██
██   ██ ██   ██ ██  ██  ██ ██ ██  ██ ██
██   ██ ██████  ██      ██ ██ ██   ████
*/
if ( is_admin() ) {
	require_once DSSD_ROOT_PATH . 'admin/inc/class-admin.php';
	add_action( 'plugins_loaded', array( 'DS_STORE_DIRECTORY_ADMIN', 'get_instance' ) );
}
