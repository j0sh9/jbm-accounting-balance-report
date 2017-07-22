<?php
function jbm_accounting_report_get_rows($results,$payment_method) {

	$order_rows = "";
	$subtotal = 0;
	$shipping_total = 0;
	$total_tax = 0;
	$discount_total = 0;
	$total = 0;
	foreach($results as $order_key => $order_id) {
		$customer_order = wc_get_order($order_id->post_id);

		$order_date = wc_format_datetime( $customer_order->get_date_created(), 'Y-m-d H:i');
		if ( ! empty($customer_order->get_date_paid()) ) 
			$date_paid = wc_format_datetime( $customer_order->get_date_paid(), 'Y-m-d H:i' );
		else $date_paid = '';
		if ( ! empty($customer_order->get_date_completed()) ) 
			$date_completed = wc_format_datetime( $customer_order->get_date_completed(), 'Y-m-d H:i' );
		else $date_completed = '';
		
		$order_rows .= "
		<tr>
			<td><a href='https://".$_SERVER['SERVER_NAME']."/wp-admin/post.php?post=".$customer_order->get_id()."&action=edit' target='_blank'>".$date_paid."</a></td>
			<td>".$customer_order->get_status()."</td>
			<td>".number_format($customer_order->get_subtotal(),2)."</td>
			<td>".number_format($customer_order->get_shipping_total(),2)."</td>
			<td>".number_format($customer_order->get_total_tax(),2)."</td>
			<td>".number_format($customer_order->get_discount_total(),2)."</td>
			<td>".number_format($customer_order->get_total(),2)."</td>
		</tr>";
		
		$subtotal += $customer_order->get_subtotal();
		$shipping_total += $customer_order->get_shipping_total();
		$total_tax += $customer_order->get_total_tax();
		$discount_total += $customer_order->get_discount_total();
		$total += $customer_order->get_total();
	}
	$order_totals = "
	<tr>
		<th>".$customer_order->get_payment_method_title()."s</th>
		<td>".count($results)."</td>
		<td>".number_format($subtotal,2)."</td>
		<td>".number_format($shipping_total,2)."</td>
		<td>".number_format($total_tax,2)."</td>
		<td>".number_format($discount_total,2)."</td>
		<td>".number_format($total,2)."</td>
	</tr>";

	return $order_totals.$order_rows;
}

global $wpdb;
if ( isset($_GET['start_date']) ) $start_date = $_GET['start_date'];
if ( isset($start_date) ) {
	$start_date = date('Y-m-d 00:00:00', strtotime($start_date));
	if ( isset($end_date) ) {
		$end_date = date('Y-m-d 00:00:00', strtotime($end_date.' + 1 days'));
	} else {	
		$end_date = date('Y-m-d 00:00:00', strtotime($start_date.' + 1 days'));
	}
} else {
	$start_date = date('Y-m-d 00:00:00', strtotime('-1 days'));
	$end_date = date('Y-m-d 00:00:00', strtotime($start_date.' +1 days'));
}
$order_rows = '';
$prefix = $wpdb->prefix;
echo $prefix;
$gateways = new WC_Payment_Gateways;
$payment_methods = $gateways->get_payment_gateway_ids();
foreach($payment_methods as $payment_method) {
	$select = "SELECT post_id FROM ".$prefix."_postmeta
	WHERE meta_key = '_paid_date'
	AND DATE(meta_value) BETWEEN '$start_date' AND '$end_date'
	AND post_id IN (
	SELECT post_id FROM ".$prefix."_postmeta
	WHERE meta_key = '_payment_method'
	AND meta_value = '$payment_method'
	)";
	//$select .= "
	//AND post_id IN (SELECT ID FROM wp_posts WHERE post_status = 'wc-completed' OR post_status = 'wc-processing')";
	//$select .= "";
	$results = false;
	$results = $wpdb->get_results( $select, OBJECT );

	if ( $results ) {
		$order_rows .= jbm_accounting_report_get_rows($results, $payment_method); 
	}
}
	
?>
<div class="balance-report">
	<table class="jb-affiliate-report">
		<thead>
			<tr>
				<th>Payment Method</th>
				<th>Orders</th>
				<th>Sub Total</th>
				<th>Shipping</th>
				<th>Tax</th>
				<th>Discounts</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?=$order_rows?>
		</tbody>
	</table>
</div>
