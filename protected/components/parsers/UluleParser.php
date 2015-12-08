<?php

class UluleParser {
    
    public function linkParser($htmlData) {
        // Link
        $pattern = '/<div class="title"><a href="(http:\/\/www.ulule.com.+)" title.+<\/a><\/div>/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['links'] = $matches[1];
        
        // Image Link
        $pattern = '/url\(.(.+).\) no-repeat 0 0;">/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = $matches[1];
        
        // Category
        $pattern = '/<a href="http:\/\/www.ulule.com\/discover\/tags\/.+" title="(.+)\'s projects">.+<\/a><\/div><\/div>/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['categories'] = $matches[1];
        return $data;
    }
   
    public function projectParser($htmlData){
        // Title
        $pattern = '/<title>(.+) - Ulule<\/title>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];
        
        // Description
        $pattern = '/<section class="from-editor">(.+)/s';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){
            $description = strip_tags($match[1]);
            $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
            $data['description'] = $description . " ...";
        }
        else $data['description'] = " ";
        
        // Creator
        $pattern = '/class="profile-link"><h3>(.*)<\/h3>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];
        
        // Location
        $pattern = '/<i class="icowl-location"><\/i><b>(.+)<\/b><\/li><li><i class="icowl-pyramid">/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Goal
        $pattern = '/<span class="amount-text">(.+)<\/span><\/strong>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = "€".$match[1];
        
        // Days to end
        $pattern = '/(\d+) days left/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['end_date'] = $match[1];

        // More categories
//        $pattern = '/<a href="http:\/\/www.ulule.com\/discover\/tags\/.+<\/i>&nbsp;(.+)/';
//        preg_match_all($pattern, $htmlData, $matches);
//        if (isset($matches[1])) $data['categories'] = $matches[1];
        
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}