<?php
/**
 * This file contain code to disable unwanted menus and submenus.
 *
 * @since 1.0.0
 *
 * @package smart_civil_employee_book_keeping
 */

/**
 * Function to check user is admin or not.
 *
 * @return boolean
 */
function is_site_administrator() {
	return in_array( 'administrator', wp_get_current_user()->roles );
}

/**
 * Remove unwanted menu from admin.
 *
 * @return void
 */
function remove_menu() {

	if ( ! is_site_administrator() ) {
		remove_menu_page( 'options-general.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'themes.php' );
		remove_submenu_page( 'index.php', 'update-core.php' );
	}

	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit.php?post_type=page' );
	remove_menu_page( 'edit-comments.php' );
	remove_menu_page( 'tools.php' );
	remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
	remove_submenu_page( 'options-general.php', 'options-writing.php' );
	remove_submenu_page( 'options-general.php', 'options-reading.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	remove_submenu_page( 'options-general.php', 'options-permalink.php' );
	remove_submenu_page( 'options-general.php', 'privacy.php' );
	remove_action( 'admin_menu', '_add_themes_utility_last', 101 );

	// Remove customize option.
	$request = urlencode( filter_input( INPUT_SERVER, 'REQUEST_URI' ) );
	remove_submenu_page( 'themes.php', 'customize.php?return=' . $request );
}
add_action( 'admin_menu', 'remove_menu' );

/**
 * It will remove the tabs, not hide them with CSS.
 *
 * @param string $old_help
 * @param int    $screen_id
 * @param bool   $screen
 *
 * @return string $old_help
 */
function scebk_remove_help_tabs( $old_help, $screen_id, $screen ) {
	$screen->remove_help_tabs();
	return $old_help;
}
add_filter( 'contextual_help', 'scebk_remove_help_tabs', 999, 3 );

/**
 * Disable default dashboard widgets.
 *
 * @return void
 */
function disable_default_dashboard_widgets() {
	global $wp_meta_boxes;
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_welcome'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] );
}
add_action( 'wp_dashboard_setup', 'disable_default_dashboard_widgets', 999 );
remove_action( 'welcome_panel', 'wp_welcome_panel' );

/**
 * Function to remove comments option from admin bar menu.
 *
 * @return void
 */
function remove_admin_bar_item() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'comments' );
	$wp_admin_bar->remove_menu( 'view-site' );
	$wp_admin_bar->remove_menu( 'site-name' );
	$wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_item' );

/**
 * Function to remove the add sub menu from header bar.
 *
 * @return void
 */
function remove_add_new_elements() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_node( 'new-post' );
	$wp_admin_bar->remove_node( 'new-link' );
	$wp_admin_bar->remove_node( 'new-media' );
	$wp_admin_bar->remove_node( 'new-page' );
	$wp_admin_bar->remove_node( 'new-user' );
	$wp_admin_bar->remove_menu( 'new-content' );
}
add_action( 'admin_bar_menu', 'remove_add_new_elements', 999 );
/**
 * Function to replace footer in admin site.
 *
 * @return void
 */
function remove_footer_admin() {
	echo '<span id="footer-thankyou">Developed by <a href="http://www.bitscamp.com" target="_blank">www.bitscamp.com</a></span>';
	// Remove WordPress version from footer.
	remove_filter( 'update_footer', 'core_update_footer' );
}

add_filter( 'admin_footer_text', 'remove_footer_admin' );

/**
 * Function to remove user role from admin site.
 *
 * @param array $all_roles List of roles.
 */
function scebk_user_role_dropdown( $all_roles ) {

	unset( $all_roles['author'] );
	unset( $all_roles['editor'] );
	unset( $all_roles['subscriber'] );
	unset( $all_roles['contributor'] );

	return $all_roles;
}

add_filter( 'editable_roles', 'scebk_user_role_dropdown' );

/**
 * Function to add new role as like admin.
 *
 * @return void
 */
function create_site_admin_role() {
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$adm = $wp_roles->get_role( 'administrator' );
	$wp_roles->add_role( 'site_contractor', 'Contractor', $adm->capabilities );
}

add_action( 'init', 'create_site_admin_role' );

/**
 * Function to add logo on admin bar.
 *
 * @param array $admin_bar
 * @return void
 */
function add_logo_admin_bar( $admin_bar ) {
	$admin_bar->add_menu(
		array(
			'id'    => 'my-item',
			'title' => 'Smart Civil Employee Book Keeping',
			'href'  => 'index.php',
		)
	);
}
add_action( 'admin_bar_menu', 'add_logo_admin_bar', 100 );
