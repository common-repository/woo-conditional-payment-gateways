<?php

if (!class_exists("WPCPG_Total")) {

	class WPCPG_Total extends WPCPG_Condition {

		public function __construct() {

			$this->setInput_type("number");
			$this->setOther_attributes("min = 1");

			parent::__construct(
					"total", __("Total", WP_CPG_TEXT_DOMAIN), "cart", true
			);
		}

		use WPCPG_Input;

		public function get_html_input($name = "", $return_as_string = true, $value = "") {

			return $this->get_input($name, $return_as_string, $value);
		}

		public function get_cart_value($args) {

			extract($args);

			$out = 0;
			if ($is_order_pay && $order) {
				$out = $order->get_total('save');
			} else {
				$out = $cart->get_total('save');
			}
			return $out;
		}

		public function prepare_values($val1, $val2) {

			return $this->prepare_float_values($val1, $val2);
		}

	}

	if(!isset($GLOBALS['wpcpg_conditions'])){
		$GLOBALS['wpcpg_conditions'] = array();
	}
	$GLOBALS['wpcpg_conditions'][] = new WPCPG_Total();
}

