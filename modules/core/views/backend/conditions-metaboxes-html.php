<?php
if (!class_exists("Vg_Payment_Gateways_Conditions_Metaboxes_Html")) {

	class Vg_Payment_Gateways_Conditions_Metaboxes_Html {

		public static function conditions_mbx_html() {

			//Getting groups from database

			global $post;

			$active_conditions = WCCPG_Helpers()->get_conditions();

			$or_groups = get_post_meta($post->ID, "wccpg_or_groups", true);

			if (!empty($or_groups)) {
				$or_groups = WCCPG_Helpers()->get_active_conditions_saved_data($or_groups, array_keys($active_conditions));
			}
			?>         

			<script id="hidden-conditions-group-template" type="text/x-custom-template">
			<?php
			self::conditions_group(0, null, true);
			?>
			</script>
			<script id="hidden-condition-row-template" type="text/x-custom-template">
			<?php
			self::condition_table_row(0, 0, null, true);
			?>
			</script>
			<div id = "wp-cpg-or-groups"> 
				<?php
				if (!empty($or_groups)) {

					foreach ($or_groups as $group_index => $or_group) {
						self::conditions_group($group_index, $or_group["conditions"]);
					}
				} else {
					self::conditions_group(0, null);
				}
				?>
			</div><br>    
			<button type = "button" class = "btn-add-group button-secondary"><?php echo __("Add 'Or' group", WP_CPG_TEXT_DOMAIN); ?></button> 
			<?php
			do_action('wccpg/metabox/after_conditions_group', $post, $active_conditions, $or_groups);
		}

		public static function conditions_group($group_index = 0, $conditions = null, $is_template = false) {
			//Inserting group table
			$group_text = sanitize_text_field(__("Group", WP_CPG_TEXT_DOMAIN));
			?>			
			<table class = "or_group_table" style = "margin: auto; margin-bottom:20px;">
				<thead>
					<tr>                    
						<th><button type = "button" class = "btn-remove-group button-secondary">x</button></th>
						<th class = "group-text"><?php echo "$group_text " . ($group_index + 1); ?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody class = "conditions">
					<?php
					//Inserting conditions	
					if ($is_template) {
						self::condition_table_row(0, 0, null, true);
					} else {

						if (!empty($conditions)) {
							foreach ($conditions as $condition_index => $condition) {
								self::condition_table_row($group_index, $condition_index, $condition);
							}
						} else {
							self::condition_table_row();
						}
					}
					?>
				</tbody>  
			</table>
			<?php
		}

		public static function condition_table_row($group = 0, $i = 0, $condition = null, $is_template = false) {

			$conditions = WCCPG_Helpers()->get_conditions();
			$conditions_keys = array_keys($conditions);
			$first_condition_key = $conditions_keys[0];
			$condition_type_name = "groups[$group][conditions][$i][type]";
			$condition_operators_name = "groups[$group][conditions][$i][operator]";
			$condition_input_name = "groups[$group][conditions][$i][data]";
			?>
			<tr class = "condition">
				<td class = "condition-name">
					<?php
					if (!$is_template) {
						$condition_text = __("condition", WP_CPG_TEXT_DOMAIN);
						echo $condition_text . " " . ($i + 1);
					}
					?>		     
				</td>
				<td class = "type-selection">
					<select name = "<?php
					if (!$is_template) {
						echo $condition_type_name;
					}
					?>" class = "condition-input-modifier">                     
							<?php
							if (!empty($condition)) {
								echo WCCPG_Helpers()->get_conditions_groups_html_options($condition["type"]);
							} else {
								echo WCCPG_Helpers()->get_conditions_groups_html_options();
							}
							?>                      
					</select>
				</td>
				<td class = "operator-selection">
					<select name = "<?php
					if (!$is_template) {
						echo $condition_operators_name;
					}
					?>" style = "width:156px;"  class = "condition-operators-selection">
							<?php
							if ($is_template) {
								echo $conditions[$first_condition_key]->getHtml_operators();
							} else {

								if (!empty($condition)) {
									echo $conditions[$condition["type"]]->getHtml_operators($condition["operator"]);
								} else {
									echo $conditions[$first_condition_key]->getHtml_operators();
								}
							}
							?>                      
					</select>
				</td>
				<td class = "input-column" data-saved-value="<?php if (!empty($condition)) echo esc_attr($condition["data"]); ?>">                  
					<?php
					if ($is_template) {
						echo $conditions[$first_condition_key]->get_html_input();
					} else {

						if (!empty($condition)) {
							echo $conditions[$condition["type"]]->get_html_input($condition_input_name, true, $condition["data"]);
						} else {
							echo $conditions[$first_condition_key]->get_html_input($condition_input_name);
						}
					}
					?>                     					
				</td>
				<td>
					<button type = "button" class = "btn-add-condition button-secondary">+</button>
				</td>		     
				<td>
					<button type = "button" class = "btn-remove-condition button-secondary">-</button>
				</td>
			</tr>
			<?php
		}

		//Payment gateways selected view
		public static function payment_gateways_mbx_html() {

			global $post;
			$id = "payment_gateways";
			$name = esc_attr($id . "[]");
			$options = get_post_meta($post->ID, "wccpg_payment_gateways", true);
			$is_disabled = get_post_meta($post->ID, "wccpg_is_payment_gateway_disabled", true);


			do_action('wccpg/metabox/before_payment_methods_area', $post, $options);
			?>	
			<p>
				<label><?php echo __("What do you want to do?", WP_CPG_TEXT_DOMAIN); ?></label><br/>
				<span><?php _e('What happens when the conditions are met?', WP_CPG_TEXT_DOMAIN); ?></span>
				<select name="wccpg_is_payment_gateway_disabled">
					<option value="" <?php selected(empty($is_disabled)); ?>><?php echo __("Enable the payment method(s)", WP_CPG_TEXT_DOMAIN); ?></option>
					<option value="yes" <?php selected($is_disabled === 'yes'); ?>><?php echo __("Disable the payment method(s)", WP_CPG_TEXT_DOMAIN); ?></option>
				</select>  	 
			</p>
			<p>
				<label><?php echo __("Select the payment methods", WP_CPG_TEXT_DOMAIN); ?></label><br/>
				<select multiple = "multiple" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
					<?php echo WCCPG_Helpers()->payment_gateways_options_html_formatted($options); ?>
				</select>  	
			</p>	
			<p>		
				<label><input type="checkbox" name="use_custom_gateways" class="use_custom_gateways"> <?php _e("My gateway doesn't appear in the list", WP_CPG_TEXT_DOMAIN); ?></label>
			<p class='custom_gateways' ><?php printf(__('1. Enter the payment gateway keys separated by commas. How to get the gateway key? Open the settings page of the payment gateway and you will see the key in the URL in the section part. <a href="%s" target="_blank">Example</a>.', WP_CPG_TEXT_DOMAIN), WPCPG_URL . "assets/imgs/gateway-key.png"); ?></p>
			<input class='custom_gateways' name='custom_gateways' style="width: 74%;">

			<p class='custom_gateways' ><?php _e('2. Maybe you don\'t have payment methods enabled. You can enable them on WooCommerce > Settings > Payments', WP_CPG_TEXT_DOMAIN); ?></p>

			</p>	
			<?php
			do_action('wccpg/metabox/after_payment_methods_area', $post, $options);
		}

	}

}


