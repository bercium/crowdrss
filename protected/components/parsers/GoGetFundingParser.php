<?php

class GoGetFundingParser {
    
    public function linkParser($htmlData) { 
        // Link and Title
        $pattern_link = '/<h2 class="cat_h2 visible-xs-block hidden-sm hidden-md hidden-lg"><a href="(.+)">(.+)<\/a><\/h2>/';
        preg_match_all($pattern_link, $htmlData, $matches);
        $data['link'] = $matches[1];
        $data['title'] = $matches[2];

        // Image link
        $pattern_image = '/class="img-responsive" src="(.+)" alt="main-img">/';
        preg_match_all($pattern_image, $htmlData, $matches);
        $data['image'] = $matches[1];
        return $data;
    }
   
    public function projectParser($htmlData){
        // Goal
        $pattern = '/Donated of (.+)<\/p>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = html_entity_decode($match[1]);

        // Creator
        $pattern = '/uid=\d+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];
        
        // End date
        $pattern = '/donate to this project before <span>(.+)<\/span>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['end_date'] = $match[1];

        // Location
        $pattern = '/country=.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Description
        $pattern = '/<div id="contentxsLimit" class="brokersText-27">(.+)/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) {
          $description = strip_tags($match[1]);
          $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
          $data['description'] = $description . " ...";
        }
        // Category
        $pattern = '/category\/.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}