<?php
/**
 * WooCommerce customization
 */

// Add WooCommerce support
add_action(
    'after_setup_theme',
    function () {
        add_theme_support('woocommerce');
    }
);

//======================================================================
// SHOP PAGE
//======================================================================
