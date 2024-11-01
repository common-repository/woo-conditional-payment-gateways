<?php

/**
 * When we edit on the checkout page a field related to conditions, reload the payment methods
 */
if (!function_exists('wccpg_trigger_update_checkout_on_change')) {
	add_filter('woocommerce_checkout_fields', 'wccpg_trigger_update_checkout_on_change', 999999);

	function wccpg_trigger_update_checkout_on_change($fields) {
		global $wpdb;
		$condition_groups = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'wccpg_or_groups' AND meta_value <> '' ");
		$field_keys = array();
		foreach ($condition_groups as $raw_group) {
			$or_groups = maybe_unserialize($raw_group);
			if (empty($or_groups) || !is_array($or_groups)) {
				continue;
			}

			foreach ($or_groups as $or_group) {
				$field_keys = array_merge($field_keys, wp_list_pluck($or_group['conditions'], 'type'));
			}
		}
		$new_keys = array();
		foreach ($field_keys as $field_key) {
			if (strpos($field_key, 'billing_') === false && strpos($field_key, 'shipping_') === false) {
				$new_keys[] = 'billing_' . $field_key;
				$new_keys[] = 'shipping_' . $field_key;
			} else {
				$new_keys[] = $field_key;
			}
		}

		$field_keys = array_unique(array_filter($new_keys));

		foreach ($field_keys as $field_key) {
			if (isset($fields['billing'][$field_key])) {
				$fields['billing'][$field_key]['class'][] = 'update_totals_on_change';
			}
			if (isset($fields['shipping'][$field_key])) {
				$fields['shipping'][$field_key]['class'][] = 'update_totals_on_change';
			}
		}

		return $fields;
	}

}