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
		add_action( 'plugins_loaded', array( $this, 'register_tables_in_wpdb' ) );
		add_action( 'admin_menu', array( $this, 'employee_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'attendance_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'report_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );

	}

	/**
	 * Create employee table for empoyee details.
	 */
	public function create_employee_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'employee';
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			$wpdb->prepare(
				'CREATE TABLE IF NOT EXISTS `%1$s` (
					emp_id bigint(20) NOT NULL AUTO_INCREMENT,
					site_id bigint(20) NOT NULL,
					emp_name varchar(50) NOT NULL,
					emp_phone varchar(15) DEFAULT NULL,
					emp_ac varchar(20) DEFAULT NULL,
					emp_address varchar(250),
					emp_join_date date,
					emp_leave_Date date DEFAULT NULL,
					emp_rate int(3),
					emp_aadhar_no int(20),
					emp_designation varchar(25),
					emp_pic_path varchar(255),
					PRIMARY KEY (emp_id)
				)ENGINE = InnoDB DEFAULT CHARSET=utf8',
				$table_name
			)
		);
		add_option( 'wp_employee_database_version', '1.0' );
	}

	

	/**	
	 * Create employee expsense.
	 */
	public function create_employee_expense_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'employee_expense';
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			$wpdb->prepare(
				'CREATE TABLE IF NOT EXISTS `%1$s` (
				emp_expense_id bigint(20) NOT NULL AUTO_INCREMENT,
				emp_id bigint(20) NOT NULL,
				taken_from varchar(25) DEFAULT NULL,
				expense_description varchar(15) DEFAULT NULL,
				taken_on date,
				PRIMARY KEY (emp_expense_id),
				KEY emp_expense_id ( emp_expense_id )
				)ENGINE = InnoDB DEFAULT CHARSET=utf8',
				$table_name
			)
		);
		add_option( 'wp_site_database_version', '1.0' );
	}

	/**	
	 * Create attendance table for employee daily work.
	 */
	public function create_attendance_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'attendance';
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(
			$wpdb->prepare(
				'CREATE TABLE IF NOT EXISTS `%1$s` (
				attendance_id bigint(20) NOT NULL AUTO_INCREMENT,
				emp_id bigint(20) NOT NULL,
				attendance varchar(25) DEFAULT NULL,
				overtime int(5) DEFAULT 0,
				worked_on date,
				PRIMARY KEY (attendance_id),
				KEY attendance_id ( attendance_id )
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
		$wpdb->employee         = $wpdb->prefix . 'employee';
		$wpdb->site             = $wpdb->prefix . 'site';
		$wpdb->site_expense     = $wpdb->prefix . 'site_expense';
		$wpdb->employee_expense = $wpdb->prefix . 'employee_expense';
		$wpdb->employee_expense = $wpdb->prefix . 'attendance';
	}


	/**
	 * Load text-domain  for internationalization.
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'smart-civil-employee-book-keeping', false, SCEBK_PLUGIN_DIR_NAME . '/languages/' );
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
			array( $this, 'employee_admin_html_page' ),
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
			array( $this, 'add_new_site_callback' )
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
	 * Add employee menu.
	 */
	public function employee_admin_menu() {
		add_menu_page(
			__( 'Employee Information', 'bestlearner' ),
			'Employee',
			'manage_options',
			'employee',
			array( $this, 'employee_admin_html_page' ),
			'dashicons-buddicons-buddypress-logo',
			11
		);

		add_submenu_page(
			'employee',
			'All Employee',
			'All Employees',
			'manage_options',
			'employee'
		);

		add_submenu_page(
			'employee',
			'Add New Employee',
			'Add New',
			'manage_options',
			'add-employee'
		);

		add_submenu_page(
			'employee',
			__( 'Expense Information', 'bestlearner' ),
			'Expense',
			'manage_options',
			'employee-expense',
			array( $this, 'employee_admin_html_page' )
		);

	}

	/**
	 * Create html page along with elements for employee.
	 */
	public function employee_admin_html_page() {
		include_once SCEBK_PLUGIN_PATH . '/inc/add-employee.php';
	}

	/**
	 * Add Attendance menu.
	 */
	public function attendance_admin_menu() {
		add_menu_page(
			__( 'Attendance Information', 'bestlearner' ),
			'Attendance',
			'manage_options',
			'attendance',
			array( $this, 'employee_admin_html_page' ),
			'dashicons-calendar-alt',
			12
		);

		add_submenu_page(
			'attendance',
			'All Attendance',
			'All Attendances',
			'manage_options',
			'attendance'
		);

		add_submenu_page(
			'attendance',
			'Add New Attendance',
			'Add New',
			'manage_options',
			'add-employee-attendance'
		);

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
