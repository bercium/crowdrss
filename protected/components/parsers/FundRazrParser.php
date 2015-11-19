<?php

class FundRazrParser {
   
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
        $pattern = '/<span class="content">(.+)<\/span>/';
        preg_match($pattern, $htmlData, $match);
        $data['description'] = strip_tags($match[1]);

        // Category
        $pattern = '/"category":"(.+)","contributionAmount/';
        preg_match($pattern, $htmlData, $match);
        $data['category'] = $match[1];

        // Location
        $pattern = '/<a href="https:..fundrazr.com.find.loc=\d+" target="_blank" class="muted" title="Find more campaigns here">(.+)<\/a><\/span>/';
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