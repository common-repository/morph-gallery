<div class="morph-carousel morph-gallery-<?php echo esc_attr($gallery_id); ?> morph-gallery-<?php echo esc_attr($gallery_slug); ?>"
    data-scroll-speed="<?php echo esc_attr($scroll_speed); ?>"
    data-wrap="<?php echo esc_attr($wrap); ?>">
	<?php foreach($items as $item): ?>
		<div class="morph-carousel-item">
			<a href="<?php echo esc_url($item['variants']['normal']);?>">
				<img src="<?php echo esc_url($item['variants']['normal']);?>" alt="<?php echo esc_attr($item['alt']); ?>" title="<?php echo esc_attr($item['title']); ?>" width="<?php echo esc_attr($item['width']); ?>" height="<?php echo esc_attr($item['height']); ?>">
			</a>
		</div>
	<?php endforeach; ?>
</div>