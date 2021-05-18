<?php
/**
 * Functions
 */

/******************************************************************************
 * Included Functions
 *****************************************************************************/

// Helpers function
require_once 'inc/helpers.php';
// Install Recommended plugins
require_once 'inc/recommended-plugins.php';
// Walker modification
require_once 'inc/class-foundation-navigation.php';
// Dynamic admin
require_once 'inc/class-dynamic-admin.php';
// Lazy Load
require_once 'inc/class-lazyload.php';
// Theme customizations
include_once 'inc/theme-customizations.php';
// Home slider function
include_once 'inc/home-slider.php';
// SVG Support
include_once 'inc/svg-support.php';
// Gravity form customizations
include_once 'inc/gravity-form-customizations.php';
// Extend WP Search with Custom fields
include_once 'inc/custom-fields-search.php';
// Google maps integration
include_once 'inc/google-maps.php';
// TinyMCE editor customizations
include_once 'inc/tiny-mce-customizations.php';
// WooCommerce functionality
include_once 'inc/woo-custom.php';
// Include all additional shortcodes
//include_once 'inc/shortcodes.php';

// Register ACF Gravity Forms field
add_action('init', function () {
    if (class_exists('ACF')) {
        require_once 'inc/class-fxy-acf-field-gf-field-v5.php';
    }
});

/******************************************************************************
 * Constants
 *****************************************************************************/

define('IMAGE_PLACEHOLDER', asset_path('images/placeholder.jpg'));

// Prevent Fatal error on site if ACF not installed/activated
add_action(
    'wp',
    function () {
        include_once 'inc/acf-placeholder.php';
    },
    PHP_INT_MAX
);

/******************************************************************************
 * Enqueue Scripts and Styles for Front-End
 *****************************************************************************/
add_action('init', function () {
    wp_register_script('runtime.js', asset_path('scripts/runtime.js'), [], null, true);
    wp_register_script('vendor.js', asset_path('scripts/vendor.js'), [], null, true);
});

add_action('wp_enqueue_scripts', function () {
    if (!is_admin()) {
        // Disable gutenberg built-in styles
        // wp_dequeue_style('wp-block-library');

        wp_enqueue_script('jquery');

        wp_enqueue_style('main.css', asset_path('styles/main.css'), 'vendor.css', null);
        wp_enqueue_script(
            'main.js',
            asset_path('scripts/main.js'),
            ['jquery', 'runtime.js', 'vendor.js'],
            null,
            true
        );

        wp_localize_script(
            'main',
            'ajax_object',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('project_nonce'),
            ]
        );
    }
});

/******************************************************************************
 * Additional Functions
 *****************************************************************************/

// Dynamic Admin
if (class_exists('theme\DynamicAdmin') && is_admin()) {
    $dynamic_admin = new theme\DynamicAdmin();
//    $dynamic_admin->addField('page', 'template', __('Page Template', 'fxy'), 'template_detail_field_for_page');
    $dynamic_admin->run();
}

// Apply lazyload to whole page content
if (class_exists('theme\CreateLazyImg')) {
    add_action(
        'template_redirect',
        function () {
            ob_start(function ($html) {
                $lazy = new theme\CreateLazyImg;
                $buffer = $lazy->ignoreScripts($html);
                $buffer = $lazy->ignoreNoscripts($buffer);

                $html = $lazy->lazyloadImages($html, $buffer);
                $html = $lazy->lazyloadPictures($html, $buffer);
                $html = $lazy->lazyloadBackgroundImages($html, $buffer);

                return $html;
            });
        }
    );
}

/*********************** PUT YOU FUNCTIONS BELOW *****************************/

// Custom media library's image sizes
add_image_size('full_hd', 1920, 0, ['center', 'center']);
add_image_size('large_high', 1024, 0, false);
// add_image_size( 'name', width, height, ['center','center']);

// Disable gutenberg
add_filter('use_block_editor_for_post_type', '__return_false');

/*****************************************************************************/


/******************* HIDE/SHOW WORDPRESS PLUGINS MENU ITEM *******************/

// Remove and Restore ability to Add new plugins to site
function remove_plugins_menu_item($role_name)
{
    $role = get_role($role_name);
    $role->remove_cap('activate_plugins');
    $role->remove_cap('install_plugins');
    $role->remove_cap('upload_plugins');
    $role->remove_cap('update_plugins');
}

function restore_plugins_menu_item($role_name)
{
    $role = get_role($role_name);
    $role->add_cap('activate_plugins');
    $role->add_cap('install_plugins');
    $role->add_cap('upload_plugins');
    $role->add_cap('update_plugins');
}

// remove_plugins_menu_item('administrator');
// restore_plugins_menu_item('administrator');

/*****************************************************************************/
