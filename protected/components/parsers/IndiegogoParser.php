<?php

class IndiegogoParser {

    public function statusParser($htmlData) {
        $pattern = '/gon.tealium_data_layer=(.+);gon.domain/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){$json = html_entity_decode($match[1]);}
        else{return false;}
        $json = str_replace('\\"', "", $json);
        $json = str_replace('\"', "", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
        if (!$jsonData->campaign_name) {return false;}
        if ($jsonData->campaign_percent_of_goal >= 1){return "successful";}
        else {return "failed";}
    }

    public function linkParser($htmlData) {
        $htmlDataSplit = explode('{"campaigns":', $htmlData);
        $htmlData = '{"campaigns":'.$htmlDataSplit[1];
        $json = html_entity_decode($htmlData);
        return json_decode($json);
    }

    public function projectParser($htmlData){
        $pattern = '/gon.tealium_data_layer=(.+);gon.gtm_data_layer/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){$json = html_entity_decode($match[1]);}
        else{return false;}
        $json = str_replace('\\"', "", $json);
        $json = str_replace('\"', "", $json);
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false; }
        if (!$jsonData->campaign_name) {return false;}

        // Description
        $data['description'] = $jsonData->campaign_description;

        // Category
        $data['category'] = $jsonData->campaign_category;

        // Goal
        $money = Yii::app()->numberFormatter->formatCurrency($jsonData->campaign_goal_amount, $jsonData->site_currency);
        $money_split = explode(".", $money);
        if ($money_split[1] == "00") {$data['goal'] = $money_split[0];
        } else {$data['goal'] = $money;}

        // Type of funding
        if ($jsonData->{'campaign_type'} == "flexible_funding") {$data['type_of_funding'] = 1;}
        else{$data['type_of_funding'] = 0;}

        // Start date
        $data['start_date'] = date("Y-m-d H:i:s", strtotime($jsonData->{'campaign_start_date'}));

        // End date
        $data['end_date'] = date("Y-m-d H:i:s", strtotime($jsonData->{'campaign_end_date'}));

        // Location
        $data['location'] = $jsonData->campaign_city . ", " . $jsonData->campaign_country;

        // Creator
        $pattern = '/owner_name":"(.+)","currency"/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];

        return($data);
    }

    public function ratingParser($htmlData, $projectDescription){
        if (!($projectDescription)){return false;}
        $jsonData = json_decode($projectDescription);
        if ($jsonData == null){ return false;}

        // Words Full Description
        $description = $jsonData->response->description_html;
        $data['#wordsContent'] = str_word_count(strip_tags($description));

        // Image number
        $data['#images'] = substr_count($description, '<img');

        // Subtitles Number
        $subtitlesNumber = substr_count($description, '<h1>');
        $subtitlesNumber += substr_count($description, '<h2>');
        $subtitlesNumber += substr_count($description, '<h3>');
        $subtitlesNumber += substr_count($description, '<h4>');
        $data['#subtitle'] = $subtitlesNumber;

        // Video number
        $videoNumber = substr_count($description, '</embed>');
        $videoNumber += substr_count($description, '</video>');
        $videoNumber += substr_count($description, '</iframe>');
        $data['#videos'] = $videoNumber;

        $pattern = '/gon.campaign=(.+);gon./';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){$json = html_entity_decode($match[1]."}]}");}
        else{return false;}
        $split_json = explode(";gon.", $json);
        $json = $split_json[0];
        $jsonData = json_decode($json);
        if ($jsonData == null){ return false;}
        // Money
        $money = $jsonData->goal;
        switch ($jsonData->currency->iso_code) {
            case "GBP": $convert = 1.69; break;
            case "EUR": $convert = 1.14; break;
            case "AUD": $convert = 0.93; break;
            case "CAD": $convert = 0.92; break;
            default: $convert = 1; break;
        }
        $data['$goal'] = $money * $convert;

        // Video/Image
        $pattern = '/id="pitchvideo"/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[0])){$vid_img = 1;}
        else{$vid_img = 0;}
         $data['Bvideo'] = $vid_img;

         // Days running
        $pattern = '/<span.*>in (\d)+ day.*<\/span>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){$data['#daysActive'] = $match[1];}
        else {$data['#daysActive'] = 0;}

        // Start date
        $data['Dlaunched'] = date("Y-m-d H:i:s", strtotime($jsonData->funding_started_at));

        // If project ended
        $pattern = '/i-contribute-button i-campaign-closed/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[0])){ $data['Bfinished'] = 1; }
        else{ $data['Bfinished'] = 0; }

        // How long allready
        if ($data['Bfinished'] == 0){
            $data['#daysLong'] = $jsonData->funding_days;
        }else{ $data['#daysLong'] = 0; }

        // Number of comments, updates, backers
        $pattern_comments = '/Comments","count":(\d+),"disabled"/';
        $pattern_updates = '/Updates","count":(\d+),"disabled"/';
        $pattern_backers = '/Backers","count":(\d+),"disabled"/';
        preg_match($pattern_comments, $htmlData, $match);
        $data['#comments'] = $match[1];
        preg_match($pattern_updates, $htmlData, $match);
        $data['#updates'] = $match[1];
        preg_match($pattern_backers, $htmlData, $match);
        $data['#backers'] = $match[1];

        // Rased money
        $data['$raised'] = $jsonData->collected_funds * $convert;

        // Pledges
        $pledgesNumber = count($jsonData->perks);
        $data['#pledges'] = $pledgesNumber;

        // Type of funding
        if ($jsonData->funding_type == "flexible"){ $data['Bfunding'] = 0;  }
        else{ $data['Bfunding'] = 1;}

        // External pages
        $beginingPosition = strpos($htmlData, 'Find This Campaign On');
        if ($beginingPosition  !== false){
            $endPosition = strpos($htmlData, 'Team</div>');
            $endPosition = $endPosition - $beginingPosition;
            $externalPages = substr($htmlData, $beginingPosition, $endPosition);
            $data['#externalPages'] = substr_count($externalPages, '<a href');
        }else{ $data['#externalPages'] = 0; }

        // Team members
        $teamMembers = count($jsonData->team_members);
        $data['#teamMembers'] = $teamMembers;

        // Succesful
        if ( $data['Bfinished'] == 1 ) {
            //if ($data['$goal'] > $data['$raised']) $data['Bsuccessful'] = 0;
            //else $data['Bsuccessful'] = 1;
            $this->projectRemoved();
            return false;
        }else $data['Bsuccessful'] = 0;

        return $data;
    }
}
