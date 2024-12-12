<?php
if (!isset($slider_id) || !isset($slider_config)) {
    return;
}

// Slider options
$slider_type = $slider_config['type'] ?? 'default';
$slider_items = $slider_config['items'] ?? [];
?>

<div id="spl-slider-<?php echo esc_attr($slider_id); ?>" class="spl-slider spl-slider-<?php echo esc_attr($slider_type); ?>">
    <?php foreach ($slider_items as $item): ?>
        <div class="spl-slider-item">
            <!-- Render slider content -->
            <?php echo esc_html($item['content'] ?? ''); ?>
        </div>
    <?php endforeach; ?>
</div>