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
    //echo count($projects)."\n<br>";
    if (!$projects) return 0;
    $i = 0;
    
    foreach ($projects as $project){
      if (strtotime($project->end) < time()) continue; // project ended
      
      $rating_class = null;

      //echo ($i++).": ".date("c")." ";
      switch ($project->platform->name) {
        case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); /*echo "ks ".$project->link;*/ break;
        case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); /*echo "igg ".$project->link;*/ break;

        default: continue; break;
      }
      if ($rating_class == null) continue;
      
      $rating = $rating_class->analize();

      //echo $project->rating."-".$rating." \n<br>";
      $project->rating = $rating;
      $project->save();
      $i++;
    }
    
    return $i;
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
    $start = strtotime("-1 day -2 hours");
    $end = strtotime("-2 hours");
    
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
    set_time_limit(0);
    $start = strtotime("-1 week");
    $end = strtotime("-1 day");
//    $start = strtotime("-1 week -4 hours");
//    $end = strtotime("-4 hours");
    
    $start = date('Y-m-d H:',$start).$start."00:00";
    $end = date('Y-m-d H:',$end)."00:00";
    
    $projects = Project::model()->findAll("time_added >= :start AND time_added < :end", array(":start"=>$start, ":end"=>$end));
    
    
    $start = time();
    
    $parsed = $this->loopProjects($projects);
    
    $end = time();
    
    // create message
    $message = new YiiMailMessage;
    $message->view = 'system';
    $message->subject = "Report: one week rating";  // 11 Dec title change
    $message->from = Yii::app()->params['scriptEmail'];

    $content = 'Projects: '.$parsed." / ".count($projects)."<br />";
    $content = 'Time: '.  timeDifference($st, $end)."<br />";

    $message->setBody(array("content"=>$content), 'text/html');
    $message->setTo('info@crowdfundingrss.com');
    Yii::app()->mail->send($message);    
    
    return 0;
  }
  
}