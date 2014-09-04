<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RatingCommand extends CConsoleCommand{
  
  /**
   * 
   */
  private function loopProjects($projects,$filename=''){
    //echo count($projects)."\n<br>";
    if (!$projects) return 0;
    $i = 1;
    
    foreach ($projects as $project){
      if (strtotime($project->end) < time()) continue; // project ended
      
      $rating_class = null;
      
      if ($filename){
        $fp = fopen($filename, "a");
        fwrite($fp, $i.": ".$project->id.": ".$project->title);
      }

      //echo ($i++).": ".date("c")." ";
      switch ($project->platform->name) {
        case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); /*echo "ks ".$project->link;*/ break;
        case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); /*echo "igg ".$project->link;*/ break;

        default: /*continue;*/ break;
      }
      if ($rating_class == null){
        if ($filename){
          fwrite($fp, "\n");
          fclose($fp);
        }
        
        continue;
      }
      
      $rating = $rating_class->analize();
      
      if ($filename){
        fwrite($fp, ": ".$project->link." - ".$rating."  <<".$project->id.">>\n");
        fclose($fp);
      }

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
  public function actionAfterDays($days = null){
    set_time_limit(0);
    if ($days == null) $days = date("G")+1;    
    if (($days > 8 && $days < 11) || ($days > 18)) return 0;
    
    $firsttime = true;
    if ($days > 10){ // redoo for failed days
      $days -= 10;  
      $firsttime = false;
    }
    $filename = Yii::app()->getRuntimePath()."/".$days.".txt";

    
    if ($days == 1) $date = strtotime("-1 day");
    else $date = strtotime("-".$days." days");
    
    $start = date('Y-m-d',$date)." 00:00:00";
    $end = date('Y-m-d',$date)." 23:59:59";
    
//    echo $start." - ".$end;
    $ids = '';
    if (!$firsttime){
      if (file_exists(Yii::app()->getRuntimePath()."/".$days."-ok.txt")) return 0; // everything OK
      $fp = fopen($filename, "a");
      fwrite($fp, "\n\nRETRY\n");
      
      $getIds = file_get_contents($filename);
      $ids = array();
      preg_match_all("/<<(\d+)>>/",$getIds,$ids);
      if (isset($ids[0])) $ids = implode(",", $ids[0]);
      else $ids = '';
      // load ids of unfailed projects
      fwrite($fp, 'Exclude: '.$ids."\n\n");

      fclose($fp);
    }else{
      if (file_exists($filename)){
        unlink($filename);
      }
    }
    
    // write a report and mail it
    if ($days == '8'){
      $content= '';
      $failed = false;
      for ($c = 1; $c < 8; $c++){
        $fn = Yii::app()->getRuntimePath()."/".$c."-ok.txt";
        if (file_exists($fn)){
          $content .= file_get_contents($fn)."<br />";
        }else{
          $content .= $c.": FAILED<br />";
          $failed = true;
        }
      }
      if (!$failed) return 0;
      
      $message = new YiiMailMessage;
      $message->view = 'system';
      if ($firsttime) $message->subject = "Report for ".date("Y-m-d");  
      else  $message->subject = "Report for retry ".date("Y-m-d");  
      $message->from = Yii::app()->params['scriptEmail'];


      $message->setBody(array("content"=>$content), 'text/html');
      $message->setTo("info@crowdfundingrss.com");
      Yii::app()->mail->send($message);
      
      return 0;
    }
    
    $processStart = date('c');
    
    // do the projects
    if ($ids) $projects = Project::model()->findAll("(time_added BETWEEN :start AND :end) AND id NOT IN (:ids)", array(":start"=>$start, ":end"=>$end, ":ids"=>$ids));
    else $projects = Project::model()->findAll("time_added BETWEEN :start AND :end", array(":start"=>$start, ":end"=>$end));
    
    
    $fp = fopen($filename, "a");
    fwrite($fp, 'Num of projects: '.count($projects)."\n\n");
    fclose($fp);
    
    // do the magic
    $checked = $this->loopProjects($projects,$filename);
    
    
    $filename = Yii::app()->getRuntimePath()."/".$days."-ok.txt";
    if (file_exists($filename)){
      unlink($filename);
    }
    $fp = fopen($filename, "a");
    fwrite($fp, $days.': '.$processStart." - ".date('c')."\n".$checked." / ".count($projects)."\n");
    fclose($fp);    
    
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