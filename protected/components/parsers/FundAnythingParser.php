<?php

class FundAnythingParser {
   
    public function firstParsing($htmlData){
        // Goal
        $pattern = '/Contributions of (.+) goal/';
        preg_match($pattern, $htmlData, $matches);
        $data['goal'] = $matches[1];

        // Creator
        $pattern = '/campaign-author">by (.+)<\/h3>/';
        preg_match($pattern, $htmlData, $matches);
        $data['creator'] = $matches[1];

        // Description
        $pattern = '/story">\s+<p>(.+)/';
        preg_match($pattern, $htmlData, $matches);
        $description = strip_tags($matches[1]);
        $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
        $data['description'] = $description . " ...";

        return($data);        
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}