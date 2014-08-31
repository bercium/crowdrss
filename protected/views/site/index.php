<?php
/* @var $this SiteController */
$this->pageTitle = 'Crowdfunding projects delivered to you';
$this->pageDesc = "Follow projects from Kickstarter, Indiegogo and others in one place.";

?>

    <div class="intro pt30">
       <a id="whatIsCRSS" class="anchor"></a>
       <div class="row">
         <div class="columns large-12 text-center">
           <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" class="mt30 mb30">
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
            Select your <strong>favorite platform</strong>, chose your <strong>interests</strong> and we will deliver <strong>the best projects</strong> right in your inbox or trough RSS feed.
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
          <p>Which platforms do you wish to follow?</p>
            
            
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
                        <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/platforms/<?php echo strtolower(str_replace(" ", "", $plat['name'])); ?>.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="<?php echo $plat['name']."<br />Projects per day: ".$plat['projPerDay']; ?>">
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
          <p>Which topics do you find interesting?</p>
            
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
          
          
          <?php // if (!Yii::app()->user->isGuest) { //* ?>
          <h2>3. Limit projects</h2>
          <p>
            Select the quantity and quality of projects you wish to receive.
            <br />
            <small><i>Currently we rate only projects from Kickstarter and Indiegogo</i></small>
          </p>

          
          <script>var slider_value = <?php if (!isset($subscription->rating)) echo "4"; else echo $subscription->rating; ?>;</script>
          <div class="row"> 
            <div class="small-2 medium-1 columns pt20" style="text-align:right;">
              <span data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="More projects, less curated">All</span>
            </div> 
            <div class="small-8 medium-10 columns"> 
              <div id="slider" class="range-slider round" data-slider data-options="display_selector: #sliderOutput; start: 0; end: 9;"> 
                <span class="range-slider-handle"></span> 
                <span class="range-slider-active-segment"></span> 
                <input type="hidden" name="rating" id="rating">
              </div> 
            </div> 
            <div class="small-2 medium-1 columns pt20" >
              <span data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Less projects, more curated">Best</span>
            </div> 
          </div>
          

          <hr>
            
            
          <h2>4. <a onclick="previewForm()" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Live preview of your selection">Preview</a> 
            and Finish</h2>
          <p>Instantly receive projects with RSS feed or subscribe to a mail digest.</p>

          <div class="row">
             <div class="columns small-12 medium-4">
               
               <div class="row" trk="switch_delivery_rss">
                  <div class="columns small-2 medium-5 large-4">
                    <div class="switch round small">
                      <input id="rss_feed" name="rss_feed" <?php if (!isset($subscription->rss)) echo 'checked'; else if ($subscription->rss) echo 'checked'; ?> type="checkbox">
                      <label for="rss_feed"></label>
                    </div>
                  </div>
                  <div class="columns small-10 medium-6 large-8">
                    <label for="rss_feed" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Instantly get all projects in your favorit RSS reader.">
                      RSS feed
                    </label>
                  </div>
                </div>
               
               <div class="row" trk="switch_delivery_dailyDigest">
                  <div class="columns small-2 medium-5 large-4">
                    <div class="switch round small">
                      <input id="daily_digest" name="daily_digest" <?php if (isset($subscription->daily_digest) && $subscription->daily_digest == 1) echo 'checked'; ?> type="checkbox">
                      <label for="daily_digest"></label>
                    </div>
                  </div>
                  <div class="columns small-10 medium-6 large-8">
                    <label for="daily_digest" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Once a day recieve an email with top 10 projects that day.">
                      Daily digest
                    </label>
                  </div>
                </div>
               
               <div class="row" trk="switch_delivery_weeklyDigest">
                  <div class="columns small-2 medium-5 large-4">
                    <div class="switch round small">
                      <input id="weekly_digest" name="weekly_digest" <?php if (isset($subscription->weekly_digest) && $subscription->weekly_digest == 1) echo 'checked'; ?> type="checkbox">
                      <label for="weekly_digest"></label>
                    </div>
                  </div>
                  <div class="columns small-10 medium-6 large-8">
                    <label for="weekly_digest" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Once a week get an overview of most popular projects in the last week.">
                      Weekly digest
                    </label>
                  </div>
                </div>
               
             </div>
             <div class="columns small-12 medium-8">
               <div class="email-field">
                <label>Email <font style="color:#f04124">*</font>
                  <input type="email" name="email" value="<?php if (isset($subscription->email)) echo $subscription->email; ?>" required>
                </label>
                <small style="font-style: italic;">We will use your email only to send you RSS link or digest and occasional site updates. We will never sell or give your email address to anyone!</small>
              </div>
             </div>
           </div>
 
          
          
           
           
          <?php /* }else{  ?>
          
          
          <h2>3. <a onclick="previewForm()" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Live preview of your selection">Preview</a> and get the RSS link</h2>
          <p>We will generate a link and send it to your email address.</p>
            
          <input id="rss_feed" name="rss_feed" type="hidden" value="1">

          <div class="email-field">
            <label>Email *
              <input type="email" name="email" value="<?php if (isset($subscription->email)) echo $subscription->email; ?>" required>
            </label>
            <small>We will use your email only to send you RSS link and occasional crowdfunding project notifications. We will never sell or give your email address to anyone!</small>
          </div>
          
          <?php } //*/ ?>
          
          <div style="margin-top: 30px;" class="text-center">
            <?php if (isset($_GET['id'])){ ?>
            <button trk="button_form_updateRSS" type="submit" name="subscribe" class="success radius large">Update subscribtion</button>
            
            <a href="<?php echo Yii::app()->params['absoluteHost']; ?>">
              <button trk="button_form_reset" class=" radius right tiny">Cancel</button>
            </a>
            <?php }else{ ?>
            <button trk="button_form_subscribe" type="submit" name="subscribe" class="success radius large">Create my feed</button>
            
            <?php /* ?>
            <button trk="button_form_preview" type="button" id="preview" class="info radius hide" style="margin-left:20px;" onclick="previewForm()">Preview</button>
            <?php */ ?>
            <button trk="button_form_reset" type="reset" class=" radius right tiny">Reset form</button>
            <?php } ?>
          </div>
            
          </form>
          
          <form method="post" id="preview_form" action="<?php echo Yii::app()->createUrl('feed/previewRss'); ?>" target="_blank">
            <input type="hidden" id="preview_platform" name="platform">
            <input type="hidden" id="preview_category" name="category">
            <input type="hidden" id="preview_subcategory" name="subcategory">
            <input type="hidden" id="preview_rating" name="preview_rating">
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
            <a trk="social_facebook_share_bottom" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo Yii::app()->params['absoluteHost']; ?>" target="_none" ><img style="height:80px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/fbw.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Share us on Facebook"></a>
            <a trk="social_twitter_share_bottom" href="https://twitter.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&text=<?php echo $this->pageDesc; ?>"  target="_none" ><img style="height:80px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/tww.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Tweet about us"></a>
            <a trk="social_google_share_bottom" href="https://plus.google.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $this->pageDesc; ?>" target="_none" ><img style="height:80px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/gpw.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Share us on Google+"></a>
        </div>
      </div>
    </div>