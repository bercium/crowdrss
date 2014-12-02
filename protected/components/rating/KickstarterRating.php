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
    if (!$this->html) $this->html = $this->getData();  //load data if not loaded
    $text = $this->html;
    
    $tmp = false;
    if ($text) {
      if (strlen($text) > 2000){
        // Link
//        $link = str_replace("-", "/", $fileinfo->getFilename());
//        $link = str_replace("_", "-", $link);
//        $link = "https://www.kickstarter.com/projects/" . $link;
//        $tmp[] = $link

          
        $pattern = '/window.current_project = "(.+)";/'; 
        preg_match($pattern, $this->html, $match);
        $json = html_entity_decode($match[1]);
        $json = str_replace('\\"', "\'", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
        
        
          
        // Words Full Description 
        $beginingPosition = strpos($text, 'About this project</h3>');
        $risksPosition = strpos($text, 'Risks and challenges</h3>');
        $endPosition = $risksPosition - $beginingPosition;
        $description = substr($text, $beginingPosition, $endPosition);
        $beginingPosition = strpos($description, '>');
        $description = substr($description, $beginingPosition);
        $descriptionNumber = str_word_count(strip_tags($description));
        $tmp['#wordsContent'] = $descriptionNumber;

        // Image number
        $imageNumber = substr_count($description, '</figure>');
        $tmp['#images'] = $imageNumber;

        // Subtitles Number
        $subtitlesNumber = substr_count($description, '</h1>');
        $tmp['#subtitles'] = $subtitlesNumber;

        // Video number
        $videoNumber = substr_count($description, '</embed>');
        $videoNumber += substr_count($description, '</video>');
        $videoNumber += substr_count($description, '</iframe>');
        $tmp['#videos'] = $videoNumber; 

        // Money
        $money = $jsonData->goal;
        switch ($jsonData->currency) {
            case "GBP": $convert = 1.69; break; 
            case "EUR": $convert = 1.34; break; 
            case "AUD": $convert = 0.93; break;
            case "CAD": $convert = 0.92; break;
            default: $convert = 1; break;
        }
        $tmp['$goal'] = $money * $convert;

        // Video/Image
        $pattern = '/data-has-video="(\w+)" id/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] == "true"){$vid_img = 1;}
        elseif($matches[1] == "false"){$vid_img = 0;}
        $tmp['Bvideo'] = $vid_img;
      
        // Words RaC
        $beginingPosition = strpos($text, 'Risks and challenges</h3>');
        $faqPosition = strpos($text, 'Learn about accountability on Kickstarter</a>');
        $endPosition = $faqPosition - $beginingPosition;
        $risks = substr($text, $beginingPosition, $endPosition);
        $beginingPosition = strpos($risks, '</h2>');
        $risks = substr($risks, $beginingPosition);
        $riskNumber = str_word_count(strip_tags($risks));
        $tmp['#wordsRisk'] = $riskNumber;

      // Words FAQ
//      $beginingPosition = strpos($text, 'id="project-faqs"');
//      $reportPosition = strpos($text, 'id="report-issue-wrap"');
//      $endPosition = $reportPosition - $beginingPosition;
//      $faq = substr($text, $beginingPosition, $endPosition);
//      $beginingPosition = strpos($risks, '</h2>');
//      $risks = substr($risks, $beginingPosition);
//      $riskNumber = str_word_count(strip_tags($faq));
//echo strip_tags($faq) . "\n\n";
//      $tmp['#wordsFaq'] = 0;
      
        // Created
        $pattern = '/<span .+>(.+) created</span>/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] == "First"){ $matches[1] = 1; }
        else {
          $pattern = '/">(.+) created</a>/';
  	  preg_match($pattern, $text, $matches);
        }
        $tmp['#personCreated'] = $matches[1];

        // Backed
        $pattern = '/<span .+>(.+) backed/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] != "0" ){
          $pattern = '/">(.+) backed</a>/';
          preg_match($pattern, $text, $matches);
        }
        if (isset($matches[1])) $tmp['#personBacked'] = $matches[1];
        else $tmp['#personBacked'] = 0;

        // Days running
        $pattern = '/data-duration="(.+)" data-end_time=/';
        preg_match($pattern, $text, $matches);
        $days = floor($matches[1]);
        $tmp['#daysActive'] = $days;

        // How long allready
        $pattern = '/data-hours-remaining="(.+)" id=/';
        preg_match($pattern, $text, $matches);
        $running = floor($matches[1]/24);
        $tmp['#daysLong'] = $days-$running;

        // If project ended
        $pattern = '/Project-ended-(.+) Project-is_/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] == "true") $tmp['Bfinished'] = 1;
        elseif ($matches[1] == "false") $tmp['Bfinished'] = 0;

        // State of project
        $pattern = '/Project-state-(.+) Project/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] == "successful") $tmp['Bsuccessful'] = 1;
        else $tmp['Bsuccessful'] = 0;
        if ($matches[1] == "suspended") $tmp['Bsuspended'] = 1;
        else $tmp['Bsuspended'] = 0;
        switch ($matches[1]){
          case "live": $tmp['Istate'] = 1; break;
          case "successful": $tmp['Istate'] = 2; break;
          case "canceled": $tmp['Istate'] = 3; break;
          case "failed": $tmp['Istate'] = 4; break;
          case "suspended": $tmp['Istate'] = 5; break;
        }

        // Number of comments
        $pattern = '/data-comments-count="(\d+)" id=/';
        preg_match($pattern, $text, $matches);
        $tmp['#comments'] = $matches[1];

        // Number of updates
        $pattern = '/data-updates-count="(\d+)" id=/';
        preg_match($pattern, $text, $matches);
        $tmp['#updates'] = $matches[1];

        // Number of backers
        $pattern = '/backers" content="(.+)"\/>/';
        preg_match($pattern, $text, $matches);
        $tmp['#backers'] = $matches[1];

        // % Rased calc to money
        $pattern = '/data-percent-raised="(.+)" data-pledged=/';
        preg_match($pattern, $text, $matches);
        $tmp['$raised'] = $money * $matches[1];

	// Launch day
        $pattern = '/datetime="(.+)">/';
        preg_match($pattern, $text, $matches);
        $tmp['Dlaunched'] = strtotime($matches[1]);

        // Pledges
        $pattern = '/Pledge[ ]*\s<span class=".+">(.+)<\/span>/';
        preg_match_all($pattern, $text, $matches);
        $pledgesNumber = count($matches[1]);
        $tmp['#pledges'] = $pledgesNumber;
//      for ($i=0; $i<$pledgesNumber; $i++){
//        $tmp[] = $matches[1][$i] . " ";
//      }
        
        if ($tmp['Bfinished'] == 1){
          if ($this->id) {
            $update = Project::model()->findByPk($this->id);
            if ($update){
              $update->removed=1;
              $update->save();
            }
          }
          $tmp = false;
        }
    
      }
    }
    
    return $tmp;
  }


}
