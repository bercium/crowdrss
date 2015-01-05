<?php
abstract class PlatformRating {
  protected $html = null;
  protected $link = '';
  protected $id = null;
  public $save = true;
  
  abstract protected function history();
  abstract protected function calcContentRating($webAgregtor);
  abstract protected function currentWebStatus();

  
  
  /**
   * get web data
   */
  protected function getData($sufix = '', $headers = array()){
    $httpClient = new elHttpClient();
    $httpClient->setUserAgent("ff3");
    $httpClient->enableRedirects();
    $httpClient->setHeaders(array_merge(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")));
    $htmlDataObject = $httpClient->get($this->link.$sufix,$headers);
    return $htmlDataObject->httpBody;
  }
  
  /**
   * get social from the web
   */
  protected function getSocial(){
    $social = new ShareCount($this->link);
    $return = array();
    $return['twitter'] = $social->get_tweets();
    $return['linkedin'] = $social->get_linkedin();
    $return['facebook'] = $social->get_fb();
    $return['google'] = $social->get_plusones();
    //$return['stumble'] = $social->get_stumble();
    //$return['delicious'] = $social->get_delicious();
    //$return['pinterest'] = $social->get_pinterest();
    $all = 0;
    foreach ($return as $rs){
      $all += $rs; 
    }
    $return['all'] = $all;
    return $return;
  }
  
  /**
   * return empty social data
   */
  private function emptySocial(){
    $return['twitter'] = 0;
    $return['linkedin'] = 0;
    $return['facebook'] = 0;
    $return['google'] = 0;
    //$return['stumble'] = 0;
    //$return['delicious'] = 0;
    //$return['pinterest'] = 0;
    $return['all'] = 0;
    return $return;
  }

  /**
   * save into DB
   */
  protected function saveRating($cws, $social = null){
    if (!$this->save) return;
    if ($this->id == null) return;
    
    if ($social == null) $social = $this->emptySocial();
    
    $rh = new RatingHistory();
    $rh->project_id = $this->id;
    $rh->data = json_encode(array("cws"=>$cws, "social"=>$social));
    $rh->save();
  }
  
 
  /**
   * first analize
   */
  public function firstAnalize(){
    $i = 0;
    while ($i < 3){
      $cws = $this->currentWebStatus();
      if ($cws === false){
        $this->html = null;
        usleep(100000+rand(30,120)*1000);  //1 000 000 = 1 sec
      }
      else break;
      $i++;
    }
    
    if ($cws === false) return null;
    $rating = $this->calcContentRating($cws);
    // save to DB
    $this->saveRating($cws);
    
    return $rating;
  }
  
  /**
   * every other analyze
   */
  public function analize(){
    $i = 0;
    while ($i < 3){
      $cws = $this->currentWebStatus();
      if ($cws === false) usleep(100000+rand(30,120)*1000);  //1 000 000 = 1 sec
      else{
        $this->projectRemoved();  // if fails 3 times but have succeded before there must be something wrong
        break;
      }
      $i++;
    }
    $detail = array();

    if ($cws === false) return null;
    $rating = $this->calcContentRating($cws);
    $detail['cws']['rating'] = $rating;
    $detail['cws']['cws'] = $cws;
    
    $social = null;
    $social =  $this->getSocial();
    $detail['social']['social'] = $social;
    
    $social_rating = 0;
    if ($this->id != null){   //!!! skip for now
      $project = Project::model()->findByPk($this->id);
      
      $social_rating = $this->calcSocialRating($social,$project->time_added);
      $detail['social']['rating'] = $social_rating;
      
      
      //$numOfHFromStart = timeDifference($project->time_added, time(),'hour');
      //$likes = ($social['all']/$numOfHFromStart)*24; // avg how many likes in a day
      
      //progress
      $money_rating = $this->calcProgressRating($cws, $project);
      $detail['money']['money']['goal'] = $project->goal;
      $detail['money']['money']['time_added'] = $project->time_added;
      $detail['money']['rating'] = $money_rating;
    

      /* overall rating:
      CONTENT
      ABSOLUTE SOCIAL (koliko like-ov shareov v sestevku)
      RELATIVE CONTENT project progress.. zbranih sredstev, komentarjev itd
      RELATIVE SOCIAL (koliko loke-ov  relativno na prejšni dan)  progress

      */
      $rating = $rating*0.60 + $social_rating*0.25 + $money_rating*0.15;
    }
    // save to DB
    $this->saveRating($cws, $social);
    
    $detail['rating'] = $rating;
    if ($this->save) return $rating;
    else return $detail;
  }
  
  /**
   * calculate social rating
   */
  private function calcSocialRating($social,$timeAdded){
    $rating = 0;
    
    $h_lapsed = timeDifference($timeAdded,time(),"hour");
      
    // less than 3 hours statisticaly too little
    if ($h_lapsed < 3) return 0;  // hard to evaluate project this young
      
    $all = 0;
    if (!isset($social['all'])) {
      foreach ($social as $rs){
        $all += $rs; 
      }
    }else $all = $social['all'];
      
    $all = ($all / $h_lapsed)*24;  // per hour
    
      
    if ($all >= 391.86) $rating++;
    if ($all >= 278.88) $rating++;
    if ($all >= 170.32) $rating++;
    if ($all >= 108.17) $rating++;
    if ($all >= 65.08) $rating++;
    if ($all >= 39) $rating++;
    if ($all >= 22.76) $rating++;
    if ($all >= 11.45) $rating++;
    if ($all >= 3.3) $rating++;
    if ($all >= 0.15) $rating++;   
    
    // max 10
    return $rating;
  }
  
  /**
   * calculate social rating
   */
  private function calcProgressRating($cws, $project){
    $rating = 0;
    
    $h_lapsed = timeDifference($project->time_added,time(),"hour");
    
    // less than 3 hours statisticaly too little
    if ($h_lapsed < 3) return 0;  // hard to evaluate project this young
    
    
    $g = filter_var($project->goal, FILTER_SANITIZE_NUMBER_INT);
    $r = filter_var($cws['$raised'], FILTER_SANITIZE_NUMBER_INT);
    
    $p = ($r/$g);
    
    //30% in first week  better chance
            
    $sp = 0;  // sucess probability
    if ($g < 10000){
      if ($p > 0.05) $sp = 70;
      if ($p > 0.1) $sp = 78;
      if ($p > 0.15) $sp = 80;
      if ($p > 0.2) $sp = 84;
      if ($p > 0.25) $sp = 87;
      if ($p > 0.35) $sp = 90;
      if ($p > 0.57) $sp = 95;
      if ($p > 0.99) $sp = 98;
      if ($p > 1) $sp = 100;
    }else
    if ($g < 100000){
      if ($p > 0.05) $sp = 52;
      if ($p > 0.1) $sp = 62;
      if ($p > 0.15) $sp = 68;
      if ($p > 0.2) $sp = 74;
      if ($p > 0.25) $sp = 79;
      if ($p > 0.35) $sp = 85;
      if ($p > 0.45) $sp = 90;
      if ($p > 0.65) $sp = 95;
      if ($p > 0.99) $sp = 98;
      if ($p > 1) $sp = 100;
    }
    else{
      if ($p > 0.05) $sp = 26;
      if ($p > 0.1) $sp = 37;
      if ($p > 0.15) $sp = 44;
      if ($p > 0.2) $sp = 49;
      if ($p > 0.25) $sp = 55;
      if ($p > 0.35) $sp = 65;
      if ($p > 0.45) $sp = 74;
      if ($p > 0.65) $sp = 80;
      if ($p > 0.95) $sp = 88;
      if ($p > 1) $sp = 100;
    }
    
    $rating = $sp / 10; //10 points
    
    $mp = 1; //multiplier
    if ($h_lapsed <= 24) $mp = 1.3;
    else
    if ($h_lapsed <= 48) $mp = 1.2;
    //if ($h_lapsed <= 144) $mp = 1.1;
    $rating = $rating * $mp;
    
    if ($rating > 10) $rating = 10;
    
      /*
    if (($p >= 0.2) && ($h_lapsed <= 48)) $rating++;
    if (($p >= 0.3) && ($h_lapsed <= 48)) $rating++;
    if (($p >= 0.45) && ($h_lapsed <= 48)) $rating++;
    if (($p >= 0.30) && ($h_lapsed <= 24)) $rating++;
    if (($p <= 0.25) && ($h_lapsed >= 144)) $rating--;  // after 6 days not even 30%
    */

    
    // max 10
    return $rating;
  }
  
  /**
   * mark project as removed
   */
  protected function projectRemoved($checkLink = false){
    if ($this->id) {
      $update = Project::model()->findByPk($this->id);
      if ($update){
        // check for page
        if ($checkLink){
          if (false){
            $update->removed=1;
            $update->save();
          }
        }else{
          $update->removed=1;
          $update->save();
        }
      }
      
    }
  }
  
  
 /* private function history($cws, $social){
    $socialRating = $social['all'];
    
  }*/
  
}