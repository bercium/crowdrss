<?php

class PubSlushParser {
   
    public function projectParser($htmlData){
        // Goal
        $pattern = '/raised of (.+) goal/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) $data['goal'] = $matches[1];
        else $data['goal'] = null;

        // Location and Category
        $pattern = '/meta-info.>\s.+i> (.+)<\/span>\s.+i> (.+)<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        $data['location'] = $matches[1];
        $data['category'] = $matches[2];

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}