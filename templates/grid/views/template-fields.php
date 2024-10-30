<div class="morph-dialog-field">
    <div class="label">
        <label for="image_normal_width"><?php _e('Image Width', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_normal_width" type="text" name="morph_template_settings_grid[image_normal_width]" value="<?php echo esc_attr($template_settings['image_normal_width']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="image_normal_height"><?php _e('Image Height', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_normal_height" type="number" name="morph_template_settings_grid[image_normal_height]" value="<?php echo esc_attr($template_settings['image_normal_height']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="image_normal_resize_mode"><?php _e('Image Resize Mode', 'morph'); ?></label>
    </div>
    <div class="control">
        <select id="image_normal_resize_mode" name="morph_template_settings_grid[image_normal_resize_mode]">
            <option <?php selected('exact', $template_settings['image_normal_resize_mode']); ?> value="exact">Exact</option>
            <option <?php selected('exactHeight', $template_settings['image_normal_resize_mode']); ?> value="exactHeight">Exact Height</option>
            <option <?php selected('exactWidth', $template_settings['image_normal_resize_mode']); ?> value="exactWidth" selected="selected">Exact Width</option>
            <option <?php selected('fill', $template_settings['image_normal_resize_mode']); ?> value="fill">Fill</option>
            <option <?php selected('fit', $template_settings['image_normal_resize_mode']); ?> value="fit">Fit</option>
        </select>
    </div>
</div>

<div class="morph-dialog-field">
    <div class="label">
        <label for="image_full_width"><?php _e('Full Image Width', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_full_width" type="text" name="morph_template_settings_grid[image_full_width]" value="<?php echo esc_attr($template_settings['image_full_width']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="image_full_height"><?php _e('Full Image Height', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_full_height" type="number" name="morph_template_settings_grid[image_full_height]" value="<?php echo esc_attr($template_settings['image_full_height']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="image_full_resize_mode"><?php _e('Full Image Resize Mode', 'morph'); ?></label>
    </div>
    <div class="control">
        <select id="image_full_resize_mode" name="morph_template_settings_grid[image_full_resize_mode]">
            <option <?php selected('exact', $template_settings['image_full_resize_mode']); ?> value="exact">Exact</option>
            <option <?php selected('exactHeight', $template_settings['image_full_resize_mode']); ?> value="exactHeight">Exact Height</option>
            <option <?php selected('exactWidth', $template_settings['image_full_resize_mode']); ?> value="exactWidth" selected="selected">Exact Width</option>
            <option <?php selected('fill', $template_settings['image_full_resize_mode']); ?> value="fill">Fill</option>
            <option <?php selected('fit', $template_settings['image_full_resize_mode']); ?> value="fit">Fit</option>
        </select>
    </div>
</div>

