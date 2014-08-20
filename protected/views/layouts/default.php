<?php $this->beginContent('//layouts/main'); ?>

    <div class="intro-defult mb20" >
       <div class="row">
         <div class="columns large-12">
           <a href="<?php echo Yii::app()->request->baseUrl; ?>">
             <h2 class="white special_title" style="margin-bottom: 0px;">
               <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" style="height:40px"> Crowdfunding RSS
             </h2>
           </a>
         </div>
       </div>
    </div>

  <?php echo $content; ?>

<?php $this->endContent(); ?>