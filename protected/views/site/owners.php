<div class="pt60" style="border-bottom:1px solid #dddddd; ">
  <div class="row">
    <div class="columns medium-12">
      <h3>Check your project rating</h3>
      <form method="get" id="checkproject_form" action="<?php echo Yii::app()->createUrl('site/owners'); ?>" data-abide>
        <p>
          How your project stacks against others listed on our site.
        </p>
        
        <div class="row">
          <div class="large-8 columns">
            <div class="row collapse">
              <div class="small-9 medium-10 columns">
                <input type="text" name="s" value="<?php echo $link; ?>" placeholder="Name or link of your campaign" required>
              </div>
              <div class="small-3 medium-2 columns">
                <button trk="button_form_checkProject" type="submit"  class="button radius postfix">Compare</button>
              </div>
            </div>
          </div>
        </div>
        
      </form>
    </div>
  </div>
</div>

<?php if ($evalProject){ ?>
<div style="border-bottom:1px solid #dddddd; ">
      <?php 
      if (!empty($project)){ ?>
    
        <div class="pt40 pb60" <?php /*/ ?> class=""style="background-image:url(<?php echo $project->image; ?>); background-size:cover;" <?php /*/?> class="parallax-window" data-parallax="scroll" data-speed="0.4" data-image-src="<?php echo $project->image; ?>" <?php //*/ ?>>      
          <div class="row">
            <div class="columns medium-12 pt20 " style="background-color: rgba(255,255,255,0.7)">

                <div class="row">
                  <div class="columns medium-4">
                    <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" rel="nofollow" trk="link_preview_<?php echo $project->id; ?>" target="_blank">
                        <img src="<?php echo $project->image; ?>" class="" style="width: 100%;">
                    </a>
                  </div>
                  <div class="columns medium-8">
                      <div class="show-for-small pt30"></div>
                        <?php if (!Yii::app()->user->isGuest){ ?><a href="<?php echo Yii::app()->createUrl("view/index",array("remove"=>$project->id,"name"=>$project->internal_link)); ?>">Remove</a> <?php }?>
                    <a href="<?php echo Yii::app()->createUrl("feed/rl",array("l"=>$project->link)); ?>" rel="nofollow" trk="link_preview_<?php echo $project->id; ?>" target="_blank"><h4><?php echo $project->title; ?></h4></a>
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

              <div class="pt30 panel mb0 text-center">

                <div class="row ">
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

                  <p class="mb10 mt20"><strong>Share your rating:</strong></p> 
                  <a href="http://www.facebook.com/sharer.php?s=100&p[title]=<?php echo $this->pageTitle; ?>&p[summary]=<?php echo $summary; ?>&p[url]=<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>"
                       trk="social_facebook_share_project"
                       onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/social-fb.png" width="60">
                    </a>
                    &nbsp;
                    <a href="http://twitter.com/share?text=<?php echo $summary; ?>&hashtags=cfrss&via=fundingtown" trk="social_twitter_share_project"
                       onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/social-tw.png" width="60">
                    </a>
                    &nbsp;
                    <a href="https://plus.google.com/share?url=<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $summary; ?>"
                       trk="social_plus_share_project"
                       onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/social-gp.png" width="60">
                    </a>
                    &nbsp;
                    <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $summary; ?>&source=CrowdfundingRSS"
                       trk="social_linkedin_share_project" rel="nofollow"
                       onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/social-in.png" width="60">
                    </a>

              </div>

            </div>
          </div>
        </div>
    
      <?php }else{ ?>
        <div class="row pb60 pt40">
          <div class="columns medium-12">      

            <div class="mt30">

            <div class="row panel">
               <div class="columns small-4 text-center">
                <p>Our rating <a href="<?php echo Yii::app()->createUrl('site/rating'); ?>"  trk="link_content_ratingInfo" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Rating is based on content quality, social reach and campaign progress. <br />Click for more info."><i class="fa fa-question-circle"></i></a>
                </p>
                <h1 style="color:#0088bb;">
                  <span style="font-size:150%"  data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Our rating (0-10) based on content quality, social reach and campaign progress. <br />Higher is better.">
                  <?php 
                  if (isset($rating)) echo round($rating ,1);
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
                <p>Platform specific position</p>
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
          </div>
        </div>
      <?php } ?>
      
      <?php if ($rating_detail){
        echo "<pre>";
        echo htmlentities(print_r($rating_detail,true));
        echo"</pre>";
       } ?>
      
    <?php  //submit ?>
      

</div>
<?php } ?>

<div class="pt60 outr-o pb30">
	
  <div class="row">
    <div class="columns text-center">
      <h2 class="mb20">Get featured in our daily or weekly digest!</h2>
      <a href="#" onclick="contact(this);" trk="link_content_featureMe" class="button success radius large">Contact us</a>
    </div>
  </div>
</div>