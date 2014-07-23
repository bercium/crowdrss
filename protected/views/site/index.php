<?php
/* @var $this SiteController */
$this->pageTitle = 'All your crowdfunding projects in one place';
$this->pageDesc = "Folow projects from Kickstarter and Indiegogo.";

?>

    <div class="intro pt30">
       <a id="whatIsCRSS" class="anchor"></a>
       <div class="row">
         <div class="columns large-12 text-center">
           <img src="images/logo.png" class="mt30 mb30">
           <h1 class="title show-for-medium-up">Crowdfunding RSS</h1>
           <h1 class="title-small show-for-small">Crowdfunding RSS</h1>
         </div>
       </div>
    </div>
  
		<div class="intro-desc pb30">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          <h1>The best place for</h1>
          <p>
            the best place for 

          </p>

        </div>
      </div>
    </div>


		<div class="mt30">
      <a id="subscribe" class="anchor"></a>
      <div class="row">
        <div class="columns large-12 large-centered">
          
          <form method="post" action="<?php echo Yii::app()->createUrl('site/index'); ?>" data-abide>
          
          <h2>1. Choose platform</h2>
          <p>Which platforms do you wish to get your news from?<p>
            
            
          <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-3">
              <?php
              $i = 0;
              foreach ($platforms as $plat){
                $i++;
                
                ?>
                <li class="text-center">
                    <div class="mb30">
                      <label for="plat_<?php echo $plat['id']; ?>" >
                        <?php if (file_exists("images/".strtolower($plat['name']).".png")){ ?>
                        <img src="images/<?php echo strtolower($plat['name']); ?>.png">
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
          
          
          <hr>
          
          
          <h2>2. Choose categories</h2>
          <p>Which topics do you find interesting?<p>
            
            <ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
              <?php
              $i = 0;
              foreach ($categories as $cat){
                $i++;
                
                ?>
                <li>
                  <div class="row">
                    <div class="columns small-4">
                      <div class="switch round small">
                        <input id="cat_<?php echo $cat['id']; ?>" name="cat[<?php echo $cat['id']; ?>]" <?php if ($cat['selected']) echo 'checked'; ?> type="checkbox">
                        <label for="cat_<?php echo $cat['id']; ?>"></label>
                      </div>
                    </div>
                    <div class="columns small-8">
                        <label for="cat_<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></label>
                    </div>
                  </div>
                </li>
                <?php
              } ?>
            </ul>
            
   
          <hr>
          
          
          <h2>3. Get the RSS link</h2>
          <p>We will generate a link and send it to your email address.<p>
            
          <div class="email-field">
            <label>Email *
              <input type="email" name="email" value="<?php echo $email; ?>" required>
            </label>
            <small>We will use your email only to send you RSS link and occasional crowdfunding project notifications. We will never sell or give your email address to anyone!</small>
          </div>
          
          <div style="margin-top: 30px;">
            <button type="submit" name="subscribe" class="success radius">Subscribe to RSS</button>
          
            <button type="reset" class="secondary radius right">Reset all</button>
          </div>
            
          </form>
        </div>
        
      </div>
    </div>



		<div class="mt30 pt30 pb30 outro">
      <a id="whatIsCRSS" class="anchor"></a>
      <div class="row">
        <div class="columns medium-6">
          <h1>Sharing is caring</h1>
          <p>
            Share with your friends and help us spread the word :)<br />
          </p>
          
        </div>
        <div class="columns medium-6">
          <br />
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo Yii::app()->params['absoluteHost']; ?>" target="_none" ><img style="height:80px" src="images/fbw.png" ></a>&nbsp;&nbsp;
            <a href="https://twitter.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&text=<?php echo $this->pageDesc; ?>"  target="_none" ><img style="height:80px" src="images/tww.png" ></a>&nbsp;&nbsp;
            <a href="https://plus.google.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $this->pageDesc; ?>"  target="_none" ><img style="height:80px" src="images/gpw.png" ></a>
          
        </div>
      </div>
    </div>