<div class="menu-top-bar fixed <?php if (!isset($main_menu)) echo"intro-defult"; ?>" style="border-bottom:1px solid rgba(255,255,255,0.1)">
    <div class="row">
        <div class="columns large-12 ">
                <nav class="top-bar" data-topbar role="navigation">


                    <ul class="title-area">
                      <li class="name" >
                        <a href="<?php echo Yii::app()->request->baseUrl."/"; ?>" data-tooltip data-options="disable_for_touch:true" title="Crowdfunding RSS" style="color:#fff;">
                            <img style="" src="/crowdfunding-rss/images/logo-tiny.png">
                            &nbsp;&nbsp;CROWDFUNDING
                        </a>
                          
                      </li>
                       <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
                      <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
                    </ul>

                    <section class="top-bar-section">
                      <!-- Right Nav Section -->
                      <ul class="right">
                            <li><a href="<?php echo Yii::app()->createUrl('crowdfunding-sites'); ?>" trk="link_top_resources" >Resources</a></li>
                            <li><a href="<?php echo Yii::app()->createUrl('top50'); ?>" trk="link_top_top50" >Top 50</a></li>
                            <li><a href="<?php echo Yii::app()->createUrl('site/index'); ?>"  trk="link_top_dailyDigest" >Project digest</a></li>
                            <li><a href="<?php echo Yii::app()->createUrl('site/owners'); ?>" trk="link_top_owners" >Search</a></li>

                      </ul>

                      <!-- Left Nav Section -- >
                      <ul class="left">
                        <li><a>Crowdfunding</a></li>
                      </ul>-->
                    </section>

                </nav>
        </div>    
    </div>    
</div>
<div class="pt30"></div>

<?php /* ?>
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
    </div><?php */ ?>