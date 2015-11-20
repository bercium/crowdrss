<?php

class PledgeMusicParser {
   
    public function linkParser($htmlData) {
        //Link
        $pattern_link = '/(\/projects.+)\?referrer=launched/';
        preg_match_all($pattern_link, $htmlData, $matches);
        $data['link'] = $matches[1];
        
        // Image link
        $pattern_image = '/alt="Mobile" src="(.+)" \/>/';
        preg_match_all($pattern_image, $htmlData, $matches);
        $data['image'] = $matches[1];
        return $data;
    }
    
    public function projectParser($htmlData){
        // Title
        $pattern = '/<h1>(.+)<\/h1>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];

        // Description
        $pattern = '/<meta content=.(.+). itemprop=.description.>/';
        preg_match($pattern, $htmlData, $match);
        $data['description'] = html_entity_decode($match[1]);
        
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