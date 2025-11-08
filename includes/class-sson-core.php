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

		// Admin hooks for search
		if ( is_admin() ) {
			// Hook into order search to extract order ID from formatted numbers
			add_filter( 'woocommerce_order_list_table_prepare_items_query_args', array( $this, 'modify_order_search' ), 10, 1 );
			add_action( 'parse_query', array( $this, 'modify_order_search_legacy' ), 20 );
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
		// Don't modify subscription objects (if WooCommerce Subscriptions is active)
		if ( class_exists( 'WC_Subscription' ) && is_a( $order, 'WC_Subscription' ) ) {
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
	 * Modify order search for HPOS (High-Performance Order Storage)
	 * Extracts order ID from formatted order numbers
	 *
	 * @param array $query_args Query arguments
	 * @return array Modified query arguments
	 */
	public function modify_order_search( $query_args ) {
		if ( ! isset( $query_args['s'] ) || empty( $query_args['s'] ) ) {
			return $query_args;
		}

		$search_term = trim( $query_args['s'] );
		
		// Try to extract order ID from formatted number
		$order_id = $this->extract_order_id_from_search( $search_term );
		
		if ( $order_id > 0 ) {
			// Replace search term with order ID (WooCommerce handles numeric searches for order IDs)
			$query_args['s'] = (string) $order_id;
		}

		return $query_args;
	}

	/**
	 * Modify order search for legacy (non-HPOS) stores
	 * Extracts order ID from formatted order numbers
	 *
	 * @param WP_Query $query Query object
	 */
	public function modify_order_search_legacy( $query ) {
		global $pagenow, $typenow;

		// Only on orders page
		if ( 'edit.php' !== $pagenow || 'shop_order' !== $typenow ) {
			return;
		}

		// Only if there's a search term
		if ( ! isset( $_GET['s'] ) || empty( $query->query_vars['s'] ) ) {
			return;
		}

		$search_term = trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) );
		
		// Try to extract order ID from formatted number
		$order_id = $this->extract_order_id_from_search( $search_term );
		
		if ( $order_id > 0 ) {
			// Modify query to search by order ID
			$query->query_vars['post__in'] = array( $order_id );
			unset( $query->query_vars['s'] );
		}
	}

	/**
	 * Extract order ID from search term (removes prefix and suffix)
	 *
	 * @param string $search_term Search term
	 * @return int Order ID or 0 if not found
	 */
	private function extract_order_id_from_search( $search_term ) {
		// Remove # prefix if present
		$search_term = ltrim( $search_term, '#' );

		// Get prefix and suffix
		$prefix = $this->get_prefix();
		$suffix = $this->get_suffix();

		// Remove prefix if it matches
		if ( $prefix && strpos( $search_term, $prefix ) === 0 ) {
			$search_term = substr( $search_term, strlen( $prefix ) );
		}

		// Remove suffix if it matches
		if ( $suffix && substr( $search_term, -strlen( $suffix ) ) === $suffix ) {
			$search_term = substr( $search_term, 0, -strlen( $suffix ) );
		}

		// Try to get order ID (should be numeric after removing prefix/suffix)
		$order_id = absint( $search_term );
		
		// Verify order exists
		if ( $order_id > 0 && wc_get_order( $order_id ) ) {
			return $order_id;
		}

		return 0;
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

