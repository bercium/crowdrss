<?php
abstract class PlatformRating {
  protected $html = null;
  protected $link = '';
  protected $id = null;
  
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
      else break;
      $i++;
    }
    
    if ($cws === false) return null;
    $rating = $this->calcContentRating($cws);
    
    $social = null;
    $social =  $this->getSocial();
    
    $social_rating = 0;
    if ($this->id != null && false){   //!!! skip for now
      $project = Project::model()->findByPk($this->id);
      
      $social_rating = $this->calcSocialRating($social,$project);
      
      //$numOfHFromStart = timeDifference($project->time_added, time(),'hour');
      //$likes = ($social['all']/$numOfHFromStart)*24; // avg how many likes in a day
      
      //progress
      
      //$this->history($cws, $social);
    

    /* overall rating:
    CONTENT
    ABSOLUTE SOCIAL (koliko like-ov shareov v sestevku)

    RELATIVE CONTENT project progress.. zbranih sredstev, komentarjev itd
    RELATIVE SOCIAL (koliko loke-ov  relativno na prejšni dan)  progress
     
    */
      $rating = $rating*0.8 + $social_rating*0.2;
    }
    // save to DB
    $this->saveRating($cws, $social);
    
    return $rating;
  }
  
  /**
   * calculate social rating
   */
  private function calcSocialRating($social,$project){
    $rating = 0;
    
    $h_lapsed = timeDifference($project->time_added,time(),"hour");
      
    // less than 3 hours statisticaly too little
    if ($h_lapsed < 3) continue;  // hard to evaluate project this young
      
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
  
  
 /* private function history($cws, $social){
    $socialRating = $social['all'];
    
  }*/
  
}