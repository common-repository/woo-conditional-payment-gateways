<?php
if (!class_exists("WP_CPG_Settings")) {

	class WP_CPG_Settings {

		public function __construct() {
			$this->init();
		}

		public function settings_menu() {
			//Adding menu page for settings
			add_submenu_page('woocommerce', WP_CPG_PLUGIN_NAME, WP_CPG_PLUGIN_NAME, "manage_woocommerce", "wp_cpg_settings_menu", array($this, "settings_menu_page_display"));
		}

		public function settings_menu_page_display() {


			$is_not_first_time = (bool) get_option('wp_cpg_is_not_first_time');
			if (!$is_not_first_time) {
				update_option("wp_cpg_enable_conditional_payment_gateways", 'on');
				update_option("wp_cpg_is_not_first_time", 1);
			}

			//Adding error messages
			if (isset($_GET["settings-updated"])) {

				add_settings_error(
						"wp_cpg_enable_conditional_payment_gateways", "wp_cpg_settings_config_success", __("Settings saved", WP_CPG_TEXT_DOMAIN), "updated"
				);
			}

			settings_errors("wp_cpg_enable_conditional_payment_gateways");

			//Display
			?> <form method='POST' action='options.php'> <?php
				settings_fields("wp_cpg_settings_config");
				do_settings_sections("wp_cpg_settings_config");
				submit_button();
				?> </form> <?php
		}

		public function all_settings() {

			//Adding settings
			register_setting("wp_cpg_settings_config", "wp_cpg_enable_conditional_payment_gateways");

			add_settings_section(
					"wp_cpg_config_section", WP_CPG_PLUGIN_NAME, "WP_CPG_Settings_Views::wp_cpg_section_description", "wp_cpg_settings_config"
			);

			add_settings_field(
					"wp_cpg_enable_conditional_payment_gateways", __("Enable conditions", WP_CPG_TEXT_DOMAIN), "WP_CPG_Settings_Views::enable_conditional_payment_gateways_option_view", "wp_cpg_settings_config", "wp_cpg_config_section"
			);

			add_settings_field(
					"conditions_field", __("Conditions", WP_CPG_TEXT_DOMAIN), "WP_CPG_Settings_Views::payment_gateway_conditions_option_view", "wp_cpg_settings_config", "wp_cpg_config_section"
			);
			register_setting("wp_cpg_settings_config", "wp_cpg_no_payment_gateways_message");
			add_settings_field(
					"wp_cpg_no_payment_gateways_message", __('Message to display when zero <br>payment gateways are enabled', WP_CPG_TEXT_DOMAIN), array($this, 'zero_payment_gateways_message_option_view'), "wp_cpg_settings_config", "wp_cpg_config_section"
			);
		}

		public function zero_payment_gateways_message_option_view() {
			//Getting option
			$option = get_option("wp_cpg_no_payment_gateways_message");
			if (empty($option)) {
				$option = '';
			} else {
				$option = esc_html($option);
			}

			//Option view
			?>		
			<input 
				id = "wp_cpg_no_payment_gateways_message" 
				name = "wp_cpg_no_payment_gateways_message" 
				value="<?php echo esc_attr($option); ?>"
				type = "text" style="width: 100%"
				>
			<span class="no-gateways-desc"><?php _e('You can leave this empty to display the default message from WooCommerce.', WP_CPG_TEXT_DOMAIN); ?></span>
			<?php
		}

		public function init() {
			//Initializing settings
			add_action("admin_menu", array($this, "settings_menu"));
			add_action("admin_init", array($this, "all_settings"));
		}

	}

}

