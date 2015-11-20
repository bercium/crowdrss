<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(-1); 

class CronController extends Controller
{
  
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow all users to perform actions
        'actions'=>array(),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
  
  
  /**
   * 
   */
  function consoleCommand($controller, $action){
    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
    $runner = new CConsoleCommandRunner();
    $runner->addCommands($commandPath);
    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
    $runner->addCommands($commandPath);
    
    $args = array('yiic', $controller, $action); // 'migrate', '--interactive=0'
    //$args = array_merge(array("yiic"), $args);
    ob_start();
    $runner->run($args);
    return htmlentities(ob_get_clean(), null, Yii::app()->charset);    
  }
  
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionTest()
	{
    echo "test";
    //echo absoluteURL()."\n<br />";
    //echo $this->consoleCommand('','');
  }
	
  
  /**
   * 
   */
  public function actionPoolCrowd(){
    //set_time_limit(60*12); //5 min
    echo $this->consoleCommand('update','kickstarter');
    echo $this->consoleCommand('update','indiegogo');
    echo $this->consoleCommand('update','goGetFunding');
    //echo $this->consoleCommand('update','pubSlush');
    echo $this->consoleCommand('update','fundAnything');
    echo $this->consoleCommand('update','fundRazr');
    echo $this->consoleCommand('update','pledgeMusic');
  }
  
  public function actionPoolKickstarter(){
    echo $this->consoleCommand('update','kickstarter');
  }
  
  public function actionPoolIndiegogo(){
    echo $this->consoleCommand('update','indiegogo');
  }

  public function actionPoolOther(){
    echo $this->consoleCommand('update','goGetFunding');
    //echo $this->consoleCommand('update','pubSlush');
    echo $this->consoleCommand('update','fundAnything');
    echo $this->consoleCommand('update','fundRazr');
    echo $this->consoleCommand('update','pledgeMusic');
  }  
  /**
   * all hidden profiles will be notified every second week
   */
  public function actionPoolSingle($type){
    switch ($type) {
      case "ks":echo $this->consoleCommand('update','kickstarter');
        break;
      case "igg":echo $this->consoleCommand('update','indiegogo');
        break;
      case "ggf":echo $this->consoleCommand('update','goGetFunding');
        break;
      //case "ps":echo $this->consoleCommand('update','pubSlush');
      //  break;
      case "fa":echo $this->consoleCommand('update','fundAnything');
        break;
      case "fr":echo $this->consoleCommand('update','fundRazr');
        break;
      case "pm":echo $this->consoleCommand('update','pledgeMusic');
        break;

      default: echo "Chose from: ks, igg, ggf, fa, fr, pm"; //ps,
        break;
    }
  }
  
  /**
   * 
   */
  public function actionFirstDay(){
    echo $this->consoleCommand('rating','firstDay');
  }
  
  
  /**
   * 
   */
  public function actionAfterFirstDay(){
    echo $this->consoleCommand('rating','afterFirstDay');
  }  
  
  /**
   * 
   */
  public function actionAfterDays(){
    echo $this->consoleCommand('rating','afterDays');
  }


  /**
   * 
   *//*
  public function actionDailyRating(){
    echo $this->consoleCommand('rating','after1day');
  }*/
  
  /**
   * 
   */
  public function actionDailyDigest(){
    echo $this->consoleCommand('mailer','dailyDigest');
  }
  
  public function actionTestDailyDigest(){
    echo $this->consoleCommand('mailer','testDailyDigest');
  }
  
  /**
   * 
   *//*
  public function actionWeeklyRating(){
    echo $this->consoleCommand('rating','after1week');
  }  */
  
  /**
   * 
   */
  public function actionWeeklyDigest(){
    echo $this->consoleCommand('mailer','weeklyDigest');
  }
  
  public function actionTestWeeklyDigest(){
    echo $this->consoleCommand('mailer','testWeeklyDigest');
  }
  
  
    /**
   * 
   */
  public function actionTwiceAWeekDigest(){
    echo $this->consoleCommand('mailer','twiceAWeekDigest');
  }
  
  public function actionTestTwiceAWeekDigest(){
    echo $this->consoleCommand('mailer','testTwiceAWeekDigest');
  }
  
  
  /**
   * 
   */
  public function actionValidateParsers(){
    echo $this->consoleCommand('mailer','validateParsers');
  }    
}
