<?php
/*
Plugin Name:  DS Directory
Plugin URI:   https://www.divspot.co.za/plugin-ds-floated-menu/
Description:  DS Directory adds to your WordPress installation a clean and flexible directory (e.g. Shops, Services, Products, Cars).
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

define( 'DSDI_BASENAME'      , plugin_basename( __FILE__ ) );
define( 'DSDI_ROOT_URL'      , plugins_url( '', DSDI_BASENAME ) . '/' ); // User-Friendly URL
define( 'DSDI_ROOT_PATH'     , __DIR__        . '/' );                   // FTP Path
define( 'DSDI_ADMIN_URL'     , DSDI_ROOT_URL  . 'admin/' );              // User-Friendly URL
define( 'DSDI_ADMIN_PATH'    , DSDI_ROOT_PATH . 'admin/' );              // FTP Path
define( 'DSDI_ASSETS_URL'    , DSDI_ROOT_URL  . 'assets/' );             // User-Friendly URL
define( 'DSDI_ASSETS_PATH'   , DSDI_ROOT_PATH . 'assets/' );             // FTP Path
define( 'DSDI_TEMPLATES_URL' , DSDI_ROOT_URL  . 'templates/' );          // User-Friendly URL
define( 'DSDI_TEMPLATES_PATH', DSDI_ROOT_PATH . 'templates/' );          // FTP Path
define( 'DSDI_TITLE'         , 'DS Directory' );                   // DSDI Title
define( 'DSDI_SLUG'          , sanitize_title( DSDI_TITLE ) );           // Plugin slug.
define( 'DSDI_VERSION'       , '1.0' );


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

		// Render the dsdi_item & category templates.
		add_filter( 'template_include', array( $this, 'directory_render_template' ), 0, 1 );
		add_filter( 'template_include', array( $this, 'single_render_template'    ), 0, 1 );

		// Redirect root "/directory" to "all".
		add_action( 'template_redirect', array( $this, 'directory_root_redirect' ) );

		// Alter the main WP_Query object to pull relevant dsdi_item's.
		add_action( 'pre_get_posts'  , array( $this, 'update_wp_query'     ), 0, 1 );
		add_action( 'parse_tax_query', array( $this, 'update_wp_tax_query' ), 0, 1 );

		// Enqueue plugin assets.
		add_action( 'wp_enqueue_scripts', function() {
			global $wp_query;

			// Continue only on relevant dsdi_item & category pages.
			if (
				   empty( $wp_query->query_vars['dsdi_root'] )
				&& empty( $wp_query->query_vars['dsdi_category'] )
				&& empty( $wp_query->query_vars['dsdi_item'] )
			)
				return;

			wp_enqueue_script( 'dsdi-script', DSDI_ASSETS_URL . 'js/script.js'                        , array( 'jquery-core' ), DSDI_VERSION );
			 wp_enqueue_style( 'dsdi-style' , DSDI_ASSETS_URL . 'css/style.css'                       , array()               , DSDI_VERSION );
		   wp_enqueue_style( 'dsdi-core'  , DSDI_ADMIN_URL  . 'assets/vendors/ds-core/css/style.css', array( 'dsdi-style' ) , DSDI_VERSION );

			// Setting based styles.
	 		if ( $dynamic_styles = $this->get_dynamic_styles() )
	 			wp_add_inline_style( 'dsdi-style', $dynamic_styles );
		} );

		// Register the directory shortcode.
		add_shortcode( 'ds_directory', array( $this, 'shortcode_directory' ), 10, 3 );
	}

	/**
	 * Handle plugin activation.
	 *
	 * @access public
	 */
	static public function activate() {
		include DSDI_ADMIN_PATH . 'default-settings.php'; // Fetch $default_settings.

		update_option( 'dsdi_version', DSDI_VERSION );

		if ( empty( get_option( 'dsdi_settings' ) ) )
			update_option( 'dsdi_settings', $default_settings );

		self::register_post_type();
		self::register_post_taxonomies();
		flush_rewrite_rules();
	}

	/**
	 * Handle plugin deactivation.
	 *
	 * @access public
	 */
	static public function deactivate() {
		unregister_post_type( 'dsdi_item' );
		 unregister_taxonomy( 'dsdi_category' );
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
		if ( !empty( $this->settings['general']['single'] ) )
			$styles .= '@media ( min-width: 992px ) {
				.dsdi-number { width: 10% !important; }
				.dsdi-title { width: 25% !important; }
				.dsdi-category { width: 35% !important; }
				.dsdi-contact-number { width: 15% !important; }
				.dsdi-view-details{ width: 15% !important; }
			}';

		// ================ Design: Paddings ================
		$paddings = '';

		// Setting values may be empty, but the array will never be considered "empty" since it contains the top-right-bottom-left keys.
		foreach ( $this->settings['design']['padding'] as $side => $padding )
			if ( !empty( $padding ) )
				$paddings .= 'padding-' . $side . ': ' . $padding . ';';

		if ( $paddings )
			$styles .= 'body #dsdi-wrapper { ' . $paddings . ' }';

		//  ================ Design: Max-width ================
		if ( !empty( $this->settings['design']['max_width'] ) )
			$styles .= 'body #dsdi-wrapper > .taxonomy-description,
									body #dsdi-wrapper > .dsdi-directory-container{
										max-width: ' . $this->settings['design']['max_width'] . '
									}';

		// ================ Design: Button colors ================
		if ( !empty( $this->settings['design']['button_color_bg'] ) )
			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"],
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button,
			            body #dsdi-wrapper > .dsdi-directory-container button {
			            	background-color: ' . $this->settings['design']['button_color_bg'] . ';
			            }';

		if ( !empty( $this->settings['design']['button_color_bg_hover'] ) ) {
			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"]:hover,
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button:hover,
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button.active,
			            body #dsdi-wrapper > .dsdi-directory-container button:hover {
			            	background-color: ' . $this->settings['design']['button_color_bg_hover'] . ';
			            }';

			// Pagination styling to match button styling.
			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container .ds-pagination > a.page-numbers:hover:after,
									body #dsdi-wrapper > .dsdi-directory-container .ds-pagination > .current:after {
										border-color: ' . $this->settings['design']['button_color_bg_hover'] . ';
									}';
		}

		if ( !empty( $this->settings['design']['button_color_text'] ) )
			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"],
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button,
			            body #dsdi-wrapper > .dsdi-directory-container button {
			            	color: ' . $this->settings['design']['button_color_text'] . ';
			            }';

		if ( !empty( $this->settings['design']['button_color_text_hover'] ) ) {
			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container input[type="submit"]:hover,
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button:hover,
			            body #dsdi-wrapper > .dsdi-directory-container .ds-button.active,
			            body #dsdi-wrapper > .dsdi-directory-container button:hover {
			            	color: ' . $this->settings['design']['button_color_text_hover'] . ';
			            }';

			$styles .= 'body #dsdi-wrapper > .dsdi-directory-container button.active .ds-icon-arrow-down:before,
									body #dsdi-wrapper > .dsdi-directory-container button.active .ds-icon-arrow-down:after,
									body #dsdi-wrapper > .dsdi-directory-container button:hover .ds-icon-arrow-down:before,
									body #dsdi-wrapper > .dsdi-directory-container button:hover .ds-icon-arrow-down:after {
										background: ' . $this->settings['design']['button_color_text_hover'] . ';
									}';
		}

		if ( !empty( $this->settings['design']['text_color'] ) )
			$styles .= 'body #dsdi-wrapper * {
			            	color: ' . $this->settings['design']['text_color'] . ';
			            }';

		return $styles;
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
		// 	'category_id' => -1
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
			'menu_icon'           => DSDI_ASSETS_URL . 'images/icon-xs.png'
		);

		register_post_type( 'dsdi_item', $args );
	}

	/**
	 * Register the post taxonomies.
	 *
	 * @access public
	 */
	static public function register_post_taxonomies() {
		register_taxonomy( 'dsdi_category', array( 'dsdi_item' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Directories', 'taxonomy general name'        , DSDI_SLUG ),
				'singular_name'     => _x( 'Directory category', 'taxonomy singular name', DSDI_SLUG ),
				'search_items'      => __( 'Search directories'           , DSDI_SLUG ),
				'not_found'         => __( 'No directories found'         , DSDI_SLUG ),
				'all_items'         => __( 'All directories'              , DSDI_SLUG ),
				'parent_item'       => __( 'Parent directory category'    , DSDI_SLUG ),
				'parent_item_colon' => __( 'Parent directory category:'   , DSDI_SLUG ),
				'edit_item'         => __( 'Edit directory category'      , DSDI_SLUG ),
				'update_item'       => __( 'Update directory category'    , DSDI_SLUG ),
				'add_new_item'      => __( 'Add new directory category'   , DSDI_SLUG ),
				'view_item'         => __( 'View directory category'      , DSDI_SLUG ),
				'new_item_name'     => __( 'New directory category name'  , DSDI_SLUG ),
				'menu_name'         => __( 'Directories'                  , DSDI_SLUG ),
		  ),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'ds-directory' )
		) );

		register_taxonomy_for_object_type( 'dsdi_category', 'dsdi_item' );

		if ( !term_exists( 'all', 'dsdi_category' ) )
			wp_insert_term( 'All directories', 'dsdi_category', ['slug' => 'all'] );
	}

	/**
	 * Register dsdi_item & dsdi_category query vars.
	 *
	 * @access public
	 */
	public function directory_render_template( $template ) {
		global $wp_query;

		if (
			   !empty( $wp_query->query_vars['dsdi_category'] )
			&& term_exists( $wp_query->query_vars['dsdi_category'] )
		)
			return DSDI_ROOT_PATH . 'templates/archive.php';

		return $template;
	}

	/**
	 * Register dsdi_item & dsdi_category query vars.
	 *
	 * @access public
	 */
	public function single_render_template( $template ) {
		global $wp_query;

		if ( empty( $wp_query->query_vars['dsdi_item'] ) )
			return $template;

		return DSDI_ROOT_PATH . 'templates/dsdi_item.php';
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & dsdi_category data.
	 *
	 * @access public
	 */
	public function update_wp_query( $query ) {
		if ( is_admin() )
			return;

		if ( !$query->is_main_query() )
			return;

		if ( !empty( $query->query_vars['dsdi_category'] ) ) {
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
				$query->set( 'orderby', $_GET['sort'] );
				$query->set( 'order'  , ( !empty( $_GET['order'] ) ? $_GET['order'] : 'ASC' ) );
			} else {
				$query->set( 'orderby', 'name'  );
				$query->set( 'order'  , 'ASC' );
			}
		}
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & dsdi_category data.
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
			   !empty( $query->query_vars['dsdi_category'] )
			&& 'all' === $query->query_vars['dsdi_category']
		) {
			$terms    = get_terms( array( 'taxonomy' => 'dsdi_category' ) );
			$term_ids = array();

			foreach ( $terms as $term )
				$term_ids[] = $term->term_id;

			$tax_query = array(
				'taxonomy'         => 'dsdi_category',
				'field'            => 'term_id',
				'terms'            => $term_ids,
				'operator'         => 'IN',
				'include_children' => true
			);
			$query->tax_query->queries[0] = $tax_query;
		}
	}

	/**
	 * Alter the main WP_Query object to modify fetched dsdi_item & dsdi_category data.
	 *
	 * @access public
	 */
	public function directory_root_redirect() {
		global $wp_query;

		// Redirect root to .../all/
		if (
			   !empty( $wp_query->query['name'] )
			&& 'ds_directory' === $wp_query->query['name']
		) {
			wp_safe_redirect( esc_url( home_url() . '/directory/all/' ) );
			exit;
		}
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
register_activation_hook( __FILE__, array( 'DS_DIRECTORY', 'activate' ) );

/**
 * Register plugin deactivation hook.
 */
register_deactivation_hook( __FILE__, array( 'DS_DIRECTORY', 'deactivate' ) );
