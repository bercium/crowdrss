<?php

class FundRazrParser {
   
    public function firstParsing($htmlData){
        // Goal
        $pattern = '/raised of (.+) goal/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) {
          $data['goal'] = $matches[1];
        } else {
          $data['goal'] = NULL;
        }

        // Image
        /*$pattern = '/<meta property="og:image" content="(.+)" \/>/';
        preg_match($pattern, $htmlData, $matches);
        $data['image'] = $matches[1];*/

        // Category
        $pattern = '/"category":"(.+)","contributionAmount/';
        preg_match($pattern, $htmlData, $matches);
        //var_dump($matches[1]); Die;
        $data['category'] = $matches[1];

        // Start date
        $pattern = '/Launched (.+)</';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) {
          $data['start_date'] = $matches[1];
        } else {
          $data['start_date'] = NULL;
        }

        // End date
        $pattern = '/Ends (.+) at(.+)<\/span>/';
        preg_match($pattern, $htmlData, $matches);
        if (isset($matches[1])) {
          $data['end_date'] = $matches[1] . $matches[2];
        } else {
          $data['end_date'] = NULL;
        }

        return($data);
    }
    
    public function ratingParser($htmlData, $projectDescription){
        
    }
}