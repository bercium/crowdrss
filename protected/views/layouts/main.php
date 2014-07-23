<?php $fullTitle = Yii::app()->name; 
if (!empty($this->pageTitle) && (Yii::app()->name != $this->pageTitle)) $fullTitle .= " - ".$this->pageTitle;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <?php /* ?><meta charset="utf-8" /><?php */ ?>
  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="en" />
  <?php if ($this->pageDesc != ''){ ?><meta name="description" content="<?php echo $this->pageDesc; ?>" /> <?php } ?>

  <!-- FB -->
  <meta property="og:title" content="<?php echo $fullTitle; ?>" />
  <meta property="og:site_name" content="<?php echo Yii::app()->name; ?>" />
  <meta property="og:description" content="<?php echo $this->pageDesc; ?>" />
  <meta property="og:image" content="<?php echo Yii::app()->createAbsoluteUrl('/images/fb-logo.png'); ?>" />
  <meta property="og:url" content="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>"/>
  <link rel="canonical" href="<?php echo Yii::app()->createAbsoluteUrl(Yii::app()->request->url); ?>" />
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

  <div class="footer">
    <div class="row">
      <div class="column small-12 pt30  text-center">
        <div class="left">
          enjoy crowdfunding projects
        </div>
        <dl class="sub-nav right">
          <dd><a htef="#" onclick="contact(this);" trk="link_bottom_contact us">Contact us</a></dd> 
          <?php if (!Yii::app()->user->isGuest){ ?>
          <dd><a href="<?php echo Yii::app()->createUrl('project/index'); ?>">Project</a></dd> 
          <dd><a href="<?php echo Yii::app()->createUrl('category/index'); ?>">Category</a></dd> 
          <dd><a href="<?php echo Yii::app()->createUrl('origCategory/index'); ?>">Orig Category</a></dd> 
          <dd><a href="<?php echo Yii::app()->createUrl('platform/index'); ?>">Platform</a></dd> 
          <?php } ?>
        </dl>
      </div>
    </div>
  </div>


</body>
</html><?php 
    // be the last to override any other CSS settings
    Yii::app()->getClientScript()->registerCssFile(Yii::app()->baseUrl.'/css/override.css'); 
    if(YII_DEBUG) Yii::app()->getClientScript()->registerScript("cleartimeout","clearTimeout(all_js_ok);");
