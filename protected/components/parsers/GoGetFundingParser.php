<?php

class GoGetFundingParser {
   
    public function projectParser($htmlData){
        // Goal
        $pattern = '/<p class="brokersText-1">Donated of (.+) goal<\/p>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = html_entity_decode($match[1]);

        // Creator
        $pattern = '/<a target="_blank" href="http:..gogetfunding.com.user..uid=\d+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];
        
        // End date
        $pattern = '/donate to this project before <span>(.+)<\/span>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['end_date'] = $match[1];

        // Location
        $pattern = '/<a href="http:..gogetfunding.com.campaigns.country=.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Description
        $pattern = '/<div id="contentxsLimit" class="brokersText-27">(.+)/';
        preg_match($pattern, $htmlData, $match);
        $description = strip_tags($match[1]);
        $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
        $data['description'] = $description . " ...";
        
        // Category
        $pattern = '/<a href="http:..gogetfunding.com.category.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];
        
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}