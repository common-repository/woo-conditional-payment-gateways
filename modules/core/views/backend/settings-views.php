<?php
/*
  Setting views class
 */

if (!class_exists("WP_CPG_Settings_Views")) {

	class WP_CPG_Settings_Views {

		//Settings section description
		public static function wp_cpg_section_description() {
			do_action('wccpg/settings_page/intro');
		}

		public static function enable_conditional_payment_gateways_option_view() {
			//Getting option
			$option = get_option("wp_cpg_enable_conditional_payment_gateways");
			$checked = "";
			//Checking option value
			if ($option === "on") {
				$checked = "checked";
			}

			//Option view
			?>		
			<input 
				id = "wp_cpg_enable_conditional_payment_gateways" 
				name = "wp_cpg_enable_conditional_payment_gateways" 
				<?php echo esc_attr($checked); ?> 
				type = "checkbox"
				>
				<?php if (empty($checked)) { ?>
				<span class="error" style="color: red;"><?php _e('The conditions are deactivated. Check this option and save to activate them', WP_CPG_TEXT_DOMAIN); ?></span>
				<?php
			}
		}

		public static function get_payment_gateway_conditions_posts() {

			//Getting vg payment gateways conditions posts
			$args = array(
				"post_type" => VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY,
				"posts_per_page" => -1
			);

			//Executing query 
			$vg_payment_gateways_conditions = new WP_query($args);

			if (!$vg_payment_gateways_conditions->have_posts()) {
				return "";
			}

			$vg_payment_gateways_conditions->posts = array_reverse($vg_payment_gateways_conditions->posts);
			while ($vg_payment_gateways_conditions->have_posts()) {

				$vg_payment_gateways_conditions->the_post();

				$title = get_the_title();

				if (empty($title)) {
					$title = __("(Untitled condition)", WP_CPG_TEXT_DOMAIN);
				}

				$id = get_the_id();
				//Inserting row with post information for each post
				self::insert_post_row($id, $title);
			}
		}

		//Function that inserts a row with a link to edit post and a link to delete post
		public static function insert_post_row($id, $title) {

			$edit_text = __("Edit", WP_CPG_TEXT_DOMAIN);
			$delete_text = __("Delete", WP_CPG_TEXT_DOMAIN);
			$link_to_edit = "post.php?post={$id}&action=edit";
			$groups = get_post_meta($id, "wccpg_or_groups", true);
			$conditions_count = self::count_conditions($groups);
			?>
			<tr id = <?php echo esc_attr("condition-{$id}"); ?>>
				<td>
					<span><?php echo esc_html($title); ?></span><br>
					<a href = <?php echo esc_attr($link_to_edit); ?>><?php echo $edit_text; ?></a> | <span id = "delete-<?php echo $id; ?>" class = "delete-conditions" style = "color:#c70000; cursor:pointer;"><?php echo $delete_text; ?></span>
				</td>
				<td>
					<span><?php echo $conditions_count; ?></span>
				</td>
				<td>
					<span><?php echo ( get_post_status($id) === 'publish') ? __('Active', WP_CPG_TEXT_DOMAIN) : __('Inactive', WP_CPG_TEXT_DOMAIN); ?></span>
				</td>
				<td><span><?php echo get_the_date('', $id); ?></span></td> 
			</tr>
			<?php
		}

		//Function that displays a table which contains payment gateway conditions post type information
		public static function payment_gateway_conditions_option_view() {

			$title_text = sanitize_text_field(__("Title", WP_CPG_TEXT_DOMAIN));
			$conditions_text = sanitize_text_field(__("Conditions", WP_CPG_TEXT_DOMAIN));
			$button_text = sanitize_text_field(__("Add condition", WP_CPG_TEXT_DOMAIN));
			do_action('wccpg/settings_page/before_conditions_list');
			?>
			<table class = "widefat wccpg-conditions-list">
				<thead>
					<tr>
						<td><strong><?php echo $title_text; ?></strong></td>
						<td><strong># <?php echo $conditions_text; ?></strong></td>
						<td><strong><?php _e('Status', WP_CPG_TEXT_DOMAIN); ?></strong></td>
						<td><strong><?php _e('Created', WP_CPG_TEXT_DOMAIN); ?></strong></td>
					</tr>
				</thead>
				<tbody>               
					<?php echo self::get_payment_gateway_conditions_posts(); ?>                
				</tbody>
				<tfoot>
				<td>
					<a class = "button-primary" href = "post-new.php?post_type=<?php echo esc_attr(VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY); ?>"><?php echo $button_text; ?></a>
				</td>  
				<td></td>
				<td></td>
				<td></td>
			</tfoot>
			</table>
			
			<?php
			do_action('wccpg/settings_page/after_conditions_list');
		}

		//Vg payment gateways conditions count 
		public static function count_conditions($groups) {

			$conditions_count = 0;

			if (empty($groups)) {
				return $conditions_count;
			}

			foreach ($groups as $group) {

				$conditions_count += count($group["conditions"]);
			}

			return $conditions_count;
		}

	}

}