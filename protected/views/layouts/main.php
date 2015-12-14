<?php $fullTitle = Yii::app()->name; 
if (!empty($this->pageTitle) && (Yii::app()->name != $this->pageTitle)) $fullTitle = $this->pageTitle." | ".$fullTitle;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head itemscope itemtype="http://schema.org/WebSite">
  <?php /* ?><meta charset="utf-8" /><?php */ ?>
  <!-- Set the viewport width to device width for mobile -->
  
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en" />
    <?php if ($this->pageDesc != ''){ ?><meta name="description" content="<?php echo $this->pageDesc; ?>" /> <?php } ?>
    <?php if ($this->keywords != ''){ ?><meta name="keywords" content="<?php echo $this->keywords; ?>" /> <?php } ?>

  <!-- FB -->
  <meta property="og:title" content="<?php echo $fullTitle; ?>" />
  <meta itemprop='name' property="og:site_name" content="<?php echo Yii::app()->name; ?>" />
  <meta property="og:description" content="<?php echo $this->pageDesc; ?>" />
  <meta property="og:image" content="<?php if ($this->fbImage != ''){ echo $this->fbImage; } else echo Yii::app()->createAbsoluteUrl('/images/fb-logo.png'); ?>" />
  <meta property="og:url" content="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>"/>
  <link rel="canonical" itemprop="url" href="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="website" />
  
  <!-- M$ -->
  <meta name="application-name" content="<?php echo Yii::app()->name; ?>" />
  <meta name="msapplication-tooltip" content="<?php echo $this->pageDesc; ?>" />
  <meta name="msapplication-starturl" content="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>" />
  <meta name="msapplication-navbutton-color" content="#89b561" />

  <!-- Mobile icons -->
  <link rel="apple-touch-icon" sizes="114x114" href="<?php echo Yii::app()->createAbsoluteUrl('/images/iphone-retina.png'); ?>">
  <link rel="apple-touch-icon" sizes="72x72" href="<?php echo Yii::app()->createAbsoluteUrl('/images/ipad.png'); ?>">
  <link rel="apple-touch-icon" href="<?php echo Yii::app()->createAbsoluteUrl('/images/iphone.png'); ?>">
		
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo Yii::app()->createAbsoluteUrl('/images/iphone.png'); ?>">
  <link rel="icon" type="image/ico" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
  <script>
    var fullURL= '<?php echo Yii::app()->request->baseUrl; ?>'; 
    <?php if(YII_DEBUG){ ?>var all_js_ok = setTimeout(function() {alert('Problem v enem izmed JS fajlov!');}, 5000); <?php } ?> 
  </script>
    
  <?php //if (YII_DEBUG){ ?>
  <link href='http://fonts.googleapis.com/css?family=Istok+Web:700,400' rel='stylesheet' type='text/css'>
  <?php //} ?>
    
	<title><?php echo $fullTitle; ?></title>
</head>
  
<body>
  
  
<?php  writeFlashes(); ?>

    
    
	<?php echo $content; ?>

  
  <div class="footer-">
	  
	<?php if (isset($this->social)  && $this->social){ ?>
	<div class=" pt30 pb30 outro">
      <a id="share" class="anchor"></a>
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
            <a trk="social_twitter_share_bottom" href="https://twitter.com/intent/tweet?url=<?php echo Yii::app()->params['absoluteHost']; ?>&text=<?php echo $this->pageTitle; ?>&hashtags=crowdfunding,kickstarter" target="_none" ><img style="height:80px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/tww.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Tweet about us"></a>
            <a trk="social_google_share_bottom" href="https://plus.google.com/share?url=<?php echo Yii::app()->params['absoluteHost']; ?>&title=<?php echo $this->pageTitle; ?>&summary=<?php echo $this->pageDesc; ?>" target="_none" ><img style="height:80px" src="<?php echo Yii::app()->request->baseUrl; ?>/images/gpw.png" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Share us on Google+"></a>
        </div>
      </div>
    </div>
	<?php } ?>
	  
	
	<div class=" pt30 pb30 <?php if (!isset($this->social) || !$this->social) echo " outro" ?>">
		<div class="row ">
		  <div class="columns large-12 large-centered mt30">
			  <ul class="small-block-grid-2 medium-block-grid-4 large-block-grid-4">
				<li class="text-center ">
				  <strong><a href="<?php echo Yii::app()->createUrl('topDaily'); ?>"  trk="link_bottom_topDaily"><i class="fa fa-star fa-5x"></i><br />Top daily</a></strong>
				</li>
				<li class="text-center ">
					<strong><a href="<?php echo Yii::app()->createUrl('top50'); ?>"  trk="link_bottom_top50"><i class="fa fa-trophy fa-5x"></i><br />Top 50</a></strong>
				</li>
				<li class="text-center ">
				  <strong><a href="<?php echo Yii::app()->createUrl('bottom50'); ?>" trk="link_bottom_bottom50"><i class="fa fa-bolt fa-5x"></i><br />Bottom 50</a></strong>
				</li>
				<li class="text-center ">
				  <strong><a href="<?php echo Yii::app()->createUrl('site/owners'); ?>"  trk="link_bottom_owners"><i class="fa fa-info-circle fa-5x"></i><br />Project owners</a></strong>
				</li>  
			  </ul>
			</div>
		</div>
	</div>
	<?php if (isset($this->social) && $this->social){ ?>
	<hr style="margin:0;"><?php } ?>
    <div style="background-color:#222; color:#FFF;">
        <div class="row">
          <div class="column small-12 pt30 text-center">
            <div class="left">
              in beta ... with <i class="fa fa-heart" style="color:#f04124" data-tooltip data-options="disable_for_touch:true" class="tip-right radius" title="Love"></i>
            </div>
            <dl class="sub-nav right" >
              <?php /* ?>
              <dd><a href="<?php echo Yii::app()->createUrl('topDaily'); ?>"  trk="link_bottom_topDaily"><i class="fa fa-star-o"></i> Daily top</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('top50'); ?>"  trk="link_bottom_top50"><i class="fa fa-trophy"></i> Top 50</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('bottom50'); ?>" trk="link_bottom_bottom50"><i class="fa fa-bolt"></i> Bottom 50</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('site/owners'); ?>"  trk="link_bottom_owners"><i class="fa fa-info-circle"></i> Project owners</a></dd>
              <?php */ ?>
              <dd ><a href="#" onclick="contact(this);" trk="link_bottom_contactUs" style="color:#bbb;"><i class="fa fa-envelope"></i> Write to us</a></dd> 
            </dl>
          </div>
        </div>
        <?php if (!Yii::app()->user->isGuest){ ?>
        <div class="row">
          <div class="column small-12">
            <dl class="sub-nav " >

              <dd><a href="<?php echo Yii::app()->createUrl('project/index'); ?>">Project</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('projectFeatured/index'); ?>">Feature a project</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('category/index'); ?>">Category</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('origCategory/index'); ?>">Orig Category</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('platform/index'); ?>">Platform</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('statistic/database'); ?>">Stat DB</a></dd> 
              <dd><a href="<?php echo Yii::app()->createUrl('statistic/socialAnalize'); ?>">Social analize</a></dd> 


            </dl>
          </div>
        </div>
        <?php } ?>
        <div class="row">
          <div class="column small-12 text-center mb30 light">
            <small>
               All published platforms and their related content and Trademarks are owned by individual platform and their respective project owners ⋅ Crowdfunding RSS is not affiliated with any of them.
            </small>
          </div>
        </div>
    </div>
  </div>


</body>
</html><?php 
    // be the last to override any other CSS settings
    Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/override.css'); 
    if(YII_DEBUG) Yii::app()->getClientScript()->registerScript("cleartimeout","clearTimeout(all_js_ok);");
