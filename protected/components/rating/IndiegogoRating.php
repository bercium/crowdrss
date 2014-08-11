<?php

class IndiegogoRating extends PlatformRating{
  
  function __construct($url, $html=null) {
    $this->html = $html;
    $this->link = $url;
  }  
  
  // full analize
  public function firstAnalize($id){
    $cws = $this->currentWebStatus();
    $rating = $this->calcWebRating($cws);
    
    // save to DB
    
    return $rating;
  }
  
  // full analize
  public function analize($id){
    $this->social();
    $cws = $this->currentWebStatus();
    $this->calcWebRating($cws);
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
  private function calcWebRating($webAgregtor){
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
    
    return round(($rating-$minRating)/($maxRating-$minRating)*9);  //normalize
  }
  

  //get all 
  private function currentWebStatus(){
    if (!$this->html) $this->getData();  //load data if not loaded
    $text = $this->html;
    
    return array();
  }


}
