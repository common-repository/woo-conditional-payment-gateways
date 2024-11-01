<?php

if (!class_exists("WPCPG_Condition")) {

	abstract class WPCPG_Condition {

		private $condition_key;
		private $label;
		private $group;
		private $operators;
		private $html_operators;
		private $is_numeric;
		private $html_input;
		private $condition_assoc;
		private $has_container_operators;

		public function __construct($condition_key, $label, $group, $is_numeric, $has_container_operators = false) {

			$this->condition_key = $condition_key;
			$this->label = $label;
			$this->group = $group;
			$this->is_numeric = $is_numeric;
			$this->has_container_operators = $has_container_operators;
			$this->init_operators();
			$this->setHtml_operators();

			$this->init();
		}

		abstract protected function get_cart_value($args);

		abstract protected function get_html_input();

		abstract protected function prepare_values($val1, $val2);

		public function condition_is_valid($args) {

			extract($args, EXTR_OVERWRITE);

			$operator = $condition["operator"];

			$val1 = $condition["data"];
			$val2 = $this->get_cart_value($args);

			if (in_array($operator, array('equal_to_field', 'not_equal_to_field'), true)) {
				foreach ($GLOBALS['wpcpg_conditions'] as $extra_condition) {
					if ($extra_condition->condition_key !== $val1) {
						continue;
					}
					$val1 = $extra_condition->get_cart_value($args);
				}
			}

			$prepared_values = $this->prepare_values($val1, $val2);

			$val1 = $prepared_values["val1"];
			$val2 = $prepared_values["val2"];

			if ($operator === 'equal_to_field') {
				$operator = 'equal_to';
			} elseif ($operator === 'not_equal_to_field') {
				$operator = 'not_equal_to';
			}

			return $this->is_valid($operator, $val1, $val2);
		}

		public function get_html_option($value = "") {

			return '<option value = "' . esc_attr($this->condition_key) . '" ' . selected($value == $this->condition_key, true, false) . '>' . $this->label . '</option>';
		}

		public function init_operators() {

			$default_operators = array(
				"equal_to" => __("=", WP_CPG_TEXT_DOMAIN),
				"not_equal_to" => __("!= (Not equal)", WP_CPG_TEXT_DOMAIN),
				"equal_to_field" => __("Equal to field", WP_CPG_TEXT_DOMAIN),
				"not_equal_to_field" => __("Not equal to field", WP_CPG_TEXT_DOMAIN),
			);

			$numeric_operators = array(
				"less_than" => __("<", WP_CPG_TEXT_DOMAIN),
				"less_or_equal_than" => __("<=", WP_CPG_TEXT_DOMAIN),
				"higher_than" => __(">", WP_CPG_TEXT_DOMAIN),
				"higher_or_equal_than" => __(">=", WP_CPG_TEXT_DOMAIN)
			);

			$container_operators = array(
				"contains" => __("Contains", WP_CPG_TEXT_DOMAIN),
				"not_contains" => __("Not contains", WP_CPG_TEXT_DOMAIN),
				"appears_in_this_list" => __("Appears in this list", WP_CPG_TEXT_DOMAIN),
				"not_appears_in_this_list" => __("Does not appear in this list", WP_CPG_TEXT_DOMAIN)
			);

			if ($this->is_numeric) {
				$this->operators = array_merge($default_operators, $numeric_operators);
			} else {
				$this->operators = $default_operators;
			}

			if ($this->has_container_operators) {
				$this->operators = array_merge($this->operators, $container_operators);
			}
		}

		public function is_valid($operator, $val1, $val2) {

			switch ($operator) {

				case "equal_to":
					if (empty($val2) && empty($val1)) {
						$result = true;
					} elseif (is_array($val2)) {
						$result = in_array($val1, $val2);
					} else {
						$result = $val1 === $val2;
					}

					break;

				case "not_equal_to":

					if (empty($val2) && empty($val1)) {
						$result = false;
					} elseif (is_array($val2)) {
						$result = !in_array($val1, $val2);
					} else {
						$result = !($val1 === $val2);
					}

					break;

				case "less_than":
					$result = $val2 < $val1;
					break;

				case "less_or_equal_than":
					$result = $val2 <= $val1;
					break;

				case "higher_than":
					$result = $val2 > $val1;
					break;

				case "higher_or_equal_than":
					$result = $val2 >= $val1;
					break;

				case "contains":
					if (is_int($val1) && is_array($val2)) {
						$result = in_array($val1, $val2, true);
					} else {
						$result1 = stripos($val2, $val1);
						$result2 = stripos($val1, $val2);
						$result = $result1 !== false || $result2 !== false ? true : false;
					}
//					$result = $result === false ? $result : true;
					break;

				case "not_contains":
					if (is_int($val1) && is_array($val2)) {
						$result = !in_array($val1, $val2, true);
					} else {
						$result = stripos($val2, $val1);
						$result = $result === false ? true : $result;
					}
					break;

				case "appears_in_this_list" :
					$val1 = array_map('trim', explode(";", $val1) );

					if( is_array($val1) && is_array($val2)){
						$val2 = array_map( 'strval', $val2 );
						$result = !empty($val2) && count( array_intersect( $val1, $val2 ) ) > 0;
					} else {
						$val2 = (string) $val2;
						$result = !empty($val1) && is_array($val1) ? in_array(strtolower($val2), array_map("strtolower", array_map("trim", $val1)), true) : false;
					}
					break;
				case "not_appears_in_this_list" :
					$val1 = explode(";", $val1);
					$result = !empty($val1) && is_array($val1) ? !in_array(strtolower($val2), array_map("strtolower", array_map("trim", $val1)), true) : false;
					break;
			}

			return $result;
		}

		public function add_condition($conditions, $add_as_assoc_array = false) {

			if ($add_as_assoc_array) {

				$this->init_condition_assoc();
				$conditions[$this->condition_key] = $this->condition_assoc;
			} else {
				$conditions[$this->condition_key] = $this;
			}

			return $conditions;
		}

		function getCondition_key() {
			return $this->condition_key;
		}

		function getLabel() {
			return $this->label;
		}

		function getGroup() {
			return $this->group;
		}

		function getOperators() {
			return $this->operators;
		}

		function getIs_numeric() {
			return $this->is_numeric;
		}

		function setCondition_key($condition_key) {
			$this->condition_key = $condition_key;
		}

		function setLabel($label) {
			$this->label = $label;
		}

		function setGroup($group) {
			$this->group = $group;
		}

		function setOperators($operators) {
			$this->operators = $operators;
		}

		function setIs_numeric($is_numeric) {
			$this->is_numeric = $is_numeric;
		}

		function getHas_container_operators() {
			return $this->has_container_operators;
		}

		function setHas_container_operators($has_container_operators) {
			$this->has_container_operators = $has_container_operators;
		}

		function getHtml_operators($selected_value = "") {
			if (empty($selected_value)) {
				return $this->html_operators;
			} else {
				return WCCPG_Helpers()->get_options_html_formatted($this->operators, $selected_value);
			}
		}

		function setHtml_operators() {
			$this->html_operators = WCCPG_Helpers()->get_options_html_formatted($this->operators);
		}

		public function setCondition_assoc($condition_assoc) {
			$this->condition_assoc = $condition_assoc;
		}

		public function getCondition_assoc() {
			return $this->condition_assoc;
		}

		public function init_condition_assoc() {
			$this->condition_assoc["condition_key"] = $this->condition_key;
			$this->condition_assoc["label"] = $this->label;
			$this->condition_assoc["group"] = $this->group;
			$this->condition_assoc["operators"] = $this->operators;
			$this->condition_assoc["html_operators"] = $this->html_operators;
			$this->condition_assoc["is_numeric"] = $this->is_numeric;
			$this->condition_assoc["html_input"] = $this->get_html_input();
		}

		public function add_to_condition_assoc($key, $value) {
			$this->condition_assoc[$key] = $value;
		}

		public function prepare_float_values($val1, $val2) {

			$val1 = floatval($val1);

			if (is_array($val2)) {
				$val2 = array_map("floatval", $val2);
			} else {
				$val2 = floatval($val2);
			}

			return array(
				"val1" => $val1,
				"val2" => $val2
			);
		}

		public function prepare_int_values($val1, $val2) {

			$val1 = intval($val1);

			if (is_array($val2)) {
				$val2 = array_map("intval", $val2);
			} else {
				$val2 = intval($val2);
			}

			return array(
				"val1" => $val1,
				"val2" => $val2
			);
		}

		public function prepare_non_numeric_values($val1, $val2) {

			$val1 = strtolower($val1);

			if (is_array($val2)) {
				$val2 = array_map("strtolower", $val2);
			} else {
				$val2 = strtolower($val2);
			}

			return array(
				"val1" => $val1,
				"val2" => $val2
			);
		}

		public function init() {

			add_filter("wpcpg_conditions", array($this, "add_condition"), 10, 2);
		}

	}

}