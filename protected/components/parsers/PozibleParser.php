<?php

class PozibleParser {
    
    public function linkParser($htmlData) {
        
        // Link
        $pattern = '/(http:\/\/www\.pozible\.com\/project\/\d+)" title/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['links'] = $matches[1];

        // Image Link
        $pattern = '/<img src="(https:\/\/s3\.amazonaws\.com\/pozibleuploads\/cache\/.+)" \/>/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = $matches[1];
        
        // Descriptions
        //$pattern = '/<a href="http:\/\/www\.pozible\.com\/project\/\d+">\n(.+)<\/a>/s';
        //preg_match_all($pattern, $htmlData, $matches);
        //$data['$descriptions'] = $matches[1];
        
        return $data;
    }
   
    public function projectParser($htmlData){
        // Title & Creator
        $pattern = '/<title>(.+)<\/title>/';
        preg_match($pattern, $htmlData, $match);
        $splited = explode(" by ", $match[1]);
        $data['title'] = $splited[0];
        if (isset($splited[1])) $data['creator'] = $splited[1];

        // Description
        $pattern = '/<\/h2><div class="v3p_p">(.+)<\/div>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])){
            $description = strip_tags($match[1]);
            $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
            $data['description'] = $description . " ...";
        }
        else $data['description'] = " ";
        
        // Location
        $pattern = '/<li><a class="a1" href="http:\/\/www\.pozible\.com\/list\/pop\/0\/all\/\d+">(.+)<\/a><\/li>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Goal
        $pattern = '/A(.\d+,\d+)/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = $match[1];
        
        // Days to end - NI FUL NATANČNO
        $pattern = '/data-num="(\d+)">/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['end_date'] = $match[1];

        // Category
        $pattern = '/<li><a class="a3" href="http:\/\/www\.pozible\.com\/list\/pop\/.+">(.+)<\/a><\/li>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}