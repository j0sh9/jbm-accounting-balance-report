<?php
/*
Plugin Name: _Accounting Report
Description: Accounting Report Page
Version: 1.0
*/

function jbm_accounting_report_faux_api() {
  if ( !isset($_GET['jbm_accounting_report_faux_api']) ) return;

  include plugin_dir_path( __FILE__ )."get-reports.php";
  die();
}
add_action( 'template_redirect', 'jbm_accounting_report_faux_api' );

add_action( 'admin_menu', 'jbm_accounting_report' );
function jbm_accounting_report() {
	$jb_parent_slug = 'woocommerce';
	$jb_page_title = 'Daily Accounting Report';
	$jb_menu_title = 'Accounting Report';
	$jb_capability = 'manage_options';
	$jb_menu_slug = 'jbm-accounting-report';
	$jb_callback = 'jbm_accounting_report_html';
	//$jb_icon_url = 'dashicons-media-spreadsheet';
	//$jb_menu_position = 120;
	add_submenu_page(  $jb_parent_slug, $jb_page_title,  $jb_menu_title,  $jb_capability,  $jb_menu_slug,  $jb_callback );
}

function jbm_accounting_report_html() {
	if ( isset($_POST['start_date']) ) {
		$start_date = $_POST['start_date'];
	} else { 
		$start_date = date('Y-m-d', strtotime(current_time('mysql').' -1 days'));
	}
	if ( isset($_POST['end_date']) ) {
		$end_date = $_POST['end_date'];
	} else { 
		$end_date = date('Y-m-d', strtotime($start_date));
	}
	
	$completed_check = $processing_check = $pending_check = $on_hold_check = $refunded_check = $cancelled_check = $failed_check = '';
	if ( isset($_POST["order_status"]) ) {
		foreach ( $_POST["order_status"] as $check_status ) {
			$varvar = str_replace('-','_',$check_status).'_check';
			${$varvar} = 'checked';
			$order_statuss[] = 'wc-'.$check_status;
		}
	} else {
		$completed_check = 'checked';
		$completed = 'completed';
		$order_statuss = array('wc-completed');
	}
		
?>
<style>
	.jb-affiliate-report, .jb-affiliate-report th, .jb-affiliate-report td {
		border: 1px solid #cdcdcd;
		border-collapse: collapse;
	}
	.jb-affiliate-report {
		width: 97%;
		margin: 3vw 1vw;
	}
	.jb-affiliate-report th, .jb-affiliate-report td {
		padding: 4px 8px;
	}
	.basic_order_search_form label {
		padding: 3px;
	}
</style>

<h1>Basic Order Report</h1>
<div>
<form action="" method="post" class="basic_order_search_form">
	<label>Start Date: <input type="date" name="start_date" id="start_date" value="<?=$start_date;?>"   /></label> Order Creation Date
	<br>
	<label>End Date: <input type="date" name="end_date" id="end_date" value="<?=$end_date;?>"   /></label><br>
	<label><input type="checkbox" name="order_status[]" value="completed" <?=$completed_check?> /> Completed</label> | 
	<label><input type="checkbox" name="order_status[]" value="processing" <?=$processing_check?> /> Processing</label> | 
	<label><input type="checkbox" name="order_status[]" value="pending" <?=$pending_check?> /> Pending</label> | 
	<label><input type="checkbox" name="order_status[]" value="on-hold" <?=$on_hold_check?> /> On Hold</label> | 
	<label><input type="checkbox" name="order_status[]" value="refunded" <?=$refunded_check?> /> Refunded</label> | 
	<label><input type="checkbox" name="order_status[]" value="cancelled" <?=$cancelled_check?> /> Cancelled</label> | 
	<label><input type="checkbox" name="order_status[]" value="failed" <?=$failed_check?> /> Failed</label>
	<p><button class="button" type="submit" name="jbm_order_query" value="1" >Search Orders</button></p>
</form>	
</div>
<?php
			include plugin_dir_path( __FILE__ )."get-reports.php";
}

