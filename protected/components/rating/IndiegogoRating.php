<?php

class IndiegogoRating extends PlatformRating{
  
  function __construct($url, $html=null) {
    $this->html = $html;
    $this->link = $url;
  }  
  
  // full analize
  public function firstAnalize($id){
    $cws = $this->currentWebStatus();
    $rating = $this->calcContentRating($cws);
    
    // save to DB
    
    return $rating;
  }
  
  // full analize
  public function analize($id){
    $this->social();
    $cws = $this->currentWebStatus();
    $this->calcContentRating($cws);
    $this->history();
    //$this->rssRating();  // when we have enough clicks

    // save to DB
  }
  
  // previous data collection
  private function history($currentWeb){
    // from DB load previous web
    return array();
  }
  
  // calculate rating
  private function calcContentRating($webAgregtor){
    $rating = 0;
    $minRating = -7-6-4;
    $maxRating = 10+6+6+4+9+7+1+2;
    
    if ($tmpData["Bvideo"] == 1) $rating_new += 10; // has video
    //if ($tmpData["#images"] > 3) $rating_new += 3; // img
    if ($tmpData["#images"] > 7) $rating_new += 6; // img
    if ($tmpData["#images"] >= 11) $rating_new += 6; // 75% more than 8 imgs
    if ($tmpData["#images"] == 0){
      if ($tmpData["#subtitle"] > 4) $rating_new += 7; // subtitle
      else 
      if ($tmpData["#subtitle"] < 2) $rating_new -= 7; // no imgs or subtitles
    }
    
    if ($tmpData["#videos"] >= 2) $rating_new += 4; // has videos inside
    
    if ($tmpData["#wordsContent"] > 845) $rating_new += 9; // median description words
    if ($tmpData["#wordsContent"] > 1000) $rating_new += 7; // mean
    if ($tmpData["#wordsContent"] < 290) $rating_new -= 6; //
    if ($tmpData["#wordsContent"] < 87) $rating_new -= 4; // 25% without description
    
    //if ($tmpData["#personCreated"] > 1) $rating_new += 1; // created
    if ($tmpData["#teamMembers"] > 3) $rating_new += 1; // created
     
    if ($tmpData["#pledges"] > 6) $rating_new += 2; // created
    
    return round(($rating-$minRating)/($maxRating-$minRating)*9);  //normalize
  }
  

  //get all 
  private function currentWebStatus(){
    if (!$this->html) $this->getData();  //load data if not loaded
    $text = $this->html;
    
    return array();
  }


}
