<?php

if ( !function_exists( 'wccpg_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wccpg_fs() {
        global $wccpg_fs;
        if ( !isset( $wccpg_fs ) ) {
            require_once WCCPG_DIST_DIR . '/vendor/freemius/start.php';
            $wccpg_fs = fs_dynamic_init( array(
                'id'             => '3029',
                'slug'           => 'woo-conditional-payment-gateways',
                'type'           => 'plugin',
                'public_key'     => 'pk_ac157b858a6b58e4fe11f42e391a5',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                'menu'           => array(
                    'slug'       => 'wp_cpg_settings_menu',
                    'first-path' => 'admin.php?page=wp_cpg_settings_menu',
                    'support'    => false,
                    'parent'     => array(
                        'slug' => 'woocommerce',
                    ),
                ),
                'is_live'        => true,
            ) );
        }
        return $wccpg_fs;
    }

    // Init Freemius.
    wccpg_fs();
    // Signal that SDK was initiated.
    do_action( 'wccpg_fs_loaded' );
    wccpg_fs()->add_filter( 'show_trial', '__return_false' );
}