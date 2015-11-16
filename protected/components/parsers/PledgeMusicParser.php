<?php

class PledgeMusicParser {
   
    public function firstParsing($htmlData){
        // Description
        $pattern = '/<div class=.copy.>(.+)<section/s';
        preg_match($pattern, $htmlData, $matches);
        $description = strip_tags($matches[1]);
        $description = preg_replace('/\s+?(\S+)?$/', '', substr($description, 0, 201));
        $data['description'] = html_entity_decode($description) . " ...";

        // Location 
        $pattern = '/<a href="\/artists\?country=.+">(.+)<\/a>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) $data['location'] = $matches[1];

        // Category
        $pattern = '/<div class=.genres.>(.+)<\/div>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) { $data['category'] = strip_tags(html_entity_decode($matches[1])); }
        else { $data['category'] = "Music";}

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}