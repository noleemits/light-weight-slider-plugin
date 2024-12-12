<?php

namespace SP_Slider\Includes\Admin;

class SliderMetaBox {

    /**
     * Initialize the meta box functionality.
     */
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
        add_action('save_post', [__CLASS__, 'save_meta_box']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    /**
     * Add the meta box to the page/post editor.
     */
    public static function add_meta_box() {
        add_meta_box(
            'sp_slider_meta',
            __('SPL Sliders', 'spl-slider'),
            [__CLASS__, 'render_meta_box'],
            ['post', 'page'],
            'normal',
            'default'
        );
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public static function enqueue_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_script(
                'sp-slider-meta-js',
                plugin_dir_url(__FILE__) . '../../assets/js/slider-meta-box.js',
                ['jquery', 'wp-mediaelement'],
                '1.0',
                true
            );
            wp_enqueue_style(
                'sp-slider-meta-css',
                plugin_dir_url(__FILE__) . '../../assets/css/slider-meta-box.css',
                [],
                '1.0'
            );
        }
    }

    /**
     * Render the meta box content.
     */
    public static function render_meta_box($post) {
        // Retrieve existing sliders
        $sliders = get_post_meta($post->ID, 'sp_sliders', true);

        // Add nonce for security
        wp_nonce_field('sp_slider_meta_nonce', 'sp_slider_meta_nonce_field');

        echo '<div id="sp-slider-meta-box">';
        echo '<button type="button" id="add-slider" class="button button-primary">' . __('Add Slider', 'spl-slider') . '</button>';

        // Display existing sliders or a placeholder if none exist
        echo '<div id="sliders-container">';
        if (!empty($sliders) && is_array($sliders)) {
            foreach ($sliders as $slider_id => $slider_config) {
                self::render_slider_config($slider_id, $slider_config);
            }
        }
        echo '</div>'; // End sliders-container

        echo '</div>'; // End sp-slider-meta-box
    }

    /**
     * Render a single slider configuration.
     */
    private static function render_slider_config($slider_id, $slider_config) {
        echo '<div class="sp-slider-config">';
        echo '<label>Slider ID:</label>';
        echo '<input type="text" name="sp_sliders[' . esc_attr($slider_id) . '][id]" value="' . esc_attr($slider_id) . '" readonly />';
        echo '<label>Slider Type:</label>';
        echo '<select name="sp_sliders[' . esc_attr($slider_id) . '][type]">';
        echo '<option value="text_icon"' . selected($slider_config['type'], 'text_icon', false) . '>Text + Icon</option>';
        echo '<option value="stacked"' . selected($slider_config['type'], 'stacked', false) . '>Stacked</option>';
        echo '</select>';
        echo '<div class="slides-repeater">';
        echo '<h4>' . __('Slides', 'spl-slider') . '</h4>';
        echo '<button type="button" class="button add-slide">' . __('Add Slide', 'spl-slider') . '</button>';
        echo '<div class="slides-container">';
        if (!empty($slider_config['slides']) && is_array($slider_config['slides'])) {
            foreach ($slider_config['slides'] as $index => $slide) {
                self::render_slide($slider_id, $index, $slide);
            }
        }
        echo '</div>'; // End slides-container
        echo '</div>'; // End slides-repeater
        echo '<button type="button" class="button button-secondary remove-slider">' . __('Remove Slider', 'spl-slider') . '</button>';
        echo '</div>'; // End sp-slider-config
    }

    /**
     * Render a single slide inside a slider.
     */
    private static function render_slide($slider_id, $index, $slide) {
        echo '<div class="slide-item">';
        echo '<label>Slide Image:</label>';
        echo '<input type="hidden" name="sp_sliders[' . esc_attr($slider_id) . '][slides][' . $index . '][image]" value="' . esc_url($slide['image'] ?? '') . '" />';
        echo '<button type="button" class="button upload-image">Upload Image</button>';
        if (!empty($slide['image'])) {
            echo '<img src="' . esc_url($slide['image']) . '" class="preview-image" style="max-width: 100px; display: block;" />';
        } else {
            echo '<img src="" class="preview-image" style="max-width: 100px; display: none;" />';
        }

        echo '<label>Primary Text Block:</label>';
        echo '<input type="text" name="sp_sliders[' . esc_attr($slider_id) . '][slides][' . $index . '][primary_text][content]" value="' . esc_attr($slide['content'] ?? '') . '" />';
        echo '<label>Text Type:</label>';
        echo '<select name="sp_sliders[' . esc_attr($slider_id) . '][slides][' . $index . '][primary_text][type]">';
        foreach (['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $type) {
            echo '<option value="' . $type . '"' . selected($slide['type'] ?? 'p', $type, false) . '>' . strtoupper($type) . '</option>';
        }
        echo '</select>';

        echo '<label>Secondary Text Block (Optional):</label>';
        echo '<input type="text" name="sp_sliders[' . esc_attr($slider_id) . '][slides][' . $index . '][secondary_text][content]" value="' . esc_attr($slide['secondary_content'] ?? '') . '" />';
        echo '<label>Text Type:</label>';
        echo '<select name="sp_sliders[' . esc_attr($slider_id) . '][slides][' . $index . '][secondary_text][type]">';
        foreach (['', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $type) {
            echo '<option value="' . $type . '"' . selected($slide['secondary_type'] ?? '', $type, false) . '>' . ($type === '' ? 'None' : strtoupper($type)) . '</option>';
        }
        echo '</select>';

        echo '<button type="button" class="button button-secondary remove-slide">Remove Slide</button>';
        echo '</div>';
    }



    /**
     * Save the meta box data.
     */
    public static function save_meta_box($post_id) {
        // Check nonce
        if (!isset($_POST['sp_slider_meta_nonce_field']) || !wp_verify_nonce($_POST['sp_slider_meta_nonce_field'], 'sp_slider_meta_nonce')) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save sliders data
        if (isset($_POST['sp_sliders']) && is_array($_POST['sp_sliders'])) {
            $sliders = array_map(function ($slider) {
                return [
                    'type' => sanitize_text_field($slider['type'] ?? 'text_icon'),
                    'slides' => array_map(function ($slide) {
                        return [
                            'content' => sanitize_text_field($slide['primary_text']['content'] ?? ''), // Primary text
                            'type' => sanitize_text_field($slide['primary_text']['type'] ?? 'p'),    // Primary text type
                            'secondary_content' => sanitize_text_field($slide['secondary_text']['content'] ?? ''), // Secondary text
                            'secondary_type' => sanitize_text_field($slide['secondary_text']['type'] ?? 'p'),       // Secondary text type
                            'image' => esc_url_raw($slide['image'] ?? ''), // Image URL
                        ];
                    }, $slider['slides'] ?? [])
                ];
            }, $_POST['sp_sliders']);

            update_post_meta($post_id, 'sp_sliders', $sliders);
        } else {
            delete_post_meta($post_id, 'sp_sliders');
        }
    }
}
