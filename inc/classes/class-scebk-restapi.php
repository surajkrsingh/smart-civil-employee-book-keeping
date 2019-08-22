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
		//http://emp.test/wp-json/scebk/v1/site/?user_id=2
		register_rest_route(
			'scebk/v1',
			'/site',
			array(
				'method'              => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_all_site_by_user_id' ),
				//'permission_callback' => array( $this, 'check_site_permission' ),
				'args'                => array(
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
		$table_site = $wpdb->prefix . 'site';
		$table_site_expense = $wpdb->prefix . 'site_expense';
		$parameters = $request->get_params();
		$user_id    = $parameters['user_id'];

		if ( empty( $user_id ) ) {
			return;
		}

		$sites = get_transient( 'user' . $user_id );

		if ( empty( $sites ) ) {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_site where user_id = %d", //phpcs:ignore
				$user_id
			);

			$sites = $wpdb->get_results( $query ); //phpcs:ignore

			$query = $wpdb->prepare(
				"SELECT * FROM $table_site_expense where site_id = %d", //phpcs:ignore
				$sites->site_id
			);

			$site_expenses = $wpdb->get_results( $query ); //phpcs:ignore
			// foreach ( $sites as $site ) {
			// 	//if ( )
			// }
			set_transient( 'user' . $user_id, $sites, 300 );
		}

		$response = new WP_REST_Response( $sites );
		$response->set_status( 200 );

		return $response;
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

