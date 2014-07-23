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
  function errorMail($link, $category){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = 'Missing category';
    $content = 'Link to project: ' . $link . "\n" . 'Category: ' . $catogery;
    $message->setBody(array("content"=>$content,"title"=>"Error"), 'text/html');
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
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] = $matches[1] . " ";
    $pattern = '/data-currency="(.+)" data-format/';
    preg_match($pattern, $htmlData, $matches);
    $data['goal'] .= $matches[1];

    //Location
    $pattern = '/<a href="\/discover\/places\/.+">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];

    //Category
    $pattern = '/class="category".+\s.+<\/span>\s(.*)\s<\/a><\/li>/';
    preg_match($pattern, $htmlData, $matches);
    $data['category'] = html_entity_decode($matches[1]);

    //Creator
    $pattern = '/id="name">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['creator'] = html_entity_decode($matches[1]);

    //Date
    $pattern = '/<time class="js-adjust" data-format="ll" datetime="(.+)">/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['start_date'] = $matches[1][0];
    $data['end_date'] = $matches[1][1];

    //Created
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

    //Type of funding
    $pattern = '/<span>(.+ Funding)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    $data['type_of_funding'] = $matches[1];

    //Start date
    $pattern = '/started on (.+)and will/';
    preg_match($pattern, $htmlData, $matches);
    $data['start_date'] = $matches[1];

    //End date
    $pattern = '/close on (.+)\(/';
    preg_match($pattern, $htmlData, $matches);
    $data['end_date'] = $matches[1];

/*    //Location
    $pattern = '/class="i.{1}byline(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    var_dump($matches);
    $data['location'] = $matches[1];
    echo $data['location'];

    //Creator
    $pattern = '/id="name">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['creator'] = $matches[1];
*/
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

    //End date
    $pattern = '/donate to this project before (.+)<\/p/';
    preg_match($pattern, $htmlData, $matches);
    $data['end_date'] = $matches[1];

    //Location
    $pattern = '/<a href="\/projects\/city.+">(.+)<\/a>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];

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
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count = $count+1;} // Counter for checking if it missed some project in the next few projects
	  else{
	    $data_single = $this->parseKickstarter($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
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
        $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
        if ($link_check){ }
        else{
	  $data_single = $this->parseIndiegogo($data->link);
          $insert=new Project;
          $insert->title=$data->title;
          $insert->description=$data->description;
          $insert->image=$data->image;
          $insert->link=$data->link;
          $insert->time_added=date("Y-m-d H:i:s");
          $insert->platform_id=$id;
          $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
          $insert->orig_category_id=$category->id;
          if (isset($data_single['start_date'])) $insert->start=date("Y-m-d H:i:s",strtotime($data_single['start_date']));
          if (isset($data_single['end_date'])) $insert->end=date("Y-m-d H:i:s",strtotime($data_single['end_date']));
          if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
          if (isset($data_single['type_of_funding'])){
            if ($data_single['type_of_funding'] == "Fixed Funding") {$typeOfFunding = 0;}
            else {$typeOfFunding = 1;}
            $insert->type_of_funding=$typeOfFunding;
          }
//          if (isset($data_single[0]->location)) $insert->location=$data_single[0]->location;
//          if (isset($data_single[0]->creator)) $insert->creator=$data_single[0]->creator;
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
	      $this->errorMail($data->link, $data->category);
	      $updateOrigCategory = new OrigCategory();
	      $updateOrigCategory->name = $data->category;
	      $updateOrigCategory->category_id = "25";
	      $updateOrigCategory->save();
	      $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
	      $insert->orig_category_id=$category->id;
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


}
