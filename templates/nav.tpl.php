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
    <div class="page-title h1">Available Computers</div>
	
	<?php if (isset($title) && $title != ""): ?>
	  <div class="nav-title-wrapper clear-fix linked" id="back" rel="<?php print $back_loc; ?>">
		  <div class="back">
  			<img src="/inc/compavail/images/home.png" width="128" height="128">
	  	</div>
		  <div class="nav-title"><?php print $title; ?></div>
	  </div>
	<?php endif; ?>
	
	<div class="row">
        <?php foreach ($comps as $btn): ?>
        <div class="comps-nav-btn col-md-12<?php  print $btn['linked'] ? ' linked" id="'.$btn['id'].'" rel="'.$btn['q'].'" scope="'.$loc.'" name="'.$btn['label'].'"' : '"'; ?>>

		<div class="label <?php if (strlen($btn['label']) > 9) print 'sm'; ?>">
        <?php print $btn['label']; ?>
        <?php if(isset($btn['description'])): ?>
            <div class="description">
                <?php print $btn['description']; ?>
            </div>
        <?php endif; ?>
        </div>

    <?php if ($btn['linked']): ?>
        <div class="next">&gt;</div>
    <?php endif; ?>

    <div class="status<?php if (!$btn['linked']): ?> right-spacer<?php endif; ?>">
        <?php if ($btn['pc_t'] > 0): ?>
            <div class="stat">
                <span class="stat-label">Windows</span>
                <span class="available pc_a"> <?php print $btn['pc_a']; ?></span>
                <span class="total"> / <?php print $btn['pc_t']; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($btn['mac_t'] > 0): ?>
            <div class="stat">
                <span class="stat-label">Mac</span>
                <span class="available mac_a"> <?php print $btn['mac_a']; ?></span>
                <span class="total"> / <?php print $btn['mac_t']; ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
    </div>
</div>


