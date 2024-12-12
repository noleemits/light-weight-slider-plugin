<?php

namespace SP_Slider\Includes;

class Main {

    public static function init() {
        // Include the Admin and Frontend classes
        require_once __DIR__ . '/admin/Admin.php';
        require_once __DIR__ . '/frontend/Frontend.php';

        // Initialize admin and frontend functionality
        Admin\Admin::init();
        Frontend\Frontend::init();
    }

    public static function activate() {
        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
