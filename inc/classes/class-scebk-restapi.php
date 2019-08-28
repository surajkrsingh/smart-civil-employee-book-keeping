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

		// Add new site by user id.
		register_rest_route(
			'scebk/v1',
			'/add-site',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_site' ),
			)
		);

		// Update existing site by user.
		register_rest_route(
			'scebk/v1',
			'/update-site',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_site' ),
			)
		);

		// Delete exising site bu user.
		register_rest_route(
			'scebk/v1',
			'/delete-site',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'delete_site' ),
			)
		);

		// Show a particular site details by site id.
		register_rest_route(
			'scebk/v1',
			'/show-site',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_site' ),
			)
		);

		// Show all site details by user id. Pending to convert by token.
		register_rest_route(
			'scebk/v1',
			'/show-all-site',
			array(
				'method'   => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_all_site' ),
			)
		);

		// User authentication.
		register_rest_route(
			'scebk/v1',
			'/login',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'login_in_site' ),
			)
		);

		// Show expense of a particular site taken from contractor.
		register_rest_route(
			'scebk/v1',
			'/show-site-all-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_site_all_expense' ),
			)
		);

		// Show a particular expense of a particular site taken from contractor by given date.
		register_rest_route(
			'scebk/v1',
			'/show-site-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_site_expense_date' ),
			)
		);

		// Add new expense for site taken from contractor.
		register_rest_route(
			'scebk/v1',
			'/add-site-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_site_expense' ),
			)
		);

		// Update expense for site taken from contractor.
		register_rest_route(
			'scebk/v1',
			'/update-site-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_site_expense' ),
			)
		);

		// Delete a particular expense for site.
		register_rest_route(
			'scebk/v1',
			'/delete-site-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'delete_expense_site' ),
			)
		);

		// Show all employee of a particular site.
		register_rest_route(
			'scebk/v1',
			'/show-all-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_all_employee' ),
			)
		);

		// Show a particular employee details.
		register_rest_route(
			'scebk/v1',
			'/show-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_employee' ),
			)
		);

		// Add new employee for site.
		register_rest_route(
			'scebk/v1',
			'/add-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_employee' ),
			)
		);

		// Delete the employee.
		register_rest_route(
			'scebk/v1',
			'/delete-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'delete_employee' ),
			)
		);

		// Update employee details.
		register_rest_route(
			'scebk/v1',
			'/update-employee',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_employee' ),
			)
		);

		// Show all expense for an employee.
		register_rest_route(
			'scebk/v1',
			'/show-employee-all-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_employee_all_expense_by_id' ),
			)
		);

		// Show a particluar employee expense per date.
		register_rest_route(
			'scebk/v1',
			'/show-employee-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_employee_expense_date' ),
			)
		);

		// Add new expense for employee.
		register_rest_route(
			'scebk/v1',
			'/add-employee-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_employee_expense' ),
			)
		);

		// Update employee expense.
		register_rest_route(
			'scebk/v1',
			'/update-employee-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_employee_expense' ),
			)
		);

		// Delete the employee expense.
		register_rest_route(
			'scebk/v1',
			'/delete-employee-expense',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'delete_employee_expense' ),
			)
		);

		// Show all attendance for employee.
		register_rest_route(
			'scebk/v1',
			'/show-employee-all-attendance',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_employee_all_attendance_by_id' ),
			)
		);

		// Show employee attendance for a particular day.
		register_rest_route(
			'scebk/v1',
			'/show-employee-attendance',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'show_employee_attendance_date' ),
			)
		);

		// Add new attendance of employees.
		register_rest_route(
			'scebk/v1',
			'/add-employee-attendance',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_employee_attendance' ),
			)
		);

		// Update attendance for employee.
		register_rest_route(
			'scebk/v1',
			'/update-employee-attendance',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'update_employee_attendance' ),
			)
		);

		// Delete attendance of an employee.
		register_rest_route(
			'scebk/v1',
			'/delete-employee-attendance',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'delete_employee_attendance' ),
			)
		);
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_all_site( $request ) {
		global $wpdb;
		$parameters = $request->get_params();
		$user_id    = $parameters['user_id'];

		if ( empty( $user_id ) ) {
			return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
		}

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
						sum(amount) AS total_amount 
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
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $sites );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get add a new site for a user.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return array
	 */
	public function add_new_site( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );

		$default = array(
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

		global $wpdb;
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

		return new WP_Error( 'something-wrong', __( 'Unable to create a site please try again', 'scebk' ), array( 'status' => 500 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function update_site( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $parameters['site_id'] ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );

		$where = array(
			'site_id' => $parameters['site_id'],
		);

		global $wpdb;
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

		return new WP_Error( 'not-found', __( 'Enable to update the data', 'text-domain' ), array( 'status' => 500 ) );
	}


	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function delete_site( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $parameters['site_id'] ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );

		$where = array(
			'site_id' => $parameters['site_id'],
		);

		global $wpdb;
		$item = $wpdb->delete( $this->table_site, $where ); //phpcs:ignore

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
	 * Function to get details of a site .
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_site( $request ) {
		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $site_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

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
						sum(amount) AS total_amount 
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

		if ( empty( $site ) ) {
			return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
		}

		$response = new WP_REST_Response( $site );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to authenticate the user.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function login_in_site( $request ) {
		global $wpdb;

		$table_site = $wpdb->prefix . 'users';
		$parameters = json_decode( $request->get_body(), true );

		if ( empty( $parameters['user_name'] ) || empty( $parameters['user_password'] ) ) {
			return new WP_Error( 'Invalid', __( 'Invalid authentication', 'scebk' ), array( 'status' => 401 ) );
		}

		$response = wp_authenticate( $parameters['user_name'], $parameters['user_password'] );

		if ( ! empty( $response->data->ID ) ) {

			$token  = bin2hex( openssl_random_pseudo_bytes( 64 ) );
			$status = update_user_meta( $response->data->ID, 'auth_token', $token );
			if ( ! empty( $status ) ) {

				return new WP_REST_Response(
					array(
						'user_id'    => $response->data->ID,
						'user_name'  => $response->data->display_name,
						'auth_token' => $token,
						'message'    => 'Logged in successfully',
						'status'     => 200,
					),
					200
				);
			}
		}

		return new WP_Error( 'Invalid', __( 'Invalid authentication', 'scebk' ), array( 'status' => 401 ) );
	}

	/**
	 * Function get add new expense for site.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function add_new_site_expense( $request ) {
		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $site_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );

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
					'message' => 'Added new expense successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'can`t-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Function to get expense of a particluar site by id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_site_all_expense( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $site_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_site_expense where site_id = %d",
			$site_id
		);
		$site_expense = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		if ( empty( $site_expense ) ) {
			return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
		}

		$response = new WP_REST_Response( $site_expense );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function get expense site for particular date.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_site_expense_date( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$taken_on   = $parameters['taken_on'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $site_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) || empty( $taken_on ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

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
	 * Function delete a particuar expense from site.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function delete_expense_site( $request ) {

		$parameters      = json_decode( $request->get_body(), true );
		$site_expense_id = $parameters['site_expense_id'];
		$token           = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $site_expense_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$where = array(
			'site_expense_id' => $site_expense_id,
		);

		global $wpdb;
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

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function update_site_expense( $request ) {

		$parameters      = json_decode( $request->get_body(), true );
		$site_expense_id = $parameters['site_expense_id'];
		$token           = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $site_expense_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$where = array(
			'site_expense_id' => $site_expense_id,
		);

		global $wpdb;
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

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to show all employee for a site.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_all_employee( $request ) {

		global $wpdb;

		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $site_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
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
					sum(amount) AS total_expense 
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
	 * Show a particular employee details.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_employee( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
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
					sum(amount) AS total_expense 
				FROM   $this->table_employee 
					LEFT JOIN $this->table_employee_expense 
						ON $this->table_employee_expense.emp_id = $this->table_employee.emp_id 
				WHERE  $this->table_employee.emp_id = %d
				GROUP BY $this->table_employee.emp_id ) AS emp 
				LEFT JOIN (SELECT $this->table_attendance.emp_id AS empID, 
							sum(work_hour)       AS total_work 
						FROM   $this->table_attendance 
						GROUP BY $this->table_attendance.emp_id) AS attendance 
					ON attendance.empid = emp.emp_id",
			$emp_id
		);
		
		$employees = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employees );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to delete employee for a site.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function delete_employee( $request ) {
		global $wpdb;

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		$where = array(
			'emp_id' => $emp_id,
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

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to update the employee dretails.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function update_employee( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$where = array(
			'emp_id' => $emp_id,
		);

		global $wpdb;
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

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to add new employee for site.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function add_new_employee( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$site_id    = $parameters['site_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $site_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$default = array(
			'site_id'         => $site_id,
			'emp_name'        => '',
			'emp_phone'       => '',
			'emp_ac'          => '',
			'emp_address'     => '',
			'emp_join_date'   => '',
			'emp_leave_date'  => '',
			'emp_rate'        => 0,
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

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to show all expense for an emplyee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_employee_all_expense_by_id( $request ) {

		global $wpdb;

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_employee_expense where emp_id = %d",
			$emp_id
		);

		$employee_expense = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employee_expense );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to show a particular date wise employee expense.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_employee_expense_date( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$taken_on   = $parameters['taken_on'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $emp_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) || empty( $taken_on ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_employee_expense where emp_id = %s and taken_on = %s",
			$emp_id,
			$taken_on
		);
		$employee_expense = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employee_expense );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to add new expense of employee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function add_new_employee_expense( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $emp_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$default = array(
			'emp_id'              => $emp_id,
			'taken_from'          => '',
			'amount'              => 0,
			'expense_description' => '',
			'taken_on'            => '',
		);

		$item = shortcode_atts( $default, $parameters );

		global $wpdb;
		$item = $wpdb->insert( $this->table_employee_expense, $item ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee expense added successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to update employee expense.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function update_employee_expense( $request ) {

		$parameters     = json_decode( $request->get_body(), true );
		$emp_expense_id = $parameters['emp_expense_id'];
		$token          = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $emp_expense_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$where = array(
			'emp_expense_id' => $emp_expense_id,
		);

		global $wpdb;
		$item  = $wpdb->update( $this->table_employee_expense, $parameters, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee expense updated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to delete the employee expense.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function delete_employee_expense( $request ) {

		$parameters     = json_decode( $request->get_body(), true );
		$emp_expense_id = $parameters['emp_expense_id'];
		$token          = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $emp_expense_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		$where = array(
			'emp_expense_id' => $emp_expense_id,
		);

		global $wpdb;
		$item = $wpdb->delete( $this->table_employee_expense, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee expense deleted successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to show the all attendance of an employee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_employee_all_attendance_by_id( $request ) {

		global $wpdb;

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_attendance where emp_id = %d",
			$emp_id
		);

		$employee_attendance = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employee_attendance );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to show attendance for a particular day.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function show_employee_attendance_date( $request ) {

		global $wpdb;
		$parameters = json_decode( $request->get_body(), true );
		$worked_on  = $parameters['worked_on'];
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $emp_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) || empty( $worked_on ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// @codingStandardsIgnoreStart
		$query = $wpdb->prepare(
			"select * from $this->table_attendance where emp_id = %s and worked_on = %s",
			$emp_id,
			$worked_on
		);
		$employee_attendance = $wpdb->get_results( $query, 'ARRAY_A' );
		// @codingStandardsIgnoreEnd

		$response = new WP_REST_Response( $employee_attendance );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Function to add attendace of an employee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function add_new_employee_attendance( $request ) {

		$parameters = json_decode( $request->get_body(), true );
		$emp_id     = $parameters['emp_id'];
		$token      = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( $parameters['auth_token'] !== $token[0] || empty( $parameters['user_id'] ) || empty( $emp_id ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$default = array(
			'emp_id'    => $emp_id,
			'work_hour' => 0,
			'worked_on' => '',
		);

		$item = shortcode_atts( $default, $parameters );

		global $wpdb;
		$item = $wpdb->insert( $this->table_attendance, $item ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee attendance added successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to update the attendace of an enmployee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function update_employee_attendance( $request ) {

		$parameters    = json_decode( $request->get_body(), true );
		$attendance_id = $parameters['attendance_id'];
		$token         = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $attendance_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		// Remove auth-token after verify.
		unset( $parameters['auth_token'] );
		unset( $parameters['user_id'] );

		$where = array(
			'attendance_id' => $attendance_id,
		);

		global $wpdb;
		$item  = $wpdb->update( $this->table_attendance, $parameters, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee attendance updated successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
	}

	/**
	 * Function to delete attendance of an employee.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array
	 */
	public function delete_employee_attendance( $request ) {

		$parameters    = json_decode( $request->get_body(), true );
		$attendance_id = $parameters['attendance_id'];
		$token         = get_user_meta( $parameters['user_id'], 'auth_token' );

		if ( empty( $attendance_id ) || $parameters['auth_token'] !== $token[0] || empty( $token ) ) {
			return new WP_Error( 'invalid-request', __( 'Your request is invalid', 'scebk' ), array( 'status' => 500 ) );
		}

		$where = array(
			'attendance_id' => $attendance_id,
		);

		global $wpdb;
		$item = $wpdb->delete( $this->table_attendance, $where ); //phpcs:ignore

		if ( ! empty( $item ) ) {

			return new WP_REST_Response(
				array(
					'message' => 'Employee attendance deleted successfully',
					'status'  => 200,
				),
				200
			);
		}

		return new WP_Error( 'not-found', __( 'Data not found', 'scebk' ), array( 'status' => 404 ) );
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
