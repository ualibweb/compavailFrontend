<?php 
/**
 * Navigation template
 *   - This template controls the HTML output for the navigation interface
 *   
 * Variables:
 *   @title - generated title of the location
 *   @back_loc - location path string for "Back" link
 *   @comps - an array or rows returned from the mysql query
 *       -- Array(
 *             Array(
 *                  [id] => INT                //--> Location's ID from location table
 *                  [parent_id] => INT         \\--> ID of location's parent
 *                  [label] => STRING          //--> Location label
 *                  [description] => STRING    \\--> Location description
 *                  [pc_a] => INT              //--> Total number of Windows computers available
 *                  [pc_t] => INT              \\--> Total number of Windows computers
 *                  [mac_a] => INT             //--> Total number of Mac computers available
 *                  [mac_t] => INT             \\--> Total number of Mac computers
 *                  [num_children] => INT      //--> Number of children the location has - used to determine if the location should be linked
 *                  [map] => BOOLEAN_INT       \\--> If the location has a map - use to call map queries instead of the default nav queries
 *                  [linked] => BOOLEAN_INT    //--> If the location should link - relates to [num_chidlren]
 *                  [q] => STRING              \\--> Type of query to call when clicked - relates to [map]
 *            )
 *       )
 */
?>

<div class="comps-wrapper">
	<h1 class="page-title">Available Computers</h1>
	
	<?php if (isset($title) && $title != ""): ?>
	  <div class="nav-title-wrapper clear-fix linked" id="back" rel="<?php print $back_loc; ?>">
		  <div class="back">
  			<img src="/inc/compavail/images/home.png" width="128" height="128">
	  	</div>
		  <div class="nav-title"><?php print $title; ?></div>
	  </div>
	<?php endif; ?>
	
	<?php foreach ($comps as $btn): ?>
  	<div role="button" class="comps-nav-btn clear-fix<?php  print $btn['linked'] ? ' linked" id="'.$btn['id'].'" rel="'.$btn['q'].'" scope="'.$loc.'" name="'.$btn['label'].'"' : '"'; ?> tabindex="0">

		<h2 class="label <?php if (strlen($btn['label']) > 9) print 'sm'; ?>">
			<?php print $btn['label']; ?>
			<?php if(isset($btn['description'])): ?>
			  <div class="description">
				  <?php print $btn['description']; ?>
			  </div>
			<?php endif; ?>
		</h2>
		
    <?php if ($btn['linked']): ?>
		  <div class="next">&gt;</div>
		<?php endif; ?>

		<div class="status<?php if (!$btn['linked']): ?> right-spacer<?php endif; ?>">
			<h3>Availability</h3>
		  <?php if ($btn['pc_t'] > 0): ?>
			  <div class="stat">
				  <h4 class="stat-label">Windows</h4> 
				  <span class="available pc_a"> <?php print $btn['pc_a'];?></span>
				  <span class="total">out of <?php print $btn['pc_t']; ?></span>
			  </div>
			<?php endif; ?>
			<?php if ($btn['mac_t'] > 0): ?>
			  <div class="stat">
				  <h4 class="stat-label">Mac</h4> 
				  <span class="available mac_a"> <?php print $btn['mac_a']; ?></span>
				  <span class="total"> out of <?php print $btn['mac_t']; ?></span>
			  </div>
			<?php endif; ?>
		</div>
	</div>
	<?php endforeach; ?>
</div>


