<?php 
$this->pageTitle = $project->title;
?><div class="pt30 pb30">
      <div class="row">
        <div class="columns medium-12">

              <div class="row">
                <div class="columns medium-4">
                <?php if (($project->rating) and ($project->deleted = 0)){ ?>
                <span class="fa-stack fa-5x fa-lg" style="color:#0088bb; right:-20px; top:-50px; margin:0; padding:0; position: absolute;" >
                  <i class="fa fa-certificate fa-stack-1x" ></i>
                  <i style="font-size: 60%; " class="fa-stack-1x fa-inverse">
                    <span style="font-style: normal;" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Our rating for this project based on content quality, social reach and campaign progress">
                      <?php echo round($project->rating); ?>
                    </span>
                  </i>
                </span>
                <?php } ?>
                  <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                    <img src="<?php echo $project->image; ?>">
                  </a>
                </div>
                <div class="columns medium-8">
                  <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank"><h4><?php echo $project->title; ?></h4></a>
                  <small><?php echo "in ".$project->origCategory->name." on ".date("D, d M Y H:i:s e",strtotime($project->time_added)); ?></small>
                  <p style="margin-top: 10px;" class="mb0">
                  <?php echo $project->description; ?>
                  <br />
                  <?php 
                    echo "<br /><strong>".$project->platform->name."</strong> - ".$project->origCategory->name." ";//." <br />";
                    if (!empty($project->creator)) echo "<br />Creator of project: <i>".$project->creator."</i> ";
                    //if (!empty($project->location)) $desc.= " \nCreator of project: ".$project->location;
                    if (!empty($project->goal)) echo "<br />Project goal: <strong>".$project->goal."</strong>";
                    if (!empty($project->type_of_funding)){
                      if ($project->type_of_funding == 0) echo " Fixed funding";
                      else echo " Flexible funding";
                    }
                    
                     //"<br />Rating: ".round($project->rating);
                  ?>
                  </p>  
                </div>

              </div>
              <div class="row">
                <div class="columns mt30 text-center">
                  
                  <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>"><input type="button" class="button success medium radius" value="Go to project site"></a>
                  
                </div>
              </div>
           
        </div>
      </div>
    </div>