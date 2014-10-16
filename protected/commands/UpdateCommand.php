<?php

//set_time_limit(60*5); //5 min
class UpdateCommand extends CConsoleCommand {

// Import.io function to get jason result for a webpage
  function query($connectorGuid, $input) {
    $url = "https://api.import.io/store/connector/" . $connectorGuid . "/_query?_user=" . urlencode("3e956d8d-5d7f-4595-927e-99ad6b078fe9") . "&_apikey=" . urlencode("cEPYMPY1DTVWS7BFw1oS4N44c/khsNvs9W8vEz8AQ7ytgQr3B6uvEXqOEzGTmyDqmNqlCoKcqmyz2TbQJThtVA==");
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
  function checkCategory($category_check, $link){
    $category_check = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $category_check);
    $category = OrigCategory::model()->findByAttributes(array('name' => $category_check));
    if ($category) {
      return $category;
    } else {
      $updateOrigCategory = new OrigCategory();
      $updateOrigCategory->name = $category_check;
      $updateOrigCategory->save();
      $category = OrigCategory::model()->findByAttributes(array('name' => $category_check));
      $this->errorMail($link, $category_check, $category->id);
      return $category;
    }
  }

// Function for geting HTML data
  function getHtml($link, $header) {
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects();
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link, $header);
    return $htmlDataObject->httpBody;
  }

// Parser for KS
  function parseKickstarter($htmlData) {

    $pattern = '/window.current_project = "(.+)";/'; 
    preg_match($pattern, $htmlData, $match);
    $json = html_entity_decode($match[1]);
    $json = str_replace('\\"', "\'", $json);
    $jsonData = json_decode($json);
    if ($jsonData == null){ return false; }
    
    // Description
    $data['description'] = $jsonData->blurb;
    
    // Title
    $data['title'] = $jsonData->name;
    
    // Goal
    $money = Yii::app()->numberFormatter->formatCurrency($jsonData->goal, $jsonData->currency);
    $money_split = explode(".", $money);
    if ($money_split[1] == "00") {$data['goal'] = $money_split[0];
    } else {$data['goal'] = $money;}

    // Location
    $data['location'] = $jsonData->{'location'}->{'displayable_name'};

    // Category
    $data['category'] = $jsonData->{'category'}->{'name'};

    // Creator
    $data['creator'] = $jsonData->{'creator'}->{'name'};

    // Date
    $data['start_date'] = date("Y-m-d H:i:s", $jsonData->{'launched_at'});
    $data['end_date'] = date("Y-m-d H:i:s", $jsonData->{'deadline'});

    // Created
    $pattern = '/<span class="text">\s(.+) created/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])){
      if ($matches[1] == "First") {
        $data['created'] = 1;
      } else {
        $pattern = '/\/created">(.+) created/';
        preg_match($pattern, $htmlData, $fixedMatches);
	if (isset($matches[1])){
	  $data['created'] = $fixedMatches[1];
	}
      }
    }

    // Backed
    $pattern = '/span>\s(.+) backed/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])){
      if ($matches[1] == "0") {
        $data['backed'] = $matches[1];
      } else {
        $pattern = '/\/backed">(.+) backed/';
        preg_match($pattern, $htmlData, $fixedMatches);
	if (isset($matches[1])){
          $data['backed'] = $fixedMatches[1];
	}
      }
    }
    return($data);
  }

// Parser for IGG
  function parseIndiegogo($htmlData) {
    
    $pattern = '/var utag_data = (.+);/'; 
    preg_match($pattern, $htmlData, $match);
    $json = html_entity_decode($match[1]);
    $json = str_replace('\\"', "\'", $json);
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

// Parser for GGF
  function parseGoGetFunding($link) {
    $htmlData = $this->getHtml($link, array());

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
  function parsePubSlush($link) {
    $htmlData = $this->getHtml($link, array());

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
  function parseFundAnything($link) {
    $htmlData = $this->getHtml($link, array());

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
  function parseFundRazr($link) {
    $htmlData = $this->getHtml($link, array());

    // Goal
    $pattern = '/raised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) {
      $data['goal'] = $matches[1];
    } else {
      $data['goal'] = NULL;
    }

    // Image
    $pattern = '/<meta property="og:image" content="(.+)" \/>/';
    preg_match($pattern, $htmlData, $matches);
    $data['image'] = $matches[1];

    // Category
    $pattern = '/"category":"(.+)","commentsEnabled/';
    preg_match($pattern, $htmlData, $matches);
    $data['category'] = $matches[1];

    // Start date
    $pattern = '/Launched (.+)</';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) {
      $data['start_date'] = $matches[1];
    } else {
      $data['start_date'] = NULL;
    }

    // End date
    $pattern = '/Ends (.+) at(.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) {
      $data['end_date'] = $matches[1] . $matches[2];
    } else {
      $data['end_date'] = NULL;
    }

    return($data);
  }

// Parser for PM
  function parsePledgeMusic($link) {
    $htmlData = $this->getHtml($link, array());

    // Goal
    $pattern = '/raised of (.+) goal/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) {
      $data['goal'] = $matches[1];
    } else {
      $data['goal'] = NULL;
    }

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
    if (isset($matches[1])) {
      $data['start_date'] = $matches[1];
    } else {
      $data['start_date'] = NULL;
    }

    // End date
    $pattern = '/Ends (.+) at(.+)<\/span>/';
    preg_match($pattern, $htmlData, $matches);
    if (isset($matches[1])) {
      $data['end_date'] = $matches[1] . $matches[2];
    } else {
      $data['end_date'] = NULL;
    }

    return($data);
  }

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
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name' => 'Kickstarter'));
    $id = $platform->id;
    while (($i <= 50) and ($check == false)) { 
      $link = "https://www.kickstarter.com/discover/advanced?page=$i&state=live&sort=newest";
      $htmlData = $this->getHtml($link, array());
      $pattern = '/<a href="(.+).ref=discovery" target="">/';
      preg_match_all($pattern, $htmlData, $matches);
      $data['links'] = $matches[1];
      $pattern = '/<img alt="Project image".+src="(.+)" width=/';
      preg_match_all($pattern, $htmlData, $matches);
      $data['images'] = $matches[1];
      
      if (isset($data['links'])&&isset($data['images'])) {
        
        for ($j=0; $j< 20; $j++) {
          $link = "https://www.kickstarter.com".$data['links'][$j];
          //echo $link."\n";
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
            $htmlData = $this->getHtml($link, array());
            $data_single = $this->parseKickstarter($htmlData);
	    if ($data_single == false) { continue; }
            $insert = new Project;
            $insert->title = $data_single['title'];
            $insert->description = $data_single['description'];
            $insert->image = $data['images'][$j];
            $insert->link = $link;
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data_single['category'], $link);
            $insert->orig_category_id = $category->id;
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
    $platform = Platform::model()->findByAttributes(array('name' => 'Indiegogo'));
    $id = $platform->id;
    $numberOfPages = 2000;
    $link = "https://www.indiegogo.com/explore?filter_browse_balance=true&filter_quick=new&per_page=$numberOfPages";
    $htmlData = $this->getHtml($link, array());
    $pattern = '/<a href="(.+)" class="i-project">/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['links'] = $matches[1];
    $pattern = '/<div class="i-img" data-src="(.+)">/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['images'] = $matches[1];
    if (isset($data['links'])&&isset($data['images'])) {
        for ($j=0; $j< $numberOfPages; $j++) {
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
          //  echo $link;
          $htmlData = $this->getHtml($link, array());
          $htmlData .= $this->getHtml($link . "/show_tab/home", array("X-Requested-With" => "XMLHttpRequest"));
          $data_single = $this->parseIndiegogo($htmlData);
	  if ($data_single == false) { continue; }
          $insert = new Project;
          $insert->title = $data_single['title'];
          $insert->description = $data_single['description'];
          $insert->image = $data['images'][$j];
          $insert->link = $link;
          $insert->time_added = date("Y-m-d H:i:s");
          $insert->platform_id = $id;
          $category = $this->checkCategory($data_single['category'], $link);
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
    $i = 1;
    $check = false;
    $count = 0;
    $platform = Platform::model()->findByAttributes(array('name' => 'Go get funding'));
    $id = $platform->id;
    while (($i <= 10) and ($check == false)) {
      $result = $this->query("4b6e0d90-3728-4135-b308-560d238de82b", array("webpage/url" => "http://gogetfunding.com/projects/index/page:" . $i . "/filter:recent_projects",), false);
    if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            $count = $count + 1;
          } // Counter for checking if it missed some project in the next few projects
          else {
            $data_single = $this->parseGoGetFunding($data->link);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data->description;
            $insert->image = $data->image;
            $insert->link = $data->link;
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data->category, $data->link);
	    $insert->orig_category_id = $category->id;
            if (isset($data_single['end_date']))
              $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
            if (isset($data_single['location']))
              $insert->location = $data_single['location'];
            if (isset($data->creator))
              $insert->creator = $data->creator;
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];
            $insert->save();
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
  public function actionPubSlush() {
    $platform = Platform::model()->findByAttributes(array('name' => 'Pubslush'));
    $id = $platform->id;
    $result = $this->query("88be9a33-d1f6-4920-b81d-3b5394ce7a22", array("webpage/url" => "http://pubslush.com/discover/results/current/all-categories/all-currencies/launch-date/"), false);
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
          $insert->orig_category_id = $category->id;
          if (isset($data->creator))
            $insert->creator = $data->creator;
          if (isset($data_single['goal']))
            $insert->goal = $data_single['goal'];
          if (isset($data_single['location']))
            $insert->location = $data_single['location'];
          //echo "deluje ";
          $insert->save();
//          print_r($insert->getErrors());
        }
      }
    }
  }

// FundAnything store in to DB
  public function actionFundAnything() {
    $i = 1;
    $platform = Platform::model()->findByAttributes(array('name' => 'Fund anything'));
    $id = $platform->id;
    while ($i <= 3) {
      $result = $this->query("2f7d701e-5144-430d-b0f2-a8c6517a4dc7", array("webpage/url" => "http://fundanything.com/en/search/category?cat_id=29&page=" . $i,), false);
      if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            
          } // Counter for checking if it missed some project in the next few projects
          else {
            $data_single = $this->parseFundAnything($data->link);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data_single['description'];
            $insert->image = $data->image;
            $insert->link = $data->link;
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data->category, $data->link);
            $insert->orig_category_id = $category->id;
            if (isset($data->location))
              $insert->location = $data->location;
            if (isset($data_single['creator']))
              $insert->creator = $data_single['creator'];
            if (isset($data_single['goal']))
              $insert->goal = $data_single['goal'];
            $insert->save();
//	    print_r($insert->getErrors());
          }
        }
      }
      $i = $i + 1;
    }
  }

// FundRazr store in to DB
  public function actionFundRazr() {
    $check = false;
    $count = 0;
    $i = 1;
    $preveri = false;
    $kategorije = array();
    $platform = Platform::model()->findByAttributes(array('name' => 'Fundrazr'));
    $id = $platform->id;
    while ($i <= 5) {
      $result = $this->query("ad4abdf0-64f8-4ab8-9cbf-12f8f40605d9", array("webpage/url" => "https://fundrazr.com/find?type=newest&page=" . $i,), false);
      if (isset($result->results)) {
        foreach ($result->results as $data) {
          $link_check = Project::model()->findByAttributes(array('link' => $data->link));
          if ($link_check) {
            $count = $count + 1;
          } // Counter for checking if it missed some project in the next few projects
          else {
            $data_single = $this->parseFundRazr($data->link);
            $insert = new Project;
            $insert->title = $data->title;
            $insert->description = $data->description;
            $insert->image = $data_single['image'];
            $insert->link = $data->link;
            $insert->time_added = date("Y-m-d H:i:s");
            $insert->platform_id = $id;
            $category = $this->checkCategory($data_single['category'], $data->link);
	    $insert->orig_category_id = $category->id;
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
    $category = $this->checkCategory($data_single['category'], $data->link);
    $insert->orig_category_id = $category->id;
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
