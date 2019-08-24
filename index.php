<?php
/**
 * Smart-civil-employee-book-keeping
 *
 * @package smart_civil_employee_book_keeping
 * @author  Suraj kumar Singh
 * @license GPL-2.0+
 *
 * @WordPress-plugin
 * Plugin Name: smart-civil-employee-book-keeping
 * Plugin URI:  https://github.com/surajkrsingh/smart-civil-employee-book-keeping
 * Description: This is employee work tacking plugin that will keep the information and employee daily report as well as final report.
 * Version:     1.0.0
 * Author:      Suraj kumar Singh
 * Author URI:  https://profiles.wordpress.org/surajkumarsingh/
 * Text Domain: smart-civil-employee-book-keeping
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file called directly then abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'SCEBK_PLUGIN_VERSION' ) ) {
	define( 'SCEBK_PLUGIN_VERSION', '1.0.0' );
}
if ( ! defined( 'SCEBK_PLUGIN_DIR' ) ) {
	define( 'SCEBK_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'SCEBK_PLUGIN_PATH' ) ) {
	define( 'SCEBK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'SCEBK_PLUGIN_DIR_NAME' ) ) {
	define( 'SCEBK_PLUGIN_DIR_NAME', dirname( plugin_basename( __FILE__ ) ) );
}

require_once SCEBK_PLUGIN_PATH . 'inc/classes/class-scebk.php';
require_once SCEBK_PLUGIN_PATH . 'inc/classes/class-site.php';
require_once SCEBK_PLUGIN_PATH . 'inc/classes/class-employee.php';
require_once SCEBK_PLUGIN_PATH . 'inc/classes/class-scebk-restapi.php';
require_once SCEBK_PLUGIN_PATH . 'inc/custom-function.php';
require_once SCEBK_PLUGIN_PATH . 'inc/customize-admin-site.php';

/**
 * Activation of plugin.
 */
function scebk_activation() {
	$scebk_obj = scebk_plugin\Employee::get_instance();
	$scebk_obj->create_employee_table();
	$scebk_obj->create_employee_expense_table();
	$scebk_obj->create_attendance_table();

	$site_obj = scebk_plugin\Site::get_instance();
	$site_obj->create_site_table();
	$site_obj->create_site_expense_table();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'scebk_activation' );

/**
 * Deactivation of plugin.
 */
function scebk_deactivation() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'scebk_deactivation' );
