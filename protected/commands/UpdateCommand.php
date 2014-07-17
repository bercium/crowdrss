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

  public function actionKickstarter(){
    $i=1;
    $check=false;
    while (($i <= 55555) and ($check == false)) {
      $result = $this->query("c2adefcc-3a4a-4bf3-b7e1-2d8f4168a411", array("webpage/url" => "https://www.kickstarter.com/discover/advanced?page=" . $i . "&state=live&sort=launch_date",), false);
      if ($result->results) {
        foreach ($result->results as $data){
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){
	    $check=true;
	    break;
	  }else{
	    $result_single = $this->query("c6cf42d9-6e28-440a-9cde-6f31a810f298", array("webpage/url" => $data->link,), false);
	    $data_single = $result_single->results[0];
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
	    if (isset($data_single->start_date)) $insert->start=date("Y-m-d H:i:s",strtotime($data_single->start_date));
	    if (isset($data_single->end_date)) $insert->end=date("Y-m-d H:i:s",strtotime($data_single->end_date));
	    if (isset($data_single->location)) $insert->location=$data_single->location;
	    if (isset($data_single->creator)) $insert->creator=$data_single->creator;
	    if (isset($data_single->created)){
	      if ($data_single->created == "First") { $created = 1; }
	      else{ $created = $data_single->created; }
	      $insert->creator_created=$created;
	    }
	    if (isset($data_single->backed)) $insert->creator_backed=$data_single->backed;
	    if (isset($data_single->goal)) $insert->goal=$data_single->goal;
//	    if (isset($data_single->start_date)) $insert->type_of_funding=0;
	    $insert->time_added=date("Y-m-d H:i:s");
	    $insert->platform_id=1;
	    $insert->category_id=1;
	    $insert->save();
//	    print_r($insert->getErrors());
          }
	}
      }
      $i++;
    }
  }

/*  public function actionIndiegogo(){
    $result = query("de02d0eb-346b-431d-a5e0-cfa2463d086e", array("webpage/url" => "https://www.indiegogo.com/explore?filter_browse_balance=true&filter_quick=new&per_page=150",), false);
    if ($result->results)
    foreach ($result->results as $data){
      $data_tmp['link'] = $data->link;
      if (compareToDb($data_tmp['link']) == true) then { break; }
      $data_tmp['image'] = $data->image;
      $data_tmp['title'] = $data->title;
      $data_tmp['description'] = $data->description;
    }
  }*/
}
