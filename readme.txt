=== Simple Sequential Order Number ===
Contributors: hassanali
Tags: woocommerce, orders, order numbers, prefix, suffix
Requires at least: 5.6
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add prefix and suffix to WooCommerce order numbers. Lightweight and simple solution.

== Description ==

Simple Sequential Order Number is a lightweight plugin that adds customizable prefix and suffix to your existing WooCommerce order numbers. Order numbers remain the same (e.g., 1983), but display with your prefix and suffix (e.g., TST1983).

== Features ==

* Add prefix to order numbers
* Add suffix to order numbers
* Date pattern support ({YYYY}, {MM}, {DD}, etc.)
* Zero-padding for order numbers (optional)
* Order search by formatted order number
* HPOS (High-Performance Order Storage) compatible
* Lightweight and simple - no database writes, formats on-the-fly

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/simple-sequential-order-number` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings in WooCommerce > Settings > Sequential Order Numbers

== Frequently Asked Questions ==

= Does this work with HPOS? =

Yes, the plugin is fully compatible with WooCommerce's High-Performance Order Storage.

= Can I customize the order number format? =

Yes, you can set a prefix, suffix, and minimum length (zero-padding) in the WooCommerce settings.

= Does this change my order numbers? =

No, your order numbers remain the same. The plugin only adds prefix and suffix for display purposes. Order #1983 will display as #TST1983 (if prefix is "TST"), but the actual order ID is still 1983.

= What date patterns are supported? =

{YYYY} - Full year (2024)
{YY} - Two-digit year (24)
{MM} - Two-digit month (01-12)
{M} - Month without leading zero (1-12)
{DD} - Two-digit day (01-31)
{D} - Day without leading zero (1-31)
{HH} - Two-digit hour (00-23)
{H} - Hour without leading zero (0-23)
{N} - Minutes (00-59)
{S} - Seconds (00-59)

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Simple Sequential Order Number.

