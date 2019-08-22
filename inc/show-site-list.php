<?php
/**
 * Show all site.
 *
 * @since   1.0
 *
 * @package smart_civil_employee_book_keeping
 */

?>
<div class="wrap">
	<h1 class="wp-heading-inline"> Site </h1>
	<a href="http://emp.test/wp-admin/admin.php?page=add-site" class="page-title-action">Add New</a>
	<form method="get">
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td  id='cb' class='manage-column column-cb check-column'>
						<input id="cb-select-all-1" type="checkbox" />
					</td>
					<th scope="col" id='username' class='manage-column column-username column-primary'>
						<span>Site Name</span>
					</th>
					<th scope="col" id='name' class='manage-column column-name'>Contractor</th>
					<th scope="col" id='rate' class='manage-column'>
						<span>Rate</span>
					</th>
					<th scope="col" id='role' class='manage-column'>Total Taken</th>
					<th scope="col" id='role' class='manage-column'>Total Employee</th>
					<th scope="col" id='posts' class='manage-column'>Status</th>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists='list:site'>
				<?php
					global $wpdb;
					$table_name = $wpdb->prefix . 'site';
					$sites      = $wpdb->get_results( "SELECT * FROM $table_name where user_id=" . get_current_user_id() );
				foreach ( $sites as $site ) {
					?>
				<tr id='user-1'>
					<th scope='row' class='check-column'>
						<input type='checkbox' name='sites[]' id='user_<?php echo $site->site_id; ?>' class='administrator' value='1' />
					</th>
					<td class=' column-username has-row-actions column-primary' data-colname="Username">
						<img alt='' src=' <?php echo $site->site_pic_path; ?>' class='avatar avatar-32 photo' height='32' width='32' />
						<strong>
							<span><?php echo esc_html( $site->site_name ); ?></span>
						</strong><br />
						<div class="row-actions">
							<span class='edit'>
								<a href="http://emp.test/wp-admin/user-edit.php?user_id=1&#038;wp_http_referer=%2Fwp-admin%2Fusers.php">Edit</a>
							</span>
							<span class='delete'>
								<a class='submitdelete' href='users.php?action=delete&amp;user=1&amp;_wpnonce=53cff04ff7'>Delete</a>
							</span>
							<span class='view'>
								<a href="http://emp.test/author/dazzling-maxwell/" aria-label="View posts by dazzling-maxwell">View</a>
							</span>
						</div>
					</td>
					<td class='manage-column' data-colname="Name">
					<?php echo $site->site_contractor; ?>
					</td>
					<td class='manage-column' data-colname="Email">
					<?php echo $site->site_rate; ?>
					</td>
					<td class='manage-column' data-colname="Role">
						Administrator
					</td>
					<td class='manage-column' data-colname="Role">
						Administrator
					</td>
					<td class='manage-column' data-colname="Posts">
						1
					</td>
				</tr>
				<?php } ?>
			<tfoot>
				<tr>
					<td  id='cb' class='manage-column column-cb check-column'>
						<input id="cb-select-all-1" type="checkbox" />
					</td>
					<th scope="col" id='username' class='manage-column column-username column-primary'>
						<span>Site Name</span>
					</th>
					<th scope="col" id='name' class='manage-column column-name'>Contractor</th>
					<th scope="col" id='rate' class='manage-column'>
						<span>Rate</span>
					</th>
					<th scope="col" id='role' class='manage-column'>Total Taken</th>
					<th scope="col" id='role' class='manage-column'>Total Employee</th>
					<th scope="col" id='posts' class='manage-column'>Status</th>
				</tr>
			</tfoot>
		</table>     
	</form>    
</div>
