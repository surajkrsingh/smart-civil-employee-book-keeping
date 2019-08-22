<?php
/**
 * Create custom form for new site.
 *
 * @since   1.0
 *
 * @package smart_civil_employee_book_keeping
 */

global $wpdb;

if ( isset( $_POST['submit'] ) ) {
	$table_name = $wpdb->prefix . 'site_expense';
	$default    = array(
		'site_id'             => '',
		'taken_from'          => '',
		'amount'              => 0,
		'expense_description' => '',
		'taken_on'            => '',
	);

	$item = shortcode_atts( $default, $_POST );

	if ( wp_verify_nonce( sanitize_key( $_POST['save_site_expense'] ), 'scebk_site_nonce_action' ) ) {
		$wpdb->insert( $table_name, $item );
	} else {
		esc_html_e( 'Failed Nonce Verification ', 'scebk' );
	}
}

$table_site = $wpdb->prefix . 'site';
$user_id    = get_current_user_id();
if ( empty( $user_id ) ) {
	die( 'User is not logged-in' );
}

$query = $wpdb->prepare(
	"SELECT site_id, site_name FROM $table_site where user_id = %d", //phpcs:ignore
	$user_id
);

$sites = $wpdb->get_results( $query ); //phpcs:ignore

?>
<div class="wrap">
	<h1 id="add-new-user">Add Site Expense</h1>
	<p>Add a new expense for site.</p>
	<form method="post" name="create_site_expense" id="create_site_expense" class="site-form validate" novalidate="novalidate" enctype="multipart/form-data">
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row"><label for="site_id">Select site name <span class="description">(required)</span></label></th>
				<td>
					<select name="site_id"  id="site_id">
					<?php
					foreach ( $sites as $site ) {
						printf( "<option value='%d'>%s</option>", esc_attr( $site->site_id ), esc_html( $site->site_name ) );
					}
					?>
					</select>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="taken_from">Enter Contractor <span class="description">(required)</span></label></th>
				<td><input name="taken_from" type="email" id="taken_from" value="" /></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="amount">Amount</label></th>
				<td><input name="amount" type="number" id="amount"  value="" /></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="expense_description">Expense description </label></th>
				<td><textarea name="expense_description" id="expense_description" value=""></textarea></td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="taken_on">Select Date</label></th>
				<td><input name="taken_on" type="date" id="taken_on" value="" /></td>
			</tr>
		</table>
		<?php
		wp_nonce_field( 'scebk_site_nonce_action', 'save_site_expense' );
		submit_button( 'Add New Expense' );
		?>
	</form>
</div>
