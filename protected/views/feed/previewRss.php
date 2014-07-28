		<div class="pt30 outro">
      <div class="row">
        <div class="columns medium-12">
          <h2 class="white">Preview of your RSS</h2>
          <form method="post" id="preview_form" action="<?php echo Yii::app()->createUrl('feed/downloadRss'); ?>" target="_blank">
          <p class="white-light">
            You can also download preview RSS file by clicking <button trk="button_form_downloadPreview" type="submit" class="info tiny radius"><strong>download RSS</strong></button>
          </p>
            <input type="hidden" id="preview_platform" name="platform" value="<?php echo $plat; ?>">
            <input type="hidden" id="preview_category" name="category" value="<?php echo $cat; ?>">
          </form>
          
        </div>
      </div>
    </div>


		<div class="pt30 pb30">
      <div class="row">
        <div class="columns medium-12">
        <?php

        if ($projects){
          $first = true;
          foreach ($projects as $project){
            if (!$first){
              echo "<hr>";
            }
            $first = false;
            ?>

              <div class="row">
                <div class="columns small-4">
                  <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                    <img src="<?php echo $project->image; ?>">
                  </a>
                </div>
                <div class="columns small-8">
                  <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank"><h4><?php echo $project->title; ?></h4></a>
                  <small><?php echo date("D, d M Y H:i:s e",strtotime($project->time_added))." - ". $project->origCategory->name; ?></small>
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
                  ?>
                  </p>  
                </div>

              </div>
              
            <?php
          }
        }else{
          ?>

          <h3>No results</h3>

          <?php
        }

        ?>          
        </div>
      </div>
    </div>

