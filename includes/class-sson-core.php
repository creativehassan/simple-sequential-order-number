<?php
/**
 * Core functionality class
 *
 * @package Simple_Sequential_Order_Number
 */

defined( 'ABSPATH' ) || exit;

/**
 * SSON_Core class
 */
class SSON_Core {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Return custom order number for display (just format existing order ID with prefix/suffix)
		// Priority 5 to run early and ensure our formatted number is used
		add_filter( 'woocommerce_order_number', array( $this, 'get_order_number' ), 5, 2 );

		// Enable order search by order number
		add_filter( 'woocommerce_shortcode_order_tracking_order_id', array( $this, 'find_order_by_order_number' ), 1 );

		// Admin hooks
		if ( is_admin() ) {
			add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'add_search_fields' ) );
			add_filter( 'woocommerce_order_table_search_query_meta_keys', array( $this, 'add_search_fields' ) );
		}
	}


	/**
	 * Get order number for display
	 *
	 * Returns the formatted order number (prefix + order_id + suffix).
	 * Uses the existing WooCommerce order ID, just adds prefix and suffix.
	 * WooCommerce will automatically add "#" before this when displaying.
	 *
	 * @param string   $order_number Default order number (order ID)
	 * @param WC_Order $order        Order object
	 * @return string Formatted order number (prefix + order_id + suffix, without #)
	 */
	public function get_order_number( $order_number, $order ) {
		// Don't modify subscription objects
		if ( $order instanceof WC_Subscription ) {
			return $order_number;
		}

		// Format the order ID with prefix and suffix (simple, no caching needed)
		return $this->format_order_number( $order->get_id(), $order->get_id() );
	}

	/**
	 * Format order number with prefix, suffix, and padding
	 *
	 * Formats the order ID (not sequential, just the existing order ID) with prefix and suffix.
	 *
	 * @param int $order_number Order ID (the actual WooCommerce order ID)
	 * @param int $order_id     Order ID (same as order_number)
	 * @return string Formatted order number (format: prefix + order_id + suffix)
	 */
	public function format_order_number( $order_number, $order_id = 0 ) {
		$order_number = (int) $order_number;
		$prefix = $this->get_prefix();
		$suffix = $this->get_suffix();
		$length = $this->get_length();

		// Apply padding - convert to string with zero padding if length is set
		if ( $length > 0 ) {
			$order_number_str = sprintf( "%0{$length}d", $order_number );
		} else {
			$order_number_str = (string) $order_number;
		}

		// Apply date patterns to prefix and suffix
		$prefix = $this->replace_date_patterns( $prefix );
		$suffix = $this->replace_date_patterns( $suffix );

		// Combine: prefix + order_id + suffix
		// WooCommerce will add "#" before this when displaying, resulting in: #prefix1983postfix
		$formatted = $prefix . $order_number_str . $suffix;

		/**
		 * Filter formatted order number
		 *
		 * @param string $formatted     Formatted order number (prefix + order_id + suffix)
		 * @param string $order_number_str Order ID as string (with padding if applicable)
		 * @param int    $order_id     Order ID
		 */
		return apply_filters( 'sson_formatted_order_number', $formatted, $order_number_str, $order_id );
	}

	/**
	 * Replace date patterns in string
	 *
	 * @param string $string String with patterns
	 * @return string String with replaced patterns
	 */
	private function replace_date_patterns( $string ) {
		$patterns = array(
			'{D}'    => date_i18n( 'j' ),
			'{DD}'   => date_i18n( 'd' ),
			'{M}'    => date_i18n( 'n' ),
			'{MM}'   => date_i18n( 'm' ),
			'{YY}'   => date_i18n( 'y' ),
			'{YYYY}' => date_i18n( 'Y' ),
			'{H}'    => date_i18n( 'G' ),
			'{HH}'   => date_i18n( 'H' ),
			'{N}'    => date_i18n( 'i' ),
			'{S}'    => date_i18n( 's' ),
		);

		return str_ireplace( array_keys( $patterns ), $patterns, $string );
	}

	/**
	 * Find order by order number
	 *
	 * Extracts order ID from formatted number by removing prefix and suffix
	 *
	 * @param string|int $order_number Order number to search for (can be formatted like "TST1983" or just "1983")
	 * @return int Order ID or 0 if not found
	 */
	public function find_order_by_order_number( $order_number ) {
		// Remove # prefix if present
		$order_number = ltrim( (string) $order_number, '#' );

		// Extract order ID by removing prefix and suffix
		$prefix = $this->get_prefix();
		$suffix = $this->get_suffix();
		
		if ( $prefix && strpos( $order_number, $prefix ) === 0 ) {
			$order_number = substr( $order_number, strlen( $prefix ) );
		}
		if ( $suffix && substr( $order_number, -strlen( $suffix ) ) === $suffix ) {
			$order_number = substr( $order_number, 0, -strlen( $suffix ) );
		}

		// Get order by ID
		$order_id = absint( $order_number );
		if ( $order_id > 0 && wc_get_order( $order_id ) ) {
			return $order_id;
		}

		return 0;
	}

	/**
	 * Add search fields for order number
	 *
	 * @param array $fields Search fields
	 * @return array Modified search fields
	 */
	public function add_search_fields( $fields ) {
		// Search works by extracting order ID from formatted number, so no meta search needed
		return $fields;
	}


	/**
	 * Get order number prefix
	 *
	 * @return string
	 */
	private function get_prefix() {
		return get_option( 'sson_order_number_prefix', '' );
	}

	/**
	 * Get order number suffix
	 *
	 * @return string
	 */
	private function get_suffix() {
		return get_option( 'sson_order_number_suffix', '' );
	}

	/**
	 * Get order number length
	 *
	 * @return int
	 */
	private function get_length() {
		return absint( get_option( 'sson_order_number_length', 0 ) );
	}
}

