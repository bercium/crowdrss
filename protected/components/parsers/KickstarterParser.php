<?php

class KickstarterParser {
    
    public function statusParser($htmlData) {
        $pattern = '/window.current_project = "(.+)";/'; 
        preg_match($pattern, $htmlData, $match);
        if (!isset($match[1])) return false;
        $json = html_entity_decode($match[1]);
        $json = str_replace('\\"', "\'", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
        return $jsonData->state;
    }
    
    public function linkParser($htmlData) {
        // Link
        //$pattern = '/(\/projects\/.+)\?ref=newest/';
        $pattern = '/{"project":"(https:\/\/www.kickstarter.com\/projects\/.+)"\,"rewards":/';
        preg_match_all($pattern, $htmlData, $matches);
        if (is_array($matches)){
            foreach ($matches[1] as $key => $val){ $links[$val] = true; }
            if (isset($links) && is_array($links)) $data['links'] = array_keys($links);
            else $data['links'] = array();
        }
        
        // Image Link
        $pattern = '/"full":"(.+)"\,"ed":"/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = str_replace("&amp;", "&", $matches[1]);
        return $data;
    }
    
    public function projectParser($htmlData){
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
/*        $pattern = '/<span .+>(.+) created<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])){
            if ($matches[1] == "First"){ $matches[1] = 1; }
        } else {
            $pattern = '/">(.+) created<\/a>/';
            preg_match($pattern, $htmlData, $matches);
        }
        $data['created'] = $matches[1];*/
        $data['created'] = 1;

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
    
    public function ratingParser($htmlData){
        $pattern = '/window.current_project = "(.+)";/'; 
        preg_match($pattern, $htmlData, $match);
        $json = html_entity_decode($match[1]);
        $json = str_replace('\\"', "\'", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
          
        // Words Full Description 
        $beginingPosition = strpos($htmlData, 'About this project</h3>');
        $risksPosition = strpos($htmlData, 'Risks and challenges</h3>');
        $endPosition = $risksPosition - $beginingPosition;
        $description = substr($htmlData, $beginingPosition, $endPosition);
        $beginingPosition = strpos($description, '>');
        $description = substr($description, $beginingPosition);
        $descriptionNumber = str_word_count(strip_tags($description));
        $data['#wordsContent'] = $descriptionNumber;

        // Image number
        $imageNumber = substr_count($description, '</figure>');
        $data['#images'] = $imageNumber;

        // Subtitles Number
        $subtitlesNumber = substr_count($description, '</h1>');
        $data['#subtitles'] = $subtitlesNumber;

        // Video number
        $videoNumber = substr_count($description, '</embed>');
        $videoNumber += substr_count($description, '</video>');
        $videoNumber += substr_count($description, '</iframe>');
        $data['#videos'] = $videoNumber; 

        // Money
        $money = $jsonData->goal;
        switch ($jsonData->currency) {
            case "GBP": $convert = 1.69; break; 
            case "EUR": $convert = 1.34; break; 
            case "AUD": $convert = 0.93; break;
            case "CAD": $convert = 0.92; break;
            default: $convert = 1; break;
        }
        $data['$goal'] = $money * $convert;

        // Video/Image
        $pattern = '/data-has-video="(\w+)" id/';
        preg_match($pattern, $htmlData, $matches);
        if ($matches[1] == "true"){$vid_img = 1;}
        elseif($matches[1] == "false"){$vid_img = 0;}
        $data['Bvideo'] = $vid_img;
      
        // Words RaC
        $beginingPosition = strpos($htmlData, 'Risks and challenges</h3>');
        $faqPosition = strpos($htmlData, 'Learn about accountability on Kickstarter</a>');
        $endPosition = $faqPosition - $beginingPosition;
        $risks = substr($htmlData, $beginingPosition, $endPosition);
        $beginingPosition = strpos($risks, '</h2>');
        $risks = substr($risks, $beginingPosition);
        $riskNumber = str_word_count(strip_tags($risks));
        $data['#wordsRisk'] = $riskNumber;
        
        // If project ended
        $pattern = '/Project-ended-(.+) Project-is_/';
        preg_match($pattern, $htmlData, $matches);
        if ($matches == array()){
            $data['Bfinished'] = 0;
        }else{
          if ($matches[1] == "true") $data['Bfinished'] = 1;
          elseif ($matches[1] == "false") $data['Bfinished'] = 0;
        }

        
        if (isset($data['Bfinished']) && ($data['Bfinished'] == 1)){
          $this->projectRemoved();
          return false;
        }        

      // Words FAQ
//      $beginingPosition = strpos($htmlData, 'id="project-faqs"');
//      $reportPosition = strpos($htmlData, 'id="report-issue-wrap"');
//      $endPosition = $reportPosition - $beginingPosition;
//      $faq = substr($htmlData, $beginingPosition, $endPosition);
//      $beginingPosition = strpos($risks, '</h2>');
//      $risks = substr($risks, $beginingPosition);
//      $riskNumber = str_word_count(strip_tags($faq));
//echo strip_tags($faq) . "\n\n";
//      $data['#wordsFaq'] = 0;
      
        // Created
/*        $pattern = '/<span .+>(.+) created<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])){
          if ($matches[1] == "First"){ $matches[1] = 1; }
        } else {
          $pattern = '/">(.+) created<\/a>/';
  	  preg_match($pattern, $htmlData, $matches);
        }
        $data['#personCreated'] = $matches[1]; */
        $data['#personCreated'] = 1;

        // Backed
        $pattern = '/<span .+>(.+) backed<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1]) != true ){
          $pattern = '/">(.+) backed<\/a>/';
          preg_match($pattern, $htmlData, $matches);
        }
        if (isset($matches[1])) $data['#personBacked'] = $matches[1];
        else $data['#personBacked'] = 0;

        // Days running
        $pattern = '/data-duration="(.+)" data-end_time=/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])){
          $days = floor($matches[1]);
          $data['#daysActive'] = $days;
        }else $data['#daysActive'] = 0;

        // How long allready
        $pattern = '/data-hours-remaining="(.+)" id=/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])){
          $running = floor($matches[1]/24);
          $data['#daysLong'] = $days-$running;
        }else $data['#daysLong'] = 0;

        // State of project
        $pattern = '/data-project-state="(.+)">/';
        //$pattern = '/Project-state-(.+) Project/';
        preg_match($pattern, $htmlData, $matches);
        if ($matches[1] == "successful") $data['Bsuccessful'] = 1;
        else $data['Bsuccessful'] = 0;
        if ($matches[1] == "suspended") $data['Bsuspended'] = 1;
        else $data['Bsuspended'] = 0;
        switch ($matches[1]){
          case "live": $data['Istate'] = 1; break;
          case "successful": $data['Istate'] = 2; break;
          case "canceled": $data['Istate'] = 3; break;
          case "failed": $data['Istate'] = 4; break;
          case "suspended": $data['Istate'] = 5; break;
        }

        // Number of comments
        $pattern = '/data-comments-count="(\d+)"/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) $data['#comments'] = $matches[1];
        else $data['#comments'] = 0;

        // Number of updates
        $pattern = '/data-updates-count="(\d+)"/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) $data['#updates'] = $matches[1];
        else $data['#updates'] = 0;

        // Number of backers
        $pattern = '/data-backers-count="(.+)" id=/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) $data['#backers'] = $matches[1];
        else $data['#backers'] = 0;

        // % Rased calc to money
        $pattern = '/data-percent-raised="(.+)" data-pledged=/';
        preg_match($pattern, $htmlData, $matches);
        $data['$raised'] = $money * $matches[1];

	// Launch day
        $pattern = '/datetime="(.+)">/';
        preg_match($pattern, $htmlData, $matches);
        $data['Dlaunched'] = strtotime($matches[1]);

        // Pledges
        $pattern = '/Pledge[ ]*\s<span class=".+">(.+)<\/span>/';
        preg_match_all($pattern, $htmlData, $matches);
        $pledgesNumber = count($matches[1]);
        $data['#pledges'] = $pledgesNumber;
//      for ($i=0; $i<$pledgesNumber; $i++){
//        $data[] = $matches[1][$i] . " ";
//      }
    
        return $data;        
    }
}