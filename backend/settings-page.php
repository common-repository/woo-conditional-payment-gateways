<?php

add_action( 'wccpg/settings_page/intro', 'wccpg_add_settings_page_intro' );
add_action( 'wccpg/metabox/after_conditions_group', 'wccpg_add_settings_page_intro' );
function wccpg_add_settings_page_intro() {
    include WCCPG_DIST_DIR . '/views/backend/settings-page-intro.php';
}

add_filter( 'vg_plugin_sdk/assets/allowed_pages', 'wccpg_enable_assets_on_settings_page' );
function wccpg_enable_assets_on_settings_page(  $allowed_pages  ) {
    $allowed_pages[] = 'wp_cpg_settings_menu';
    return $allowed_pages;
}
