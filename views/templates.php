<div class="morph-template-current">
	<div class="morph-template-current-thumbnail">
		<img src="<?php echo esc_url($current_template->get_dir_url()); ?>/images/icon.png" alt="">
	</div>
	<div class="morph-template-current-name">
		<?php echo esc_html( ucwords( $current_template->get_name() ) ); ?>
	</div>
	<?php echo $current_template->get_template_fields_html($template_settings); ?>
</div>
<ul class="morph-template-list">
<?php foreach($templates->get_templates() as $template): ?>
	<li>
		<label class="morph-template <?php echo ( $settings['template'] === $template->get_name() ) ? 'morph-template-chosen' : ''; ?>" for="template-<?php echo esc_attr($template->get_name()); ?>">
			<span class="morph-template-thumbnail">
				<img src="<?php echo esc_url($template->get_dir_url()); ?>/images/icon.png" alt="">
			</span>
			<input <?php checked( $settings['template'], $template->get_name()); ?> id="template-<?php echo esc_attr($template->get_name()); ?>" type="radio" name="morph_gallery_settings[template]" value="<?php echo esc_attr($template->get_name()); ?>" />
			<span class="morph-template-name">
				<?php echo esc_html(ucwords($template->get_name())); ?>
			</span>
		</label>
	</li>
<?php endforeach; ?>
</ul>
<div class="clear"></div>