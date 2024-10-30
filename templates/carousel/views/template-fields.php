<div class="morph-dialog-field">
    <div class="label">
        <label for="image_width"><?php _e('Image Width', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_width" type="text" name="morph_template_settings_carousel[image_normal_width]" value="<?php echo esc_attr($template_settings['image_normal_width']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="image_height"><?php _e('Image Height', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="image_height" type="number" name="morph_template_settings_carousel[image_normal_height]" value="<?php echo esc_attr($template_settings['image_normal_height']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="scroll_speed"><?php _e('Scroll Speed', 'morph'); ?></label>
    </div>
    <div class="control">
        <input id="scroll_speed" type="number" name="morph_template_settings_carousel[scroll_speed]" value="<?php echo esc_attr($template_settings['scroll_speed']); ?>">
    </div>
</div>
<div class="morph-dialog-field">
    <div class="label">
        <label for="wrap"><?php _e('Wrap', 'morph'); ?></label>
    </div>
    <div class="control">
        <select id="wrap" name="morph_template_settings_carousel[wrap]">
            <option <?php selected('true', $template_settings['wrap']); ?> value="true">Yes</option>
            <option <?php selected('false', $template_settings['wrap']); ?> value="false">No</option>
        </select>
    </div>
</div>
