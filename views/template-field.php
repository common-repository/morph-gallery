<div class="morph-dialog-field">
	<label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label); ?></label>

	<?php if ( in_array( $field_type, array('text', 'number'))) : ?>
		<?php
		$this->render(
			'fields/input.php',
			array(
				'field_id' => $field_id,
				'field_type' => $field_type,
				'field_key' => $field_key,
				'field_value' => $field_value
			)
		);
		?>
	<?php elseif( 'select' === $field_type ): ?>

		<?php
		$this->render(
				'fields/select.php',
				array(
						'field_id' => $field_id,
						'field_type' => $field_type,
						'field_key' => $field_key,
						'field_value' => $field_value,
						'options' => $field['options']
				)
		);
		?>

	<?php endif; ?>
</div>

