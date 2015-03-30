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
   * 
   */
  public function actionPoolCrowd(){
    //set_time_limit(60*12); //5 min
    echo $this->consoleCommand('update','kickstarter');
    echo $this->consoleCommand('update','indiegogo');
    echo $this->consoleCommand('update','goGetFunding');
    echo $this->consoleCommand('update','pubSlush');
    echo $this->consoleCommand('update','fundAnything');
    echo $this->consoleCommand('update','fundRazr');
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
      case "ps":echo $this->consoleCommand('update','pubSlush');
        break;
      case "fa":echo $this->consoleCommand('update','fundAnything');
        break;
      case "fr":echo $this->consoleCommand('update','fundRazr');
        break;

      default: echo "Chose from: ks, igg, ggf, ps, fa, fr";
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
  
  
  
   /** 
   * mark project removed from DB
   */
  private function removeFromDB($id){
    $update = Project::model()->findByPk($id);
    if ($update){
      $update->removed=1;
      $update->save();
    }
  }
  
  /** 
   * check project link if it's OK
   */
  private function checkProjectLink($link, $id = ''){
    $httpClient = new elHttpClient();
    $httpClient->setUserAgent("ff3");
    $httpClient->enableRedirects();
    $httpClient->setHeaders(array_merge(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")));
    $htmlDataObject = $httpClient->get($link,array("X-Requested-With" => "XMLHttpRequest"));
    $text = $htmlDataObject->httpBody;
    if (strpos($link, "indiegogo.com") !== false){
      if (strpos($text, "i-illustration-not_found") !== false){
        $this->removeFromDB($id);
        return false;
      }
    }else{
      if (strpos($link, "kickstarter.com") !== false){
        if (strlen($text) < 2000){
          $this->removeFromDB($id);
          return false;
        }
      }
    }
    
    return true;
  }

  private function createSQL($sub, $days = 1){
    $sql = '';
    if ($sub->category){
    //if ($this->validateId($sub->category)){
      //$orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($sub->category)."))");
      
      $subcat = array();
      if ($sub->exclude_orig_category){
  //    if ($this->validateId($sub->exclude_orig_category)){
        $subcat = explode(",",$sub->exclude_orig_category);
  //      $subcat = explode(",",$this->validateId($sub->exclude_orig_category));
      }
      
      $orgCat = OrigCategory::model()->findAll("(category_id IN (".$sub->category."))");

      $allCats = array();
      foreach ($orgCat as $cat){
        if (!in_array($cat->id, $subcat)) $allCats[$cat->id] = $cat->id;
      }
      $sql .= " (orig_category_id IN (".implode(',',$allCats).")) AND ";
    }
    if ($sub->platform) $sql .= " (platform_id IN (".$sub->platform.")) AND ";
    //if ($this->validateId($sub->platform)) $sql .= " (platform_id IN (".$this->validateId($sub->platform).")) AND ";
    else{
      $platforms = Platform::model()->findAll("active = :active",array(":active"=>0));
      $selplat = '';
      foreach ($platforms as $platform){
        if ($selplat) $selplat .= ',';
        $selplat .= $platform->id;
      }
      if ($selplat) $sql .= " (platform_id NOT IN (".$selplat.")) AND ";
    }
    //$sql .= ' 1 ';
    $hours = $days*24+3;
    $sql .= " (removed = 0) AND (time_added < DATE_ADD(NOW(),INTERVAL -3 HOUR)) AND (time_added >= DATE_ADD(NOW(),INTERVAL -".$hours." HOUR)) ";  // one day slot with 3h delay
    if ($sub->rating > 0)  $sql .= " AND ((rating IS NULL) OR (rating >= ".$sub->rating.")) ";

    $sql .= " ORDER BY rating DESC, time_added ASC";
    //$sql .= " LIMIT 12";
    return $sql;
  }
  
  
   
  /**
   * 
   */
  private function sortProjects($sub, $projects, $paidProjects){
    if (count($paidProjects) > 0){
        foreach ($paidProjects as $pp){
            $platA = explode(",",$sub->platform);
            $catA = explode(",",$sub->category);
            $subCatA = explode(",",$sub->exclude_orig_category);

            if ($sub->platform && !in_array($pp->project->platform_id, $platA)) continue; // has platforms but not in
            if (in_array($pp->project->orig_category_id, $subCatA)) continue; // exclude list
            if ($sub->category && !in_array($pp->project->origCategory->category_id, $catA)) continue; // not in category

            $pp->show_count++;
            $pp->save();
            $paidProject = $pp->project; //get one project
            // set the rating higher so we know it's special
            if ($paidProject->rating) $paidProject->rating += 11;
            else $paidProject->rating = 11;
            break;
        }
    }
        
    
    $featured = $regular = $regularNull = array();
    $i = 0;
    foreach ($projects as $project){
      $i++;
      if (($paidProject) && ($paidProject->id == $project->id)) continue; // skip featured project from the list

      if (!$this->checkProjectLink($project->link,$project->id)) continue;

      if ($i <= 4) $featured[] = $project;
      else{
        if ($project->rating == null) $regularNull[] = $project;
        else $regular[] = $project;
      }
    }

    if ($paidProject) array_unshift($featured,$paidProject);  //add to the beginning of the queue


    shuffle($regularNull);
    $regularNull = array_slice($regularNull,0,4);
    $regular = array_merge(array_slice($regular,0,8-count($regularNull)),$regularNull);

    if (count($regular) < 4) $regular = array();
    else if (count($regular) < 8) $regular = array_slice($regular, 0, 4);
    
    return array("featured"=>$featured, "regular"=>$regular);
  }

  
   /**
   * 
   */
  private function sendNewsletter($sub, $title, $subject, $trackingCode, $projects){
    //set mail tracking
    $tc = mailTrackingCode();
    $ml = new MailLog();
    $ml->tracking_code = mailTrackingCodeDecode($tc);
    $ml->type = $trackingCode;
    $ml->subscription_id = $sub->id;
    $ml->save();


    // create message
    $message = new YiiMailMessage;
    $message->view = 'digest';
    $message->subject = $subject; 
    $message->from = Yii::app()->params['noreplyEmail'];

    $content = '';

    $count = count($projects['featured'])+count($projects['regular']);
    // not enough projects
    if ($count < 4){
      $content = 'We found just a few projects for you. <br />Maybe your rules are too strict? Consider editing your feed.<hr>';
    }

    $editLink = absoluteURL()."site/index?id=".$sub->hash;

    $message->setBody(array("tc"=>$tc,"user_id"=>$sub->id,
                            "content"=>$content, "title"=>$title,
                            "featuredProjects"=>$projects['featured'], "projects"=>$projects['regular'],
                            "showEdit"=>true,"editLink"=>$editLink
                            ), 'text/html');
    $message->setTo($sub->email);
    if ($count > 0) Yii::app()->mail->send($message);
  }
  
  
  /**
   * 
   */
  public function actionWeeklyDigest1(){
    $test = true;
    /*if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
    else*/ 
        $subscriptions = Subscription::model()->findAllByAttributes(array('weekly_digest'=>1));

    if ($subscriptions){

        $paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 2 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));


        if (date("M", strtotime("-1 days")) == date("M", strtotime("-8 days"))){
            $date = addOrdinalNumberSuffix(date("j", strtotime("-8 days")))." - ".addOrdinalNumberSuffix(date("j", strtotime("-1 days")))." ".date("M", strtotime("-1 days"));
        }else{
            $date = addOrdinalNumberSuffix(date("j", strtotime("-8 days")))." ".date("M", strtotime("-8 days"))." - ".addOrdinalNumberSuffix(date("j", strtotime("-1 days")))." ".date("M", strtotime("-1 days"));
        }

        foreach ($subscriptions  as $sub){

            $sql = $this->createSQL($sub, 7);

            // get projects
            $projects = Project::model()->findAll($sql);

            $sorted = $this->sortProjects($sub,$projects,$paidProjects);

            if ($test){
                echo $sub->email."\n";
                print_r($sorted);
                echo "\n\n";
            }


        }
    }

  }
  
}
