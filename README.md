# Simple Sequential Order Number

A lightweight WordPress plugin that adds customizable prefix and suffix to WooCommerce order numbers.

**Author:** Hassan Ali | Coresol Studio  
**Website:** https://coresolstudio.com  
**License:** GPL v2 or later

## Description

Simple Sequential Order Number is a lightweight plugin that adds prefix and suffix to your existing WooCommerce order numbers. Your order numbers remain the same (e.g., 1983), but they display with your custom prefix and suffix (e.g., #TST1983).

**Key Features:**
- ✅ Lightweight - No database writes, formats on-the-fly
- ✅ Simple - Just add prefix/suffix, no complex configuration
- ✅ Fast - No caching needed, instant formatting
- ✅ Compatible - Works with HPOS (High-Performance Order Storage)

## Installation

1. Download or clone this repository
2. Upload the `simple-sequential-order-number` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **WooCommerce → Settings → Sequential Order Numbers** to configure

## Configuration

After activation, configure your order number format:

1. Navigate to **WooCommerce → Settings → Sequential Order Numbers**
2. Set your **Order Number Prefix** (e.g., "TST", "ORD-", "{YYYY}-")
3. Set your **Order Number Suffix** (optional, e.g., "-{MM}")
4. Set **Order Number Length** for zero-padding (optional, e.g., 6 = 001983)

### Example Configurations

**Simple Prefix:**
- Prefix: `TST`
- Result: Order #1983 displays as **#TST1983**

**With Date:**
- Prefix: `ORD-{YYYY}-`
- Result: Order #1983 displays as **#ORD-2024-1983**

**With Padding:**
- Prefix: `INV-`
- Length: `6`
- Result: Order #1983 displays as **#INV-001983**

## Date Patterns

You can use date patterns in your prefix and suffix:

| Pattern | Description | Example |
|---------|-------------|---------|
| `{YYYY}` | Full year | 2024 |
| `{YY}` | Two-digit year | 24 |
| `{MM}` | Two-digit month | 01-12 |
| `{M}` | Month without leading zero | 1-12 |
| `{DD}` | Two-digit day | 01-31 |
| `{D}` | Day without leading zero | 1-31 |
| `{HH}` | Two-digit hour | 00-23 |
| `{H}` | Hour without leading zero | 0-23 |
| `{N}` | Minutes | 00-59 |
| `{S}` | Seconds | 00-59 |

## How It Works

- The plugin uses WooCommerce's `woocommerce_order_number` filter
- Order numbers are formatted on-the-fly (no database writes)
- Your existing order IDs remain unchanged
- Only the display format changes

**Example:**
- Order ID: `1983`
- Prefix: `TST`
- Display: `#TST1983`

## Requirements

- WordPress 5.6 or higher
- WooCommerce 3.9 or higher
- PHP 7.4 or higher

## Compatibility

- ✅ WooCommerce HPOS (High-Performance Order Storage)
- ✅ Traditional WooCommerce order storage
- ✅ WooCommerce Subscriptions (doesn't modify subscription objects)

## Support

For issues, questions, or contributions, please visit:
- **Website:** https://coresolstudio.com

## Changelog

### 1.0.0
- Initial release
- Add prefix and suffix to order numbers
- Date pattern support
- Zero-padding support
- Order search functionality

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2024 Hassan Ali | Coresol Studio

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## Credits

Developed by **Hassan Ali** from **Coresol Studio**  
Website: https://coresolstudio.com

