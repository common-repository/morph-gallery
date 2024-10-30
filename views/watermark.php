<div class="morph-watermark-settings upit">
    <div class="cover">

        <p><a href="https://www.codefleet.net/morph-gallery-productivity-enhancement-suite/">Install the Productivity Enhancement Suite to add:
                <br>Watermarking
                <br>Import
                <br>Export
            </a>
        </p>
    </div>
    <div class="watermark-controls">
        <div class="tabs">
            <div class="tab-head">
                <button type="button" class="active"><?php _e('Image', 'morph'); ?></button>
                <button type="button">Text</button>
            </div>
            <div class="tab-body">
                <div class="active">
                    <div class="watermark-buttons">
                        <div class="button-group">
                            <button type="button" id="morph-watermark-add" class="morph-show-media">Watermark</button>
                            <button type="button" id="morph-watermark-remove">Remove</button>
                        </div>
                    </div>
                    <h2>Size</h2>
                    <div class="field">
                        <div class="label">
                            <label for="field-watermark-width"><?php _e('Width', 'morph'); ?></label>
                        </div>
                        <div class="control">
                            <input id="field-watermark-width" type="number" class="field-short" name="morph_gallery_settings[watermark_width]" value="<?php echo esc_attr($settings['watermark_width']); ?>">
                            <select name="morph_gallery_settings[watermark_width_unit]" id="field-watermark-width-unit">
                                <option <?php selected('%', $settings['watermark_width_unit']); ?> value="%">%</option>
                                <option <?php selected('px', $settings['watermark_width_unit']); ?> value="px">px</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="label">
                            <label for="field-watermark-height"><?php _e('Height', 'morph'); ?></label>
                        </div>
                        <div class="control">
                            <input id="field-watermark-height" type="number" min="0" class="field-short" name="morph_gallery_settings[watermark_height]" value="<?php echo esc_attr($settings['watermark_height']); ?>">
                            <select name="morph_gallery_settings[watermark_height_unit]" id="field-watermark-height-unit">
                                <option <?php selected('%', $settings['watermark_height_unit']); ?> value="%">%</option>
                                <option <?php selected('px', $settings['watermark_height_unit']); ?> value="px">px</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="label">
                            <label for="field-watermark-mode"><?php _e('Mode', 'morph'); ?></label>
                        </div>
                        <div class="control">
                            <select name="morph_gallery_settings[watermark_mode]" id="field-watermark-mode">
                                <option <?php selected('fit', $settings['watermark_mode']); ?> value="fit">Fit</option>
                                <option <?php selected('exact', $settings['watermark_mode']); ?> value="exact">Exact</option>
                                <option <?php selected('exactWidth', $settings['watermark_mode']); ?> value="exactWidth">Exact Width</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    2
                </div>
            </div>
        </div>
        <div class="inner">
            <h2>Position</h2>
            <div class="position-holder">
                <div class="position-pen">
                    <button type="button" data-pos="top-left" title="Top-Left" class="<?php echo ('left'==$settings['watermark_x'] and 'top'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="top-center" title="Top-Center" class="<?php echo ('center'==$settings['watermark_x'] and 'top'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="top-right" title="Top-Right"class="<?php echo ('right'==$settings['watermark_x'] and 'top'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>

                    <button type="button" data-pos="center-left" title="Center-Left" class="<?php echo ('left'==$settings['watermark_x'] and 'center'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="center-center" title="Center" class="<?php echo ('center'==$settings['watermark_x'] and 'center'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="center-right" title="Center-Right" class="<?php echo ('right'==$settings['watermark_x'] and 'center'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>

                    <button type="button" data-pos="bottom-left" title="Bottom-Left" class="<?php echo ('left'==$settings['watermark_x'] and 'bottom'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="bottom-center" title="Bottom-Center" class="<?php echo ('center'==$settings['watermark_x'] and 'bottom'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                    <button type="button" data-pos="bottom-right" title="Bottom-Right" class="<?php echo ('left'==$settings['watermark_x'] and 'bottom'==$settings['watermark_y']) ? 'active' : ''; ?>"></button>
                </div>
            </div>
        </div>
        <input id="field-hidden-watermark-type" type="hidden" value="<?php echo esc_attr($settings['watermark_type']); ?>" name="morph_gallery_settings[watermark_type]">
        <input id="field-hidden-watermark-x" type="hidden" value="<?php echo esc_attr($settings['watermark_x']); ?>" name="morph_gallery_settings[watermark_x]">
        <input id="field-hidden-watermark-y" type="hidden" value="<?php echo esc_attr($settings['watermark_y']); ?>" name="morph_gallery_settings[watermark_y]">
        <input id="field-hidden-watermark" type="hidden" value="<?php echo esc_attr($settings['watermark']); ?>" name="morph_gallery_settings[watermark]">
    </div>
    <div id="watermark-playpen" class="playpen">
        <?php if(isset($watermark_url)) : ?>
            <img src="<?php echo esc_url($watermark_url); ?>" data-width="<?php echo esc_attr($width); ?>" data-height="<?php echo esc_attr($height); ?>" alt="watermark image">
        <?php endif; ?>
    </div>
</div>