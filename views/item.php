<div class="morph-item" tabindex="0">
    <div class="morph-item-thumbnail">
        <img src="<?php echo esc_url( $src ); ?>">
    </div>
    <input type="hidden" name="preview" value="<?php echo esc_url($preview); ?>">
    <input type="hidden" class="morph-item-json" name="morph_gallery_items[<?php echo esc_attr( $index ); ?>][json]" value="<?php echo $json; ?>">
</div>
