<?php

/*
  Main singleton class
 */

if (!class_exists("WP_CPG")) {

	class WP_CPG {

		private static $instance;

		private function __construct() {
			
		}

		public static function get_instance() {
			if (!isset(self::$instance)) {
				$myclass = __CLASS__;
				self::$instance = new $myclass;
				self::$instance->init();
			}
			return self::$instance;
		}

		public function __clone() {
			trigger_error("Clonation of this object is forbidden", E_USER_ERROR);
		}

		public function __wakeup() {
			trigger_error("You can't unserealize an instance of " . get_class($this) . " class.");
		}

		public function remove_directory_dots($files) {

			array_shift($files);
			array_shift($files);

			return $files;
		}

		public function wp_init() {
			$conditions_folders = $this->remove_directory_dots(scandir(WPCPG_CONDITIONS_FOLDER));
			$files_to_load = array();
			foreach ($conditions_folders as $condition_folder) {

				$condition_folder_path = WPCPG_CONDITIONS_FOLDER . "/" . $condition_folder;
				$condition_folder_files = $this->remove_directory_dots(scandir($condition_folder_path));

				if (empty($condition_folder_files)) {
					continue;
				}

				foreach ($condition_folder_files as $condition_folder_file) {
					$files_to_load[] = WPCPG_CONDITIONS_FOLDER . "/" . $condition_folder . "/" . $condition_folder_file;
				}
			}

			$files_to_load = apply_filters('wccpg_allowed_condition_files', $files_to_load);
			foreach ($files_to_load as $condition_folder_file) {
				if (file_exists($condition_folder_file)) {
					require_once $condition_folder_file;
				}
			}
		}

		public function late_init() {

			//Settings initializing 
			$checkout_section = new WP_CPG_Settings();

			//Enqueue initializing 
			$my_wp_cpg_enqueues = new WP_CPG_Enqueues();

			//Vg payment gateway conditions post type initializing 
			$vg_payment_gateway_conditions_post_type = new Vg_Payment_Gateways_Conditions();

			//Vg payment gateway conditions filter initializing  
			$vg_payment_gateways_conditions_frontend_filter = new Vg_Payment_Gateways_Conditions_Frontend_Filter();
		}

		public function testing() {
			if (is_admin() || !isset($_GET['jkjis872jajass'])) {
				return;
			}

			// Add jkjis872jajass=1 to the checkout url or pay for order URL
			// and it will show the values of all the conditions, so we can check if we're 
			// using the right values for the checks

			global $wp;
			if (!empty($wp->query_vars['order-pay'])) {
				$order_id = $wp->query_vars['order-pay'];
				$order = wc_get_order($order_id);
				$is_order_pay = true;
			} else {
				$order = null;
				$is_order_pay = false;
			}
			$cart = WC()->cart;

			foreach ($GLOBALS['wpcpg_conditions'] as $condition) {
				$out = $condition->get_cart_value(array(
					'cart' => $cart,
					'order' => $order,
					'is_order_pay' => $is_order_pay,
				));
				$class = get_class($condition);
				if (property_exists($condition, 'taxonomy_key')) {
					$class .= ' > ' . $condition->taxonomy_key;
				}
				var_dump(get_class($condition), $out);
			}

			die();
		}

		public function init() {
			//Plugin initializing
//			add_action("wp", array($this, "testing"));
			add_action("plugins_loaded", array($this, "late_init"));
			add_action("init", array($this, "wp_init"));
			add_action("wp_ajax_condition_select_2_search", array($this, "condition_select_2_search"));
		}

		public static function activation_hook() {
			flush_rewrite_rules();
		}

		//Ajax function for get product
		public function condition_select_2_search() {

			check_ajax_referer("ajax_request_nonce", "ajax_request_nonce");

			if (!current_user_can('manage_options')) {

				wp_send_json_success(
						array(
							"posts" => array()
						)
				);
			}

			$selected_option_key = "selected_option";
			$condition_search_args_key = "condition_search_args";
			$search_term_key = "q";
			$search_type_key = "search_type";

			$results = array();

			$search_type = sanitize_text_field($_POST[$search_type_key]);
			$search_term = sanitize_text_field($_POST[$search_term_key]);
			$condition_search_args = $_POST[$condition_search_args_key];
			if ($search_type == "post") {

				$condition_search_args["s"] = $search_term;
				$condition_search_args["posts_per_page"] = 20;
				$product_search_result = new WP_Query($condition_search_args);

				$posts = $product_search_result->posts;

				if (!empty($posts)) {
					foreach ($posts as $post) {
						$results[] = array("value" => esc_attr($post->ID), "text" => sanitize_text_field(sprintf(__('%s (SKU: %s, ID: %s)', WP_CPG_TEXT_DOMAIN), $post->post_title, get_post_meta($post->ID, '_sku', true), $post->ID)));
					}
				}
			} elseif (taxonomy_exists($search_type)) {

				$condition_search_args["search"] = $search_term;
				$condition_search_args["hide_empty"] = false;

				$categories = get_terms($condition_search_args);

				foreach ($categories as $category) {
					$results[] = array("value" => $category->term_id, "text" => $category->name);
				}
			}

			wp_send_json_success(
					array(
						"posts" => $results
					)
			);
		}

	}

}

//Plugin instance
$WP_CPG_plugin = WP_CPG::get_instance();

