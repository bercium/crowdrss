<?php

class IndiegogoRating extends PlatformRating{
  
  function __construct($url, $id = null, $html=null) {
    $this->html = $html;
    $this->link = $url;
    $this->id = $id;
  }  
 
  // previous data collection
  protected function history(){
    // from DB load previous web
    return array();
  }
  
  // calculate rating
  protected function calcContentRating($webAgregtor){
    $rating = 0;
    $minRating = -7-6-4;
    $maxRating = 10+6+6+4+9+7+3+4+2;
    
    if ($webAgregtor["Bvideo"] == 1) $rating += 10; // has video
    //if ($tmpData["#images"] > 3) $rating_new += 3; // img
    if ($webAgregtor["#images"] > 7) $rating += 6; // img
    if ($webAgregtor["#images"] >= 11) $rating += 6; // 75% more than 8 imgs
    if ($webAgregtor["#images"] == 0){
      if ($webAgregtor["#subtitle"] > 4) $rating += 7; // subtitle
      else 
      if ($webAgregtor["#subtitle"] < 2) $rating -= 7; // no imgs or subtitles
    }
    
    if ($webAgregtor["#videos"] >= 2) $rating += 4; // has videos inside
    
    if ($webAgregtor["#wordsContent"] > 845) $rating += 9; // median description words
    if ($webAgregtor["#wordsContent"] > 1000) $rating += 7; // mean
    if ($webAgregtor["#wordsContent"] < 290) $rating -= 6; //
    if ($webAgregtor["#wordsContent"] < 87) $rating -= 4; // 25% without description
    
    //if ($tmpData["#personCreated"] > 1) $rating_new += 1; // created
    if ($webAgregtor["#teamMembers"] > 3) $rating += 3; // created
    if ($webAgregtor["#externalPages"] > 2) $rating += 4; // created
     
    if ($webAgregtor["#pledges"] > 6) $rating += 2; // created
    
    return round(($rating-$minRating)/($maxRating-$minRating)*10,3);  //normalize
  }
  

  //get all 
  protected function currentWebStatus(){
    $parsing = new IndiegogoParser();
    $web = new webText();
    // false true
    $proxy_set = false;
    $link = $this->link;
    $split_link = explode("/",$link);
    if (!$this->html){
      $this->html = $web->getHtml($link,"",$proxy_set); //load data if not loaded
    }
    $projectDescription = $web->getHtml("https://www.indiegogo.com/private_api/campaigns/$split_link[4]/description","",$proxy_set);
    $htmlData = $this->html;
    // check validity of data
    if (strpos($htmlData, "i-illustration-not_found")){
      $this->projectRemoved();
      return false;
    }
    $tmp = $parsing->ratingParser($htmlData, $projectDescription);
    return $tmp;
  }
}
