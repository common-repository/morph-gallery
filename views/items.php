<input type="hidden" name="<?php echo esc_attr($nonce_name); ?>" value="<?php echo esc_attr($nonce); ?>" />
<div class="morph-area-main">
	<div id="morph-area-items" class="morph-area-items">
		<?php foreach($items as $index=>$item): ?>
			<?php $this->render( 'item.php', array(
					'src'   => $item['src'],
					'index' => $index,
					'preview' => $item['preview'],
                    'json' => htmlentities(json_encode($item)) // Encode it to work inside hidden fields
			) ); ?>
		<?php endforeach; ?>
	</div>
	<div class="morph-area-editor">
		<div id="morph-preview" class="morph-preview"></div>
		<div class="fields">
            <div class="field-group">
                <div class="field-group-label"><?php _e('Item Settings', 'morph'); ?></div>
                <div class="field">
                    <div class="label">
                        <label for="morph-field-caption"><?php _e('Caption', 'morph'); ?></label>
                    </div>
                    <div class="control">
                        <textarea class="morph-editor-field" data-source="morph-item-caption" type="text" id="morph-field-caption" name="caption"></textarea>
                    </div>
                </div>
                <div class="field">
                    <div class="label">
                        <label for="morph-field-name"><?php _e('Name', 'morph'); ?></label>
                    </div>
                    <div class="control">
                        <input class="morph-editor-field" data-source="morph-item-name" type="text" id="morph-field-name" name="name" value="">
                    </div>
                </div>
            </div>
            <div class="field-group">
                <div class="field-group-label"><?php _e('Image Tag Settings', 'morph'); ?></div>
                <div class="field">
                    <div class="label">
                        <label for="morph-field-alt"><?php _e('Alt', 'morph'); ?></label>
                    </div>
                    <div class="control">
                        <input class="morph-editor-field" data-source="morph-item-alt" type="text" id="morph-field-alt" name="alt" value="">
                    </div>
                </div>
                <div class="field">
                    <div class="label">
                        <label for="morph-field-title"><?php _e('Title', 'morph'); ?></label>
                    </div>
                    <div class="control">
                        <input class="morph-editor-field" data-source="morph-item-title" type="text" id="morph-field-title" name="title" value="">
                    </div>
                </div>
            </div>
            <?php if($editor_fields_html): ?>
            <div class="field-group">
                <div class="field-group-label"><?php _e('Template Settings', 'morph'); ?></div>
                <?php echo $editor_fields_html; ?>
            </div>
            <?php endif; ?>
            <?php echo $custom_fields_group_html; ?>
		</div>
	</div>
</div>
<div class="morph-area-actions">
	<input type="button" value="<?php esc_attr_e('Add Items', 'morph'); ?>" class="morph-show-media button-secondary" id="morph-multiple-items" />
	<input type="button" value="<?php esc_attr_e('Remove Item', 'morph'); ?>" class="morph-remove-item button-secondary" />
	<input type="button" value="<?php esc_attr_e('Update Item', 'morph'); ?>" class="morph-show-media button-secondary" id="morph-update-item" />
</div>
