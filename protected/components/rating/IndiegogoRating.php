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
    if (!$this->html){
      $this->html = $this->getData();  //load data if not loaded
      $this->html .= $this->getData("/home");  //load secondary data if not loaded
    }
    $text = $this->html;
    
    // Words Full Description 
    $beginingPosition = strpos($text, 'class="i-description');
    $endPosition = strpos($text, '<div class="i-lined-header">');
    $endPosition = $endPosition - $beginingPosition;
    $description = substr($text, $beginingPosition, $endPosition);
    $beginingPosition = strpos($description, '>');
    $description = substr($description, $beginingPosition);
    $descriptionNumber = str_word_count(strip_tags($description));
    $tmp['#wordsContent'] = $descriptionNumber;

    // Image number
    $imageNumber = substr_count($description, '<img');
    $tmp['#images'] = $imageNumber;

    // Subtitles Number
    $subtitlesNumber = substr_count($description, '</h1>');
    $subtitlesNumber = substr_count($description, '</h2>');
    $subtitlesNumber = substr_count($description, '</h3>');
    $tmp['#subtitle'] = $subtitlesNumber;

    // Video number
    $videoNumber = substr_count($description, '</embed>');
    $videoNumber += substr_count($description, '</video>');
    $videoNumber += substr_count($description, '</iframe>');
    $tmp['#videos'] = $videoNumber; 

    // Money
    $pattern = '/raised of <span class="currency"><span>(.+)<\/span><\/span> goal/';
    preg_match($pattern, $text, $matches);
    $money = str_replace(',', '', $matches[1]);
    $pattern = '/\d+/';
    preg_match($pattern, $money, $matches);
    $money = $matches[0];
    $pattern = '/span><em>(.+)<\/em>/';
    preg_match($pattern, $text, $matches);
    switch ($matches[1]) {
      case "GBP": $convert = 1.69; break; 
      case "EUR": $convert = 1.34; break; 
      case "AUD": $convert = 0.93; break;
      case "CAD": $convert = 0.92; break;
      default: $convert = 1; break;
    }
    $tmp['$goal'] = $money * $convert;

    // Video/Image
    $pattern = '/<div id="pitchvideo">/';
    preg_match($pattern, $text, $matches);
    if (isset($matches[0])){$vid_img = 1;}
    else{$vid_img = 0;}
    $tmp['Bvideo'] = $vid_img;
      
    // Days running
    $pattern = '/and will close on (.+) \(.+\)\.<\/div>/';
    preg_match($pattern, $text, $matches);
    if (!isset($matches[1])){
      $pattern = '/and closed on (.+) \(.+\)\.<\/div>/';
      preg_match($pattern, $text, $matches);
    }
    $date_end = strtotime($matches[1]);
    $year = explode(" ", $matches[1]);
    $pattern = '/campaign started on (.+) and/';
    preg_match($pattern, $text, $matches);
    $date_start = $matches[1] . ", " . $year[2];
    $date_start = strtotime($date_start);
    if ($date_start > $date_end){
      $date_start =  $matches[1] . ", " . ($year[2] - 1);
      $date_start = strtotime($date_start);
    }
    $datediff = $date_end - $date_start;
    $tmp['#daysActive'] = floor($datediff/(60*60*24));

    // Start date
    $tmp['Dlaunched'] = $date_start;

    // If project ended
    $pattern = '/i-contribute-button i-campaign-closed/';
    preg_match($pattern, $text, $matches);
    if (isset($matches[0])){ $tmp['Bfinished'] = 1; }
    else{ $tmp['Bfinished'] = 0; }

    // How long allready
    if ($tmp['Bfinished'] == 0){
      $pattern = '/<span>(\d+) days left<\/span>/';
      preg_match($pattern, $text, $matches);
      if (isset($matches[1])){ $running = $matches[1]; }
      else {
        $pattern = '/<span>(\d+) hours/';
        preg_match($pattern, $text, $matches);
        $running = floor($matches[1]/24);
      }
      $tmp['#daysLong'] = $running;
    }else{ $tmp['#daysLong'] = 0; }

    // Number of comments, updates, backers
    $pattern = '/<span class="i-count">(.+)<\/span>/';
    preg_match_all($pattern, $text, $matches);
    $tmp['#comments'] = $matches[1][1];
    $tmp['#updates'] = $matches[1][0];
    $tmp['#backers'] = $matches[1][2];

    // Rased money
    $pattern = '/currency-xlarge"><span>.(.+)<\/span><em>/';
    preg_match($pattern, $text, $matches);
    $tmp['$raised'] = str_replace(",", "", $matches[1]) * $convert;

    // Pledges
    $pattern = '/i-perk-title">/';
    preg_match_all($pattern, $text, $matches);
    $pledgesNumber = count($matches[0]);
    $tmp['#pladges'] = $pledgesNumber;

    // Type of funding
    $pattern = '/<span>(.+) Funding<\/span>/';
    preg_match($pattern, $text, $matches);
    if ($matches[1] == "Flexible"){ $tmp['Bfunding'] = 0;  }
    else{ $tmp['Bfunding'] = 1;}
      
    // External pages
    $beginingPosition = strpos($text, 'Find This Campaign On');
    if ($beginingPosition  !== false){ 
      $endPosition = strpos($text, 'Team</div>');
      $endPosition = $endPosition - $beginingPosition;
      $externalPages = substr($text, $beginingPosition, $endPosition);
      $tmp['#externalPages'] = substr_count($externalPages, '<a href');
    }else{ $tmp['#externalPages'] = 0; }

    // Team members
    $beginingPosition = strpos($text, 'Team</div>');
    $endPosition = strlen($text);
    $endPosition = $endPosition - $beginingPosition;
    $teamMembers = substr($text, $beginingPosition, $endPosition);
    $tmp['#teamMembers'] = substr_count($teamMembers, 'class="i-name">');

    // Succesful
    if ( $tmp['Bfinished'] == 1 ) {  
      if ($tmp['$goal'] > $tmp['$raised']) { $tmp['Bsuccessful'] = 0; }
      else { $tmp['Bsuccessful'] = 1; }
    }else{ $tmp['Bsuccessful'] = 0; }

    return array();
  }


}
