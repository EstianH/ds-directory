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

		// Render the store & store category template.
		add_filter( 'template_include', array( $this, 'store_directory_render_template' ), 0, 1 );
		add_filter( 'template_include', array( $this, 'store_single_render_template'    ), 0, 1 );

		// Redirect root "/store-directory" to "all-stores".
		add_action( 'template_redirect', array( $this, 'store_directory_root_redirect' ) );

		// Alter the main WP_Query object to pull relevant stores.
		add_action( 'pre_get_posts'  , array( $this, 'update_wp_query'     ), 0, 1 );
		add_action( 'parse_tax_query', array( $this, 'update_wp_tax_query' ), 0, 1 );

		// Enqueue plugin assets.
		add_action( 'wp_enqueue_scripts', function() {
			global $wp_query;

			// Continue only on relevant store and store category pages.
			if (
				   empty( $wp_query->query_vars['store_directory_root'] )
				&& empty( $wp_query->query_vars['store_directory_category'] )
				&& empty( $wp_query->query_vars['store'] )
			)
				return;

			wp_enqueue_script( 'dssd-script', DSSD_ASSETS_URL . 'js/script.js' , array( 'jquery-core' ), DSSD_VERSION );
		   wp_enqueue_style( 'dssd-style' , DSSD_ASSETS_URL . 'css/style.css', array()               , DSSD_VERSION );

			// Setting based styles.
	 		if ( $dynamic_styles = $this->get_dynamic_styles() )
	 			wp_add_inline_style( 'dssd-style', $dynamic_styles );
		} );

		// Register the store directory shortcode.
		add_shortcode( 'store_directory', array( $this, 'shortcode_handler' ), 10, 3 );
	}

	/**
	 * Handle plugin activation.
	 *
	 * @access public
	 */
	static public function activate() {
		include DSSD_ADMIN_PATH . 'default-settings.php'; // Fetch $default_settings.

		update_option( 'dssd_version', DSSD_VERSION );

		if ( empty( get_option( 'dssd_settings' ) ) )
			update_option( 'dssd_settings', $default_settings );

		self::register_store_post_type();
		self::register_store_post_taxonomies();
		flush_rewrite_rules();
	}

	/**
	 * Handle plugin deactivation.
	 *
	 * @access public
	 */
	static public function deactivate() {
		unregister_post_type( 'store' );
		 unregister_taxonomy( 'store_directory_category' );
		 flush_rewrite_rules();
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

		// ================ General ================
		if ( !empty( $this->settings['general']['store_single'] ) )
			$styles .= '@media ( min-width: 992px ) {
				.store-number { width: 10% !important; }
				.store-title { width: 25% !important; }
				.store-category { width: 35% !important; }
				.store-contact-number { width: 15% !important; }
				.store-view-details{ width: 15% !important; }
			}';

		// ================ Design: Paddings ================
		$paddings = '';

		// Setting values may be empty, but the array will never be considered "empty" since it contains the top-right-bottom-left keys.
		foreach ( $this->settings['design']['padding'] as $side => $padding )
			if ( !empty( $padding ) )
				$paddings .= 'padding-' . $side . ': ' . $padding . ';';

		if ( $paddings )
			$styles .= 'body #dssd-wrapper { ' . $paddings . ' }';

		//  ================ Design: Max-width ================
		if ( !empty( $this->settings['design']['max_width'] ) )
			$styles .= 'body #dssd-wrapper > .store-directory-container { max-width: ' . $this->settings['design']['max_width'] . ' }';

		// ================ Design: Button colors ================
		if ( !empty( $this->settings['design']['button_color_bg'] ) )
			$styles .= 'body #dssd-wrapper > .store-directory-container input[type="submit"],
			            body #dssd-wrapper > .store-directory-container .ds-button,
			            body #dssd-wrapper > .store-directory-container button {
			            	background-color: ' . $this->settings['design']['button_color_bg'] . ';
			            }';

		if ( !empty( $this->settings['design']['button_color_bg_hover'] ) )
			$styles .= 'body #dssd-wrapper > .store-directory-container input[type="submit"]:hover,
			            body #dssd-wrapper > .store-directory-container .ds-button:hover,
			            body #dssd-wrapper > .store-directory-container .ds-button.active,
			            body #dssd-wrapper > .store-directory-container button:hover {
			            	background-color: ' . $this->settings['design']['button_color_bg_hover'] . ';
			            }';

		if ( !empty( $this->settings['design']['button_color_text'] ) )
			$styles .= 'body #dssd-wrapper > .store-directory-container input[type="submit"],
			            body #dssd-wrapper > .store-directory-container .ds-button,
			            body #dssd-wrapper > .store-directory-container button {
			            	color: ' . $this->settings['design']['button_color_text'] . ';
			            }';

		if ( !empty( $this->settings['design']['button_color_text_hover'] ) )
			$styles .= 'body #dssd-wrapper > .store-directory-container input[type="submit"]:hover,
			            body #dssd-wrapper > .store-directory-container .ds-button:hover,
			            body #dssd-wrapper > .store-directory-container .ds-button.active,
			            body #dssd-wrapper > .store-directory-container button:hover {
			            	color: ' . $this->settings['design']['button_color_text_hover'] . ';
			            }';

		if ( !empty( $this->settings['design']['text_color'] ) )
			$styles .= 'body #dssd-wrapper * {
			            	color: ' . $this->settings['design']['text_color'] . ';
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
	static public function register_store_post_type() {
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
	static public function register_store_post_taxonomies() {
		register_taxonomy( 'store_directory_category', array( 'store' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Store Categories', 'taxonomy general name' , DSSD_SLUG ),
				'singular_name'     => _x( 'Store Category',   'taxonomy singular name', DSSD_SLUG ),
				'search_items'      => __( 'Search Store Categories'  , DSSD_SLUG ),
				'not_found'         => __( 'No Store Categories found', DSSD_SLUG ),
				'all_items'         => __( 'All Store Categories'     , DSSD_SLUG ),
				'parent_item'       => __( 'Parent Store Category'    , DSSD_SLUG ),
				'parent_item_colon' => __( 'Parent Store Category:'   , DSSD_SLUG ),
				'edit_item'         => __( 'Edit Store Category'      , DSSD_SLUG ),
				'update_item'       => __( 'Update Store Category'    , DSSD_SLUG ),
				'add_new_item'      => __( 'Add New Store Category'   , DSSD_SLUG ),
				'view_item'         => __( 'View Store Category'      , DSSD_SLUG ),
				'new_item_name'     => __( 'New Store Category Name'  , DSSD_SLUG ),
				'menu_name'         => __( 'Store Categories'         , DSSD_SLUG ),
		  ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'store-directory' )
		) );

		register_taxonomy_for_object_type( 'store_directory_category', 'store' );

		if ( !term_exists( 'all-stores', 'store_directory_category' ) ) {
			$args = array(
				'slug' => 'all-stores'
			);
			wp_insert_term( 'All Stores', 'store_directory_category', $args );
		}
	}

	/**
	 * Register store & store category query vars.
	 *
	 * @access public
	 */
	public function store_directory_render_template( $template ) {
		global $wp_query;

		if (
			!empty( $wp_query->query_vars['store_directory_category'] )
			&& term_exists( $wp_query->query_vars['store_directory_category'] )
		)
			return DSSD_ROOT_PATH . 'templates/archive.php';

		return $template;
	}

	/**
	 * Register store & store category query vars.
	 *
	 * @access public
	 */
	public function store_single_render_template( $template ) {
		global $wp_query;

		if ( !empty( $wp_query->query_vars['store'] ) )
			return DSSD_ROOT_PATH . 'templates/store.php';

		return $template;
	}

	/**
	 * Alter the main WP_Query object to modify fetched store & store category data.
	 *
	 * @access public
	 */
	public function update_wp_query( $query ) {
		if ( is_admin() )
			return;

		if ( !$query->is_main_query() )
			return;

		if ( !empty( $query->query_vars['store_directory_category'] ) ) {
			if (
				    !empty( $this->settings['general']['store_load_condition'] )
				&& 'all' !== $this->settings['general']['store_load_condition']
			)
				$query->set( 'posts_per_page', $this->settings['general']['store_load_count'] );
			else
				$query->set( 'posts_per_page', -1 );

			$query->set( 'post_status', 'publish' );
			$query->set( 'post_type'  , 'store' );

			if (
				      !empty( $_GET['sort'] )
				&& 'name' === $_GET['sort']
			) {
				$query->set( 'orderby', $_GET['sort']  );
				$query->set( 'order', (
					      !empty( $_GET['order'] )
					&& 'DESC' === $_GET['order']
					? $_GET['order']
					: 'ASC'
				) );
			}
		}
	}

	/**
	 * Alter the main WP_Query object to modify fetched store & store category data.
	 * Handle store directory root query (Fetch all stores).
	 *
	 * @access public
	 */
	public function update_wp_tax_query( $query ) {
		if ( is_admin() )
			return;

		if ( !$query->is_main_query() )
			return;

		if (
			!empty( $query->query_vars['store_directory_category'] )
			&& 'all-stores' === $query->query_vars['store_directory_category']
		) {
			$terms = get_terms( array( 'taxonomy' => 'store_directory_category' ) );
			$term_ids = array();

			foreach ( $terms as $term )
				$term_ids[] = $term->term_id;

			$tax_query = array(
				'taxonomy'         => 'store_directory_category',
				'field'            => 'term_id',
				'terms'            => $term_ids,
				'operator'         => 'IN',
				'include_children' => true
			);
			$query->tax_query->queries[0] = $tax_query;
		}
	}

	/**
	 * Alter the main WP_Query object to modify fetched store & store category data.
	 *
	 * @access public
	 */
	public function store_directory_root_redirect() {
		global $wp_query;
// echo '<pre>'; var_dump($wp_query); echo '</pre>';
		if (
			!empty( $wp_query->query['name'] )
			&& 'store-directory' === $wp_query->query['name']
		) {
			wp_safe_redirect( esc_url( home_url() . '/store-directory/all-stores/' ) );
			exit;
		}
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
register_activation_hook( __FILE__, array( 'DS_STORE_DIRECTORY', 'activate' ) );

/**
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, array( 'DS_STORE_DIRECTORY', 'deactivate' ) );
