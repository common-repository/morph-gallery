<div class="morph-publish">
	<div class="morph-publish-buttons">
		<input type="hidden" name="post_status" value="publish">
		<a class="button button-danger button-large" href="<?php echo esc_url(get_delete_post_link()); ?>" id="morph-trash"><?php esc_html_e('Move to Trash', 'morph'); ?></a>
        <input name="save" data-morph-gallery-id="<?php echo esc_attr($gallery_id); ?>" type="submit" class="button button-primary button-large" id="morph-saver" value="<?php esc_attr_e('Save Gallery', 'morph'); ?>">
	</div>
	<div class="morph-resize-progress">
		<div class="morph-resize-label"><?php esc_html_e('Processing...', 'morph'); ?></div>
		<div class="morph-resize-progress-bar">
			<div></div>
		</div>
	</div>
</div>