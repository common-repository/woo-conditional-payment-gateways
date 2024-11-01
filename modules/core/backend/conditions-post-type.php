<?php

if (!class_exists("Vg_Payment_Gateways_Conditions")) {

	class Vg_Payment_Gateways_Conditions {

		//Defining main variables
		private $labels = array();
		private $args = array();
		private $post_key = VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY;

		public function __construct() {

			//Intializing labels
			$this->labels = array(
				"name" => __("Payment gateways conditions", WP_CPG_TEXT_DOMAIN),
				"add_new_item" => __("Add new condition", WP_CPG_TEXT_DOMAIN),
				"edit_item" => __("Edit condition", WP_CPG_TEXT_DOMAIN)
			);

			//Initializing vg payment gateway conditions post type args
			$this->args = array(
				"labels" => $this->labels,
				"public" => false,
				"has_archive" => false,
				"supports" => array(),
				"capability_type" => "post",
				"show_ui" => true,
				"show_in_menu" => false,
				"show_in_nav_menu" => false,
				"show_in_admin_bar" => false,
				"rewrite" => array("slug" => $this->post_key)
			);

			$this->init();
		}

		public function register() {
			//Registering vg payment gateway conditions post type
			register_post_type($this->post_key, $this->args);
		}

		public function remove_support() {
			//Removing editor from vg payment gateway conditions post type
			remove_post_type_support($this->post_key, 'editor');
		}

		//This function deletes conditions
		public function delete_conditions() {
			if (!current_user_can('manage_woocommerce') || empty($_POST["post_id"])) {
				wp_send_json_success(array(
					"message" => __("Invalid user role", WP_CPG_TEXT_DOMAIN),
					"condition_deleted" => 0
				));
			}

			//Getting post id
			$post_id = (int) $_POST["post_id"];

			//Checking nonce 
			check_ajax_referer("ajax_delete_conditions_security_nonce", "ajax_delete_conditions_nonce");

			//Flag for condition deletion
			//0 indicates fail and 1 indicates success
			$condition_deleted = 0;

			if (get_post_type($post_id) !== VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY) {
				/*
				  If the post that is being deleted is not a vg payment gateway conditions post type
				  a Warning is send, condition deleted flag is zero
				 */
				wp_send_json_success(array(
					"message" => __("Invalid post type deleting", WP_CPG_TEXT_DOMAIN),
					"condition_deleted" => $condition_deleted
				));
			}

			if (!wp_delete_post($post_id, true)) {
				//If post elimination fails an error message is send and condition deleted flag is set to zero
				$message = __("An error ocurred while deleting", WP_CPG_TEXT_DOMAIN);
				$condition_deleted = 0;
			} else {
				//If post elimination is succesull a message is send and condition deleted flag is set to 1
				$message = __("Condition succefully deleted", WP_CPG_TEXT_DOMAIN);
				$condition_deleted = 1;
			}

			//Here the data is send 
			wp_send_json_success(array(
				"message" => $message,
				"condition_deleted" => $condition_deleted
			));
		}

		function redirect_on_post_save($location, $post_id) {

			if (get_post_type($post_id) === $this->post_key && (isset($_POST['save']) || isset($_POST['publish']))) {
				// Maybe test $post_id to find some criteria.
				$location = admin_url("admin.php?page=wp_cpg_settings_menu");
			}

			return $location;
		}

		public function init() { 
			//Initializing Vg payment gateway conditions post  
			add_action("init", array($this, "register"));
			add_action("init", array($this, "remove_support"));
			add_filter('redirect_post_location', array($this, 'redirect_on_post_save'), 10, 2);
			add_action("wp_ajax_delete_conditions", array($this, "delete_conditions"));
			$vg_payment_gateway_conditions_mbxs = new Vg_Payment_Gateway_Conditions_Metaboxes($this->post_key);
		}

	}

}
