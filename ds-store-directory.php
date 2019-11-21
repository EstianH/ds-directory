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

define( 'DSSD_BASENAME'  , plugin_basename( __FILE__ ) );
define( 'DSSD_URL'       , plugins_url( '', DSSD_BASENAME ) . '/' ); // User-Friendly URL
define( 'DSSD_ROOT'      , __DIR__   . '/' ); // FTP Path
define( 'DSSD_ADMIN'     , DSSD_ROOT . 'admin/' ); // FTP Path
define( 'DSSD_ASSETS'    , DSSD_ROOT . 'assets/' ); // FTP Path
define( 'DSSD_TEMPLATES' , DSSD_ROOT . 'templates/' ); // FTP Path
define( 'DSSD_TITLE'     , 'DS Store Directory' );
define( 'DSSD_SLUG'      , sanitize_title( DSSD_TITLE ) ); // Plugin slug.
define( 'DSSD_VERSION'   , '1.0' );


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

		// Enqueue plugin assets.
		add_action( 'wp_enqueue_scripts', function() {
			// if ( true !== $this->has_active_menu() ) // Modify this condition to render only on SD pages.
				// return;

		  wp_enqueue_script( 'dssd-script', DSSD_ASSETS . 'js/script.js',  array( 'jquery-core' ), DSSD_VERSION );
		   wp_enqueue_style( 'dssd-style' , DSSD_ASSETS . 'css/style.css', array(),                DSSD_VERSION );

			// Setting based styles.
			if ( $dynamic_styles = $this->get_dynamic_styles() )
				wp_add_inline_style( 'dssd-style', $dynamic_styles );
		} );

		// Render menus in/above the footer.
		// add_action( 'wp_footer', array( $this, 'render_menu_locations' ) );
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

		return $styles;
	}
}

if ( !is_admin() )
	add_action( 'plugins_loaded', array( 'DS_STORE_DIRECTORY', 'get_instance' ) );
else {
	require_once DSSD_ROOT . 'admin/inc/class-admin.php';
	add_action( 'plugins_loaded', array( 'DS_STORE_DIRECTORY_ADMIN', 'get_instance' ) );
}
