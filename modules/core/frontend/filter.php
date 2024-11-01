<?php
//Class that filter the payment gateways in the frontend
if (!class_exists("Vg_Payment_Gateways_Conditions_Frontend_Filter")) {

	class Vg_Payment_Gateways_Conditions_Frontend_Filter {

		public function __construct() {
			$this->init();
		}

		public function check_vg_payment_gateways_conditions($args) {
			global $wp;

			extract($args, EXTR_OVERWRITE);

			//Payment gateways that will be shown in the frontend
			$enabled_payment_gateways = array();

			$wpcpg_conditions_keys = array_keys($wpcpg_conditions);

			if (!empty($wp->query_vars['order-pay'])) {
				$order_id = $wp->query_vars['order-pay'];
				$order = wc_get_order($order_id);
				$is_order_pay = true;
			} else {
				$order = null;
				$is_order_pay = false;
			}

			$payment_gateways_check_results = array();

			//Foreach post of conditions (vg payment gateways conditions)
			foreach ($payment_gateways_conditions as $payment_gateways_condition) {

				//Getting groups of current vg payment gateways conditions post type
				$groups = WCCPG_Helpers()->get_active_conditions_saved_data($payment_gateways_condition["groups"], $wpcpg_conditions_keys);

				if (empty($groups)) {
					continue;
				}

				//Groups test starts equal to false
				$groups_successful = false;

				//Foreach groups of the current post 
				foreach ($groups as $key => $group) {

					//Getting conditions of the current group
					$group_conditions = $group["conditions"];

					if (empty($group_conditions)) {
						continue;
					}

					//Conditions test starts equal to true
					$conditions_test = true;

					//Foreach condition
					foreach ($group_conditions as $i => $condition) {

						extract($condition, EXTR_OVERWRITE);

						if (in_array($type, $wpcpg_conditions_keys)) {
							$current_condition_test = $wpcpg_conditions[$type]->condition_is_valid(
									array(
										"cart" => $cart,
										"condition" => $condition,
										'order' => $order,
										'is_order_pay' => $is_order_pay,
									)
							);
						}

						$conditions_test = $conditions_test && $current_condition_test;
					}

					//Realizing "or" boolean operation for current group 
					$groups_successful = $groups_successful || $conditions_test;
				}

				// If the payment method should be disabled if conditions are met, we invert the check results
				// so if the conditions are met we pretend they weren't and hide it, and if conditions aren't 
				// met we pretend they were met and show it
				if (!empty($payment_gateways_condition['is_payment_gateway_disabled'])) {
					$groups_successful = !$groups_successful;
				}

				foreach ($payment_gateways_condition["payment_gateways"] as $payment_method_key) {
					if (!isset($payment_gateways_check_results[$payment_method_key])) {
						$payment_gateways_check_results[$payment_method_key] = array();
					}
					$payment_gateways_check_results[$payment_method_key][] = $groups_successful;
				}
			}

			// If at least one conditions post was successful, we enable the payment method
			// this way we can have multiple posts related to the same payment method
			foreach ($payment_gateways_check_results as $payment_method_key => $results) {
				if (in_array(true, $results, true)) {
					$enabled_payment_gateways[] = $payment_method_key;
				}
			}

			// We will auto enable all gateways not found on conditions
			$all_condition_gateways = implode(', ', array_map('serialize', wp_list_pluck($payment_gateways_conditions, 'payment_gateways')));
			foreach (array_keys($default_gateways) as $enabled_gateway) {

				if (strpos($all_condition_gateways, '"' . $enabled_gateway . '"') === false) {
					$enabled_payment_gateways[] = $enabled_gateway;
				}
			}

			return $enabled_payment_gateways;
		}

		public function filter_payment_gateways($available_gateways) {

			global $woocommerce;
			if(!is_array($available_gateways)){
				return $available_gateways;
			}
			
			// Don't disable gateways after the order was created and the cart was emptied
			// Because we already filtered before during the checkout validation and early processing
			if( did_action('woocommerce_checkout_order_processed')){
				return $available_gateways;
			}

			$wpcpg_conditions = WCCPG_Helpers()->get_conditions();

			//Getting the cart 
			$cart = $woocommerce->cart;

			//Getting the conditional payment gateways option
			$conditional_payment_gateways_option = get_option("wp_cpg_enable_conditional_payment_gateways");

			//Checking if filter is enabled			
			if ($conditional_payment_gateways_option !== "on") {
				return $available_gateways;
			}

			//Checking if the cart is empty
			if (empty($cart)) {
				return $available_gateways;
			}

			//Setting the args of wp query
			$args = array(
				"post_type" => VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY,
				"fields" => "ids",
				'posts_per_page' => -1,
				'post_status' => 'publish'
			);

			//Realizing wp query to get the ids of the payment gateways conditions posts
			$vg_payment_gateways_conditions_query = new WP_Query($args);

			//Reverting the ids
			$payment_gateway_conditions_ids = array_reverse($vg_payment_gateways_conditions_query->posts);

			//If there is no payment gateways conditions posts we do nothing
			if (empty($payment_gateway_conditions_ids)) {

				return $available_gateways;
			}

			//Initializing array of posts conditions and payment gateways
			$payment_gateways_conditions = array();

			//Filling the array with the posts data (conditions and gateways)
			foreach ($payment_gateway_conditions_ids as $id) {

				$payment_gateways = get_post_meta($id, "wccpg_payment_gateways", true);

				if (empty($payment_gateways)) {
					$payment_gateways = array();
				}

				$payment_gateways_conditions[] = array(
					"groups" => get_post_meta($id, "wccpg_or_groups", true),
					"payment_gateways" => $payment_gateways,
					'is_payment_gateway_disabled' => !empty(get_post_meta($id, "wccpg_is_payment_gateway_disabled", true))
				);
			}

			$check_payment_gateway_conditions_args = array(
				"payment_gateways_conditions" => $payment_gateways_conditions,
				"cart" => $cart,
				"wpcpg_conditions" => $wpcpg_conditions,
				'default_gateways' => $available_gateways
			);

			//Checking conditions
			$enabled_payment_gateways = $this->check_vg_payment_gateways_conditions($check_payment_gateway_conditions_args);

			$available_gateways_keys = array_keys($available_gateways);

			//Disabling or enabling payment gateways
			foreach ($available_gateways_keys as $available_gateway_key) {

				if (!in_array($available_gateway_key, $enabled_payment_gateways)) {
					unset($available_gateways[$available_gateway_key]);
				}
			}

			$GLOBALS['cpg_last_available_gateways'] = $available_gateways;
			add_action('woocommerce_review_order_after_submit', array($this, 'toggle_body_class'));
			return $available_gateways;
		}

		function toggle_body_class() {
			$active_gateways = !empty($GLOBALS['cpg_last_available_gateways']) ? $GLOBALS['cpg_last_available_gateways'] : array();
			?>
			<style>	
				/*Show message in checkout when no payment method is available*/
				.woocommerce-checkout .woocommerce-checkout-payment li.woocommerce-notice {
					display: block !important;
					font-weight: bold;
				}
				.cpg-gateways-inactive:not(.cpg-gateways-active) #place_order{
					display: none;
				} 
			</style>
			<script>
				var cpgGatewaysActive = <?php echo json_encode(!empty($active_gateways)); ?>;
				jQuery('body').addClass('cpg-gateways-count<?php echo count($active_gateways); ?>');
				if (!cpgGatewaysActive) {
					jQuery('body').addClass('cpg-gateways-inactive');
					jQuery('body').removeClass('cpg-gateways-active');
				} else {
					jQuery('body').addClass('cpg-gateways-active');
					jQuery('body').removeClass('cpg-gateways-inactive');
				}</script>
			<?php
		}

		function filter_no_payment_gateways_message($message) {

			$option = get_option("wp_cpg_no_payment_gateways_message");
			if (!empty($option)) {
				$message = esc_html($option);
			}
			return $message;
		}

		public function init() {
			//Initializing filter
			add_filter("woocommerce_available_payment_gateways", array($this, "filter_payment_gateways"), 9999990);
			add_filter("woocommerce_no_available_payment_methods_message", array($this, "filter_no_payment_gateways_message"));
		}

	}

}

