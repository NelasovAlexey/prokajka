<?php
/**
 * Google maps integration
 */

$default_google_maps_api_key = 'AIzaSyBgg23TIs_tBSpNQa8RC0b7fuV4SOVN840';

add_filter(
    'acf/load_field/type=google_map',
    function ($field) {
        global $default_google_maps_api_key;
        $google_map_api = 'https://maps.googleapis.com/maps/api/js';
        $api_args = [
            'key' => get_theme_mod('google_maps_api') ?: $default_google_maps_api_key,
            'language' => 'en',
            'v' => '3.exp'
        ];
        wp_enqueue_script('google.maps.api', add_query_arg($api_args, $google_map_api), null, null, true);

        return $field;
    }
);

/**
 * Set Google Map API key
 */
add_action(
    'acf/init',
    function () {
        global $default_google_maps_api_key;
        acf_update_setting(
            'google_api_key',
            get_theme_mod('google_maps_api') ?: $default_google_maps_api_key
        );
    }
);

/**
 * Register Google Maps API key settings in customizer
 */
add_action(
    'customize_register',
    /**
     * @param $wp_customize Object|WP_Customize_Manager
     */
    function ($wp_customize) {
        global $default_google_maps_api_key;
        $wp_customize->add_section('google_maps', [
            'title' => __('Google Maps', 'fxy'),
            'priority' => 30,
        ]);

        $wp_customize->add_setting('google_maps_api', [
            'default' => $default_google_maps_api_key,
        ]);

        $wp_customize->add_control('google_maps_api', [
            'label' => __('Google Maps API key', 'fxy'),
            'section' => 'google_maps',
            'settings' => 'google_maps_api',
            'type' => 'text',
        ]);

        $wp_customize->add_setting('outline_color', []);

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                'outline_color',
                [
                    'label' => __('Outline color', 'fxy'),
                    'section' => 'colors',
                    'settings' => 'outline_color'
                ]
            )
        );
    }
);
