<ul id="morph-gallery-<?php echo esc_attr($gallery_slug); ?>" class="morph-gallery morph-gallery-masonry morph-gallery-<?php echo esc_attr($gallery_id); ?> morph-gallery-<?php echo esc_attr($gallery_slug); ?>">
	<li class="grid-sizer"></li>
	<?php foreach($items as $item): ?>
		<li class="grid <?php echo esc_attr($item['item_css_class']); ?>">
			<a href="<?php echo esc_url($item['variants']['full']); ?>" data-caption="<?php echo esc_attr($item['caption']); ?>">
				<img src="<?php echo esc_url($item['variants']['normal']);?>" alt="<?php echo esc_attr($item['alt']); ?>">
			</a>
		</li>
	<?php endforeach; ?>
</ul>