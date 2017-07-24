<?php

class CrowdfunderUKParser {
    
    public function linkParser($htmlData) {
        
        // Link
        $pattern = '/<a class="project-thumb" href="(.+)"/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['links'] = $matches[1];

        // Image Link
        $pattern = '/<img src="(http.+)" alt/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = $matches[1];
        return $data;
    }
   
    public function projectParser($htmlData){
        // Title
        $pattern = '/<meta property="og:title" content="(.+)" \/>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];

        // Description
        $pattern = '/<meta property="og:description" content="(.+)" \/>.<meta property="og:image"/s';
        preg_match($pattern, $htmlData, $match);
        $data['description'] = $match[1];

        // Creator
        $pattern = '/<a href="http:..www.crowdfunder.co.uk.user.+?">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['creator'] = $match[1];        

        // Location
//        $pattern = '/<a href="\/projects\/search\/string:.+\/">(.+) {12}<\/a>/';
//        preg_match($pattern, $htmlData, $match);
//        if (isset($match[1])) $data['location'] = $match[1];
        
        // Goal
        $pattern = '/raised of (.+) target/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = html_entity_decode($match[1]);
        
        // Days to end
        $pattern = '/by (.+\d{4})<\/small>/';
        preg_match($pattern, $htmlData, $match);
        //var_dump($match[1]);
        if (isset($match[1])) $data['end_date'] = strtotime($match[1]);

        // Category
        $pattern = '/<a href="\/projects\/search\/category:.+"><\/i> (.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}