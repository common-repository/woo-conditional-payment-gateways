<?php

if (!class_exists("Vg_Payment_Gateway_Conditions_Metaboxes")) {

	class Vg_Payment_Gateway_Conditions_Metaboxes {

		private $post_type;

		public function __construct($post_type) {
			$this->post_type = $post_type;
			$this->init();
		}

		public function metaboxes() {
			//Adding metaboxes
			add_meta_box(
					"vg_payment_gateways_mbx", __("Payment gateways", WP_CPG_TEXT_DOMAIN), "Vg_Payment_Gateways_Conditions_Metaboxes_Html::payment_gateways_mbx_html", $this->post_type
			);
			add_meta_box(
					"vg_payment_gateways_conditions_mbx", __("Conditions", WP_CPG_TEXT_DOMAIN), "Vg_Payment_Gateways_Conditions_Metaboxes_Html::conditions_mbx_html", $this->post_type
			);

		}

		public function save_metaboxes($post_id) {
			//Saving metaboxes
			//Checking post type
			if (get_post_type($post_id) !== VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY) {
				return;
			}

			if (isset($_POST["groups"])) {

				$wccpg_or_groups = $this->sanitize_groups($_POST["groups"]);
			} else {
				$wccpg_or_groups = "";
			}

			update_post_meta($post_id, "wccpg_or_groups", $wccpg_or_groups);

			if (isset($_POST["payment_gateways"])) {

				$wccpg_payment_gateways = array_map('sanitize_text_field', $_POST["payment_gateways"]);
			} else {
				$wccpg_payment_gateways = array();
			}
			
			if( !empty($_REQUEST['custom_gateways']) && !empty($_REQUEST['use_custom_gateways'])){
				$wccpg_payment_gateways = array_map('sanitize_text_field', explode(',', $_REQUEST["custom_gateways"]));
			}

			update_post_meta($post_id, "wccpg_payment_gateways", $wccpg_payment_gateways);
			update_post_meta($post_id, "wccpg_is_payment_gateway_disabled", !empty($_REQUEST['wccpg_is_payment_gateway_disabled']) ? 'yes' : '');
		}

		//Function that sanitize data
		public function sanitize_groups($groups) {

			if (empty($groups)) {
				return $groups;
			}

			$sanitized_groups = array();

			foreach ($groups as $key => $group) {

				$group_conditions = $group["conditions"];

				foreach ($group_conditions as $index => $condition) {

					$sanitized_conditon = array(
						"type" => sanitize_text_field($condition["type"]),
						"operator" => sanitize_text_field($condition["operator"]),
						"data" => sanitize_text_field($condition["data"])
					);

					$sanitized_groups[$key]["conditions"][] = $sanitized_conditon;
				}
			}

			return $sanitized_groups;
		}

		public function init() {
			//Adding metaboxes
			add_action("add_meta_boxes", array($this, "metaboxes"));
			add_action("save_post", array($this, "save_metaboxes"));
		}

	}

}

