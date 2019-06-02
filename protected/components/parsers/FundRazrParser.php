<?php

class FundRazrParser {
    
    public function linkParser($htmlData) {
        // Link
        $pattern_link = '/campaign" href="(.+)" target="_top">.+<\/a>/';
        preg_match_all($pattern_link, $htmlData, $matches);
        $data['link'] = $matches[1];
        
        // Image link
        $pattern_image = '/url\(\'(.+)\'\);/';
        preg_match_all($pattern_image, $htmlData, $matches);
        $data['image'] = $matches[1];
        return $data;
    }
   
    public function projectParser($htmlData){
        // Goal
        $pattern = '/raised of (.+) goal/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) {
          $data['goal'] = $match[1];
        } else {
          $data['goal'] = NULL;
        }

        // Title & Creator
        $pattern = '/<title>(.+)<\/title>/';
        preg_match($pattern, $htmlData, $match);
        $split = explode(" by ", $match[1]);
        $data['title'] = $split[0];
        if (isset($match[1])) $data['creator'] = $split[1];
        
        // Description
        $pattern = '/data-update-attribute="introduction">(.+)
								<\/span>.+<a data-action="view-story"/s';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['description'] = strip_tags($match[1]);
        $data['description'] = '';

        // Category
        $pattern = '/"category":"(.+)","contributionAmount/';
        preg_match($pattern, $htmlData, $match);
        $data['category'] = $match[1];

        // Location
        $pattern = '/formattedAddress":"(.+)","latitude"/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) $data['location'] = $match[1];
        
        // Start date
        $pattern = '/Campaign launched on &lt;br\/&gt; (.+)" class="/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) {
          $data['start_date'] = $match[1];
        } else {
          $data['start_date'] = NULL;
        }

        // End date
        $pattern = '/Campaign ends on &lt;br\/&gt; (.+) at (.+) PT"/';
        preg_match($pattern, $htmlData, $match);
        if (isset($match[1])) {
          $data['end_date'] = $match[1] . " " . $match[2];
        } else {
          $data['end_date'] = NULL;
        }
        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}