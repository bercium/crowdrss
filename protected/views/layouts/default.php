<?php $this->beginContent('//layouts/main'); ?>

    <div class="intro-defult " >
       <div class="row">
         <div class="columns large-12 ">
           
          <dl class="sub-nav right white-top">
            <dd><a href="<?php echo Yii::app()->createUrl('site/index'); ?>"  trk="link_top_dailyDigest" data-tooltip data-options="disable_for_touch:true" title="Select your <strong>favorite platform</strong>, chose your <strong>interests</strong> and we will deliver <strong>the best projects</strong> right in your inbox or trough RSS feed.">Best projects digest</a></dd> 
            <dd><a href="<?php echo Yii::app()->createUrl('site/owners'); ?>" trk="link_top_owners" >Project owner</a></dd> 
          </dl>
           
           <h3 class="white special_title" style="margin-bottom: 0px;">
             <a href="<?php echo Yii::app()->request->baseUrl."/"; ?>" style="color: inherit" trk="link_top_index">
               <span class="hide-for-small"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo-tiny.png" style="height:40px"> Crowdfunding</span>
               <span class="show-for-small"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo-tiny.png" style="height:40px"></span>
             </a>
           </h3>
           
         </div>
       </div>
    </div>

  <?php echo $content; ?>

<?php $this->endContent(); ?>