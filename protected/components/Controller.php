<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/default';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
    public $pageDesc = '';
    public $keywords = '';
    public $fbImage = '';
  
  
public function init(){
    $baseUrl = Yii::app()->baseUrl; 
    $cs = Yii::app()->getClientScript();
      
    //$cs->registerCssFile($baseUrl.'/css/foundation.css');
    $cs->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/normalize.min.css');
    $cs->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/css/foundation.min.css');
    
    $cs->registerCssFile($baseUrl.'/css/layout.css'.getVersionID());   
    $cs->registerCssFile($baseUrl.'/css/chosen/chosen.min.css');
    $cs->registerCssFile($baseUrl.'/css/tipr.css');
    
    //$cs->registerCssFile($baseUrl.'/css/font-awesome.min.css');
    $cs->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css');
    

		// JAVASCRIPTS
    $cs->registerCoreScript('jquery');  //core jquery lib
    //$cs->registerCoreScript('jquery-ui');  //core jquery lib

    $cs->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',CClientScript::POS_HEAD);  //modernizer
    
  
    //$cs->registerScriptFile($baseUrl.'/js/respond.min.js');
    $cs->registerScriptFile($baseUrl.'/js/vendor/fastclick.js');
    //$cs->registerScriptFile($baseUrl.'/js/foundation.min.js');
    //$cs->registerScriptFile($baseUrl.'/js/foundation/foundation.js');
    //$cs->registerScriptFile($baseUrl.'/js/foundation/foundation.equalizer.js');
    //$cs->registerScriptFile($baseUrl.'/js/foundation/foundation.reveal.js'); //scroll tracker
    
    $cs->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/foundation/5.5.3/js/foundation.min.js');
    
    $cs->registerScriptFile($baseUrl.'/js/chosen.jquery.min.js');  // new dropdown
    
    $cs->registerScriptFile($baseUrl.'/js/jquery.timers.min.js');  // timers
    $cs->registerScriptFile($baseUrl.'/js/jquery.scrolldepth.min.js'); //scroll tracker
    
    $cs->registerScriptFile($baseUrl.'/js/tipr.min.js');
    
    $cs->registerScriptFile('https://platform.twitter.com/widgets.js');
    

    //$cs->registerCoreScript($baseUrl.'jquery.ui');
    //$cs->registerCoreScript($baseUrl.'autocomplete');
    
    // google analytics
    $cs->registerScript("ganalytics","
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-9773251-6', 'auto');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');
     ",CClientScript::POS_HEAD);
    
    $cs->registerScript("mobileads",'
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-0534207295672567",
          enable_page_level_ads: true
        });
     ',CClientScript::POS_HEAD);
    //ga('set', '&uid', <?php echo ? >); // Set the user ID using signed-in user_id.
     
    //ga('require', 'linkid', 'linkid.js');
    $cs->registerScript("scrollDepth","
      $(function() {
        $.scrollDepth();
      });");
     
    
    // startup scripts
    $cs->registerScriptFile($baseUrl.'/js/app.js'.getVersionID());
    
    parent::init();
  }
  
  public function run($in_actionID){
    $baseUrl = Yii::app()->baseUrl; 
    $cs = Yii::app()->getClientScript();
    // general controller JS
    if (file_exists("js/controllers/".Yii::app()->controller->id."/controller.js"))
      $cs->registerScriptFile($baseUrl."/js/controllers/".Yii::app()->controller->id."/controller.js".getVersionID());
    // specific action JS
    if (!$in_actionID) $actionID = $this->defaultAction;
    else $actionID =  $in_actionID;

    if (file_exists("js/controllers/".Yii::app()->controller->id."/".$actionID.".js"))
      $cs->registerScriptFile($baseUrl."/js/controllers/".Yii::app()->controller->id."/".$actionID.".js".getVersionID());
    
    parent::run($in_actionID);
  }  
}