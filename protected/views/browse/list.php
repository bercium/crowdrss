<?php 
$this->pageTitle = $title;
?>

  <div class="pb50 pt50">
    <div class="row">
      <div class="columns">
		<?php if ($title == 'Top projects for today'){ ?>
			<h2><?php echo $title; ?></h2>
			<p>Best newcomers in the last 24h</p>
		<?php }else{ ?>
			<h2><?php echo $title; ?></h2>
			<p>List is generated from new projects from past 7 days</p>
		<?php } ?>
      </div>
    </div>
	 
	<div class='row' style='margin-bottom: 20px;'>
    <?php 
    $i = 0;
    foreach ($projects as $project){
		$i++;
		if ($i > 3) break;
    ?>		
		<div class="columns medium-4 text-center">
			<?php /*<a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" target="_blank" style="color:inherit;" trk="link_<?php echo $listType."_".$project->id; ?>">*/ ?>
            <a href="<?php echo $internal_link; ?>" trk="link_<?php echo $listType."_".$project->id; ?>">
				<div class="panel callout">
					<i class="fa fa-external-link right" style="color:#999;"></i>
					
					<h4 style=""><?php echo $i; ?></h4>
					<img src="<?php echo $project->image; ?>" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="<img src='<?php echo $project->image; ?>'>">

					<h1 style="font-size: 16px; font-weight: bold; margin-top:8px;"><?php echo $project->title; if ($project->creator) echo '<i style="font-weight: normal"> by '.$project->creator."</i>"; ?></h1>
					<div style="padding-top:5px; text-align: left; line-height: 18px;">
					  <strong><?php echo $project->platform->name; ?></strong><?php if (isset($project->origCategory)) echo ": ".$project->origCategory->name; ?>
					  <br />Goal: <?php echo $project->goal; ?><br />
					   <small>Ends on: <?php echo $project->end; ?></small>
					</div>
				</div>
			</a>
		</div>
		
	<?php } ?>
	</div>
	
	
    <?php 
    $i = 0;
    foreach ($projects as $project){
		$i++;
		if ($i < 4) continue;
    ?>
  
  <div class="row">
    <div class="columns">
      <?php /*<a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" target="_blank" style="color:inherit;" trk="link_<?php echo $listType."_".$project->id; ?>">*/ ?>
      <a href="<?php echo $internal_link; ?>" trk="link_<?php echo $listType."_".$project->id; ?>">
        <div class="row panel <?php if ($i < 4) echo "callout"; ?>" style="padding: 1rem; margin-bottom:1.25rem">
          <div class="columns small-1 ">
            <h4 style="margin:0;">
              
              <?php echo $i; ?>

              <?php 
              /*
                $rand = rand(1,3);
                if ($rand == 1){
                  ?><i class="fa fa-caret-up right" style="color:#43ac6a"></i><?php
                }
                if ($rand == 2){
                  ?><i class="fa fa-caret-down right"  style="color:#f04124"></i><?php
                }
                if ($rand == 3){
                  ?><i class="fa fa-minus right" style="color:#007095"></i><?php
                }
               */
              ?>

            </h4>
          </div>
          <div class="columns small-1 show-for-medium-up text-center">
            <img src="<?php echo $project->image; ?>" style="max-height:32px;" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="<img src='<?php echo $project->image; ?>'>">
          </div>
          <div class="columns small-11 medium-7">
            
            <i class="fa fa-external-link right show-for-small"></i>
            <?php
              echo "<strong>".$project->title."</strong>";
              if ($project->creator) echo "<i> by ".$project->creator."</i>";
            ?>
            <br />
            <div style="padding-top:5px;">
              <small>
              <?php
                if ($allPlatforms) echo "<strong>".$project->platform->name."</strong>";
                if (isset($project->origCategory)) echo ": ".$project->origCategory->name;
              ?>
              </small>
            </div>
          </div>
          <div class="columns show-for-medium-up medium-3">
            <i class="fa fa-external-link right"></i>
            <?php 
            //
            echo "Goal: ".$project->goal; ?>
            <br />
            <div style="padding-top:5px;">
              <small>
              <?php
                echo "Ends on: ".$project->end;
              ?>
              </small>
            </div>

          </div>

        </div>
      </a>
    </div>
  </div>
   <?php } ?>
  
</div>