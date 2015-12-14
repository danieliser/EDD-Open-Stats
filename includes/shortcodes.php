<?php
/**
 * Shortcode Functions
 *
 * @package     EDD\OpenStats\Shortcodes
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Gets the average number of sales per customer. Sales(not orders) are divided by the total number of customers.
 * @param  array $atts Shortcode Attributes - Currently None
 * @return float Avg sales per customer rounded to the nearest hundredth.
 */
function edd_open_stats_avg_sales_per_customer( $atts ) {
	$amount = 0;
	$amount = edd_open_stats_sales() / edd_count_total_customers();
	return number_format( $amount, 2 );
}
add_shortcode( 'edd_avg_sales_per_customer', 'edd_open_stats_avg_sales_per_customer' );


/**
 * Gets the average number of orders per customer. Total orders are divided by total customers.
 * @param  array $atts Shortcode Attributes - Currently None
 * @return float Avg orders per customer rounded to the nearest hundredth.
 */
function edd_open_stats_avg_orders_per_customer( $atts ) {
	$amount = edd_get_total_sales() / edd_count_total_customers();
	return number_format( $amount, 2 );
}
add_shortcode( 'edd_avg_orders_per_customer', 'edd_open_stats_avg_orders_per_customer' );


// Sales count
function edd_open_stats_sales( $atts ) {
	$amount = 0;
	$query = new WP_Query( array( 'post_type' => 'download' ) );
	foreach( $query->posts as $post ) {
		$amount = $amount + edd_get_download_sales_stats( $post->ID );
	}
	return $amount;
}
add_shortcode( 'edd_sales', 'edd_open_stats_sales' );

// Orders count
function edd_open_stats_orders( $atts ) {
	return '<span class="amount">' . edd_get_total_sales() . '</span>';
}
add_shortcode( 'edd_orders', 'edd_open_stats_orders' );

// Orders count today
function edd_open_stats_orders_today( $atts ) {
	$stats = new EDD_Payment_Stats;
	$amount = $stats->get_sales( 0, 'today', false, array( 'publish', 'revoked' ) );
	return '<span class="amount">' . $amount . '</span>';
}
add_shortcode( 'edd_orders_today', 'edd_open_stats_orders_today' );

// Orders count month
function edd_open_stats_orders_month( $atts ) {
	$stats = new EDD_Payment_Stats;
	$amount = $stats->get_sales( 0, 'this_month', false, array( 'publish', 'revoked' ) );
	return '<span class="amount">' . $amount . '</span>';
}
add_shortcode( 'edd_orders_month', 'edd_open_stats_orders_month' );

// Avg. order size
function edd_open_stats_avg_order( $atts ) {
	$amount = edd_get_total_earnings() / edd_get_total_sales();
	return '$' . '<span class="amount">' . number_format($amount) . '</span>';
}
add_shortcode( 'edd_avg_order', 'edd_open_stats_avg_order' );

// Avg. spend by customer
function edd_open_stats_avg_spend_per_customer( $atts ) {
	$totals = edd_get_total_earnings();
	$customers = edd_count_total_customers();
	$amount = $totals / $customers;
	return '$' . '<span class="amount">' . number_format( $amount ) . '</span>';
}
add_shortcode( 'edd_avg_spend_per_customer', 'edd_open_stats_avg_spend_per_customer' );

// Show customers count
function edd_open_stats_count_total_customers( $atts ) {
	return '<span class="amount">' . edd_count_total_customers() . '</span>';
}
add_shortcode( 'edd_count_total_customers', 'edd_open_stats_count_total_customers' );

// Show today sales
function edd_open_stats_today_sales( $atts ) {
	$stats = new EDD_Payment_Stats;
	return '$' . '<span class="amount">' . number_format( $stats->get_earnings( 0, 'today', false ) ) . '</span>';
}
add_shortcode( 'edd_today_sales', 'edd_open_stats_today_sales' );

// Show monthly sales
function edd_open_stats_month_sales( $atts ) {
	$stats = new EDD_Payment_Stats;
	return '$' . '<span class="amount">' . number_format( $stats->get_earnings( 0, 'this_month' ) ) . '</span>';
}
add_shortcode( 'edd_month_sales', 'edd_open_stats_month_sales' );

// Show total sales
function edd_open_stats_total_sales( $atts ) {
	return '$' . '<span class="amount">' . number_format( edd_get_total_earnings() ) . '</span>';
}
add_shortcode( 'edd_total_sales', 'edd_open_stats_total_sales' );


// recent payments
function edd_open_stats_recent_payments( $atts ) {

	$p_query = new EDD_Payments_Query( array(
		'number'   => 12,
		'status'   => 'publish'
	) );

	$payments = $p_query->get_payments();

	if ( $payments ) { ?>
	<div class="table recent_purchases edd-frontend-purchases">
		<table>
			<tbody>
				<?php
				$i = 0;
				foreach ( $payments as $payment ) {

					$i++;
					
					$items = '';
					$saved = 0;
					foreach($payment->cart_details as $k => $arr ) {
						$saved = $saved + $arr['discount'];
						$items .= '<span>' . $arr['name'] .'</span>';
					}
					
					if ( $saved > 0 ) {
						$discount = '<span class="edd_price_save">&mdash; Saved <span>$' . $saved . '</span> on this order with a discount code</span>';
					} else {
						$discount = '';
					}
					
					$when = human_time_diff( strtotime( $payment->date ), current_time('timestamp') );
					$when = '<span class="edd_bought_when">&mdash; ' . $when . ' ago</span>';
					
					?>
					<tr style="<?php if ( $i == count( $payments ) ) { echo 'opacity:0.4;'; } ?> <?php if ( $i == count( $payments ) - 1 ) { echo 'opacity:0.8;'; } ?>">
						<td class="edd_order_amount"><span class="edd_price_label"><?php echo edd_currency_filter( edd_format_amount( $payment->total ), edd_get_payment_currency_code( $payment->ID ) ); ?></span></td>
						<td class="edd_order_label">Someone bought <span class="edd_items_bought"><?php echo $items; ?></span> <?php echo $discount; ?> <?php echo $when; ?></td>
					</tr>
					<?php
				} // End foreach ?>
			</tbody>
		</table>
	</div>
	<?php }
}
add_shortcode( 'edd_recent_payments', 'edd_open_stats_recent_payments' );