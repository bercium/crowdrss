<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RatingCommand extends CConsoleCommand{
  
  /**
   * 
   */
  private function loopProjects($projects){
    echo count($projects)."\n<br>";
    if (!$projects) return;
    
    foreach ($projects as $project){
      $rating_class = null;

      switch ($project->platform->name) {
        case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); echo "ks"; break;
        case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); echo "igg".$project->link; break;

        default: continue; break;
      }
      if ($rating_class == null) continue;
      
      $rating = $rating_class->analize();

      echo $project->rating."-".$rating." \n<br>";
      $project->rating = $rating;
      $project->save();
    }
  }
  
  /**
   * 
   */
  public function actionAfter3h(){

    $time = strtotime("-3 hours");
    
    $start = floor(date("i",$time) / 15)*15;
    $end = $start+14;
    
    if ($start == 0) $start = '00';
    
    $start = date('Y-m-d H:',$time).$start.":00";
    $end = date('Y-m-d H:',$time).$end.":59";
    //echo $start." - ".$end;
    
    $projects = Project::model()->findAll("time_added >= :start AND time_added <= :end", array(":start"=>$start, ":end"=>$end));
    
    $this->loopProjects($projects);
    return 0;
  }
  
  /**
   * 
   */
  public function actionAfter1day(){
    $start = strtotime("-1 day -3 hours");
    $end = strtotime("-3 hours");
    
    $start = date('Y-m-d H:',$start)."00:00";
    $end = date('Y-m-d H:',$end)."00:00";
    
    $projects = Project::model()->findAll("time_added >= :start AND time_added < :end", array(":start"=>$start, ":end"=>$end));
    
    $this->loopProjects($projects);
    
    return 0;
  }

  /**
   * 
   */
  public function actionAfter1week(){
    $start = strtotime("-1 week -3 hours");
    $end = strtotime("-3 hours");
    
    $start = date('Y-m-d H:',$start).$start."00:00";
    $end = date('Y-m-d H:',$end)."00:00";
    
    $projects = Project::model()->findAll("time_added >= :start AND time_added < :end", array(":start"=>$start, ":end"=>$end));
    
    $this->loopProjects($projects);
    
    return 0;
  }
  
}