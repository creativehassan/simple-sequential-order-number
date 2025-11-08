<?php
/**
 * Plugin Name: Simple Sequential Order Number
 * Plugin URI: https://coresolstudio.com
 * Description: Provides custom sequential order numbers for WooCommerce orders. Lightweight and simple.
 * Version: 1.0.0
 * Author: Hassan Ali | Coresol Studio
 * Author URI: https://coresolstudio.com
 * Text Domain: simple-sequential-order-number
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * WC requires at least: 3.9
 * WC tested up to: 9.6
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Simple_Sequential_Order_Number
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants
define( 'SSON_VERSION', '1.0.0' );
define( 'SSON_PLUGIN_FILE', __FILE__ );
define( 'SSON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main plugin class
 */
final class Simple_Sequential_Order_Number {

	/**
	 * Plugin instance
	 *
	 * @var Simple_Sequential_Order_Number
	 */
	private static $instance = null;

	/**
	 * Core class instance
	 *
	 * @var SSON_Core
	 */
	public $core = null;

	/**
	 * Get plugin instance
	 *
	 * @return Simple_Sequential_Order_Number
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 10 );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	/**
	 * Initialize plugin
	 */
	public function init() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		// Load core class
		require_once SSON_PLUGIN_DIR . 'includes/class-sson-core.php';
		$this->core = new SSON_Core();

		// Load settings class
		if ( is_admin() ) {
			require_once SSON_PLUGIN_DIR . 'includes/class-sson-settings.php';
			new SSON_Settings();

			// Add custom field type handlers
			add_action( 'woocommerce_admin_field_sson_sample', array( $this, 'render_sample_field' ) );
			add_action( 'woocommerce_admin_field_sson_regenerate', array( $this, 'render_regenerate_field' ) );
		}
	}

	/**
	 * Render sample order number field
	 *
	 * @param array $value Field settings
	 */
	public function render_sample_field( $value ) {
		$core = sson()->core;
		// Use a sample order ID (e.g., 1983) to show how it will look
		$sample_order_id = 1983;
		$sample = $core->format_order_number( $sample_order_id, $sample_order_id );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
					<p class="description sson-sample-display" style="font-size: 16px; font-weight: 600; color: #2271b1; margin: 0;">
						<?php echo esc_html( $sample ); ?>
					</p>
					<p class="description">
						<?php esc_html_e( 'This is how your order numbers will appear based on your current settings.', 'simple-sequential-order-number' ); ?>
					</p>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render info field (formerly regenerate field)
	 *
	 * @param array $value Field settings
	 */
	public function render_regenerate_field( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ); ?></span></legend>
					<p class="description">
						<?php echo esc_html( $value['desc'] ); ?>
					</p>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	/**
	 * Activation hook
	 */
	public function activate() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die(
				esc_html__( 'Simple Sequential Order Number requires WooCommerce to be installed and active.', 'simple-sequential-order-number' ),
				esc_html__( 'Plugin Activation Error', 'simple-sequential-order-number' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * WooCommerce missing notice
	 */
	public function woocommerce_missing_notice() {
		?>
		<div class="error">
			<p>
				<strong><?php esc_html_e( 'Simple Sequential Order Number', 'simple-sequential-order-number' ); ?></strong>
				<?php esc_html_e( 'requires WooCommerce to be installed and active.', 'simple-sequential-order-number' ); ?>
			</p>
		</div>
		<?php
	}
}

/**
 * Get plugin instance
 *
 * @return Simple_Sequential_Order_Number
 */
function sson() {
	return Simple_Sequential_Order_Number::instance();
}

// Initialize plugin
sson();

