<?php
set_time_limit(60*5); //5 min
class UpdateCommand extends CConsoleCommand{

// Import.io function to get jason result for a webpage
  function query($connectorGuid, $input, $additionalInput) {
    $url = "https://api.import.io/store/connector/" . $connectorGuid . "/_query?_user=" . urlencode("3e956d8d-5d7f-4595-927e-99ad6b078fe9") . "&_apikey=" . urlencode("cEPYMPY1DTVWS7BFw1oS4N44c/khsNvs9W8vEz8AQ7ytgQr3B6uvEXqOEzGTmyDqmNqlCoKcqmyz2TbQJThtVA==");
    $data = array("input" => $input);
    if ($additionalInput) {
      $data["additionalInput"] = $additionalInput;
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
  }

// Function for emailing of problematic project
  function errorMail($link, $category, $id){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = 'Missing original category';
    $content = 'Category: ' . $catogery . '<br>Id: ' . $id . '<br>Link to project: ' . $link;
    $message->setBody(array("content"=>$content,"title"=>"Added new original category"), 'text/html');
    $message->to = Yii::app()->params['adminEmail'];
    $message->from = Yii::app()->params['noreplyEmail'];
    Yii::app()->mail->send($message);
  }

// Parser for KS
  function parseKickstarter($link){
    $httpClient = new elHttpClient();
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/data-goal="(.+)" data-percent-raised/';
    preg_match($pattern, $htmlData, $matchesGoal);
    $pattern = '/data-currency="(.+)" data-format/';
    preg_match($pattern, $htmlData, $matchesCurrency);
    $money = Yii::app()->numberFormatter->formatCurrency($matchesGoal[1], $matchesCurrency[1]);
    $money_split = explode(".", $money);
    if ($money_slit[1] == "00"){ $data['goal'] = $money_slit[0]; }
    else { $data['goal'] = $money; }

    // Location
    $pattern = '/<a href="\/discover\/places\/.+">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];

    // Category
    $pattern = '/class="category".+\s.+<\/span>\s(.*)\s<\/a><\/li>/';
    preg_match($pattern, $htmlData, $matches);
    $data['category'] = html_entity_decode($matches[1]);

    // Creator
    $pattern = '/id="name">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['creator'] = html_entity_decode($matches[1]);

    // Date
    $pattern = '/<time class="js-adjust" data-format="ll" datetime="(.+)">/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['start_date'] = $matches[1][0];
    $data['end_date'] = $matches[1][1];

    // Created
    $pattern = '/<span class="text">\s(.+) created/';
    preg_match($pattern, $htmlData, $matches);
    if ($matches[1] == "First"){ $data['created'] = 1; }
    else {
      $pattern = '/\/created">(.+) created/';
      preg_match($pattern, $htmlData, $fixedMatches);
      $data['created'] = $fixedMatches[1];
    }

    // Backed
    $pattern = '/span>\s(.+) backed/';
    preg_match($pattern, $htmlData, $matches);
    if ($matches[1] == "0" ){ $data['backed'] = $matches[1]; }
    else {
      $pattern = '/\/backed">(.+) backed/';
      preg_match($pattern, $htmlData, $fixedMatches);
      $data['backed'] = $fixedMatches[1];
    }

    return($data);
  }

// Parser for IGG
  function parseIndiegogo($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/class="currency"><span>(.+)<\/span><\/span>/';
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] = $matches[1];

    // Type of funding
    $pattern = '/<span>(.+ Funding)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    $data['type_of_funding'] = $matches[1];

    // Start date
    $pattern = '/started on (.+)and will/';
    preg_match($pattern, $htmlData, $matches);
    $data['start_date'] = $matches[1];

    // End date
    $pattern = '/close on (.+)\(/';
    preg_match($pattern, $htmlData, $matches);
    $data['end_date'] = $matches[1];

    $link = str_replace("pinw", "show_tab/home", $link);
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Location
    $pattern = '/location-link">(.+)<\/a/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];

    return($data);
  }

// Parser for GGF
  function parseGoGetFunding($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/donated of (.+)<.+>(.+)<\/s/';
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] = $matches[1] . $matches[2];

    // End date
    $pattern = '/donate to this project before (.+)<\/p/';
    preg_match($pattern, $htmlData, $matches);
    $data['end_date'] = $matches[1];

    // Location
    $pattern = '/<a href="\/projects\/city.+">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];

    return($data);
  }

// Parser for PS
  function parsePubSlush($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/aised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] = $matches[1];

    // Location and Category
    $pattern = '/meta-info.>\s.+i> (.+)<\/span>\s.+i> (.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];
    $data['category'] = $matches[2];

    return($data);
  }

// Parser for FA
  function parseFundAnything($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/Contributions of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] = $matches[1];

    // Creator
    $pattern = '/campaign-author">by (.+)<\/h3>/';
    preg_match($pattern, $htmlData, $matches);
    $data['creator'] = $matches[1];

    // Description
    $pattern = '/story">\s+<p>(.+)/';
    preg_match($pattern, $htmlData, $matches);
    $description = strip_tags($matches[1]);
    $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
    $data['description'] = $description . " ...";

    return($data);
  }


// Parser for FR
  function parseFundRazr($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/raised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['goal'] = $matches[1]; }
    else { $data['goal'] = NULL; }

    // Image
    $pattern = '/<meta property="og:image" content="(.+)" \/>/';
    preg_match($pattern, $htmlData, $matches);
    $data['image'] = $matches[1];

    // Category
    $pattern = '/category":"(.+)","commentsEnabled/';
    preg_match($pattern, $htmlData, $matches);
    $data['category'] = $matches[1];

    // Start date
    $pattern = '/Launched (.+)</';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['start_date'] = $matches[1]; }
    else { $data['start_date'] = NULL; }

    // End date
    $pattern = '/Ends (.+) at(.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['end_date'] = $matches[1] . $matches[2];}
    else { $data['end_date'] = NULL; }

    return($data);
  }

// Parser for PM
  function parsePledgeMusic($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

    // Goal
    $pattern = '/raised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['goal'] = $matches[1]; }
    else { $data['goal'] = NULL; }

    // Image
    $pattern = '/<meta property="og:image" content="(.+)" \/>/';
    preg_match($pattern, $htmlData, $matches);
    $data['image'] = $matches[1];

    // Category
    $pattern = '/category":"(.+)","commentsEnabled/';
    preg_match($pattern, $htmlData, $matches);
    $data['category'] = $matches[1];

    // Start date
    $pattern = '/Launched (.+)</';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['start_date'] = $matches[1]; }
    else { $data['start_date'] = NULL; }

    // End date
    $pattern = '/Ends (.+) at(.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) { $data['end_date'] = $matches[1] . $matches[2];}
    else { $data['end_date'] = NULL; }

    return($data);
  }

// Kickstarter store in to DB
  public function actionKickstarter(){
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name'=>'Kickstarter'));
    $id = $platform->id;
    while (($i <= 50) and ($check == false)) {
      $result = $this->query("c2adefcc-3a4a-4bf3-b7e1-2d8f4168a411", array("webpage/url" => "https://www.kickstarter.com/discover/advanced?page=" . $i . "&state=live&sort=launch_date",), false);
      if ($result->results) {
        foreach ($result->results as $data){
	  $link = str_replace("?ref=discovery", "", $data->link);
	  $link_part = explode("/", $link);
          $link_check_old = Project::model()->findByAttributes(array('link'=>$data->link));
          $link_check = Project::model()->findByAttributes(array('link'=>$link));
	  $link_part_check = Project::model()->findByAttributes(array('link'=>'%/' . $link_part[5]));
          if ($link_check || $link_check_old || $link_part_check){ $count = $count+1;} // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parseKickstarter($link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
            $insert->link=$link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
            $insert->orig_category_id=$category->id;
	    if (isset($data_single['start_date'])) $insert->start=date("Y-m-d H:i:s", strtotime($data_single['start_date']));
	    if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s", strtotime($data_single['end_date']));
	    if (isset($data_single['location'])) $insert->location=$data_single['location'];
	    if (isset($data_single['creator'])) $insert->creator=$data_single['creator'];
	    if (isset($data_single['created'])){ $insert->creator_created=$data_single['created']; }
	    if (isset($data_single['backed'])) $insert->creator_backed=$data_single['backed'];
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    $insert->save();
	    $count = 0;
//	    print_r($insert->getErrors());
          }
	  if ($count >= 30){ $check=true; break; }
	}
      }
      $i=$i+1;
    }
  }


// Indiegogo store to DB
  public function actionIndiegogo(){
    $platform = Platform::model()->findByAttributes(array('name'=>'Indiegogo'));
    $id = $platform->id;
    $result = $this->query("de02d0eb-346b-431d-a5e0-cfa2463d086e", array("webpage/url" => "https://www.indiegogo.com/explore?filter_browse_balance=true&filter_quick=new&per_page=2400",), false);
    if ($result->results) {
      foreach ($result->results as $data){
        $link = str_replace("/pinw", "", $data->link);
        $link = str_replace("?sa=0&sp=0", "", $link);
        $link_check_old = Project::model()->findByAttributes(array('link'=>str_replace("?sa=0&sp=0", "", $data->link)));
	$link_deform =  Project::model()->findByAttributes(array('link'=>$data->link));
	$link_check = Project::model()->findByAttributes(array('link'=>$link));
	$title_check = Project::model()->findByAttributes(array('title'=>$data->title));
        if ($link_check || $link_check_old || $title || $link_deform){ }
        else{
	  $data_single = $this->parseIndiegogo($link);
          $insert=new Project;
          $insert->title=$data->title;
          $insert->description=$data->description;
          $insert->image=$data->image;
          $insert->link=$link;
          $insert->time_added=date("Y-m-d H:i:s");
          $insert->platform_id=$id;
          $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
          $insert->orig_category_id=$category->id;
          if (isset($data_single['start_date'])) $insert->start=date("Y-m-d H:i:s",strtotime($data_single['start_date']));
          if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s",strtotime($data_single['end_date']));
          if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
          if (isset($data_single['location'])) $insert->location=$data_single['location'];
          if (isset($data_single['type_of_funding'])){
            if ($data_single['type_of_funding'] == "Fixed Funding") {$typeOfFunding = 0;}
            else {$typeOfFunding = 1;}
            $insert->type_of_funding=$typeOfFunding;
          }
          $insert->save();
//          print_r($insert->getErrors());
        }
      }
    }
  }


// GoGetFunding store to DB
  public function actionGoGetFunding(){
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name'=>'Go get funding'));
    $id = $platform->id;
    while (($i <= 10) and ($check == false)) {
      $result = $this->query("4b6e0d90-3728-4135-b308-560d238de82b", array("webpage/url" => "http://gogetfunding.com/projects/index/page:" . $i . "/filter:recent_projects",), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count = $count+1;} // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parseGoGetFunding($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
	    if ($category) { $insert->orig_category_id=$category->id; }
	    else {
	      $updateOrigCategory = new OrigCategory();
	      $updateOrigCategory->name = $data->category;
	      //$idCategory = Category::model()->findByAttributes(array('name'=>'Other'));
	      //$updateOrigCategory->category_id = $idCategory;
	      $updateOrigCategory->save();
	      $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
	      $insert->orig_category_id=$category->id;
	      $this->errorMail($data->link, $data->category, $category->id);
	    }
	    if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s", strtotime($data_single['end_date']));
	    if (isset($data_single['location'])) $insert->location=$data_single['location'];
	    if (isset($data->creator)) $insert->creator=$data->creator;
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    $insert->save();
	    $count = 0;
//	    print_r($insert->getErrors());
          }
	  if ($count >= 10){ $check=true; break; }
	}
      }
      $i=$i+1;
    }
  }


// PubSlush store to DB
  public function actionPubSlush(){
    $platform = Platform::model()->findByAttributes(array('name'=>'Pubslush'));
    $id = $platform->id;
    $result = $this->query("c25cf290-ca42-45d8-a506-683d1a3e1fe7", array("webpage/url" => "http://pubslush.com/discover/results/current/all-categories/all-currencies/launch-date/",), false);
    if ($result->results) {
      foreach ($result->results as $data){
        $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
        if ($link_check){ }
        else{
	  $data_single = $this->parsePubSlush($data->link);
          $insert=new Project;
          $insert->title=$data->title;
          $insert->description=$data->description;
          $insert->image=$data->image;
          $insert->link=$data->link;
          $insert->time_added=date("Y-m-d H:i:s");
          $insert->platform_id=$id;
          $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category'], 'category_id'=>'24'));
          $insert->orig_category_id=$category->id;
	  if (isset($data->creator)) $insert->creator=$data->creator;
          if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
          if (isset($data_single['location'])) $insert->location=$data_single['location'];
          $insert->save();
//          print_r($insert->getErrors());
        }
      }
    }
  }


// FundAnything store in to DB
  public function actionFundAnything(){
    $i = 1;
    $platform = Platform::model()->findByAttributes(array('name'=>'Fund anything'));
    $id = $platform->id;
    while ($i <= 3) {
      $result = $this->query("2f7d701e-5144-430d-b0f2-a8c6517a4dc7", array("webpage/url" => "http://fundanything.com/en/search/category?cat_id=29&page=" . $i,), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ } // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parseFundAnything($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data_single['description'];
	    $insert->image=$data->image;
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
            $insert->orig_category_id=$category->id;
	    if (isset($data->location)) $insert->location=$data->location;
	    if (isset($data_single['creator'])) $insert->creator=$data_single['creator'];
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    $insert->save();
//	    print_r($insert->getErrors());
          }
	}
      }
      $i=$i+1;
    }
  }


// FundRazr store in to DB
  public function actionFundRazr(){
    $check = false;
    $count = 0;
    $i = 1;
    $preveri=false;
    $kategorije = array();
    $platform = Platform::model()->findByAttributes(array('name'=>'Fundrazr'));
    $id = $platform->id;
    while ($i <= 5) {
      $result = $this->query("ad4abdf0-64f8-4ab8-9cbf-12f8f40605d9", array("webpage/url" => "https://fundrazr.com/find?type=newest&page=" . $i,), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count = $count+1; } // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parseFundRazr($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data_single['image'];
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
	    if ($category) { $insert->orig_category_id=$category->id; }
	    else {
	      $updateOrigCategory = new OrigCategory();
	      $updateOrigCategory->name = $data_single['category'];
	      $updateOrigCategory->save();
	      $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
	      $insert->orig_category_id=$category->id;
	      $this->errorMail($data->link, $data_single['category'], $category->id);
	    }
	    if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s", strtotime($data_single['end_date']));
	    if (isset($data->location)) $insert->location=$data->location;
	    if (isset($data->creator)) $insert->creator=$data->creator;
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    if (isset($data_single['start_date'])) $insert->start=date("Y-m-d H:i:s", strtotime($data_single['start_date']));
	    if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s", strtotime($data_single['end_date']));
	    $insert->save();
//	    print_r($insert->getErrors());
          }
	  if ($count >= 10){ $check=true; break; }
	}
      }
      $i=$i+1;
    }
  }


/*
// PledgeMusic store to DB
  public function actionPledgeMusic(){
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name'=>'Pledge music'));
    $id = $platform->id;
    while (($i <= 10) and ($check == false)) {
      $result = $this->query("", array("webpage/url" => "" . $i . "",), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count = $count+1;} // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parsePledgeMusic($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
	    if ($category) { $insert->orig_category_id=$category->id; }
	    else {
	      $updateOrigCategory = new OrigCategory();
	      $updateOrigCategory->name = $data_single['category'];
	      $idCategory = Category::model()->findByAttributes(array('name'=>'Music'));
	      $updateOrigCategory->category_id = $idCategory;
	      $updateOrigCategory->save();
	      $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
	      $insert->orig_category_id=$category->id;
	      $this->errorMail($data->link, $data_single['category'], $category->id);
	    }
	    if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s", strtotime($data_single['end_date']));
	    if (isset($data_single['location'])) $insert->location=$data_single['location'];
	    if (isset($data->creator)) $insert->creator=$data->creator;
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    $insert->save();
	    $count = 0;
//	    print_r($insert->getErrors());
          }
	  if ($count >= 10){ $check=true; break; }
	}
      }
      $i=$i+1;
    }
  }
*/


}
