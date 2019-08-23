<?php
/**
 * This file contain smart-civil-employee-book-keeping class.
 *
 * @since 1.0.0
 *
 * @package smart_civil_employee_book_keeping
 */

namespace scebk_plugin;

/**
 * Class SCEBK for setup the initial configuration and database.
 *
 * @package smart_civil_employee_book_keeping
 *
 * @author Suraj kumar Singh
 */
class SCEBK {
	/**
	 * Instance of this class.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    object    $instance    Instance of this class.
	 */
	protected static $instance;
	/**
	 * Returns new or existing instance.
	 *
	 * @since 1.0
	 *
	 * @return SCEBK instance.
	 */
	final public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new SCEBK();
			static::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks.
	 *
	 * @since 1.0
	 */
	protected function setup() {
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
		add_action( 'admin_menu', array( $this, 'report_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );

	}


	/**
	 * Load text-domain  for internationalization.
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'smart-civil-employee-book-keeping', false, SCEBK_PLUGIN_DIR_NAME . '/languages/' );
	}

	/**
	 * Add Report menu.
	 */
	public function report_admin_menu() {
		add_menu_page(
			__( 'Report', 'bestlearner' ),
			'Report',
			'manage_options',
			'report',
			array( $this, 'employee_admin_html_page' ),
			'dashicons-book-alt',
			14
		);
	}

	/**
	 * Admin css.
	 */
	public function admin_style() {
		$file_modified_time = filemtime( SCEBK_PLUGIN_PATH . '/src/css/style.css' );
		wp_enqueue_style( 'admin-styles', SCEBK_PLUGIN_DIR . 'src/css/style.css', false, $file_modified_time );
	}
}
SCEBK::get_instance();
