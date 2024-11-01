=== WooCommerce Disable Payment Methods based on cart conditions ===
Contributors: vegacorp,josevega, freemius
Tags: woocommerce, payment gateways
Tested up to: 6.7
Stable tag: 1.16.3
Requires at least: 4.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable or disable WooCommerce payment gateways based on cart conditions like the order total.

== Description ==
Enable or disable WooCommerce payment gateways based on cart conditions like the order total.

= Examples: =
- Enable paypal for orders higher than $100
- Enable credit card only when the order total is higher than $1000
- Disable stripe when the order total is < than $30
- Enable "cash on delivery" when the order total is lower than $10
- Only allow bank transfers for orders > $1000

= Use Cases: =
- Save money by using the cheapest payment processors when the order total is too low
- Make more money by enabling the best payment gateway based on the user order
- Hide payment gateways that don't allow transactions < $5 or charge too much fees on small transactions

= Features: =

**The free plugin works with:**

- All payment gateways
- You can create unlimited conditions for every gateway or multiple gateways at once
- You can create AND and OR conditions. i.e. "Enable paypal when the total order is > $100 and < $200"
- You can use conditions based on "cart total", "subtotal", and "subtotal exc. taxes" only

**Extra conditions available on the premium plugin:**

- Enable or disable payment methods based on the user address
- Enable or disable payment methods for specific city
- Enable or disable payment methods for specific user state
- Enable or disable payment methods for specific zip code
- Enable or disable payment methods for specific country
- Enable or disable payment methods for specific user roles
- Enable or disable payment methods for old or new customers
- Enable or disable payment methods based on customer registration date
- Enable or disable payment methods based on previous customers orders
- etc.

[Try Premium Plugin for FREE for 7 Days](https://wpsuperadmins.com/plugins/woocommerce-conditional-payment-gateways/?utm_source=wp.org&utm_campaign=readme.txt&utm_medium=web)

- Enable or disable payment methods for category
- Enable or disable payment methods based on product height
- Enable or disable payment methods based on product length
- Enable or disable payment methods based on product weight
- Enable or disable payment methods based on the product quantity
- Enable or disable payment methods for specific product brands
- Enable or disable payment methods for specific product sizes or colors or attributes
- Enable or disable payment methods for specific product taxonomies
- etc.

You can enable payment methods based on cart information:

- Enable or disable payment methods for specific coupons used
- Enable or disable payment methods for specific shipping method
- Enable or disable payment methods based on the products in the cart
- Enable or disable payment methods based on the total tax
- Enable or disable payment methods based on the total weight
- Enable or disable payment methods based on the total coupon discounts
- Enable or disable payment methods based on the total shipping cost
- Enable or restrict payment methods based on the currency
- etc.

You can rotate payment methods:
- Enable or disable payment methods by day of the week, day of the month, month, year, or full date
- Rotate payment methods for every order. For example: bank account 1 for order 1, bank account 2 for order 2, etc.

[Try Premium Plugin for FREE for 7 Days](https://wpsuperadmins.com/plugins/woocommerce-conditional-payment-gateways/?utm_source=wp.org&utm_campaign=readme.txt&utm_medium=web)

== Installation ==
= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type the plugin name and click Search Plugins. Once you’ve found our plugin you can install it by simply clicking “Install Now”.

= Manual installation =
The manual installation method involves downloading our plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here.](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Screenshots ==
1. Global settings
2. Payment gateway settings

== Changelog ==

= 1.16.3 - 2024-10-24 =
* CHANGE - Updated dependencies
* FIX - Compatibility issue with Table Rate Shipping plugin by WooCommerce
* FIX - Compatibility issue with WPML

= 1.16.2 = 2024-05-20 =
* CHANGE - Improve the custom fields detection
* FIX - Fatal error when using the appears_in_this_list operator with the variation ids condition

= 1.16.1 = 2023-07-14 =
* FIX - Small bug fix

= 1.16.0 = 2023-07-05 =
* NEW - Add more conditions
* CHANGE - Updated freemius sdk

= 1.15.1 = 2022-11-08 = 
* FIX - Small bug fix

= 1.15.0 = 2022-10-13 =
* NEW - Add more conditions
* FIX - Small bug fixes

= 1.14.0 = 2022-05-25 =
* NEW - Add more conditions

= 1.13.1.1 = 2022-03-01 =
* FIX - The contains operator doesn't work well sometimes
* CHANGE - Improve conditions
* CHANGE - Upgraded Freemius SDK to v2.4.3

= 1.13.1 = 2021-10-05 =
* CHANGE - Improve the shipping method condition

= 1.13.0 = 2021-08-27 =
* NEW - Add conditions
* FIX - Minor bug

= 1.12.0 = 2021-07-01 =
* NEW - Add conditions
* CHANGE - General improvements

= 1.11.1 = 2021-05-11 =
* CHANGE - Improve the currencies condition
* CHANGE - Improve some conditions
* FIX - CONTAINS operator doesn't work sometimes

= 1.11.0 = 2021-04-14 =
* NEW - Condition: is sales agent?
* NEW - Condition: Order status
* NEW - Condition: Orders count by email
* NEW - Compatibility with the plugin "WooCommerce MultiSite Global Cart"
* NEW - Condition: Requested invoice (requires the plugin "timologia for WooCommerce")
* CHANGE - Improve the taxonomy condition
* FIX - Billing email condition does not work sometimes.
* FIX - User search does not work

= 1.10.0 = 2020-12-15 =
* NEW - Allow to disable or enable purchase when they buy a product for a second time
* NEW - Allow to disable payment gateways when they buy from different categories at the same time
* NEW - Allow to change the message when no payment methods are available
* CHANGE - General improvements
* FIX - Minor fixes

= 1.9.0 = 2020-10-22 =
* NEW - Add support for EasyPack Parcel 24/7 shipping method
* NEW - Allow to disable or enable payment methods based on the product vendor
* CHANGE - Improve the handling of backorder products
* CHANGE - Improve the rotation

= 1.8.0 = 2020-09-25 =
* NEW - Allow to disable or enable payment methods based on the currency

= 1.7.0 = 2020-08-27 =
* NEW - Add support for custom checkout fields

= 1.6.4.1 = 2020-08-06 =
* FIX - Problem saving conditions

= 1.6.4 = 2020-07-31 =
* CHANGE - Add support for multiple roles

= 1.6.3 = 2020-06-24 =
* NEW - Make the conditions more flexible

= 1.6.2 = 2020-04-27 =
* NEW - Add condition: is product backordered
* CHANGE - Add payment methods count to the body classes

= 1.6.1 = 2020-03-15 =
* FIX - Shipping method conditions don't work with the "WooCommerce Table Rate Shipping" plugin

= 1.6.0 = 2020-02-21 =
* NEW - Add conditions: Variation attribute - to enable or disable payment methods based on the variation that's being purchased
* NEW - Add condition: Hour of the day - to enable or disable payment methods based on the hour
* NEW - Add operator: Equal to this field - to create conditions where one field is equal to another
* NEW - Add operator: Not equal to this field - to create conditions where one field is not equal to another
* CHANGE - Condition: contains product - Show the product id and sku in the dropdown so we can differentiate products with same name

= 1.5.2 - 2019-12-23 =
* CHANGE - Show notification when users add more than 4 AND conditions, so they consider adding them as OR conditions to prevent support requests
* FIX - The free version shows the action buttons twice on the metabox

= 1.5.1 - 2019-11-17 =
* CHANGE - When we edit on the checkout page a field related to conditions, reload the payment methods

= 1.5.0 - 2019-10-11 =
* NEW - Allow to enable/disable payment methods by day of the week
* NEW - Allow to enable/disable payment methods by day of the month
* NEW - Allow to enable/disable payment methods by month
* NEW - Allow to enable/disable payment methods by year
* NEW - Allow to enable/disable payment methods by full date
* NEW - Allow to rotate payment methods for every order. For example: bank account 1 for order 1, bank account 2 for order 2, etc.
* CHANGE - If no gateways are activated, remove the "place order" button on the checkout page
* CHANGE - Add class "cpg-gateways-inactive" to the body when no gateways are activated to faciliate hiding other page elements
* INTERNAL - Added configuration for WPML

= 1.4.1 - 2019-08-26 =
* FIX - The metabox field "is disabled" doesn't show the saved value, but it saved successfully

= 1.4.0 - 2019-07-20 =
* NEW - Allow to show/hide payment methods based on custom taxonomies of products (premium)
* NEW - Added URL parameter ?wpcpg_no_gateway to load the metabox without displaying the list of payment methods
* CHANGE - Show all terms in the taxonomy condition dropdown, even if no products are using it (premium)
* CHANGE - Allow to create multiple condition posts for the same gateway and show the gateway if at least one post conditions are valid
* CHANGE - Allow to select in the condition post if the payment method should be enabled/disabled when the conditions are valid
* FIX - The conditions metabox saves the wrong key for some shipping methods, making it not match on the checkout restrictions (premium)

= 1.3.0 - 2019-03-20 =
* NEW - Allow to show/hide payment methods from guest users (premium)
* NEW - Allow to show/hide payment methods based on the shipping method (premium)
* NEW - Allow to show/hide payment methods based on the user registration date (premium)
* NEW - Allow to show/hide payment methods based on the number of previous orders from the customer (premium)
* NEW - Added operator APPEARS IN THIS LIST to text fields (premium)
* NEW - Allow to type the payment gateway manually in case it doesn't appear in the dropdown when creating conditions
* NEW - Add compatibility for the "order pay" (different to the checkout page)
* CHANGE - When we open the settings page for the first time, activate the conditions automatically to avoid confussions
* CHANGE - Now the settings page shows the last 10 tutorials from the blog (only on the premium version)
* FIX - We can't delete conditions from the list of active conditions
* FIX - Error, it applies only the first 10 conditions
* FIX - Product category condition wasn't working for variable products (premium)

= 1.2.0 - 2019-03-19 =
* NEW - Allow to show/hide payment methods based on billing company (premium)
* NEW - Allow to show/hide payment methods based on shipping company (premium)
* NEW - Allow to show/hide payment methods based on shipping city (premium)
* NEW - Allow to show/hide payment methods based on shipping state (premium)
* NEW - Allow to show/hide payment methods based on shipping country (premium)
* NEW - Allow to show/hide payment methods based on shipping zip (premium)
* NEW - Allow to show/hide payment methods based on customer email (premium)
* NEW - Allow to show/hide payment methods based on billing email (premium)
* NEW - Added operators CONTAINS and NOT CONTAINS to text fields (premium)

= 1.1.0 - 2019-03-03 =
* NEW - Allow to show/hide payment methods based on total discounts (premium)
* NEW - Allow to show/hide payment methods based on total shipping cost (premium)
* NEW - Allow to show/hide payment methods based on total (including taxes, shipping, fees, etc.)
* CHANGE - Updated to freemius v2.2.4
* CHANGE - Redirect to the conditions list after creating or updating condition post

= 1.0.0 - 2018-12-26 =
* Initial release