<?php

/**
 * Plugin Name: SPL Slider
 * Description: A lightweight and customizable slider plugin.
 * Version: 1.0
 * Author: Lee Hernandez
 * Text Domain: spl-slider
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include core files.
require_once __DIR__ . '/includes/Main.php';

// Initialize the plugin.
SP_Slider\Includes\Main::init();

// Activation and deactivation hooks.
register_activation_hook(__FILE__, [SP_Slider\Includes\Main::class, 'activate']);
register_deactivation_hook(__FILE__, [SP_Slider\Includes\Main::class, 'deactivate']);

// Uninstall hook (uninstall.php will handle cleanup).
if (!defined('WP_UNINSTALL_PLUGIN')) {
    define('WP_UNINSTALL_PLUGIN', true);
}
