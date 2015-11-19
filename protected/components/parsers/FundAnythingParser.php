<?php

class FundAnythingParser {
   
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