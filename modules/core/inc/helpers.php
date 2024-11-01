<?php

if (!class_exists("WP_CPG_Helpers")) {

	//This class contains functions that returns associative arrays of options, also contains a function to format those arrays into html options
	class WP_CPG_Helpers {

		static private $instance = false;

		private function __construct() {
			
		}

		/*
		 * Function that return an array of options that are considered numeric, this is for know 
		 * which operators remove from operator select
		 */

		//Function that format a given array into html options
		public function get_options_html_formatted($options_array, $selected_option = "") {

			$options = "";

			//Checking if are multiple selected options		
			$check_multiple_selected_options = is_array($selected_option) && !empty($selected_option);

			foreach ($options_array as $key => $option) {

				//Here the key (or value) can be "" because it serves as default option in some cases
				if (empty($option)) {
					continue;
				}

				if ($check_multiple_selected_options) {
					//if are multiple selected option, the array of selected options is checked
					$selected = selected(in_array($key, $selected_option), true, false);
				} else {
					$selected = selected($selected_option == $key, true, false);
				}
				$options .= "<option value = '" . esc_attr($key) . "' " . $selected . ">$option</option>";
			}

			return $options;
		}

		public function get_optgroups_options_html_formatted($optgroups, $selected_option = "") {

			$options = "";

			foreach ($optgroups as $group_key => $group_options) {
				if (empty($group_options)) {
					continue;
				}

				$current_group_open_tag = '<optgroup label = "' . esc_attr($group_key) . '">';
				$current_group_closed_tag = '</optgroup>';
				$current_group_options = $this->get_options_html_formatted($group_options, $selected_option);
				$options .= $current_group_open_tag . $current_group_options . $current_group_closed_tag;
			}

			return $options;
		}

		public function get_conditions_groups() {

			return apply_filters("wpcpg_conditions_groups", array(
				"cart" => array(
					"label" => __("Cart", WP_CPG_TEXT_DOMAIN)
				),
				"user_details" => array(
					"label" => __("User details", WP_CPG_TEXT_DOMAIN)
				),
				"product" => array(
					"label" => __("Product", WP_CPG_TEXT_DOMAIN)
				),
				"date" => array(
					"label" => __("Date", WP_CPG_TEXT_DOMAIN)
				)
					)
			);
		}

		public function get_conditions_groups_html_options($selected_value = "") {

			$conditions = $this->get_conditions();
			$groups = $this->get_conditions_groups();

			$options_html = "";

			foreach ($groups as $group_key => $group) {

				$current_group_open_tag = '<optgroup class = "' . esc_attr($group_key) . '" label = "' . esc_attr($group["label"]) . '">';
				$current_group_closed_tag = '</optgroup>';
				$current_group_options = '';

				foreach ($conditions as $condition_key => $condition) {
					if ($condition->getGroup() == $group_key) {
						$current_group_options .= $condition->get_html_option($selected_value);
					}
				}

				if (!empty($current_group_options)) {
					$options_html .= $current_group_open_tag . $current_group_options . $current_group_closed_tag;
				}
			}

			return apply_filters("wpcpg_conditions_groups_html_options", $options_html, $selected_value, $conditions, $groups);
		}

		public function get_conditions($assoc = false) {

			return apply_filters("wpcpg_conditions", array(), $assoc);
		}

		public function user_details_optgroup_options_array() {

			$user_details_optgroup_options_array = array(
				"zipcode" => __("Zipcode", WP_CPG_TEXT_DOMAIN),
				"city" => __("City", WP_CPG_TEXT_DOMAIN),
				"state" => __("State", WP_CPG_TEXT_DOMAIN),
				"country" => __("Country", WP_CPG_TEXT_DOMAIN),
				"user_role" => __("User role", WP_CPG_TEXT_DOMAIN)
			);

			return $user_details_optgroup_options_array;
		}

		public function get_stock_status_array() {

			$stock_status_options = array(
				"instock" => __("In stock", WP_CPG_TEXT_DOMAIN),
				"outofstock" => __("Out of stock", WP_CPG_TEXT_DOMAIN)
			);

			return $stock_status_options;
		}

		public function products_options_array() {

			$args = array(
				"return" => "objects",
				"numberposts" => -1
			);

			$products = wc_get_products($args);
			$products_array = array();

			foreach ($products as $product) {
				$products_array[$product->get_id()] = $product->get_name();
			}

			return $products_array;
		}

		public function get_categories() {

			$args = array(
				"taxonomy" => "product_cat",
				"hide_empty" => false
			);

			$categories = get_categories($args);

			$category_options = array();

			foreach ($categories as $category) {

				$category_options[$category->term_id] = $category->name;
			}

			return $category_options;
		}

		public function get_shipping_classes() {

			$args = array(
				"taxonomy" => "product_shipping_class",
				"hide_empty" => false
			);

			$shipping_classes = get_categories($args);

			$shipping_classes_array = array();

			foreach ($shipping_classes as $shipping_class) {

				$shipping_classes_array[$shipping_class->term_id] = $shipping_class->name;
			}

			return $shipping_classes_array;
		}

		public function get_user_roles() {

			$user_roles = get_editable_roles();

			$user_roles_array = array();

			$user_roles_array["guest"] = __("Guest customer", WP_CPG_TEXT_DOMAIN);
			foreach ($user_roles as $key => $user_role) {

				$user_roles_array[$key] = $user_role["name"];
			}
			$user_roles_array["customer"] = __("Registered customer", WP_CPG_TEXT_DOMAIN);

			return $user_roles_array;
		}

		public function get_coupons() {

			$args = array(
				"post_type" => "shop_coupon"
			);

			$coupon_posts_objs = get_posts($args);

			$coupons_array = array();

			foreach ($coupon_posts_objs as $coupon_post_obj) {
				$coupons_array[$coupon_post_obj->ID] = $coupon_post_obj->post_name;
			}

			return $coupons_array;
		}

		public function payment_gateways_options_array() {

			if (!empty($_GET['wpcpg_no_gateway'])) {
				$payment_gateways = array();
			} else {
				remove_all_filters('woocommerce_available_payment_gateways');
				$payment_gateways = WC()->payment_gateways->payment_gateways();
				foreach ($payment_gateways as $key => $gateway) {
					if ($gateway->is_available()) {
						$payment_gateways[$key] = $gateway->get_title() . ' (' . $key . ')';
					} else {
						unset($payment_gateways[$key]);
					}
				}
			}

			if (is_admin() && !empty($_GET['post'])) {
				$custom_gateways = get_post_meta((int) $_GET['post'], "wccpg_payment_gateways", true);
				if (!empty($custom_gateways) && is_array($custom_gateways)) {
					foreach ($custom_gateways as $gateway) {
						if (!isset($payment_gateways[$gateway])) {
							$payment_gateways[$gateway] = $gateway;
						}
					}
				}
			}

			return $payment_gateways;
		}

		public function get_states() {

			$countries = new WC_Countries();

			$countries_array = $countries->get_countries();

			$state_options = array();

			foreach ($countries_array as $country_key => $country) {

				$country_states = $countries->get_states($country_key);

				if (empty($country_states)) {
					continue;
				}

				$state_options[$country] = $country_states;
			}

			return $state_options;
		}

		function get_successful_orders_by_billing_email($billing_email, $statuses = null) {
			if (empty($billing_email)) {
				return 0;
			}
			if (!$statuses) {
				$statuses = array('wc-completed', 'wc-processing');
			}

			$orders = new WP_Query(array(
				'numberposts' => -1,
				'meta_key' => '_billing_email',
				'meta_value' => $billing_email,
				'post_type' => wc_get_order_types(),
				'post_status' => $statuses,
				'fields' => 'ids'
			));

			return $orders->found_posts;
		}

		public function get_customer_completed_orders() {

			if (!is_user_logged_in()) {
				return 0;
			}
			$orders = get_posts(array(
				'numberposts' => -1,
				'meta_key' => '_customer_user',
				'meta_value' => get_current_user_id(),
				'post_type' => wc_get_order_types(),
				'post_status' => array_keys(wc_get_order_statuses())
			));

			$completed_orders = 0;

			if (empty($orders)) {
				return $completed_orders;
			}

			foreach ($orders as $index => $order) {

				$completed_orders = in_array($order->post_status, array('wc-completed', 'wc-processing')) ? ( $completed_orders + 1 ) : $completed_orders;
			}

			return $completed_orders;
		}

		public function payment_gateways_options_html_formatted($selected_options = "") {
			return $this->get_options_html_formatted($this->payment_gateways_options_array(), $selected_options);
		}

		public function products_options_html_formatted($selected_option = "") {

			return "<option value = ''>" . __("Select product", WP_CPG_TEXT_DOMAIN) . "</option>" . $this->get_options_html_formatted($this->products_options_array(), $selected_option);
		}

		public function get_active_conditions_saved_data($or_groups, $active_conditions) {

			$groups = array();

			if( ! $or_groups || ! is_array($or_groups)){
				$or_groups = array();
			} 

			foreach ($or_groups as $or_group) {

				$group_conditions = array();

				foreach ($or_group["conditions"] as $condition) {

					if (in_array($condition["type"], $active_conditions)) {
						$group_conditions[] = $condition;
					}
				}

				if (!empty($group_conditions)) {
					$groups[]["conditions"] = $group_conditions;
				}
			}

			return $groups;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_CPG_Helpers::$instance) {
				WP_CPG_Helpers::$instance = new WP_CPG_Helpers();
			}
			return WP_CPG_Helpers::$instance;
		}

	}

	function WCCPG_Helpers() {
		return WP_CPG_Helpers::get_instance();
	}

}

