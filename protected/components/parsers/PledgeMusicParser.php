<?php

class PledgeMusicParser {
   
    public function firstParsing($htmlData){
        // Title
        $pattern = '/<h1>(.+)<\/h1>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];
        
        // Description
        $pattern = '/<h2>(.+)<\/h2>/';
        preg_match($pattern, $htmlData, $match);
        $data['description'] = $match[1];
        
        // Creator
        $pattern = '/<h1 itemprop=.name.>(.+)<\/h1>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];
        
        // Time
        $pattern = '/<div class=.timer.>(\d+).+<\/div>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['time'] = $match[1];
        
        // Location 
        $pattern = '/<a href="\/artists\?country=.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];

        // Category
        $pattern = '/<div class=.genres.>(.+)<\/div>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) { $data['category'] = strip_tags(html_entity_decode($match[1])); }
        else { $data['category'] = "Music";}
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}