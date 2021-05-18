<?php
/**
 * GravityForms customizations
 */

// Display GravityForms fields label if it set to Hidden
add_action(
    'admin_head',
    function () {
        ob_start(); ?>
        <style>
            .hidden_label label.gfield_label {
                visibility: visible;
                line-height: inherit;
            }

            .theme-overlay .theme-version {
                display: none;
            }
        </style>
        <?php
        echo ob_get_clean();
    }
);

// Enable GF Honeypot for all forms
add_action(
    'gform_after_save_form',
    /**
     * @param $form
     * @param $is_new
     */
    function ($form, $is_new) {
        if ($is_new) {
            $form['enableHoneypot'] = true;
            $form['is_active'] = 1;
            GFAPI::update_form($form);
        }
    },
    10,
    2
);

// Disable date field autocomplete popup
add_filter(
    'gform_field_content',
    /**
     * @param string $input field HTML markup
     * @param object $field GForm field object
     *
     * @return string
     */
    function ($input, $field) {
        if (is_admin()) {
            return $input;
        }
        if (GFFormsModel::is_html5_enabled() && $field->type == 'date') {
            $input = str_replace('<input', '<input autocomplete="off" ', $input);
        }

        return $input;
    },
    11,
    2
);

// Prevent page jumping on form submit
add_filter('gform_confirmation_anchor', '__return_false');

// Show Gravity Form field label appearance dropdown
add_filter('gform_enable_field_label_visibility_settings', '__return_true');

// Replace standard form input with button
add_filter(
    'gform_submit_button',
    function ($button, $form) {
        if ($form['button']['type'] == 'image' && !empty($form['button']['imageUrl'])) {
            return $button;
        }

        $button_inner = $form['button']['text'] ?: __('Submit', 'fxy');

        return str_replace(array('input', '/>'), array('button', '>'), $button) . "{$button_inner}</button>";
    },
    10,
    2
);
