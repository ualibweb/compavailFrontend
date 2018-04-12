<?php 
/**
 * Map template
 *   - This template controls the HTML output for viewing the maps
 *
 * Variables:
 *   @title - generated title of the location
 *   @back_loc - location path string for "Back" link
 *   @comps - an array or rows returned from the mysql query
 *     -- Array(
 *          Array(
 *                  [computer_name] => STRING
 *                  [status] => INT             //--> 0 for available, 1 for in use
 *                  [left_pos] => INT           \\--> X or Left position in pixels
 *                  [top_pos] => INT            //--> Y or Top position in pixels
 *                  [computer_type] => STRING   \\--> Operating System
 *                  [classes] => STRING         //--> CSS classes for computer DIV
 *                  [styles] => STRING          \\--> CSS styles for computer DIV
 *              )
 *        )
 *   @map - array of map info
 *     -- Array(
 *            [image] => STRING    //--> Path to the image
 *            [w] => INT           \\--> Width of image
 *            [h] => INT           //--> Height of image
 *            [style] => STRING    \\--> CSS styles of map DIV
 *        )
 */
?>
<div class="comps-wrapper">
	<div class="page-title">Available Computers</div>
	
	<?php if (isset($title) && $title != ""): ?>
	  <div class="map-title-wrapper clear-fix linked" id="back" rel="<?php print $back_loc; ?>">
		  <div class="back">
  			<img src="/inc/compavail/images/home.png" width="48" height="48">
	  	</div>
		  <div class="map-title"><?php print $title; ?></div>
	  </div>
	<?php endif; ?>
	
	<div class="comps-map-key clear-fix">
			<div class="comps-map-key-item">
				<div class="PC open">
					<div class="comps-map-comp-border"></div>
				</div>
				<div class="comps-map-key-label">Windows - available</div>
			</div>
			<div class="comps-map-key-item">
				<div class="PC closed">
					<div class="comps-map-comp-border"></div>
				</div>
				<div class="comps-map-key-label">Windows - in use</div>
			</div>
			<div class="comps-map-key-item">
				<div class="PC2 closed">
					<div class="comps-map-comp-border"></div>
				</div>
				<div class="comps-map-key-label">Dual Monitor</div>
			</div>			
			<div class="comps-map-key-item">
				<div class="MAC open">
					<div class="comps-map-comp-border"></div>
				</div>
				<div class="comps-map-key-label">Mac - available</div>
			</div>
			<div class="comps-map-key-item">
				<div class="MAC closed">
					<div class="comps-map-comp-border"></div>
				</div>
				<div class="comps-map-key-label">Mac - in use</div>
			</div>		
	</div>
		
	<div class="comps-map" style="<?php print $map['style']; ?> clear: both;">
	  <?php foreach ($comps as $comp): ?>
		
					<?php if ((strpos($comp['classes'], 'open')) !== false): ?>
		
						<div class="comps-map-comp <?php print $comp['classes']; ?>" id="<?php print $comp['computer_name']; ?>" style="<?php print $comp['styles']; ?>" aria-label="<?php print $comp['computer_name']; ?> available computer" tabindex="0">
					
					<?php else : ?>
										
						<div class="comps-map-comp <?php print $comp['classes']; ?>" id="<?php print $comp['computer_name']; ?>" style="<?php print $comp['styles']; ?>" aria-label="<?php print $comp['computer_name']; ?> unavailable computer">
						
					<?php endif ?>
		
			<div class="comps-map-comp-border"></div>
		</div>
		<?php endforeach; ?>
	</div>
	
</div>