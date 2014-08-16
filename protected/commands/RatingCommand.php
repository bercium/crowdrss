<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RatingCommand extends CConsoleCommand{
  
  public function actionAfter3h(){

    $time = strtotime("-3 hours");
    $start = (date("i",$time) % 15)*15;
    $end = $start+14;
    
    $start = date('Y-m-d H:',$time).$start.":00";
    $end = date('Y-m-d H:',$time).$end.":59";
    
    $projects = Project::model()->findAll("time_added >= :start AND time_added <= :end");
    
    if ($projects){
      foreach ($projects as $project){
        $rating_class = null;

        switch ($project->platform->name) {
          case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); break;
          case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); break;

          default: continue; break;
        }
        
        $rating = $rating_class->analize();

        $project->rating = $rating;
        $project->save();
      }
    }
    return 0;
  }
  
  public function actionAfter1day(){
    return 0;
  }

  public function actionAfter1week(){
    return 0;
  }
  
}