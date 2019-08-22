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
		add_action( 'rest_api_init', array( $this, 'register_custom_endpoints_for_site' ) );
	}


	/**
	 * Function to resgiter custom endpoints for site info.
	 */
	public function register_custom_endpoints_for_site() {
		// http://emp.test/wp-json/scebk/v1/site/?user_id=2
		//https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
		register_rest_route(
			'scebk/v1',
			'/site',
			array(
				'method'   => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_all_site_by_user_id' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
				'args'     => array(
					'context'       => array(
						'default' => 'view',
					),
					'wp_rest_nonce' => array(
						'validate_callback' => array( $this, 'create_wp_rest_nonce' ),
					),
				),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'add_new_site' ),
				// 'permission_callback' => array( $this, 'check_site_permission' ),
				'args'     => $this->get_endpoint_args_for_item_schema( true ),
			)
		);

		//http://emp.test/wp-json/scebk/v1/site/2
		register_rest_route(
			'scebk/v1',
			'/site/(?P<user_id>[\d]+)',
			array(
				'method'   => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_all_site_by_user_id' ),
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
	}

	/**
	 * Function get site details by user id.
	 *
	 * @param WP_REST_Request $request User request.
	 * @return array $response Json data.
	 */
	public function get_all_site_by_user_id( $request ) {

		global $wpdb;
		$table_site         = $wpdb->prefix . 'site';
		$table_site_expense = $wpdb->prefix . 'site_expense';
		$table_employee     = $wpdb->prefix . 'employee';
		$parameters         = $request->get_params();
		$user_id            = $parameters['user_id'];

		if ( empty( $user_id ) ) {
			return;
		}

		$sites = get_transient( 'user' . $user_id );

		if ( empty( $sites ) ) {
			// @codingStandardsIgnoreStart
			$query = $wpdb->prepare(
				"select 
				*
				from
				(
					select
						$table_site.site_id,
						site_name,
						site_contractor,
						site_address,
						site_start_date,
						site_rate,
						site_height,
						site_pic_path,
						site_status,
						sum(amount) as total_amount
					from
						$table_site
						left join $table_site_expense on $table_site_expense.site_id = $table_site.site_id
					where
						user_id = %d
					group by
						$table_site.site_id
				) as site
				left join
					(
						select
							$table_employee.site_id,
							count(emp_id) as total_employee
						from
							$table_employee
						group by
							$table_employee.site_id
					) as emp on site.site_id = emp.site_id",
				$user_id
			);

			$sites = $wpdb->get_results( $query, 'ARRAY_A' );
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
		$table_site = $wpdb->prefix . 'site';
		return json_encode( $request );
		die();
		$item = '';
		if ( function_exists( 'slug_some_function_to_create_item' ) ) {
			$data = slug_some_function_to_create_item( $item );
			if ( is_array( $data ) ) {
				return new WP_REST_Response( $data, 200 );
			}
		}

		return new WP_Error( 'cant-create', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
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

/*
$query = $wpdb->prepare(
		"SELECT sum(amount) as amount FROM $table_site_expense where site_id = %d", //phpcs:ignore
		$site['site_id']
	);

$site_expenses = $wpdb->get_results( $query , 'ARRAY_A' ); //phpcs:ignore

"SELECT $table_site.site_id, site_name, site_contractor,site_address, site_start_date,
site_rate, site_height,site_pic_path,site_status, sum(amount) as amount
from wp_site left join $table_site_expense on wp_site_expense.site_id = wp_site.site_id
where user_id=%d GROUP by $table_site.site_id"

 */
