<?php

class GoGetFundingParser {
   
    public function firstParsing($htmlData){
        // Goal
        $pattern = '/donated of (.+)<.+>(.+)<\/s/';
        preg_match($pattern, $htmlData, $matches);
        $data['goal'] = $matches[1] . $matches[2];

        // End date
        $pattern = '/donate to this project before (.+)<\/p/';
        preg_match($pattern, $htmlData, $matches);
        $data['end_date'] = $matches[1];

        // Location
        $pattern = '/<a href="\/projects\/city.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $matches);
        $data['location'] = $matches[1];

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}