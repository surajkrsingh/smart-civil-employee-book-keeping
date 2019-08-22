<?php
/**
 * Create custom form for new site.
 *
 * @since   1.0
 *
 * @package smart_civil_employee_book_keeping
 */

if ( isset( $_POST['submit'] ) ) {

	global $wpdb;
	$table_name = $wpdb->prefix . 'site';
	$file_path  = wp_handle_upload( $_FILES['site_pic_path'], array( 'test_form' => false ) );

	$file_url = '';
	if ( ! empty( $file_path ) ) {
		$file_url = filter_var( $file_path['url'], FILTER_SANITIZE_URL );
	}

	$default = array(
		'user_id'         => get_current_user_id(),
		'site_name'       => '',
		'site_contractor' => '',
		'site_address'    => '',
		'site_start_date' => '',
		'site_rate'       => '',
		'site_height'     => '',
		'site_pic_path'   => $file_url,
		'site_status'     => 1,
	);

	$item = shortcode_atts( $default, $_POST );

	if ( wp_verify_nonce( sanitize_key( $_POST['save_site_info'] ), 'scebk_site_nonce_action' ) ) {
		$wpdb->insert( $table_name, $item );
	} else {
		esc_html_e( 'Failed Nonce Verification ', 'scebk' );
	}
}

?>
<div class="wrap">
	<h1 id="add-new-user">Add New Site</h1>
	<p>Add a new site which is newly open.</p>
	<form method="post" name="createsite" id="createsite" class="site-form validate" novalidate="novalidate" enctype="multipart/form-data">
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row"><label for="site_name">Site Name <span class="description">(required)</span></label></th>
				<td><input name="site_name" type="text" id="site_name" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" /></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="site_contractor">Site Contractor <span class="description">(required)</span></label></th>
				<td><input name="site_contractor" type="email" id="site_contractor" value="" /></td>
			<tr class="form-field">
				<th scope="row"><label for="site_no_of_floor">No Of floor </label></th>
				<td><input name="site_height" type="number" id="site_height" value="" /></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="site_rate">Rate Per Squre Foot</label></th>
				<td><input name="site_rate" type="number" id="site_rate"  value="" /></td>
			</tr>
			</tr>
				<tr class="form-field">
				<th scope="row"><label for="site_address">Address </label></th>
				<td><textarea name="site_address" id="site_address" value=""></textarea></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="site_start_date">Start Date</label></th>
				<td><input name="site_start_date" type="date" id="site_start_date" class="code" value="" /></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="site_pic">Site Photo</label></th>
				<td><input name="site_pic_path" type="file" id="site_pic_path" value="" /></td>
			</tr>
		</table>
		<?php
		wp_nonce_field( 'scebk_site_nonce_action', 'save_site_info' );
		submit_button( 'Add New Site' );
		?>
	</form>
</div>
