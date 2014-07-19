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
    $count=1;
    $id_ks=1; // originalno prebrat iz baze
    while (($i <= 5) and ($check == false)) {
      $result = $this->query("c2adefcc-3a4a-4bf3-b7e1-2d8f4168a411", array("webpage/url" => "https://www.kickstarter.com/discover/advanced?page=" . $i . "&state=live&sort=launch_date",), false);
      if ($result->results) {
        foreach ($result->results as $data){
          sleep(2);
          $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
          if ($link_check){ $count=$count+1;}
	  else{
	    $result_single = $this->query("c6cf42d9-6e28-440a-9cde-6f31a810f298", array("webpage/url" => $data->link,), false);
	    if (isset($result_single)) $data_single = $result_single->results;
	    $insert=new Project;
	    $insert->title=$data->title;
	    $insert->description=$data->description;
	    $insert->image=$data->image;
	    $insert->link=$data->link;
            $insert->time_added=date("Y-m-d H:i:s");
            $insert->platform_id=$id_ks;
	    $category = OrigCategory::model()->findByAttributes(array('name'=>$data_single[0]->category));
            $insert->orig_category_id=$category->id;
//	    $insert->type_of_funding=0;
	    if (isset($data_single[0]->start_date)) $insert->start=date("Y-m-d H:i:s",strtotime($data_single[0]->start_date));
	    if (isset($data_single[0]->end_date)) $insert->end=date("Y-m-d H:i:s",strtotime($data_single[0]->end_date));
	    if (isset($data_single[0]->location)) $insert->location=$data_single[0]->location;
	    if (isset($data_single[0]->creator)) $insert->creator=$data_single[0]->creator;
	    if (isset($data_single[0]->created)){
	      if ($data_single[0]->created == "First") { $created = 1; }
	      else{ $created = $data_single[0]->created; }
	      $insert->creator_created=$created;
	    }
	    if (isset($data_single[0]->backed)) $insert->creator_backed=$data_single[0]->backed;
	    if (isset($data_single[0]->goal)) $insert->goal=$data_single[0]->goal;
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
	sleep(2);
        $link_check = Project::model()->findByAttributes(array('link'=>$data->link));
        if ($link_check){ $count=$count+1;}
        else{
          $result_single = $this->query("d3239d9f-f333-4201-b1d8-e84fc4385606", array("webpage/url" => $data->link,), false);
          if (isset($result_single)) $data_single = $result_single->results;
          $insert=new Project;
          $insert->title=$data->title;
          $insert->description=$data->description;
          $insert->image=$data->image;
          $insert->link=$data->link;
          $insert->time_added=date("Y-m-d H:i:s");
          $insert->platform_id=$id_igg;
          $category = OrigCategory::model()->findByAttributes(array('name'=>$data->category));
          $insert->orig_category_id=$category->id;
          if (isset($data_single[0]->start_date)) $insert->start=date("Y-m-d H:i:s",strtotime($data_single[0]->start_date));
          if (isset($data_single[0]->end_date)) $insert->end=date("Y-m-d H:i:s",strtotime($data_single[0]->end_date));
          if (isset($data_single[0]->goal)) $insert->goal=$data_single[0]->goal;
          if (isset($data_single[0]->type_of_funding)){
            if ($data_single[0]->type_of_funding == "Fixed Funding") {$typeOfFunding = 0;}
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
