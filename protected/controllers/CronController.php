<?php

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
   * all hidden profiles will be notified every second week
   */
  public function actionPoolCrowd(){
    set_time_limit(60*5); //5 min
    echo $this->consoleCommand('update','kickstarter');
    echo $this->consoleCommand('update','indiegogo');
  }
  
  /**
   * all hidden profiles will be notified every second week
   */
  public function actionPoolKs(){
    echo $this->consoleCommand('update','kickstarter');
  }  
  
  /**
   * all hidden profiles will be notified every second week
   */
  public function actionPoolIgg(){
    echo $this->consoleCommand('update','indiegogo');
  }
  
}
