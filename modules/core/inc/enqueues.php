<?php

if (!class_exists("WP_CPG_Enqueues")) {

	class WP_CPG_Enqueues {

		//Vg payment gateway conditions post type enqueues

		public function __construct() {
			$this->init();
		}

		public function payment_gateway_conditions_enqueues($hook) {

			global $post;

			//Checking page enqueue	  
			if ($hook === "post-new.php" || $hook === "post.php") {

				//Checking post type
				if ($post->post_type === VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY) {

					//Scripts enqueue
					wp_enqueue_script("my_wp_cpg_add_condition_script", WPCPG_URL . "assets/js/add-condition-page-script.js", array("jquery"));
					wp_enqueue_script("wp_cpg_select2_js", WPCPG_URL . "assets/vendor/select2-4.0.5/js/select2.min.js", array("jquery"));

					//Styles enqueue 
					wp_enqueue_style("wp_cpg_select2_css", WPCPG_URL . "assets/vendor/select2-4.0.5/css/select2.min.css");
					wp_enqueue_style("wp_cpg_vg_payment_gateway_conditions_css", WPCPG_URL . "assets/css/vg-payment-gateway-conditions-css.css");

					//Sending data to main script     
					wp_localize_script(
							"my_wp_cpg_add_condition_script", "wp_cpg_gateway_conditions_obj", array(
						"settings_page_url" => admin_url('admin.php?page=wp_cpg_settings_menu'),
						"conditions" => WCCPG_Helpers()->get_conditions(true),
						"text" => array(//Page texts
							"too_many_and_conditions" => __("You have added a large number of AND conditions. Are you sure these conditions will be met together? If not, you should add them as OR conditions.", WP_CPG_TEXT_DOMAIN),
							"condition_text" => __("Condition", WP_CPG_TEXT_DOMAIN),
							"group_text" => __("Group", WP_CPG_TEXT_DOMAIN),
							"select_product_text" => __("Select product", WP_CPG_TEXT_DOMAIN),
							"requesting_products_text" => __("Loading products ...", WP_CPG_TEXT_DOMAIN),
							"requesting_products_success_text" => __("Products loaded", WP_CPG_TEXT_DOMAIN),
							"requesting_products_error_text" => __("An error has ocurred while loading products", WP_CPG_TEXT_DOMAIN),
							"go_to_settings" => __("Go to conditions list", WP_CPG_TEXT_DOMAIN)
						),
						"ajax" => array(//Ajax url and nonce
							"url" => admin_url("admin-ajax.php"),
							"ajax_request_nonce" => wp_create_nonce("ajax_request_nonce")
						)
							)
					);
				}
			}
		}

		//Settings page enqueues	
		public function options_page_enqueues($hook) {

			//Checking page enqueue		
			if (!is_admin()) {
				return;
			}

			if (!isset($_GET["page"]) || $_GET["page"] !== "wp_cpg_settings_menu") {
				return;
			}
			//Checking end
			//Scripts enqueue
			wp_enqueue_script("wp_cpg_options_page_script", WPCPG_URL . "assets/js/wp-cpg-options-page-script.js", array("jquery"));

			//Sending data to main script
			wp_localize_script(
					"wp_cpg_options_page_script", "wp_cpg_options_page_obj", array(
				"ajax" => array(//Ajax url and nonce
					"url" => admin_url("admin-ajax.php"),
					"ajax_delete_conditions_nonce" => wp_create_nonce("ajax_delete_conditions_security_nonce")
				),
				"text" => array(//Page texts
					"delete_condition_error_message" => "An error has ocurred while deleting conditions"
				)
					)
			);
		}

		public function enqueue_all($hook) {
			//Calling enqueues
			$this->payment_gateway_conditions_enqueues($hook);
			$this->options_page_enqueues($hook);
		}

		public function init() {
			//Initialiing enqueue
			add_action("admin_enqueue_scripts", array($this, "enqueue_all"), 10, 1);
		}

	}

}
