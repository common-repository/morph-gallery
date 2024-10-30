<div class="morph-pagination">
	<?php
	if($paginator->get_ending_page() > 1 ):
		for($i=$paginator->get_starting_page(); $i<= $paginator->get_ending_page(); $i++):
			?><a href="<?php echo esc_url($base_url); ?>&current-page=<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a> <?php
		endfor;
	endif; ?>
</div>

