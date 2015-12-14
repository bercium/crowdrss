<?php

class PledgeMeParser {
    
    public function linkParser($htmlData) {
        
        // Link
        $pattern = '/img-thumb" href="(.+)" target="_parent">/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['links'] = $matches[1];

        // Image Link
        $pattern = '/src="(https:\/\/s3\-ap\-southeast\-2\.amazonaws\.com\/pledgeme.+)" \/>/';
        preg_match_all($pattern, $htmlData, $matches);
        $data['images'] = $matches[1];

        return $data;
    }
   
    public function projectParser($htmlData){
        // Title
        $pattern = '/<title>(.+) \| PledgeMe<\/title>/';
        preg_match($pattern, $htmlData, $match);
        $data['title'] = $match[1];
                
        // Description
        $pattern = '/<meta content=.(.+). name=.description.>/';
        preg_match($pattern, $htmlData, $match);
        $data['description'] = $match[1];
        
        // Creator
        $pattern = '/<meta content=.(.+). name=.author.>/';
        preg_match($pattern, $htmlData, $match);
        $data['creator'] = $match[1];
        
        // Goal
        $pattern = '/title=.Target: (.+).><\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['goal'] = $match[1];
        
        // Days to end
        $pattern = '/<span class=\'counter pledges-close-date\'>(.+)<\/span>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) {
            $match[1] = str_replace("/", "-", $match[1]);
            $data['end_date'] = strtotime(str_replace("at", "", $match[1]));
        }

        // Category
        $pattern = '/<a href=.\/projects\?category\=.+>(.+)<\/a>/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['category'] = $match[1];
        else $data['category'] = "Other";

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}