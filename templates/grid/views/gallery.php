<ul class="morph-gallery morph-gallery-grid morph-gallery-<?php echo esc_attr($gallery_id); ?> morph-gallery-<?php echo esc_attr($gallery_slug); ?>">
	<?php foreach($items as $item): ?>
		<li>
			<a href="<?php echo esc_url($item['variants']['full']);?>">
				<img src="<?php echo esc_url($item['variants']['normal']);?>" alt="<?php echo esc_attr($item['alt']); ?>" title="<?php echo esc_attr($item['title']); ?>">
			</a>
		</li>
	<?php endforeach; ?>
</ul>
