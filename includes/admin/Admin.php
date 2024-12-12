<?php

namespace SP_Slider\Includes\Admin;

class Admin {

    public static function init() {
        // Hook to register admin-specific settings and functionality
        add_action('admin_menu', [__CLASS__, 'register_admin_menu']);
    }

    public static function register_admin_menu() {
        // Include and initialize the meta box functionality
        require_once __DIR__ . '/SliderMetaBox.php';
        SliderMetaBox::init();
        // Add an admin menu page
        add_menu_page(
            __('SPL Slider', 'spl-slider'),
            __('SPL Slider', 'spl-slider'),
            'manage_options',
            'spl-slider',
            [__CLASS__, 'render_settings_page']
        );
    }

    public static function render_settings_page() {
        echo '<h1>' . __('SPL Slider Settings', 'spl-slider') . '</h1>';
    }
}
