<?php

class KickstarterRating extends PlatformRating{
  
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
    $minRating = -7-6-4-3;
    $maxRating = 10+6+6+4+9+7+3+2+2+1+2;
    
    //*
    if ($webAgregtor["Bvideo"] == 1) $rating += 10; // has video
    //if ($tmpData["#images"] > 3) $rating_new += 3; // img
    if ($webAgregtor["#images"] > 7) $rating += 6; // img
    if ($webAgregtor["#images"] >= 13) $rating += 6; // 75% more than 8 imgs
    if ($webAgregtor["#images"] == 0){
      if ($webAgregtor["#subtitles"] > 4) $rating += 7; // subtitle
      else 
      if ($webAgregtor["#subtitles"] < 2) $rating -= 7; // no imgs or subtitles
    }
    
    if ($webAgregtor["#videos"] >= 2) $rating += 4; // has videos inside
    
    if ($webAgregtor["#wordsContent"] > 520) $rating += 9; // description words
    if ($webAgregtor["#wordsContent"] > 700) $rating += 7; // 75%
    if ($webAgregtor["#wordsContent"] < 220) $rating -= 6; //
    if ($webAgregtor["#wordsContent"] < 87) $rating -= 4; // 25% without description
    
    if ($webAgregtor["#wordsRisk"] > 120) $rating += 3; // risk words
    if ($webAgregtor["#wordsRisk"] < 64) $rating -= 3; // risk words min
    
    if ($webAgregtor["#personBacked"] >= 2) $rating += 2; // backed
    if ($webAgregtor["#personBacked"] >= 7) $rating += 2; // backed
    
    if ($webAgregtor["#personCreated"] > 1) $rating += 1; // created
     
    if ($webAgregtor["#pledges"] > 7) $rating += 2; // created
    
    return round(($rating-$minRating)/($maxRating-$minRating)*10,3);  //normalize
  }
  

  //get all 
  protected function currentWebStatus(){
    $parsing = new KickstarterParser();
    $web = new webText();
    $link = $this->link;
    if (!$this->html) $this->html = $web->getHtml($link);  //load data if not loaded
    $htmlData = $this->html;
    $tmp = false;
    if ($htmlData) {
        if (strlen($htmlData) > 2000){
            $tmp = $parsing->ratingParser($htmlData);    
        }
    }
    return $tmp;
  }

}
