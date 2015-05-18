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

      default: echo "Chose from: ks, igg, ggf, fa, fr"; //ps,
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
  
  
  
  
  
  
  
  
  
  
   // Tole je za TEST
  public function actionIndiegogo() {
    $platform = Platform::model()->findByAttributes(array('name' => 'Indiegogo'));
    $id = $platform->id;
    $numberOfPages = 10;
    $link = "https://www.indiegogo.com/explore?filter_browse_balance=true&filter_quick=new&per_page=$numberOfPages";
    $htmlData = $this->getHtml($link, array());
    var_dump($htmlData); Die();
    $pattern = '/(\/projects\/.+)\/pinw/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['links'] = $matches[1];
    $pattern = '/src="(.+cloudinary.+)"/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['images'] = $matches[1];
    //var_dump($data['links']); die;
    if (isset($data['links'])&&isset($data['images'])) {
        $count_links = count($data['links'])-1;
        for ($j=0; $j<= $count_links; $j++) {
        $link = "https://www.indiegogo.com".$data['links'][$j];
        $link = str_replace("/pinw", "", $link);
        $link = str_replace("/qljw", "", $link);
        $link = str_replace("/pimf", "", $link);
        $link = str_replace("?sa=0&sp=0", "", $link);
        $link = str_replace("?sa=0&amp;sp=0", "", $link);
        $project_check = Project::model()->find("link LIKE :link1  OR  link LIKE :link2  OR  link LIKE :link3  OR  image LIKE :image ",
                                                array(':link1' => str_replace("?sa=0&sp=0", "", $data['links'][$j]),
                                                      ':link2' => $data['links'][$j], 
                                                      ':image' => $data['images'][$j], 
                                                      ':link3' => $link));
        if (!$project_check) {
          $htmlData = $this->getHtml($link, array(), 'igg');
          var_dump($data_single); die;
          $htmlData .= $this->getHtml($link . "/show_tab/home", array("X-Requested-With" => "XMLHttpRequest", 'igg'));
          $data_single = $this->parseIndiegogo($htmlData);
          var_dump($data_single); die;
	  if ($data_single == false) { continue; }
          $insert = new Project;
          $insert->title = $data_single['title'];
          $insert->description = $data_single['description'];
          $insert->image = $data['images'][$j];
          $insert->link = $link;
          $insert->time_added = date("Y-m-d H:i:s");
          $insert->platform_id = $id;
          $category = $this->checkCategory($data_single['category'], $link, "");
          $insert->orig_category_id = $category->id;
          if (isset($data_single['start_date']))
            $insert->start = $data_single['start_date'];
          if (isset($data_single['end_date']))
            $insert->end = $data_single['end_date'];
          if (isset($data_single['goal']))
            $insert->goal = $data_single['goal'];
          if (isset($data_single['location']))
            $insert->location = $data_single['location'];
          if (isset($data_single['type_of_funding'])) {
            if ($data_single['type_of_funding'] == "Fixed Funding") {
              $typeOfFunding = 0;
            } else {
              $typeOfFunding = 1;
            }
            $insert->type_of_funding = $typeOfFunding;
          }
          var_dump($insert); die;
          $insert->save();


          $id_project = $insert->id;
          // Category add
          $insert_category = new ProjectOrigcategory;
          $insert_category->project_id = $id_project;
          $category = $this->checkCategory($data_single['category'], $link, "");
	  $insert_category->orig_category_id = $category->id;
	  $insert_category->save();
          
          // get rating 
          $IggRating = new IndiegogoRating($link, $insert->id, $htmlData);
          $rating = $IggRating->firstAnalize();
          $insert->rating = $rating;
          $insert->save();          
//          print_r($insert->getErrors());
        }
      }
    }
  }
  
  function parseIndiegogo($htmlData) {
    
    $pattern = '/var utag_data = (.+);/'; 
    preg_match($pattern, $htmlData, $match);
    if (isset($match[1])){$json = html_entity_decode($match[1]);}
    else{return false;}
    $json = str_replace('\\"', "", $json);
    $json = str_replace('\"', "", $json);
    $jsonData = json_decode($json);
    if ($jsonData == null){ return false; }
    if ($jsonData->page_name == "Invalid Page | Indiegogo") {return false;}
    
    // Title
    $data['title'] = $jsonData->campaign_name;
            
    // Description
    $data['description'] = $jsonData->campaign_description;
            
    // Category
    $data['category'] = $jsonData->campaign_category;

    // Goal
    $money = Yii::app()->numberFormatter->formatCurrency($jsonData->{'campaign_goal_amount'}, $jsonData->{'site_currency'});
    $money_split = explode(".", $money);
    if ($money_split[1] == "00") {$data['goal'] = $money_split[0];
    } else {$data['goal'] = $money;}

    // Type of funding
    if ($jsonData->{'campaign_type'} == "flexible_funding") {$data['type_of_funding'] = 1;}
    else{$data['type_of_funding'] = 0;}

    // Start date
    $data['start_date'] = date("Y-m-d H:i:s", strtotime($jsonData->{'campaign_start_date'}));

    // End date
    $data['end_date'] = date("Y-m-d H:i:s", strtotime($jsonData->{'campaign_end_date'}));

    // Location
    $pattern = '/gon.ga_impression_data=(.+);gon.env=/';
    preg_match($pattern, $htmlData, $match);
    $json = html_entity_decode($match[1]);
    $json = str_replace('\\"', "\'", $json);
    $jsonData = json_decode($json);
    if ($jsonData != null) { $data['location'] = $jsonData->{'list'}; }

    return($data);
  }
  
  function getHtml($link, $header) {
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects();
    $httpClient->setProxy("111.161.126.98", 80);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link, $header);
    return $htmlDataObject->httpBody;
  }
  
  function checkCategory($category_check, $link, $platform){
    $category_check = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $category_check);
    if ($platform == "PledgeMusic") {
      $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '15'));
    }elseif ($platform == "PubSlush"){
      $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '24'));
    }else{
      $category = OrigCategory::model()->findByAttributes(array('name' => $category_check));
    }
    if ($category) {
      return $category;
    } else {
      $updateOrigCategory = new OrigCategory();
      if ($platform == "PledgeMusic") {
        $updateOrigCategory->category_id = 15;
      }elseif ($platform == "PubSlush"){
        $updateOrigCategory->category_id = 24;
      }
      $updateOrigCategory->name = $category_check;
      $updateOrigCategory->save();
      if ($platform == "PledgeMusic") {
        $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '15'));
      }elseif ($platform == "PubSlush"){
        $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '24'));
      }else{
        $category = OrigCategory::model()->findByAttributes(array('name' => $category_check));
      }
      //$this->errorMail($link, $category_check, $category->id);
      return $category;
    }
  }
  
  function errorMail($link, $category, $id) {
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = 'Missing original category';
    $content = 'Category: ' . $category . '<br>Id: ' . $id . '<br>Link to project: ' . $link;
    $message->setBody(array("content" => $content, "title" => "Added new original category"), 'text/html');
    $message->to = Yii::app()->params['scriptEmail'];
    $message->from = Yii::app()->params['noreplyEmail'];
    Yii::app()->mail->send($message);
  }
  
    
}
