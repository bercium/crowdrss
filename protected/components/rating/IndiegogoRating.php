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
    if (!$this->html){
      $this->html = $this->getData();  //load data if not loaded
      //<a href="#home" class="js-tab-link" data-url="/projects/fear-and-fail-at-crowdfunding-conference/show_tab/home">Story</a>
      $this->html .= $this->getData("/show_tab/home",array("X-Requested-With" => "XMLHttpRequest"));  //load secondary data if not loaded
    }
    $text = $this->html;
    //echo "a".substr_count($text,'<html>');
    // check validity of data
    if ((substr_count($text,'<html>') > 1) || (strpos($text, "i-illustration-not_found"))){
      if (strpos($text, "i-illustration-not_found")){
        $this->projectRemoved();
      }
      return false;
    }

    $pattern = '/var utag_data = (.+);/'; 
    preg_match($pattern, $text, $match);
    $json = html_entity_decode($match[1]);
    $json = str_replace('\\"', "\'", $json);
    $jsonData = json_decode($json);
    if ($jsonData == null){ return false; }
    if ($jsonData->page_name == "Invalid Page | Indiegogo") {return false;}
    
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
    $money = $jsonData->{'campaign_raised_amount'};
    switch ($jsonData->{'site_currency'}) {
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
    $pattern = '/<span.*>in (\d)+ day.*<\/span>/';
    preg_match($pattern, $text, $match);
    if (isset($match[1])){$tmp['#daysActive'] = $match[1];}
    else {$tmp['#daysActive'] = 0;}

    // Start date
    $tmp['Dlaunched'] = date("Y-m-d H:i:s", strtotime($jsonData->{'campaign_start_date'}));

    // If project ended
    $pattern = '/i-contribute-button i-campaign-closed/';
    preg_match($pattern, $text, $matches);
    if (isset($matches[0])){ $tmp['Bfinished'] = 1; }
    else{ $tmp['Bfinished'] = 0; }

    // How long allready
    if ($tmp['Bfinished'] == 0){
      $tmp['#daysLong'] = $jsonData->{'campaign_days_left'};
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
    $tmp['#pledges'] = $pledgesNumber;

    // Type of funding
    if ($jsonData->{'campaign_type'} == "flexible_funding"){ $tmp['Bfunding'] = 0;  }
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
      //if ($tmp['$goal'] > $tmp['$raised']) $tmp['Bsuccessful'] = 0;
      //else $tmp['Bsuccessful'] = 1;
      
      $this->projectRemoved();
      return false;
      
    }else $tmp['Bsuccessful'] = 0;

   
    return $tmp;
  }


}
