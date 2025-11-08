<?php
/**
 * Settings class
 *
 * @package Simple_Sequential_Order_Number
 */

defined( 'ABSPATH' ) || exit;

/**
 * SSON_Settings class
 */
class SSON_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_sson', array( $this, 'output_settings' ) );
		add_action( 'woocommerce_update_options_sson', array( $this, 'save_settings' ) );
		add_action( 'admin_footer', array( $this, 'add_settings_script' ) );
	}

	/**
	 * Add settings tab
	 *
	 * @param array $tabs Existing tabs
	 * @return array Modified tabs
	 */
	public function add_settings_tab( $tabs ) {
		$tabs['sson'] = __( 'Sequential Order Numbers', 'simple-sequential-order-number' );
		return $tabs;
	}

	/**
	 * Output settings page
	 */
	public function output_settings() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Save settings
	 */
	public function save_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Add JavaScript for dynamic sample update
	 */
	public function add_settings_script() {
		// Only on our settings page
		if ( ! isset( $_GET['tab'] ) || 'sson' !== sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
			return;
		}

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			function updateSample() {
				var prefix = $('#sson_order_number_prefix').val() || '';
				var suffix = $('#sson_order_number_suffix').val() || '';
				var length = parseInt($('#sson_order_number_length').val()) || 0;
				
				// Use sample order ID (1983) to show how it will look
				var orderId = 1983;
				
				// Format the number with padding
				var number = orderId.toString();
				if (length > 0) {
					number = number.padStart(length, '0');
				}
				
				// Replace date patterns (simplified for preview)
				var now = new Date();
				var patterns = {
					'{YYYY}': now.getFullYear(),
					'{YY}': (now.getFullYear() % 100).toString().padStart(2, '0'),
					'{MM}': (now.getMonth() + 1).toString().padStart(2, '0'),
					'{M}': (now.getMonth() + 1).toString(),
					'{DD}': now.getDate().toString().padStart(2, '0'),
					'{D}': now.getDate().toString(),
					'{HH}': now.getHours().toString().padStart(2, '0'),
					'{H}': now.getHours().toString(),
					'{N}': now.getMinutes().toString().padStart(2, '0'),
					'{S}': now.getSeconds().toString().padStart(2, '0')
				};
				
				var formattedPrefix = prefix;
				var formattedSuffix = suffix;
				
				Object.keys(patterns).forEach(function(pattern) {
					var regex = new RegExp(pattern.replace(/[{}]/g, '\\$&'), 'gi');
					formattedPrefix = formattedPrefix.replace(regex, patterns[pattern]);
					formattedSuffix = formattedSuffix.replace(regex, patterns[pattern]);
				});
				
				var sample = formattedPrefix + number + formattedSuffix;
				
				$('.sson-sample-display').text(sample);
			}
			
			// Update on input change
			$('#sson_order_number_prefix, #sson_order_number_suffix, #sson_order_number_length').on('input change', updateSample);
			
			// Initial update
			updateSample();
		});
		</script>
		<?php
	}

	/**
	 * Get settings array
	 *
	 * @return array Settings array
	 */
	public function get_settings() {
		$settings = array(
			array(
				'title' => __( 'Order Number Format', 'simple-sequential-order-number' ),
				'type'  => 'title',
				'desc'  => __( 'Add a prefix and/or suffix to your existing WooCommerce order numbers. Order numbers will remain the same (e.g., 1983), but will display with your prefix and suffix (e.g., TST1983).', 'simple-sequential-order-number' ),
				'id'    => 'sson_options',
			),

			array(
				'title'    => __( 'Order Number Prefix', 'simple-sequential-order-number' ),
				'desc'     => __( 'Text to display before the order number. Supports date patterns: {YYYY}, {MM}, {DD}, {YY}, {M}, {D}, {H}, {HH}, {N}, {S}', 'simple-sequential-order-number' ),
				'id'       => 'sson_order_number_prefix',
				'type'     => 'text',
				'default'  => '',
				'desc_tip' => true,
				'css'      => 'min-width: 300px;',
			),

			array(
				'title'    => __( 'Order Number Suffix', 'simple-sequential-order-number' ),
				'desc'     => __( 'Text to display after the order number. Supports date patterns: {YYYY}, {MM}, {DD}, {YY}, {M}, {D}, {H}, {HH}, {N}, {S}', 'simple-sequential-order-number' ),
				'id'       => 'sson_order_number_suffix',
				'type'     => 'text',
				'default'  => '',
				'desc_tip' => true,
				'css'      => 'min-width: 300px;',
			),

			array(
				'title'    => __( 'Order Number Length', 'simple-sequential-order-number' ),
				'desc'     => __( 'Minimum length of the order number (padding with zeros). Set to 0 to disable padding. Example: 4 = 0001, 0002, etc.', 'simple-sequential-order-number' ),
				'id'       => 'sson_order_number_length',
				'type'     => 'number',
				'default'  => 0,
				'desc_tip' => true,
				'css'      => 'width: 100px;',
				'custom_attributes' => array(
					'min' => 0,
					'max' => 20,
					'step' => 1,
				),
			),

			array(
				'title'    => __( 'Sample Order Number', 'simple-sequential-order-number' ),
				'type'     => 'sson_sample',
				'id'       => 'sson_sample',
			),

			array(
				'title'    => __( 'About Order Numbers', 'simple-sequential-order-number' ),
				'type'     => 'sson_regenerate',
				'id'       => 'sson_regenerate',
				'desc'     => __( 'Order numbers are formatted automatically when displayed. No regeneration needed - your prefix and suffix settings apply immediately to all orders.', 'simple-sequential-order-number' ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'sson_options',
			),
		);

		return apply_filters( 'sson_settings', $settings );
	}

}

