<?php
/**
 * This file contain Employee class for smart-civil-employee-book-keeping plugin.
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
class Employee {
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
	 * @return Employee instance.
	 */
	final public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new Employee();
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
		add_action( 'admin_menu', array( $this, 'employee_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'attendance_admin_menu' ) );
		add_action( 'plugins_loaded', array( $this, 'register_tables_in_wpdb' ) );
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
					emp_aadhar_no varchar(20),
					emp_designation varchar(25),
					emp_pic_path varchar(255),
					emp_status int DEFAULT 1,
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
				amount bigint(20) DEFAULT 0,
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
				work_hour int(5) DEFAULT 0,
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
		$wpdb->employee_expense = $wpdb->prefix . 'employee_expense';
		$wpdb->employee_expense = $wpdb->prefix . 'attendance';
	}
}

Employee::get_instance();
