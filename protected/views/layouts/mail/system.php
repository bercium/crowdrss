<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<meta name="viewport" content="width=device-width" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Crowdfunding RSS</title>

</head>
 
<body bgcolor="#FFFFFF">

            <?php echo $content; ?>
					
              <a href="<?php if (!empty($tc)) echo mailLinkTracking($tc,Yii::app()->params['absoluteHost'],"footer-page"); else echo Yii::app()->params['absoluteHost']; ?>">
                Crowdfunding RSS
              </a>
						

  <?php if (!empty($tc)){ ?>
    <img src="<?php echo absoluteURL().'/track/mailOpen?tc='.$tc; ?>" />
  <?php } ?>

</body>
</html>
