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
    $return['stumble'] = 0;
    $return['delicious'] = 0;
    $return['pinterest'] = 0;
    return $return;
  }

  /**
   * save into DB
   */
  protected function saveRating($cws,$social = null){
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
    $cws = $this->currentWebStatus();
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
    $cws = $this->currentWebStatus();
    if ($cws === false) return null;
    $rating = $this->calcContentRating($cws);
    
    
    $social =  $this->getSocial();
    
    //$ows = $this->history();
    
    //$this->calculateOverallRating($cws, $social, $ows);
    //$this->rssRating();  // when we have enough clicks


    /* overall rating:
    CONTENT
    ABSOLUTE SOCIAL (koliko like-ov shareov v sestevku)

    RELATIVE CONTENT project progress.. zbranih sredstev, komentarjev itd
    RELATIVE SOCIAL (koliko loke-ov  relativno na prejšni dan)  progress
     
    */
    // save to DB
    $this->saveRating($cws, $social);
    
    return $rating;
  }
    
  
}