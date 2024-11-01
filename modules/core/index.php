<?php

/*
 * @wordpress-plugin
 * Plugin Name:         WP CPG
 * Plugin URI:          
 * Description:         My description
 * Version:             1.0.0
 * Author:              VegaCorp
 * Author URI:          
 * License:             GPL2
 * License URI:         
 * Text Domain:         text-domain
 * Domain Path:         /languages
 */

//Global constans
if (!defined("WP_CPG_TEXT_DOMAIN")) {
	define("WP_CPG_TEXT_DOMAIN", WCCPG_TEXTDOMAIN);
}
if (!defined("WP_CPG_PLUGIN_NAME")) {
	define("WP_CPG_PLUGIN_NAME", WCCPG()->args['plugin_name']);
}
if (!defined("WPCPG_PATH")) {
	define("WPCPG_PATH", plugin_dir_path(__FILE__));
}

if (!defined("WPCPG_URL")) {
	define("WPCPG_URL", plugin_dir_url(__FILE__));
}

if (!defined("VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY")) {
	define("VG_PAYMENT_GATEWAYS_CONDITIONS_POST_KEY", "vg_gateway_condition");
}

if (!defined("WPCPG_CONDITIONS_FOLDER")) {
	define("WPCPG_CONDITIONS_FOLDER", WPCPG_PATH . "backend/conditions");
}

require_once WPCPG_PATH . "inc/init.php";
require_once WPCPG_PATH . "inc/helpers.php";
require_once WPCPG_PATH . "inc/enqueues.php";
require_once WPCPG_PATH . "inc/condition.php";
require_once WPCPG_PATH . "inc/traits.php";
require_once WPCPG_PATH . "views/backend/settings-views.php";
require_once WPCPG_PATH . "backend/settings.php";
require_once WPCPG_PATH . "backend/conditions-post-type.php";
require_once WPCPG_PATH . "backend/conditions-metaboxes.php";
require_once WPCPG_PATH . "views/backend/conditions-metaboxes-html.php";
require_once WPCPG_PATH . "frontend/filter.php";
require_once WPCPG_PATH . "frontend/reload-checkout.php";

register_activation_hook(__FILE__, "WP_CPG::activation_hook");




