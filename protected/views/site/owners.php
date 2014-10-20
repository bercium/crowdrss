<?php
$this->pageTitle = 'How does your project stack to others';


?>

<div class="pb30">
  <div class="row">
    <div class="columns medium-12">
      <h3>Check your project rating</h3>
      <form method="post" id="checkproject_form" action="<?php echo Yii::app()->createUrl('site/owners'); ?>" data-abide>
        <p>
          How your project stacks against others listed on our site.
        </p>
        
        <div class="row">
          <div class="large-8 columns">
            <div class="row collapse">
              <div class="small-10 columns">
                <input type="text" name="link" value="<?php echo $link; ?>" placeholder="Name or link of your campaign" required>
              </div>
              <div class="small-2 columns">
                <button trk="button_form_checkProject" type="submit" name="checkLink" class="button radius postfix">Compare</button>
              </div>
            </div>
          </div>
        </div>
        
      </form>
      
      <?php 
      if (!empty($project)){ ?>
      <hr>
      
      <div class="row">
        <div class="columns medium-4">
          <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
            <img src="<?php echo $project->image; ?>" class="">
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
      
      <div class="mt30">
      <div class="row panel">
        <div class="columns small-4 text-center">
          <p>Our rating <a href="<?php echo Yii::app()->createUrl('site/rating'); ?>"  trk="link_content_ratingInfo" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Rating is based on content quality, social reach and campaign progress. <br />Click for more info."><i class="fa fa-question-circle"></i></a>
          </p>
          <h1 style="color:#0088bb;">
            <span style="font-size:150%"  data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Our rating (0-10) based on content quality, social reach and campaign progress. <br />Higher is better.">
            <?php 
            if ($project->rating != null) echo round($project->rating ,1);
            else echo "n/a"; ?>
            </span>
          </h1>
        </div>
        <div class="columns small-4 text-center">
          <p>Overall position</p>
          <h1>
            <span style="font-size:150%"  data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Position out of this weeks projects. <br />Lower is better.">
            <?php echo $onPage; ?>
            </span>
          </h1>
        </div>
        <div class="columns small-4 text-center">
          <p><?php echo $project->platform->name; ?> position</p>
          <h1>
            <span style="font-size:150%"  data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Position out of this weeks projects inside the same platform. <br />Lower is better.">
            <?php echo $inPlatform; ?>
            </span>
            
          </h1>
        </div>
          <?php if ($onPage > 15){ ?>
            <div class="columns text-center">
              <br /><a href="#" onclick="contact(this);" trk="link_content_featureMe" class="button radius large">Improve your position<br /><small>contact us</small></a>
            </div>
          <?php } ?>
      </div>
      </div>
      
      
      <?php if ($rating_detail){
        echo "<pre>";
        print_r($rating_detail);
        echo"</pre>";
        ?>
      
      <?php } ?>
      
      <?php } ?>
      
    </div>
  </div>
</div>

<div class="pt30 outro pb30">
  <div class="row">
    <div class="columns text-center">
      <h2 class="white">Get featured in our daily or weekly digest!</h2>
      <a href="#" onclick="contact(this);" trk="link_content_featureMe" class="button success radius large">Contact us</a>
    </div>
  </div>
</div>