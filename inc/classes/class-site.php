<?php
/**
 * This file contain site class for smart-civil-employee-book-keeping plugin.
 *
 * @since 1.0.0
 *
 * @package smart_civil_employee_book_keeping
 */

namespace scebk_plugin;

/**
 * Class site for setup the initial configuration and database.
 *
 * @package smart_civil_employee_book_keeping
 *
 * @author Suraj kumar Singh
 */
class Site {
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
	 * @return Site instance.
	 */
	final public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new Site();
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
		add_action( 'admin_menu', array( $this, 'site_admin_menu' ) );
		add_action( 'plugins_loaded', array( $this, 'register_tables_in_wpdb' ) );
		add_action( 'register_site_settings', array( $this, 'register_site_settings' ) );
	}

	/**
	 * Create site table for site details.
	 */
	public function create_site_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'site';
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			$wpdb->prepare(
				'CREATE TABLE IF NOT EXISTS `%1$s` (
				site_id bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				site_name varchar(15) DEFAULT NULL,
				site_contractor varchar(20) DEFAULT NULL,
				site_address varchar(250),
				site_start_date date,
				site_rate int(3),
				site_height varchar(25),
				site_pic_path varchar(255),
				site_status tinyint DEFAULT 1,
				PRIMARY KEY (site_id),
				KEY site_id ( site_id )
				)ENGINE = InnoDB DEFAULT CHARSET=utf8',
				$table_name
			)
		);
		add_option( 'wp_site_database_version', '1.0' );
	}

	/**
	 * Create site expense table.
	 */
	public function create_site_expense_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'site_expense';
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			$wpdb->prepare(
				'CREATE TABLE IF NOT EXISTS `%1$s` (
				site_expense_id bigint(20) NOT NULL AUTO_INCREMENT,
				site_id bigint(20) NOT NULL,
				taken_from varchar(25) DEFAULT NULL,
				amount bigint(20) NOT NULL,
				expense_description varchar(15) DEFAULT NULL,
				taken_on date,
				PRIMARY KEY (site_expense_id),
				KEY site_expense_id ( site_expense_id )
				)ENGINE = InnoDB DEFAULT CHARSET=utf8',
				$table_name
			)
		);
		add_option( 'wp_site_database_version', '1.0' );
	}

	/**
	 * Register tables in WP_query.
	 */
	public function register_tables_in_wpdb() {
		global $wpdb;
		$wpdb->site         = $wpdb->prefix . 'site';
		$wpdb->site_expense = $wpdb->prefix . 'site_expense';
	}

	/**
	 * Add site menu.
	 */
	public function site_admin_menu() {
		add_menu_page(
			__( 'Site Information', 'bestlearner' ),
			'Site',
			'manage_options',
			'site',
			array( $this, 'add_site_list_callback' ),
			'dashicons-admin-home',
			10
		);

		add_submenu_page(
			'site',
			'All Sites',
			'All Sites',
			'manage_options',
			'site'
		);

		add_submenu_page(
			'site',
			'Add New Site',
			'Add New',
			'manage_options',
			'add-site',
			array( $this, 'add_new_site_callback' )
		);

		add_submenu_page(
			'site',
			'Expense',
			'Expense',
			'manage_options',
			'site-expense',
			array( $this, 'add_site_expense_callback' )
		);

	}

	/**
	 * Function to add new site form.
	 *
	 * @return void
	 */
	public function add_new_site_callback() {
		include_once SCEBK_PLUGIN_PATH . 'inc/add-new-site-form.php';
	}

	/**
	 * Function to add site expense form.
	 *
	 * @return void
	 */
	public function add_site_expense_callback() {
		include_once SCEBK_PLUGIN_PATH . 'inc/add-site-expense-form.php';
	}

	/**
	 * Function to show site list.
	 *
	 * @return void
	 */
	public function add_site_list_callback() {
		include_once SCEBK_PLUGIN_PATH . 'inc/show-site-list.php';
	}

}
Site::get_instance();
