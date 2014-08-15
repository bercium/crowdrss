<?php
abstract class PlatformRating {
  protected $html = null;
  protected $url = '';
  
  abstract public function firstAnalize($id);
  abstract public function analize($id);
  
  protected function getData($sufix = ''){
    $httpClient = new elHttpClient();
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    $htmlDataObject = $httpClient->get($this->url.$sufix);
    return $htmlDataObject->httpBody;
  }
  
  /**
   * get social from the web
   */
  protected function getSocial(){
    $social = new shareCount($this->url);
    $return = array();
    $return['twitter'] = $social->get_tweets();
    $return['twitter'] = $social->get_linkedin();
    $return['twitter'] = $social->get_fb();
    $return['twitter'] = $social->get_plusones();
    $return['twitter'] = $social->get_stumble();
    $return['twitter'] = $social->get_delicious();
    $return['twitter'] = $social->get_pinterest();
    
    return $return;
  }
  
}