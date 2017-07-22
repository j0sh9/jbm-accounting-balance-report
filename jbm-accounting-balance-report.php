<?php
/*
Plugin Name: _Payment Method Report
Description: Payment Method Report by oredr Paid Date
Version: 1.1
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
	$jb_page_title = 'Payment Method Report';
	$jb_menu_title = 'Payment Method Report';
	$jb_capability = 'manage_affiliates';
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

<h1>Payment Method Report</h1>
<div>
<form action="" method="post" class="basic_order_search_form">
	<label>Start Date: <input type="date" name="start_date" id="start_date" value="<?=$start_date;?>"   /></label> Order PAID Date
	<br>
	<label>End Date: <input type="date" name="end_date" id="end_date" value="<?=$end_date;?>"   /></label><br>
	<p><button class="button" type="submit" name="jbm_order_query" value="1" >Search Orders</button></p>
</form>	
</div>
<?php
			include plugin_dir_path( __FILE__ )."get-reports.php";
}

