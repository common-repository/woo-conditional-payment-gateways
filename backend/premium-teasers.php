<?php

if ( !function_exists( 'wccpg_get_checkout_fields' ) ) {
    function wccpg_get_checkout_fields() {
        $default_field_keys = array(
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_country',
            'billing_address_1',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_phone',
            'billing_email',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_country',
            'shipping_address_1',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
            'order_comments'
        );
        $custom_fields = array();
        // Support for the plugin: Checkout Field Editor and Manager for WooCommerce by Acowebs
        if ( defined( 'AWCFE_FIELDS_KEY' ) ) {
            $customSections = get_option( AWCFE_FIELDS_KEY );
            if ( !empty( $customSections ) && is_array( $customSections ) ) {
                foreach ( $customSections['fields'] as $section ) {
                    foreach ( $section['fields'] as $fields ) {
                        foreach ( $fields as $field ) {
                            if ( empty( $field['name'] ) || empty( $field['label'] ) || in_array( $field['name'], $default_field_keys, true ) ) {
                                continue;
                            }
                            $custom_fields[$field['name']] = $field['label'];
                        }
                    }
                }
            }
        }
        // Support for Checkout Field Editor for WooCommerce by ThemeHIgh FREE
        if ( defined( 'THWCFD_VERSION' ) ) {
            $billing_fields = get_option( 'wc_fields_billing' );
            $shipping_fields = get_option( 'wc_fields_shipping' );
            $additional_fields = get_option( 'wc_fields_additional' );
            if ( empty( $billing_fields ) || !is_array( $billing_fields ) ) {
                $billing_fields = array();
            }
            if ( empty( $shipping_fields ) || !is_array( $shipping_fields ) ) {
                $shipping_fields = array();
            }
            if ( empty( $additional_fields ) || !is_array( $additional_fields ) ) {
                $additional_fields = array();
            }
            $all_fields = array_merge( $billing_fields, $shipping_fields, $additional_fields );
            foreach ( $all_fields as $field ) {
                if ( empty( $field['name'] ) || empty( $field['label'] ) || in_array( $field['name'], $default_field_keys, true ) ) {
                    continue;
                }
                $custom_fields[$field['name']] = $field['label'];
            }
        }
        // Support for Checkout Field Editor for WooCommerce by ThemeHIgh Pro
        if ( defined( 'THWCFE_VERSION' ) ) {
            $sections = get_option( 'thwcfe_sections' );
            if ( is_array( $sections ) ) {
                foreach ( $sections as $section_key => $section_obj ) {
                    if ( is_array( $section_obj->fields ) ) {
                        foreach ( $section_obj->fields as $field ) {
                            if ( empty( $field->id ) || empty( $field->title ) || in_array( $field->id, $default_field_keys, true ) ) {
                                continue;
                            }
                            $custom_fields[$field->id] = $field->title;
                        }
                    }
                }
            }
        }
        // Support for Checkout Manager for WooCommerce by QuadLayers
        if ( defined( 'WOOCCM_PLUGIN_NAME' ) ) {
            $billing_fields = get_option( 'wooccm_billing' );
            $shipping_fields = get_option( 'wooccm_shipping' );
            $additional_fields = get_option( 'wooccm_additional' );
            if ( empty( $billing_fields ) || !is_array( $billing_fields ) ) {
                $billing_fields = array();
            }
            if ( empty( $shipping_fields ) || !is_array( $shipping_fields ) ) {
                $shipping_fields = array();
            }
            if ( empty( $additional_fields ) || !is_array( $additional_fields ) ) {
                $additional_fields = array();
            }
            $all_fields = array_merge( $billing_fields, $shipping_fields, $additional_fields );
            foreach ( $all_fields as $field ) {
                if ( empty( $field['key'] ) || empty( $field['label'] ) || in_array( $field['key'], $default_field_keys, true ) ) {
                    continue;
                }
                $custom_fields[$field['key']] = $field['label'];
            }
        }
        // Support for WooCommerce Checkout & Account Field Editor by ThemeLocation
        if ( function_exists( 'tl_fields' ) && defined( 'WCFE_VERSION' ) ) {
            $billing_fields = get_option( 'wc_fields_billing' );
            $shipping_fields = get_option( 'wc_fields_shipping' );
            $additional_fields = get_option( 'wc_fields_additional' );
            if ( empty( $billing_fields ) || !is_array( $billing_fields ) ) {
                $billing_fields = array();
            }
            if ( empty( $shipping_fields ) || !is_array( $shipping_fields ) ) {
                $shipping_fields = array();
            }
            if ( empty( $additional_fields ) || !is_array( $additional_fields ) ) {
                $additional_fields = array();
            }
            $all_fields = array_merge( $billing_fields, $shipping_fields, $additional_fields );
            foreach ( $all_fields as $name => $field ) {
                if ( empty( $field['label'] ) || in_array( $name, $default_field_keys, true ) ) {
                    continue;
                }
                $custom_fields[$name] = $field['label'];
            }
        }
        // Support for plugin WooCommerce Conditional Product Fields at Checkout by Lagudi Domenico
        if ( defined( 'WCPFC_PLUGIN_PATH' ) && function_exists( 'wcpfc_setup' ) ) {
            $all_fields = get_option( 'wcpfc_field_configuration_data' );
            if ( empty( $all_fields ) || !is_array( $all_fields ) ) {
                $all_fields = array();
            }
            foreach ( $all_fields as $field_index => $field ) {
                $index = str_replace( 'id', '', $field_index );
                $name = 'order_' . $field['id'] . '_onetimefield-' . $index . '-0[value]';
                $label = current( $field['name'] );
                $custom_fields[$name] = $label;
            }
        }
        return $custom_fields;
    }

}
add_filter( 'wpcpg_conditions_groups_html_options', 'wccpg_tease_premium_condition_options' );
function wccpg_tease_premium_condition_options(  $options_html  ) {
    $teaser = '<optgroup class="cart" label="Cart"><option disabled value="currency">Currency (Pro)</option><option disabled value="contains_product">Contains product (Pro)</option><option disabled value="contains_shipping_class">Contains shipping class (Pro)</option><option disabled value="coupon_discounts_total">Coupon Discounts total (Pro)</option><option disabled value="coupon">Coupon (Pro)</option><option disabled value="is_order_pay_page">Is order pay page? (Pro)</option><option disabled value="quantity">Quantity (Pro)</option><option disabled value="shipping_method">Shipping method (Pro)</option><option disabled value="shipping_total">Shipping total (Pro)</option><option disabled value="subtotal_ext_taxes">Subtotal exc. taxes (Pro)</option><option disabled value="subtotal">Subtotal (Pro)</option><option disabled value="tax">Tax (Pro)</option><option disabled value="total">Total (Pro)</option></optgroup><optgroup class="user_details" label="User details"><option disabled value="city">Billing city (Pro)</option><option disabled value="billing_company">Billing company (Pro)</option><option disabled value="country">Billing country (Pro)</option><option disabled value="billing_email">Billing email (Pro)</option><option disabled value="state">Billing state (Pro)</option><option disabled value="zipcode">Billing zipcode (Pro)</option><option disabled value="customer_email">Customer email (Pro)</option><option disabled value="days_since_registration_date">Days since registration date (Pro)</option><option disabled value="orders_count">Previous orders from the customer (Pro)</option><option disabled value="shipping_city">Shipping city (Pro)</option><option disabled value="shipping_company">Shipping company (Pro)</option><option disabled value="shipping_country">Shipping Country (Pro)</option><option disabled value="shipping_state">Shipping state (Pro)</option><option disabled value="shipping_zipcode">Shipping zipcode (Pro)</option><option disabled value="user_role">User role (Pro)</option></optgroup><optgroup class="product" label="Product"><option disabled value="vendor">Product vendor (Pro)</option><option disabled value="backorder">Is on backorder? (Pro)</option><option disabled value="category">Category (Pro)</option><option disabled value="height">Height (Pro)</option><option disabled value="length">Length (Pro)</option>';
    $checkout_fields = wccpg_get_checkout_fields();
    foreach ( $checkout_fields as $field_key => $label ) {
        $teaser = str_replace( '<optgroup class="cart" label="Cart">', '<optgroup class="cart" label="Cart"><option disabled value="' . esc_attr( $field_key ) . '">' . esc_html( $label ) . ' (Pro)</option>', $teaser );
    }
    $taxonomies = wp_list_filter( get_object_taxonomies( 'product', 'objects' ), array(
        'show_ui' => true,
    ) );
    foreach ( $taxonomies as $taxonomy ) {
        $teaser .= '<option disabled value="product_cat">Taxonomy: ' . $taxonomy->label . ' (Pro)</option>';
    }
    foreach ( $taxonomies as $taxonomy ) {
        $teaser .= '<option disabled value="variation_attributepa_size">Variation_Attribute: ' . $taxonomy->name . ' (Pro)</option>';
    }
    $teaser .= '<option disabled value="variation_id">Variation IDs (Pro)</option><option disabled value="weight">Weight (Pro)</option><option disabled value="width">Width (Pro)</option></optgroup><optgroup class="date" label="Date"><option disabled value="full_date">Date (YYYY-MM-DD) (Pro)</option><option disabled value="hour_day">Hour of the day (number from 0 to 23) (Pro)</option><option disabled value="month_day">Day of the month (number from 1 to 31) (Pro)</option><option disabled value="week_day">Day of the week (Pro)</option></optgroup>';
    $options_html .= $teaser;
    return $options_html;
}

add_action( 'wccpg/metabox/after_conditions_group', 'wccpg_tease_invite_pro_after_condition_groups' );
function wccpg_tease_invite_pro_after_condition_groups() {
    ?>
		<hr>
		<?php 
    printf( __( '<p><b>Go Premium</b>. You can use all conditions, including: Product information (category, dimensions), Shipping methods, User information (zip, state, city, address), Taxes, You can rotate payment methods by date or sequentially by order, and more. <a href="%s" target="_blank" class="wccpg-go-premium-link">%s</a></p>', WCCPG_TEXTDOMAIN ), WCCPG()->args['buy_url'], WCCPG()->args['buy_text'] );
    ?>
		<?php 
}
