<?php

class FundedByMeParser {
    
    public function linkParser($htmlData) {
        
        // Link
        $pattern = '/<a href="(\/en\/campaign\/.+)" onclick/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['links'] = $matches[1];

        // Image Link
        $pattern = '/<img src="(https:\/\/s3-eu-west-1.amazonaws.com\/media2.fundedbyme.com.+)" class/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = $matches[1];

        return $data;
    }
   
    public function projectParser($htmlData){
        // Title
        $pattern = '/<h1>(.+)<\/h1>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['title'] = $match[1];

        // Description
        $pattern = '/<meta property="og:description" content="(.+)"\/>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['description'] = $match[1];

        // Creator
        $pattern = '/<h5 class="colorHeading">(.+)<\/h5>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];        

        // Location
        $pattern = '/icon-location-on"><\/i>\s([a-zA-Z,]+)\s<\/a>/s';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Goal
        $pattern = '/"funding_goal": "(.+)",/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = $match[1];
        
        // Days to end
        $pattern = '/raised by (.+) otherwise/';
        preg_match($pattern, $htmlData, $match);
        //var_dump($match[1]);
        if (isset($match[1])) $data['end_date'] = strtotime($match[1]);

        // Category
        $pattern = '/"category": "(.+)",/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];
        
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}