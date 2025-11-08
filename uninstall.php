<?php
/**
 * Uninstall script
 *
 * @package Simple_Sequential_Order_Number
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
delete_option( 'sson_order_number_prefix' );
delete_option( 'sson_order_number_start' );
delete_option( 'sson_order_number_suffix' );
delete_option( 'sson_order_number_length' );

// Note: We don't delete order meta (_order_number, _order_number_formatted)
// as these are part of order data and should be preserved

