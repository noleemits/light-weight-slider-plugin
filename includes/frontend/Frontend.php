<?php

namespace SP_Slider\Includes\Frontend;

class Frontend {

    /**
     * Initialize frontend functionality.
     */
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_shortcode('sp_slider', [__CLASS__, 'shortcode']);
    }


    /**
     * Enqueue frontend assets conditionally.
     */
    public static function enqueue_assets() {
        if (self::should_load_sliders()) {
            wp_enqueue_style(
                'spl-slider-style',
                plugin_dir_url(__FILE__) . '../../assets/css/frontend.css',
                [],
                '1.0'
            );

            wp_enqueue_script(
                'spl-slider-script',
                plugin_dir_url(__FILE__) . '../../assets/js/frontend.js',
                [],
                '1.0',
                true
            );
        }
    }

    /**
     * Check if sliders should be loaded on the current page.
     */
    public static function should_load_sliders() {
        global $post;
        if (is_singular() && get_post_meta($post->ID, 'sp_sliders', true)) {
            return true;
        }

        return false;
    }

    /**
     * Render all sliders on the current page.
     */
    public static function render_sliders() {
        global $post;

        // Retrieve all sliders for the current page.
        $sliders = get_post_meta($post->ID, 'sp_sliders', true);

        if (!empty($sliders) && is_array($sliders)) {
            foreach ($sliders as $slider_id => $slider_config) {
                self::render_single_slider($slider_id, $slider_config);
            }
        }
    }

    /**
     * Render a single slider with a given ID and configuration.
     *
     * @param string $slider_id The unique identifier for the slider.
     * @param array $slider_config Configuration for this slider.
     */
    private static function render_single_slider($slider_id, $slider_config) {
        $slider_type = esc_attr($slider_config['type'] ?? 'text_icon'); // Default to text_icon

        echo '<div id="spl-slider-' . esc_attr($slider_id) . '" class="spl-slider spl-slider-' . $slider_type . '">';

        // Loop through all slides
        if (!empty($slider_config['slides']) && is_array($slider_config['slides'])) {
            foreach ($slider_config['slides'] as $slide) {
                echo '<div class="spl-slide">';

                // Display image if it exists
                if (!empty($slide['image'])) {
                    echo '<img src="' . esc_url($slide['image']) . '" alt="" class="spl-slide-image" />';
                }

                // Display primary text block
                if (!empty($slide['content'])) {
                    $primary_type = esc_html($slide['type'] ?? 'p'); // Default to <p>
                    echo '<' . $primary_type . ' class="spl-slide-primary">';
                    echo esc_html($slide['content']);
                    echo '</' . $primary_type . '>';
                }

                // Display secondary text block if it exists
                if (!empty($slide['secondary_content'])) {
                    $secondary_type = esc_html($slide['secondary_type'] ?? 'p'); // Default to <p>
                    echo '<' . $secondary_type . ' class="spl-slide-secondary">';
                    echo esc_html($slide['secondary_content']);
                    echo '</' . $secondary_type . '>';
                }

                echo '</div>'; // End spl-slide
            }
        } else {
            echo '<p>' . __('No slides available.', 'spl-slider') . '</p>';
        }

        echo '</div>'; // End spl-slider
    }



    /**
     * Shortcode handler for rendering a specific slider.
     *
     * @param array $atts Shortcode attributes.
     * @return string Rendered slider HTML or an error message.
     */
    public static function shortcode($atts) {

        $atts = shortcode_atts(['id' => ''], $atts, 'sp_slider');

        if (empty($atts['id'])) {
            return '<p>' . __('No slider ID provided.', 'spl-slider') . '</p>';
        }

        global $post;

        $sliders = get_post_meta($post->ID, 'sp_sliders', true);
        $slider_config = $sliders[$atts['id']] ?? null;

        if (!$slider_config) {
            return '<p>' . __('Slider not found.', 'spl-slider') . '</p>';
        }

        ob_start();
        self::render_single_slider($atts['id'], $slider_config);
        return ob_get_clean();
    }
}
