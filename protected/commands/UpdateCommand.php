<?php
//set_time_limit(60*5); //5 min
class UpdateCommand extends CConsoleCommand {
    
// Import.io function to get jason result for a webpage
  function query($connectorGuid, $input) {
    $url = "https://api.import.io/store/connector/" . $connectorGuid . "/_query?_user=" . urlencode("3e956d8d-5d7f-4595-927e-99ad6b078fe9") . "&_apikey=" . urlencode("3e956d8d-5d7f-4595-927e-99ad6b078fe9:cEPYMPY1DTVWS7BFw1oS4N44c/khsNvs9W8vEz8AQ7ytgQr3B6uvEXqOEzGTmyDqmNqlCoKcqmyz2TbQJThtVA==");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Content-Type: application/json",
		"import-io-client: import.io PHP client",
		"import-io-client-version: 2.0.0"
    ));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("input" => $input)));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
  }

// Function for emailing of problematic project
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

// Check if category exists
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

// Parser for PS
/*  function parsePubSlush($link) {
    $htmlData = $this->getHtml($link, array());

    // Goal
    $pattern = '/raised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) $data['goal'] = $matches[1];
    else $data['goal'] = null;

    // Location and Category
    $pattern = '/meta-info.>\s.+i> (.+)<\/span>\s.+i> (.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    $data['location'] = $matches[1];
    $data['category'] = $matches[2];

    return($data);
  }*/

  private function importioError($error, $platform){
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = "Importio problem";
    $message->from = Yii::app()->params['noreplyEmail'];

    $content = 'Error text: '.$error;
    $title = $platform;

    $message->setBody(array("content"=>$content, "title"=>$title,), 'text/html');
    $message->to = Yii::app()->params['scriptEmail'];
    Yii::app()->mail->send($message);
  }

// Kickstarter store in to DB
  public function actionKickstarter() {
    $parsing = new KickstarterParser();
    $web = new webText();
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name' => 'Kickstarter'));
    $id = $platform->id;
    while (($i <= 50) and ($check == false)) { 
      $link = "https://www.kickstarter.com/discover/advanced?page=$i&state=live&sort=newest";
      $htmlData = $web->getHtml($link, array());
      $pattern = '/(\/projects\/.+)\?ref=discovery/';
      preg_match_all($pattern, $htmlData, $matches);
      if (is_array($matches)){
          foreach ($matches[1] as $key => $val){ $links[$val] = true; }
          if (is_array($links)) $data['links'] = array_keys($links);
          else $data['links'] = array();
      }
      $pattern = '/class="project-thumbnail-img" src="(.+)" \w/';
      preg_match_all($pattern, $htmlData, $matches);
      $data['images'] = str_replace("&amp;", "&", $matches[1]);      
      if (isset($data['links'])&&isset($data['images'])) {
        for ($j=0; $j< 20; $j++) {
          $link = "https://www.kickstarter.com".$data['links'][$j];
          if (strpos($link,"?") !== false) $link = substr($link, 0, strpos($link,"?"));
          $link_parts = explode("/", $link);
          $count_link_parts = count($link_parts);
          $project_check = Project::model()->find("link LIKE :link1  OR  link LIKE :link2  OR  link LIKE :link3 OR image LIKE :image",
                                                  array(':link1' => '%/' . $link_parts[$count_link_parts - 1],
                                                        ':link2' => $data['links'][$j], 
                                                        ':link3' => $link,
                                                        ':image' => $data['images'][$j]));
          
          if ($project_check) {
            $count = $count + 1;
          } // Counter for checking if it missed some project in the next few projects
          else {
            $count = 0;
            $htmlData = $web->getHtml($link, array());
            $data_single = $parsing->firstParsing($htmlData);
            //var_dump($data_single);die;
	    if ($data_single == false) { continue; }
            $insert = new Project;
            $insert->title = $data_single['title'];
            $insert->description = $data_single['description'];
            $insert->image = $data['images'][$j];
            $insert->link = $link;
            $insert->internal_link = toAscii($data_single['title']);
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data_single['category'], $link, ""); // ZAČASNO*****************************************************************
            $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
            if (isset($data_single['start_date']))
              $insert->start = $data_single['start_date'];
            if (isset($data_single['end_date']))
              $insert->end = $data_single['end_date'];
            if (isset($data_single['location']))
              $insert->location = $data_single['location'];
            if (isset($data_single['creator']))
              $insert->creator = $data_single['creator'];
            if (isset($data_single['created'])) {
              $insert->creator_created = $data_single['created'];
            }
            if (isset($data_single['backed']))
              $insert->creator_backed = $data_single['backed'];
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];

            $insert->save();

            $id_project = $insert->id;
	    // Category add
            $insert_category = new ProjectOrigcategory;
	    $insert_category->project_id = $id_project;
            $category = $this->checkCategory($data_single['category'], $link, "");
	    $insert_category->orig_category_id = $category->id;
	    $insert_category->save();

            // get rating 
            $KsRating = new KickstarterRating($link, $insert->id, $htmlData);
            $rating = $KsRating->firstAnalize();
            $insert->rating = $rating;
            $insert->save();
            
//	    print_r($insert->getErrors());
          }
          if ($count >= 30) {
            $check = true;
            break;
          }
        }
      }//else echo "NO";
      $i = $i + 1;
    }
  }

// Indiegogo store to DB
  public function actionIndiegogo() {
    $parsing = new IndiegogoParser();
    $web = new webText();
    $platform = Platform::model()->findByAttributes(array('name' => 'Indiegogo'));
    $id = $platform->id;
    $numberOfPages = 100;
    // false true
    $proxy_set = false;
    $link = "https://www.indiegogo.com/private_api/explore?experiment=true&filter_funding=&filter_percent_funded=&filter_quick=new&filter_status=&locale=en&per_page=$numberOfPages";
    $htmlData = $web->getHtml($link, array(), $proxy_set);
    $htmlDataSplit = explode('{"campaigns":', $htmlData);
    $htmlData = '{"campaigns":'.$htmlDataSplit[1];
    $json = html_entity_decode($htmlData);
    $jsonData = json_decode($json);
    if ($jsonData == null){ return false; }
    if (count($jsonData->campaigns)>$numberOfPages/2) {
        for ($j=0; $j<=count($jsonData->campaigns)-1; $j++) {
        $link = "https://www.indiegogo.com".$jsonData->campaigns[$j]->url;
        $image = $jsonData->campaigns[$j]->compressed_image_url;
        $title = $jsonData->campaigns[$j]->title;
        $project_check = Project::model()->find("link LIKE :link OR  image LIKE :image",
                                                array(':link' => $link, ':image' => $image));
        if (!$project_check) {
          $htmlData = $web->getHtml($link, array(), $proxy_set);
          $data_single = $parsing->firstParsing($htmlData);
	  if ($data_single == false) { continue; }
          $insert = new Project;
          $insert->title = $title;
          $insert->description = $data_single['description'];
          $insert->image = $image;
          $insert->link = $link;
          $insert->internal_link = toAscii($title);
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

// GoGetFunding store to DB
  public function actionGoGetFunding() {
    $parsing = new GoGetFundingParser();
    $web = new webText();
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name' => 'Go get funding'));
    $id = $platform->id;
    while (($i <= 10) and ($check == false)) {
      $result = $this->query("20e3c3aa-4727-477a-9b9e-4aa40e1c0ecd", array("webpage/url" => "http://gogetfunding.com/projects/index/page:" . $i . "/filter:recent_projects",), false);
    if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            $count = $count + 1;
          } // Counter for checking if it missed some project in the next few projects
          else {
            $htmlData = $web->getHtml($data->link, array());
            $data_single = $parsing->firstParsing($htmlData);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data->description;
            $insert->image = $data->image;
            $insert->link = $data->link;
            $insert->internal_link = toAscii($data->title);
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data->category, $data->link, ""); // ZAČASNO*****************************************************************
	    $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
            if (isset($data_single['end_date']))
              $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
            if (isset($data_single['location']))
              $insert->location = $data_single['location'];
            if (isset($data->creator))
              $insert->creator = $data->creator;
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];
            $insert->save();

            $id_project = $insert->id;
	    // Category add
            $insert_category = new ProjectOrigcategory;
	    $insert_category->project_id = $id_project;
            $category = $this->checkCategory($data->category, $data->link, "");
	    $insert_category->orig_category_id = $category->id;
	    $insert_category->save();

            $count = 0;
//	    print_r($insert->getErrors());
          }
          if ($count >= 10) {
            $check = true;
            break;
          }
        }
      }
      $i = $i + 1;
    }
  }

// PubSlush store to DB
/*  public function actionPubSlush() {
    $platform = Platform::model()->findByAttributes(array('name' => 'Pubslush'));
    $id = $platform->id;
    $result = $this->query("88be9a33-d1f6-4920-b81d-3b5394ce7a22", array("webpage/url" => "http://pubslush.com/discover/results/all-campaigns/all-categories/all-currencies/launch-date/"), false);
    if (isset($result->results)) {
      foreach ($result->results as $data) {
        $link_check = Project::model()->findByAttributes(array('link' => $data->link));
        if ($link_check) {          
        } else {
          $data_single = $this->parsePubSlush($data->link);
          $insert = new Project;
          $insert->title = $data->title;
          $insert->description = $data->description;
          $insert->image = $data->image;
          $insert->link = $data->link;
          $insert->time_added = date("Y-m-d H:i:s");
          $insert->platform_id = $id;
          $category = OrigCategory::model()->findByAttributes(array('name' => $data->category, 'category_id' => '24'));
          //$category = $this->checkCategory($data_single['category'], $data->link, "PubSlush");          
          
          $insert->orig_category_id = $category->id;
          if (isset($data->creator))
            $insert->creator = $data->creator;
          if (isset($data_single['goal']))
            $insert->goal = $data_single['goal'];
          if (isset($data_single['location']))
            $insert->location = $data_single['location'];
          //echo "deluje ";
          $insert->save();

          $id_project = $insert->id;
	  // Category add
          $insert_category = new ProjectOrigcategory;
	  $insert_category->project_id = $id_project;
	  $category = $this->checkCategory($data_single['category'], $data->link, "PubSlush");
	  $insert_category->orig_category_id = $category->id;
	  $insert_category->save();

//          print_r($insert->getErrors());
        }
      }
    }
  }*/

// FundAnything store in to DB
  public function actionFundAnything() {
    $parsing = new FundAnythingParser();
    $web = new webText();
    $i = 1;
    $platform = Platform::model()->findByAttributes(array('name' => 'Fund anything'));
    $id = $platform->id;
    while ($i <= 3) {
      $result = $this->query("61381ca5-efb9-4525-b384-63238681f1a7", array("webpage/url" => "http://fundanything.com/en/search/category?cat_id=29&page=" . $i,), false);
      if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            
          } // Counter for checking if it missed some project in the next few projects
          else {
            $htmlData = $web->getHtml($data->link, array());
            $data_single = $parsing->firstParsing($htmlData);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data_single['description'];
            $insert->image = $data->image;
            $insert->link = $data->link;
            $insert->internal_link = toAscii($data->title);
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data->category, $data->link, ""); // ZAČASNO*****************************************************************
            $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
            if (isset($data->location))
              $insert->location = $data->location;
            if (isset($data_single['creator']))
              $insert->creator = $data_single['creator'];
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];
            $insert->save();

            $id_project = $insert->id;
	    // Category add
            $insert_category = new ProjectOrigcategory;
	    $insert_category->project_id = $id_project;
            $category = $this->checkCategory($data->category, $data->link, "");
	    $insert_category->orig_category_id = $category->id;
	    $insert_category->save();

//	    print_r($insert->getErrors());
          }
        }
      }
      $i = $i + 1;
    }
  }

// FundRazr store in to DB
  public function actionFundRazr() {
    $parsing = new FundRazrParser();
    $web = new webText();
    $check = false;
    $count = 0;
    $i = 1;
    $preveri = false;
    $kategorije = array();
    $platform = Platform::model()->findByAttributes(array('name' => 'Fundrazr'));
    $id = $platform->id;
    while ($i <= 5) {
      $result = $this->query("ef6bf213-17bb-4b2e-8459-7cec991cb375", array("webpage/url" => "https://fundrazr.com/find?type=newest&page=" . $i,), false);
      if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            $count = $count + 1;
          } // Counter for checking if it missed some project in the next few projects
          else {
            $htmlData = $web->getHtml($data->link, array());
            $data_single = $parsing->firstParsing($htmlData);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data->description;
            //$insert->image = $data_single['image'];
            $insert->image = $data->image;
            $insert->link = $data->link;
            $insert->internal_link = toAscii($data->title);
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data_single['category'], $data->link, ""); // ZAČASNO*****************************************************************
	    $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
            if (isset($data_single['end_date']))
              $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
            if (isset($data->location))
              $insert->location = $data->location;
            if (isset($data->creator))
              $insert->creator = $data->creator;
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];
            if (isset($data_single['start_date']))
              $insert->start = date("Y-m-d H:i:s", strtotime($data_single['start_date']));
            if (isset($data_single['end_date']))
              $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
            $insert->save();

            $id_project = $insert->id;
	    // Category add
            $insert_category = new ProjectOrigcategory;
	    $insert_category->project_id = $id_project;
            $category = $this->checkCategory($data_single['category'], $data->link, "");
	    $insert_category->orig_category_id = $category->id;
	    $insert_category->save();

//	    print_r($insert->getErrors());
          }
          if ($count >= 10) {
            $check = true;
            break;
          }
        }
      }
      $i = $i + 1;
    }
  }

// PledgeMusic store to DB
    public function actionPledgeMusic(){
        $parsing = new PledgeMusicParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        $platform = Platform::model()->findByAttributes(array('name'=>'Pledge music'));
        $id = $platform->id;
        while (($i <= 10) and ($check == false)) {
            $htmlData = $web->getHtml("http://www.pledgemusic.com/projects/index/launched?page=$i", array());
            $pattern_link = '/(\/projects.+)\?referrer=launched/';
            $pattern_image = '/alt="Mobile" src="(.+)" \/>/';
            preg_match_all($pattern_link, $htmlData, $matches);
            $links = $matches[1];
            preg_match_all($pattern_image, $htmlData, $matches);
            $images = $matches[1];
            for ($j=0; $j< (count($links)-1); $j++) {
                $link = "http://www.pledgemusic.com".$links[$j];
                if (strpos($link,"?") !== false) $link = substr($link, 0, strpos($link,"?"));
                $link_parts = explode("/", $link);
                $count_link_parts = count($link_parts);
                $project_check = Project::model()->find("link LIKE :link1  OR  link LIKE :link2  OR  link LIKE :link3",
                                                          array(':link1' => '%/' . $link_parts[$count_link_parts - 1],
                                                                ':link2' => $links[$j], 
                                                                ':link3' => $link));
                if ($project_check) {$count = $count+1;} // Counter for checking if it missed some project in the next few projects
                else{
                    $htmlData = $web->getHtml($link, array());
                    $data_single = $parsing->firstParsing($htmlData);
                    $category_all = explode(', ', $data_single['category']);
                    $insert=new Project;
                    $insert->title=$data_single['title'];
                    $insert->description=$data_single['description'];
                    $insert->image=$images[$j];
                    $insert->link=$link;
                    $insert->time_added=date("Y-m-d H:i:s");
                    $insert->platform_id=$id;
                    $category = $this->checkCategory($data_single['category'][0], $link, ""); // ZAČASNO*****************************************************************
                    $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                    if (isset($data_single['creator'])) {
                        $insert->creator = $data_single['creator'];
                        $internal_link = $data_single['creator'];
                    }
                    if (isset($data_single['time'])) {
                      if ($data_single['time'] <> "In Progress"){
                        $insert->end=date("Y-m-d H:i:s", strtotime("+" . $data_single['time'] . "days"));
                      }
                    }
                    if (isset($data_single['location'])) $insert->location=$data_single['location'];
                    $internal_link .= " " . $data_single['title'];
                    $insert->internal_link = toAscii($internal_link);
                    $insert->save();

                    $id_project = $insert->id;
                    // Category add
                    for ($i=0; $i< count($category_all); $i++){
                      $insert_category = new ProjectOrigcategory;
                      $insert_category->project_id = $id_project;
                      $category = $this->checkCategory($category_all[$i], $link, "PledgeMusic");
                      $insert_category->orig_category_id = $category->id;
                      $insert_category->save();
                    }
                    $count = 0;
                    // print_r($insert->getErrors());
                }
                if ($count >= 40){ $check=true; break; }
                $i=$i+1;
            }
        }
    }
}