<?php
class UpdateCommand extends CConsoleCommand{
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
    $data['creator'] = $matches[1];

    //Date
    $pattern = '/<time class="js-adjust" data-format="ll" datetime="(.+)">/';
    preg_match_all($pattern, $htmlData, $matches);
    $data['start_date'] = $matches[1][0];
    $data['end_date'] = $matches[1][1];

/*    //Created
    $pattern = '//';
    preg_match($pattern, $htmlData, $matches);
    $data['created'] = $matches[1];

    //Backed
    $pattern = '//';
    preg_match($pattern, $htmlData, $matches);
    $data['backed'] = $matches[1];*/

    return($data);
  }

function parseIndiegogo($link){
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects(true);
//    $httpClient->enableAutoReferer(true);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($link);
    $htmlData = $htmlDataObject->httpBody;

//echo $htmlData;

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

    //Created
    $pattern = '//';
    preg_match($pattern, $htmlData, $matches);
    $data['created'] = $matches[1];

    $pattern = '/\d{4} \((.+)\)/';
    preg_match($pattern, $htmlData, $matches);
    $data['start_date'] .= $matches[1];
    $data['end_date'] .= $matches[1];

    //Backed
    $pattern = '//';
    preg_match($pattern, $htmlData, $matches);
    $data['backed'] = $matches[1];*/

    return($data);
  }


  public function actionKickstarter(){
    $i=1;
    $check=false;
    $count=1;
    $id_ks=1; // originalno prebrat iz baze
    while (($i <= 5) and ($check == false)) {
      $result = $this->query("c2adefcc-3a4a-4bf3-b7e1-2d8f4168a411", array("webpage/url" => "https://www.kickstarter.com/discover/advanced?page=" . $i . "&state=live&sort=launch_date",), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count=$count+1;}
	  else{
	    $data_single = $this->parseKickstarter($data->link);
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id_ks;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single['category']));
            $insert->orig_category_id=$category->id;
//	    $insert->type_of_funding=0;
	    if (isset($data_single['start_date'])) $insert->start=date(strtotime($data_single['start_date']));
	    if (isset($data_single['end_date'])) $insert->end=date(strtotime($data_single['end_date']));
	    if (isset($data_single['location'])) $insert->location=$data_single['location'];
	    if (isset($data_single['creator'])) $insert->creator=$data_single['creator'];
//	    if (isset($data_single[0]->created)){
//	      if ($data_single[0]->created == "First") { $created = 1; }
//	      else{ $created = $data_single[0]->created; }
//	      $insert->creator_created=$created;
//	    }
//	    if (isset($data_single[0]->backed)) $insert->creator_backed=$data_single[0]->backed;
	    if (isset($data_single['goal'])) $insert->goal=$data_single['goal'];
	    $insert->save();
//	    print_r($insert->getErrors());
          }
	  if ($count > 30){ $check=true; break; }
	}
      }
      $i=$i+1;
    }
  }

  public function actionIndiegogo(){
    $id_igg=2; // originalno prebrat iz baze
    $result = $this->query("de02d0eb-346b-431d-a5e0-cfa2463d086e", array("webpage/url" => "https://www.indiegogo.com/explore?filter_browse_balance=true&filter_quick=new&per_page=150",), false);
    if ($result->results) {
      foreach ($result->results as $data){
        $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
        if ($link_check){ $count=$count+1;}
        else{
	  $data_single = $this->parseIndiegogo($data->link);
          $insert=new Project;
          $insert->title=$data->title;
          $insert->description=$data->description;
          $insert->image=$data->image;
          $insert->link=$data->link;
          $insert->time_added=date("Y-m-d H:i:s");
          $insert->platform_id=$id_igg;
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
/*          if (isset($data_single[0]->location)) $insert->location=$data_single[0]->location;
          if (isset($data_single[0]->creator)) $insert->creator=$data_single[0]->creator;
          if (isset($data_single[0]->created)){
            if ($data_single[0]->created == "First") { $created = 1; }
            else{ $created = $data_single[0]->created; }
            $insert->creator_created=$created;
          }
          if (isset($data_single[0]->backed)) $insert->creator_backed=$data_single[0]->backed;*/
          $insert->save();
//          print_r($insert->getErrors());
        }
      }
    }
  }
}
