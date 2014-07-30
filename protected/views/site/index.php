<?php
/* @var $this SiteController */
$this->pageTitle = 'Crowdfunding projects delivered to you';
$this->pageDesc = "Follow projects from Kickstarter, Indiegogo and others in one place.";

?>

    <div class="intro pt30">
       <a id="whatIsCRSS" class="anchor"></a>
       <div class="row">
         <div class="columns large-12 text-center">
           <img src="images/logo.png" class="mt30 mb30">
           <h1 class="white title show-for-medium-up">Crowdfunding RSS</h1>
           <h1 class="white title-small show-for-small">Crowdfunding RSS</h1>
         </div>
       </div>
    </div>
  
		<div class="intro-desc pb30">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          <h3 class="white">Crowdfunding projects delivered to you!</h3>
          <p class="white-light">
            Select your favorite platform, chose your interests and we will generate a personalized RSS feed for you. 
            Then just add it to your favorite RSS reader and enjoy.
          </p>

        </div>
      </div>
    </div>


		<div class="mt30">
      <a id="subscribe" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          
          <form method="post" id="rssfeed_form" action="<?php echo Yii::app()->createUrl('site/index'); ?>" data-abide>
          
          <h2>1. Choose a platform</h2>
          <p>Which platforms do you wish to follow?<p>
            
            
          <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
              <?php
              $i = 0;
              foreach ($platforms as $plat){
                $i++;
                
                ?>
                <li class="text-center mb30" trk="switch_platform_<?php echo $plat['name']; ?>">
                    <div class="mb20">
                      <label for="plat_<?php echo $plat['id']; ?>" >
                        <?php if (file_exists("images/platforms/".strtolower(str_replace(" ", "", $plat['name'])).".png")){ ?>
                        <img src="images/platforms/<?php echo strtolower(str_replace(" ", "", $plat['name'])); ?>.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="<?php echo $plat['name']; ?>">
                        <?php }else{?>
                        <div class="panel radius text-center small-8 small-offset-2 mb0" style="height:150px;">
                          <h2><?php echo $plat['name']; ?></h2>
                        </div>
                        <?php } ?>
                      </label>
                    </div>
                  
                    <div class="switch round large" style="width:90px; margin-left: auto; margin-right: auto;">
                      <input id="plat_<?php echo $plat['id']; ?>" type="checkbox" name="plat[<?php echo $plat['id']; ?>]" <?php if ($plat['selected']) echo 'checked'; ?> onclick="uncheckPlatforms(<?php echo $plat['id']; ?>);">
                      <label for="plat_<?php echo $plat['id']; ?>"></label>
                    </div>
              
                </li>
                <?php
              } ?>
          </ul>
          
          
          <hr class="mt0">
          
          
          <h2>2. Choose categories</h2>
          <p>Which topics do you find interesting?<p>
            
            <ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
              <?php
              $i = 0;
              foreach ($categories as $cat){
                $i++;
                
                ?>
                <li trk="switch_category_<?php echo $cat['name']; ?>">
                  <div class="row">
                    <div class="columns small-4">
                      <div class="switch round small">
                        <input id="cat_<?php echo $cat['id']; ?>" name="cat[<?php echo $cat['id']; ?>]" <?php if ($cat['selected']) echo 'checked'; ?> type="checkbox" onchange="toggleSubCat(<?php echo $cat['id']; ?>)">
                        <label for="cat_<?php echo $cat['id']; ?>"></label>
                      </div>
                    </div>
                    <div class="columns small-8">
                        <?php if(count($cat['subcat']) > 1){ ?>
                        <a class="<?php if (!$cat['selected']) echo 'hide'; ?> right" id="subCatLink_<?php echo $cat['id']; ?>" onclick="showSubCat(<?php echo $cat['id']; ?>);" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Select subcategories">
                          <i class="fa fa-sort-down" style="font-size: 20px; padding-left:6px; padding-right:6px;"></i>
                        </a>
                        <?php } ?>
                      
                        <label for="cat_<?php echo $cat['id']; ?>" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="<?php echo $cat['hint']; ?>">
                          <?php echo $cat['name']; ?>
                        </label>
                      
                    </div>
                  </div>
                  <?php if(count($cat['subcat']) > 1){ ?>
                  <div class="row hide" id="subCatHolder_<?php echo $cat['id']; ?>">
                    <div class="columns small-12 mb20 mt10 ml15">
                      <?php /* ?>
                      <a class="close right" onclick="$('#subCatHolder_<?php echo $cat['id']; ?>').slideUp();">
                        <i class="fa fa-times"></i>
                      </a>
                      <br /><?php */ ?>
                      
                      <?php foreach ($cat['subcat'] as $subcat) { ?>
                      <div class="mt10">
                        <div class="row">
                          <div class="columns small-4">
                            <div class="switch round tiny">
                              <input id="subcat_<?php echo $subcat['id']; ?>" name="subcat[<?php echo $subcat['id']; ?>]" <?php if ($subcat['selected']) echo 'checked'; ?> type="checkbox">
                              <label class="success" for="subcat_<?php echo $subcat['id']; ?>"></label>
                            </div>
                          </div>
                          <div class="columns small-8">
                              <label for="subcat_<?php echo $subcat['id']; ?>">
                                <?php echo $subcat['name']; ?>
                              </label>
                          </div>
                        </div>
                      </div>
                      <?php } ?>
                                           
                    </div>
                  </div>
                  <?php } ?>
                  
                </li>
                <?php
              } ?>
            </ul>
            
   
          <hr>
          
          
          <h2>3. <a onclick="previewForm()" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Live preview of your selection">Preview</a> and get the RSS link</h2>
          <p>We will generate a link and send it to your email address.<p>
            
          <div class="email-field">
            <label>Email *
              <input type="email" name="email" value="<?php echo $email; ?>" required>
            </label>
            <small>We will use your email only to send you RSS link and occasional crowdfunding project notifications. We will never sell or give your email address to anyone!</small>
          </div>
          
          <div style="margin-top: 30px;">
            <?php if (isset($_GET['id'])){ ?>
            <button trk="button_form_updateRSS" type="submit" name="subscribe" class="success radius">Update RSS feed</button>
            
            <a href="<?php echo Yii::app()->params['absoluteHost']; ?>">
              <button trk="button_form_reset" class="secondary radius right">Cancel</button>
            </a>
            <?php }else{ ?>
            <button trk="button_form_subscribe" type="submit" name="subscribe" class="success radius">Subscribe to RSS</button>
            
            <?php /* ?>
            <button trk="button_form_preview" type="button" id="preview" class="info radius hide" style="margin-left:20px;" onclick="previewForm()">Preview</button>
            <?php */ ?>
            <button trk="button_form_reset" type="reset" class="secondary radius right">Reset all</button>
            <?php } ?>
          </div>
            
          </form>
          
          <form method="post" id="preview_form" action="<?php echo Yii::app()->createUrl('feed/previewRss'); ?>" target="_blank">
            <input type="hidden" id="preview_platform" name="platform">
            <input type="hidden" id="preview_category" name="category">
            <input type="hidden" id="preview_subcategory" name="subcategory">
          </form>
        </div>
        
      </div>
    </div>



		<div class="mt30 pt30 pb30 outro">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns medium-6">
          <h1 class="white">Sharing is caring</h1>
          <p class="white-light">
            Share with your friends and help us spread the word!<br />
          </p>
          
        </div>
        <div class="columns medium-6 social">
          <br />
            <a trk="social_facebook_share_bottom" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo Yii::app()->params['absoluteHost']; ?>" target="_none" ><img style="height:80px" src="images/fbw.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Share us on Facebook"></a>
            <a trk="social_twitter_share_bottom" href="https://twitter.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&text=<?php echo $this->pageDesc; ?>"  target="_none" ><img style="height:80px" src="images/tww.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Tweet about us"></a>
            <a trk="social_google_share_bottom" href="https://plus.google.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $this->pageDesc; ?>"  target="_none" ><img style="height:80px" src="images/gpw.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Share us on Google+"></a>
        </div>
      </div>
    </div>