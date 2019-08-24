<?php
/**
 * This file contain RestAPI.
 *
 * @since 1.0.0
 *
 * @package smart_civil_employee_book_keeping
 */

/**
 * Class site for RestAPI.
 *
 * @package smart_civil_employee_book_keeping
 *
 * @author Suraj kumar Singh
 */
class SCEBK_RestAPI extends WP_REST_Controller {


	/**
	 * Alias for site table.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $table_site Site.
	 */
	public $table_site;

	/**
	 * Alias for site expesnse table.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $table_site_expense Site expense.
	 */
	public $table_site_expense;

	/**
	 * Alias for employee table.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $table_site Employee.
	 */
	public $table_employee;

	/**
	 * Alias for employee expense table.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $table_site Employee expense.
	 */
	public $table_employee_expense;

	/**
	 * Alias for attendance table.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $table_site Attendance.
	 */
	public $table_attendance;

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
	 * @return SCEBK_RestAPI instance.
	 */
	final public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new SCEBK_RestAPI();
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

		global $wpdb;
		$this->table_site             = $wpdb->prefix . 'site';
		$this->table_site_expense     = $wpdb->prefix . 'site_expense';
		$this->table_employee         = $wpdb->prefix . 'employee';
		$this->table_attendance       = $wpdb->prefix . 'attendance';
		$this->table_employee_expense = $wpdb->prefix . 'employee_expense';

		add_action( 'rest_api_init', array( $this, 'register_custom_endpoints_for_site' ) );
	}


	/**
	 * Function to resgiter custom endpoints for site info.
	 */
	public function register_custom_endpoints_for_site() {
		register_rest_route(
			'scebk/v1',
			'/add-site',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);
		register_rest_route(
			'scebk/v1',
			'/update-site',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/delete-site/(?P<site_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/show-site/(?P<site_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/all-site/(?P<user_id>[\d]+)',
			array(
				'method'   => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_all_site_by_user_id' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
				'args'     => array(
					'context'       => array(
						'default' => 'view',
					),
					'wp_rest_nonce' => array(
						'validate_callback' => array( $this, 'create_wp_rest_nonce' ),
					),
				),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/login',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'login_in_site' ),
			)
		);

		// Site expense routes.
		register_rest_route(
			'scebk/v1',
			'/show-site-all-expense/(?P<site_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_site_all_expense_by_id' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/show-site-expense',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_site_expense_date' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/add-site-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_site_expense' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/update-site-expense',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_site_expense' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/delete-site-expense/(?P<site_expense_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_expense_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		// Employee related task.
		register_rest_route(
			'scebk/v1',
			'/show-all-employee/(?P<site_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_all_employee' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/show-employee/(?P<employee_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'show_employee' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/add-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_employee' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/delete-employee/(?P<employee_id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_employee' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

		register_rest_route(
			'scebk/v1',
			'/update-employee',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_employee' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
			)
		);

	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_all_site_by_user_id( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$user_id    = $parameters['user_id'];

		if ( empty( $user_id ) ) {
			return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
		}

		$sites = get_transient( 'user' . $user_id );

		if ( empty( $sites ) ) {
			// @codingStandardsIgnoreStart
			$query = $wpdb->prepare(
				"SELECT site.site_id, 
						site_name, 
						site_contractor, 
						site_address, 
						site_start_date, 
						site_rate, 
						site_height, 
						site_pic_path, 
						site_status, 
						total_amount, 
						total_employee 
				FROM  
					(SELECT $this->table_site.site_id, 
							site_name, 
							site_contractor, 
							site_address, 
							site_start_date, 
							site_rate, 
							site_height, 
							site_pic_path, 
							site_status, 
							Sum(amount) AS total_amount 
					FROM   $this->table_site 
							LEFT JOIN $this->table_site_expense 
								ON $this->table_site_expense.site_id = 
									$this->table_site.site_id 
					WHERE  user_id = %d 
					GROUP BY $this->table_site.site_id) AS site 
				LEFT JOIN (SELECT $this->table_employee.site_id AS emp_site_id, 
								  COUNT(emp_id)                 AS total_employee 
						   FROM   $this->table_employee 
						   GROUP BY $this->table_employee.site_id) AS emp 
					   ON site.site_id = emp.emp_site_id",
				$user_id
			);
			
			$sites = $wpdb->get_results( $query, 'ARRAY_A' );
			//error_log( print_r($sites,true) );die();
			// @codingStandardsIgnoreEnd
			set_transient( 'user' . $user_id, $sites, 60 );
		}

		$response = new WP_REST_Response( $sites );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function add_new_site( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$default    = array(
			'user_id'         => get_current_user_id(),
			'site_name'       => '',
			'site_contractor' => '',
			'site_address'    => '',
			'site_start_date' => '',
			'site_rate'       => '',
			'site_height'     => '',
			'site_pic_path'   => '',
			'site_status'     => 1,
		);

		$item = shortcode_atts( $default, $parameters );
		$item = $wpdb->insert( $this->table_site, $item ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'New site created successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function update_site( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$where      = array(
			'site_id' => $parameters['site_id'],
		);

		$item = $wpdb->update( $this->table_site, $parameters, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Site updated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}


	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function delete_site( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$where      = array(
			'site_id' => $parameters['site_id'],
		);
		$item       = $wpdb->delete( $this->table_site, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Site deleted successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_site( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$site_id    = $parameters['site_id'];

		if ( empty( $site_id ) ) {
			return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
		}

		$site = get_transient( 'site' . $site_id );

		if ( empty( $site ) ) {
			// @codingStandardsIgnoreStart
			$query = $wpdb->prepare(
				"SELECT site.site_id, 
					site_name, 
					site_contractor, 
					site_address, 
					site_start_date, 
					site_rate, 
					site_height, 
					site_pic_path, 
					site_status, 
					total_amount, 
					total_employee 
				FROM
					(SELECT $this->table_site.site_id, 
							site_name, 
							site_contractor, 
							site_address, 
							site_start_date, 
							site_rate, 
							site_height, 
							site_pic_path, 
							site_status, 
							Sum(amount) AS total_amount 
					FROM   $this->table_site 
							LEFT JOIN $this->table_site_expense 
								ON $this->table_site_expense.site_id = 
									$this->table_site.site_id 
					WHERE  $this->table_site.site_id = %d 
					GROUP BY $this->table_site.site_id) AS site 
					LEFT JOIN (SELECT $this->table_employee.site_id AS emp_site_id, 
									COUNT(emp_id)                 AS total_employee 
							FROM   $this->table_employee 
							GROUP BY $this->table_employee.site_id) AS emp 
						ON site.site_id = emp.emp_site_id",
				$site_id
			);
			$site = $wpdb->get_results( $query, 'ARRAY_A' );

			// @codingStandardsIgnoreEnd
			set_transient( 'site' . $site_id, $site, 60 );
		}

		$response = new WP_REST_Response( $site );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function login_in_site( $request ) {
		global $wpdb;
		$table_site = $wpdb->prefix . 'users';
		$parameters = $request->get_params();
		$response   = wp_authenticate( $parameters['user_name'], $parameters['user_password'] );
		if ( ! empty( $response->data ) ) {

			return new WP_REST_Response(
				array(
					'user-info' => $response,
					'message'   => 'Logged in successfully',
					'status'    => 200,
				),
				200
			);
		}

		return new WP_Error( 'unauthenticated', __( 'Invalid authentication', 'scebk' ), array( 'status' => 401 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function add_new_site_expense( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$site_id    = $parameters['site_id'];

		if ( empty( $site_id ) ) {
			return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
		}

		$default = array(
			'site_id'             => $site_id,
			'taken_from'          => '',
			'amount'              => 0,
			'expense_description' => '',
			'taken_on'            => '',
		);

		$item = shortcode_atts( $default, $parameters );
		$item = $wpdb->insert( $this->table_site_expense, $item ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'New site expensecreated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_site_all_expense_by_id( $request ) {

		global $wpdb;
		$parameters = $request->get_params();
		$site_id    = $parameters['site_id'];

		if ( empty( $site_id ) ) {
			return;
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_site_expense where site_id = %d",
			$site_id
		);

		$site = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $site );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_site_expense_date( $request ) {

		global $wpdb;
		$parameters = $request->get_params();

		if ( empty( $parameters['taken_on'] ) || empty( $parameters['site_id'] ) ) {
			return null;
		}
		$taken_on = $parameters['taken_on'];
		$site_id  = $parameters['site_id'];
		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_site_expense where site_id = %s and taken_on = %s",
			$site_id,
			$taken_on
		);
		$site = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $site );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function delete_expense_site( $request ) {

		global $wpdb;
		$parameters = $request->get_params();

		if ( empty( $parameters['site_expense_id'] ) ) {
			return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
		}

		$where = array(
			'site_expense_id' => $parameters['site_expense_id'],
		);

		$item = $wpdb->delete( $this->table_site_expense, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Site expense deleted successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function update_site_expense( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$where      = array(
			'site_expense_id' => $parameters['site_expense_id'],
		);

		$item  = $wpdb->update( $this->table_site_expense, $parameters, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Site expense updated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_all_employee( $request ) {

		global $wpdb;
		$parameters = $request->get_params();
		$site_id    = $parameters['site_id'];
		if ( empty( $site_id ) ) {
			return null;
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"SELECT emp.emp_id, 
					emp_name, 
					emp_ac, 
					emp_phone, 
					emp_address, 
					emp_join_date, 
					emp_leave_date, 
					emp_rate, 
					emp_aadhar_no, 
					emp_designation, 
					emp_pic_path, 
					emp_status, 
					total_expense, 
					total_work 
			FROM  
				(SELECT $this->table_employee.emp_id, 
							emp_name, 
							emp_ac, 
							emp_phone, 
							emp_address, 
							emp_join_date, 
							emp_leave_date, 
							emp_rate, 
							emp_aadhar_no, 
							emp_designation, 
							emp_pic_path, 
							emp_status, 
						Sum(amount) AS total_expense 
				FROM   $this->table_employee 
						LEFT JOIN $this->table_employee_expense 
							ON $this->table_employee_expense.emp_id = $this->table_employee.emp_id 
				WHERE  site_id = %d 
				GROUP BY $this->table_employee.emp_id ) AS emp 
				LEFT JOIN (SELECT $this->table_attendance.emp_id AS empID, 
								Sum(work_hour)       AS total_work 
						FROM   $this->table_attendance 
						GROUP BY $this->table_attendance.emp_id) AS attendance 
					ON attendance.empid = emp.emp_id",
			$site_id
		);
		$employees = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employees );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function show_employee( $request ) {

		global $wpdb;
		$parameters  = $request->get_params();
		$employee_id = $parameters['employee_id'];
		if ( empty( $employee_id ) ) {
			return null;
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"SELECT emp.emp_id,
					emp_name,
					emp_ac,
					emp_phone,
					emp_address,
					emp_join_date,
					emp_leave_date,
					emp_rate,
					emp_aadhar_no,
					emp_designation,
					emp_pic_path,
					emp_status,
					total_expense,
					total_work
			FROM  
				(SELECT $this->table_employee.emp_id, 
							emp_name, 
							emp_ac, 
							emp_phone, 
							emp_address, 
							emp_join_date, 
							emp_leave_date, 
							emp_rate, 
							emp_aadhar_no, 
							emp_designation, 
							emp_pic_path, 
							emp_status, 
						Sum(amount) AS total_expense 
				FROM   $this->table_employee 
						LEFT JOIN $this->table_employee_expense 
							ON $this->table_employee_expense.emp_id = $this->table_employee.emp_id 
				WHERE  $this->table_employee.emp_id = %d
				GROUP BY $this->table_employee.emp_id ) AS emp 
				LEFT JOIN (SELECT $this->table_attendance.emp_id AS empID, 
								Sum(work_hour)       AS total_work 
						FROM   $this->table_attendance 
						GROUP BY $this->table_attendance.emp_id) AS attendance 
					ON attendance.empid = emp.emp_id",
			$employee_id
		);
		
		$employees = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employees );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function delete_employee( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$where      = array(
			'emp_id' => $parameters['employee_id'],
		);

		$item = $wpdb->delete( $this->table_employee, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Emloyee deleted successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function update_employee( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$where      = array(
			'emp_id' => $parameters['emp_id'],
		);

		$item = $wpdb->update( $this->table_employee, $parameters, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee updated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function add_new_employee( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$default    = array(
			'site_id'         => $parameters['site_id'],
			'emp_name'        => '',
			'emp_phone'       => '',
			'emp_ac'          => '',
			'emp_address'     => '',
			'emp_join_date'   => '',
			'emp_leave_date'  => '',
			'emp_rate'        => '',
			'emp_aadhar_no'   => '',
			'emp_designation' => '',
			'emp_pic_path'    => '',
			'emp_status'      => 1,
		);

		$item = shortcode_atts( $default, $parameters );
		$item = $wpdb->insert( $this->table_employee, $item ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'New Employee added successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function to check user is authenticate or not.
	 *
	 * @param WP_Rest_Request $request array.
	 */
	public function check_site_permission( $request ) {
		return current_user_can( 'edit_something' );
	}

	/**
	 * Create wp rest nonce.
	 *
	 * @param string $wp_rest_nonce Rest nonce.
	 */
	public function create_wp_rest_nonce( $wp_rest_nonce ) {
		return wp_verify_nonce( $wp_rest_nonce, 'foo_nonce' );
	}

}
require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-request.php';
require_once ABSPATH . '/wp-includes/pluggable.php';
SCEBK_RestAPI::get_instance();
