<?php
/* @var $this SiteController */
$this->pageTitle = 'Crowdfunding projects delivered to you';
$this->pageDesc = "Select your favorite platform, chose your interests and we will deliver the best projects right in your inbox or trough RSS feed.";

?>

<?php //* ?>
<div class="top-menu fixed pt15 show-for-medium-up">
  <div class="row">
    <div class="columns large-12 text-center">
      <dl class="sub-nav right" >
        <dd><a href="<?php echo Yii::app()->createUrl('topDaily'); ?>"  trk="link_top_topDaily">Best of today</a></dd> 
        <dd><a href="<?php echo Yii::app()->createUrl('site/owners'); ?>" trk="link_top_owners">Get featured</a></dd> 
      </dl>
    </div>
  </div>
</div>
<?php //*/ ?>

    <div class="intro">
       <div class="pt60 hide-for-small"></div>
       <a id="whatIsCRSS" class="anchor"></a>
       <div class="row">
         <div class="columns large-12 text-center">
           <div class="hide-for-small"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" class="mt20 mb30 "></div>
           <div class="show-for-small mb30 mt20"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png" class="mt30 mb30 pb20" style="width:100px; height:100px;"></div>
           <h1 class="white title">Crowdfunding projects delivered to you</h1>
           <?php if (!Yii::app()->user->isGuest) echo "<h2 class=''>".$subscribers." subscribers</h2>"; ?>
         </div>
       </div>
    </div>
  
    <div class="intro-desc pb30">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns ">
          <h3 class="white">
            Select your <strong>favorite platform</strong>, chose your <strong>interests</strong> and we will deliver 
            <strong>the best projects</strong> right in your <span style="color:#222">inbox <i class="fa fa-envelope-o"></i></span> or trough <span style="color:#222">RSS feed <i class="fa fa-rss"></i></span>
          </h3>
          <a class="project-owner button tiny secondary radius right" href="<?php echo Yii::app()->createUrl('site/owners'); ?>" trk="button_intro_projectOwner">
            I am a project owner
          </a>
          

        </div>
        
      </div>
    </div>


		<div class="mt30">
      <a id="subscribe" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          
          <form method="post" id="rssfeed_form" action="<?php echo Yii::app()->createUrl('site/index'); ?>" data-abide>
          
          <a id="platform" class="anchor"></a>
          <h2>1. Select a platform</h2>
          <p>Which platforms do you wish to follow?</p>
            
          <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
              <?php
              $i = 0;
              foreach ($platforms as $plat){
                $i++;

                if ($i == 5){
                ?>
                </ul>
                <div class="more-platforms-btn" style="width:100%; text-align: center; margin-bottom: 25px;">
                  <a onclick="$('.more-platforms').slideDown(); $('.more-platforms-btn').slideUp();">
                    <i class="fa fa-sort-down" style="font-size: 20px; padding-left:6px; padding-right:6px;"></i>
                    More platforms
                    <i class="fa fa-sort-down" style="font-size: 20px; padding-left:6px; padding-right:6px;"></i>
                  </a>
                </div>
                <ul class="more-platforms small-block-grid-1 medium-block-grid-2 large-block-grid-4">
                <?php
                }
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
                //if ($i == count($platforms)) echo '</span>';
              } ?>
          </ul>
          
          
          <hr class="mt0">
          
          <a id="categories" class="anchor"></a>
          <h2>2. Choose categories</h2>
          <p>Which topics do you find interesting? 
            
            <br /><i style="color:#888;">Access sub-categories with a click on</i>
            
            <i class="fa fa-sort-down" style="color:#888;font-size: 20px; padding-left:6px; padding-right:6px;"></i>
            
          </p>
            
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
                        <a class="<?php if (!$cat['selected']) echo 'hide'; ?> right" id="subCatLink_<?php echo $cat['id']; ?>" <?php 
                        
                           if ($subscription) echo 'onclick="showSubCat('.$cat['id'].');"';
                           else echo 'data-reveal-id="tweettounlock" dref="'.$cat['id'].'"';
                        
                           ?> data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Select subcategories">
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
                  <?php }else{ 
                    foreach ($cat['subcat'] as $subcat) { ?>
                    <input id="subcat_<?php echo $subcat['id']; ?>" name="subcat[<?php echo $subcat['id']; ?>]" value="1" type="hidden">
                  <?php }
                      }?>
                  
                </li>
                <?php
              } ?>
            </ul>
            
          <hr>
          
          
          <?php // if (!Yii::app()->user->isGuest) { //* ?>
          <a id="limitProjects" class="anchor"></a>          
          <h2>3. Limit projects</h2>
          <p>
            Select the quantity and quality of projects you wish to receive. <a href="<?php echo Yii::app()->createUrl('site/rating'); ?>"  trk="link_content_ratingInfo" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Rating is based on content quality, social reach and campaign progress. <br />Click for more info."><i class="fa fa-question-circle"></i></a>
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
            
          <a id="preview" class="anchor"></a>
          <h2>4. <a onclick="previewForm()" trk="link_content_preview" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Live preview of your selection">Preview</a> 
            and Finish</h2>
          <p>Instantly receive projects with RSS feed or subscribe to a mail digest.</p>

          <div class="row">
             <div class="columns small-12 medium-4">
               
               <div class="row" trk="switch_delivery_rss">
                  <div class="columns small-3 medium-5 large-4">
                    <div class="switch round small">
                      <input id="rss_feed" name="rss_feed" <?php if (!isset($subscription->rss)) echo 'checked'; else if ($subscription->rss) echo 'checked'; ?> type="checkbox">
                      <label for="rss_feed"></label>
                    </div>
                  </div>
                  <div class="columns small-9 medium-6 large-8">
                    <label for="rss_feed" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Instantly get all projects in your favorit RSS reader.">
                      RSS feed link <i class="fa fa-rss"></i>
                    </label>
                  </div>
                </div>
               
               <div class="row" trk="switch_delivery_dailyDigest">
                  <div class="columns small-3 medium-5 large-4">
                    <div class="switch round small">
                      <input id="daily_digest" name="daily_digest" <?php if (!isset($subscription->daily_digest)) echo 'checked'; else if ($subscription->daily_digest) echo 'checked'; ?> type="checkbox">
                      <label for="daily_digest"></label>
                    </div>
                  </div>
                  <div class="columns small-9 medium-6 large-8">
                    <label for="daily_digest" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Daily update with top 10 projects of that day.">
                       Daily email digest <i class="fa fa-envelope-o"></i>
                    </label>
                  </div>
                </div>
               
               <div class="row" trk="switch_delivery_twoTimesWeeklyDigest">
                  <div class="columns small-3 medium-5 large-4">
                    <div class="switch round small">
                      <input id="two_times_weekly_digest" name="two_times_weekly_digest" <?php if (isset($subscription->two_times_weekly_digest) && $subscription->two_times_weekly_digest == 1) echo 'checked'; ?> type="checkbox">
                      <label for="two_times_weekly_digest"></label>
                    </div>
                  </div>
                  <div class="columns small-9 medium-6 large-8">
                    <label for="two_times_weekly_digest" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Once a week get an overview of most popular projects in the last week.">
                      Two emails per week <i class="fa fa-envelope-o"></i>
                    </label>
                  </div>
                </div>
                 
               <div class="row" trk="switch_delivery_weeklyDigest">
                  <div class="columns small-3 medium-5 large-4">
                    <div class="switch round small">
                      <input id="weekly_digest" name="weekly_digest" <?php if (isset($subscription->weekly_digest) && $subscription->weekly_digest == 1) echo 'checked'; ?> type="checkbox">
                      <label for="weekly_digest"></label>
                    </div>
                  </div>
                  <div class="columns small-9 medium-6 large-8">
                    <label for="weekly_digest" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Once a week get an overview of most popular projects in the last week.">
                      Weekly email digest <i class="fa fa-envelope-o"></i>
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




<div id="tweettounlock" class="reveal-modal medium" data-reveal>
  <h2>Tweet to unlock</h2>
  <p>
    This functionality is locked. To unlock it please tweet about us :)
    <br />
    If you don't have twitter <a onclick="contact(this);" trk="link_bottom_noTwitter">contact us</a>.
    <br />
    <div class="text-center">
      <a href="http://twitter.com/share" class="twitter-share-button" data-text="<?php echo $this->pageTitle; ?>" data-hashtags="crowdfunding,kickstarter" data-via="eberce_ltd" data-dnt="true" data-count="none" data-size="large">Tweet to unlock</a>
    </div>
    <br />
    <br />
    <i>
      If you already subscribed please use "edit your feed" button from your verification email to hide this message.
    </i>
  </p>
  <a class="close-reveal-modal">&#215;</a>
</div>

<script type="text/javascript">
  sessionID = '<?php echo base64_encode(session_id());?>';
</script>
