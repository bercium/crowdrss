<?php

class FundAnythingParser {
    
    public function linkParser($htmlData) {
        // Link
        $pattern_link = '/<a href="(.+)" title=".+" target="_top">/';
        preg_match_all($pattern_link, $htmlData, $match);
        $data['link'] = $match[1];
        
        // Image link
        $pattern_image = '/<img alt=".+" class="" data-ctitle="" src="(.+)" style/';
        preg_match_all($pattern_image, $htmlData, $match);
        $data['image'] = $match[1];
        
        // Category
        $pattern_category = '/<a href="http:..fundanything.com.en.search.category.cat_id=\d+" target="_top">(.+)<\/a>/';
        preg_match_all($pattern_category, $htmlData, $match);
        $data['categorie'] = $match[1];
        
        // Location
        $pattern_location = '/locpin.png" \/>\s\s{8}(.+)\s.+<\/div>/';
        preg_match_all($pattern_location, $htmlData, $match);
        $data['location'] = $match[1];
        return $data;
    }
   
    public function projectParser($htmlData){
        // Goal
        $pattern = '/Contributions of (.+) goal/';
        preg_match($pattern, $htmlData, $match);
        $data['goal'] = $match[1];

        // Creator
        $pattern = '/campaign-author">by (.+)<\/h3>/';
        preg_match($pattern, $htmlData, $match);
        $data['creator'] = $match[1];

        // Description
        $pattern = '/story">\s+<p>(.+)/';
        preg_match($pattern, $htmlData, $match);
        $description = strip_tags($match[1]);
        $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
        $data['description'] = $description . " ...";
        
        // Title
        $pattern = '/\/en\/campaigns\/.+">(.+)<\/a><\/h1>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];
                
        return($data);        
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}