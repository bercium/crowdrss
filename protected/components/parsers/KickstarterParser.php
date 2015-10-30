<?php

class KickstarterParser {
    
    public function firstParisng($html){
        $pattern = '/window.current_project = "(.+)";/'; 
        preg_match($pattern, $htmlData, $match);
        $json = html_entity_decode($match[1]);
        $json = str_replace('\\"', "\'", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
    
        // Description
        $data['description'] = $jsonData->blurb;
    
        // Title
        $data['title'] = $jsonData->name;
    
        // Goal
        $money = Yii::app()->numberFormatter->formatCurrency($jsonData->goal, $jsonData->currency);
        $money_split = explode(".", $money);
        if ($money_split[1] == "00") {$data['goal'] = $money_split[0];
        } else {$data['goal'] = $money;}

        // Location
        $data['location'] = $jsonData->{'location'}->{'displayable_name'};

        // Category
        $data['category'] = $jsonData->{'category'}->{'name'};

        // Creator
        $data['creator'] = $jsonData->{'creator'}->{'name'};

        // Date
        $data['start_date'] = date("Y-m-d H:i:s", $jsonData->{'launched_at'});
        $data['end_date'] = date("Y-m-d H:i:s", $jsonData->{'deadline'});

        // Created
        $pattern = '/<span .+>(.+) created<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])){
            if ($matches[1] == "First"){ $matches[1] = 1; }
        } else {
            $pattern = '/">(.+) created<\/a>/';
            preg_match($pattern, $htmlData, $matches);
        }
        $data['created'] = $matches[1];

        // Backed
        $pattern = '/<span .+>(.+) backed<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1]) != true ){
            $pattern = '/">(.+) backed<\/a>/';
            preg_match($pattern, $htmlData, $matches);
        }
        if (isset($matches[1])) $data['backed'] = $matches[1];
        else $data['backed'] = 0;
    
        return($data);  
    }
    
    public function ratingParser($html){
        
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
        
        // If project ended
        $pattern = '/Project-ended-(.+) Project-is_/';
        preg_match($pattern, $text, $matches);
        if ($matches[1] == "true") $tmp['Bfinished'] = 1;
        elseif ($matches[1] == "false") $tmp['Bfinished'] = 0;
        
        if (isset($tmp['Bfinished']) && ($tmp['Bfinished'] == 1)){
          $this->projectRemoved();
          return false;
        }        

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
        $pattern = '/<span .+>(.+) created<\/span>/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])){
          if ($matches[1] == "First"){ $matches[1] = 1; }
        } else {
          $pattern = '/">(.+) created<\/a>/';
  	  preg_match($pattern, $text, $matches);
        }
        $tmp['#personCreated'] = $matches[1];

        // Backed
        $pattern = '/<span .+>(.+) backed<\/span>/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1]) != true ){
          $pattern = '/">(.+) backed<\/a>/';
          preg_match($pattern, $text, $matches);
        }
        if (isset($matches[1])) $tmp['#personBacked'] = $matches[1];
        else $tmp['#personBacked'] = 0;

        // Days running
        $pattern = '/data-duration="(.+)" data-end_time=/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])){
          $days = floor($matches[1]);
          $tmp['#daysActive'] = $days;
        }else $tmp['#daysActive'] = 0;

        // How long allready
        $pattern = '/data-hours-remaining="(.+)" id=/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])){
          $running = floor($matches[1]/24);
          $tmp['#daysLong'] = $days-$running;
        }else $tmp['#daysLong'] = 0;

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
        $pattern = '/data-comments-count="(\d+)"/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])) $tmp['#comments'] = $matches[1];
        else $tmp['#comments'] = 0;

        // Number of updates
        $pattern = '/data-updates-count="(\d+)"/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])) $tmp['#updates'] = $matches[1];
        else $tmp['#updates'] = 0;

        // Number of backers
        $pattern = '/data-backers-count="(.+)" id=/';
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])) $tmp['#backers'] = $matches[1];
        else $tmp['#backers'] = 0;

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

      }
    }
    
    return $tmp;
        
    }
    
}

