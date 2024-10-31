<div class="wrap">
<h2><?php _e('Posts Carousel Options'); ?></h2>

	<form method="post" action="options.php"> 
		<?php 
			settings_fields( 'postcarouopts' ); 
			$options = get_option('postcarouopts');
			
			/* print_r( $options ); */
		?>
		
		<p>
			<label for="speed"><?php _e('Speed:'); ?></label>  
			<input type="text" name="postcarouopts[speed]" value="<?php echo $options['speed']; ?>" />
			<i>default: 1000</i>
		</p>
		
		
		<p>
			<label for="scroll_milli"><?php _e('Auto scroll (milliseconds):'); ?></label>  
			<input type="text" name="postcarouopts[scroll_milli]" value="<?php echo $options['scroll_milli']; ?>" />
			<i>default: 800</i>
		</p>
		
		<p>
			<label for="hover_pause"><?php _e('Hover on pause?'); ?></label>  
			<input type="checkbox" name="postcarouopts[hover_pause]" value="1" <?php if( $options['hover_pause'] == 1) echo 'checked="checked"'; ?> />
		</p>
		
		<p>
			<label for="visible"><?php _e('Visible slides per transition:'); ?></label>
			<select name="postcarouopts[visible]" style="width: 120px;">
				<?php
					$max = 1;
					while( $max <= 10 ){
				?>
					<option value="<?php echo $max; ?>" <?php if( $options['visible'] == $max ) echo 'selected="selected"'; ?> ><?php echo $max; ?></option>
				<?php
					$max++;
					}
				?>
			</select>
		</p>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" name="save" />
		</p>
		
	</form>

</div>