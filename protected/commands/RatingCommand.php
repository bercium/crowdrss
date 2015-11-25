<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RatingCommand extends CConsoleCommand{
  
  /**
   * 
   */
  private function loopProjects($projects,$filename='',$exclude = ''){
    //echo count($projects)."\n<br>";
    if (!$projects) return 0;
    $i = 1;
    if ($exclude != array()) $exclude = explode (",", $exclude);
    else $exclude = array();
    
    foreach ($projects as $project){
      if (strtotime($project->end) < time()) continue; // project ended
      if (in_array($project->id, $exclude)) continue; //exclude projects
      if (!$project->platform->download) continue; // exclude platforms that shouldn't be downloaded
      
      $rating_class = null;
      
      if ($filename){
        $fp = fopen($filename, "a");
        fwrite($fp, $i." - ".$project->id." (".date("H:i:s")."): ".$project->title);
      }

      //echo ($i++).": ".date("c")." ";
      switch ($project->platform->name) {
        case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); /*echo "ks ".$project->link;*/ break;
        case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); /*echo "igg ".$project->link;*/ break;

        default: /*continue;*/ break;
      }
      if ($rating_class == null){
        if ($filename){
          fwrite($fp, "  <<".$project->id.">>\n");
          //fwrite($fp, "\n");
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
      if ($rating != null){
		  $project->rating = $rating;
		  $project->save();
	  }
      $i++;
    }
    
    return $i;
  }
  
  private function min15Span($time){
    $start = floor(date("i",$time) / 15)*15;
    $end = $start+14;
    
    if ($start == 0) $start = '00';
    
    $start = date('Y-m-d H:',$time).$start.":00";
    $end = date('Y-m-d H:',$time).$end.":59";
    
    return array('start'=>$start,'end'=>$end);
  }
  
  /**
   * 
   */
  public function actionFirstDay(){
    
    $time3 = $this->min15Span(strtotime("-3 hours"));
    $time9 = $this->min15Span(strtotime("-9 hours"));
    $time18 = $this->min15Span(strtotime("-18 hours"));
    //3,9,18 // ,30,42
    //echo $start." - ".$end;
    
    $projects = Project::model()->findAll("(time_added BETWEEN :start3 AND :end3) OR 
                                           (time_added BETWEEN :start9 AND :end9) OR 
                                           (time_added BETWEEN :start18 AND :end18)", 
                                          array(":start3"=>$time3['start'], ":end3"=>$time3['end'],
                                                ":start9"=>$time9['start'], ":end9"=>$time9['end'],
                                                ":start18"=>$time18['start'], ":end18"=>$time18['end'],
                                               )
                                         );
    
    $this->loopProjects($projects);
    return 0;
  }
  
  
  public function actionAfterFirstDay(){
    set_time_limit(0);
    
    $filename = Yii::app()->getRuntimePath()."/rating-log.txt";
    
    if (file_exists($filename)){
      // do a backup of failed file
      $nfn = "failed-".date("Y-m-d H:i").".txt";
      if (!rename($filename, Yii::app()->getRuntimePath()."/".$nfn)){
        $fc = file_get_contents($filename);
        file_put_contents(Yii::app()->getRuntimePath()."/".$nfn,$fc);
      }
      
      if (date("H") == '04'){
        $failedFiles = array();
        $content = "Failed file(s): ";
        foreach (glob(Yii::app()->getRuntimePath()."/".date("Y-m-d")."*.txt") as $filename) {
            $content .= "<br />".basename($filename);
        }

        $message = new YiiMailMessage;
        $message->view = 'system';
        $message->subject = "Failed rating".date("Y-m-d");  
        $message->from = Yii::app()->params['scriptEmail'];

        //$content = "File: ".$nfn;
        $message->setBody(array("content"=>$content), 'text/html');
        $message->setTo("info@crowdfundingrss.com");
        Yii::app()->mail->send($message);
      }
    }
    
    $time1 = $this->min15Span(strtotime("-24 hours"));
    $time2 = $this->min15Span(strtotime("-48 hours"));
    $time3 = $this->min15Span(strtotime("-72 hours"));
    $time4 = $this->min15Span(strtotime("-96 hours"));
    $time5 = $this->min15Span(strtotime("-128 hours"));
    $time6 = $this->min15Span(strtotime("-144 hours"));
    $time7 = $this->min15Span(strtotime("-168 hours"));

    $projects = Project::model()->findAll("(time_added BETWEEN :start1 AND :end1) OR 
                                           (time_added BETWEEN :start2 AND :end2) OR 
                                           (time_added BETWEEN :start3 AND :end3) OR 
                                           (time_added BETWEEN :start4 AND :end4) OR 
                                           (time_added BETWEEN :start5 AND :end5) OR 
                                           (time_added BETWEEN :start6 AND :end6) OR 
                                           (time_added BETWEEN :start7 AND :end7)", 
                                          array(":start1"=>$time1['start'], ":end1"=>$time1['end'],
                                                ":start2"=>$time2['start'], ":end2"=>$time2['end'],
                                                ":start3"=>$time3['start'], ":end3"=>$time3['end'],
                                                ":start4"=>$time3['start'], ":end4"=>$time3['end'],
                                                ":start5"=>$time3['start'], ":end5"=>$time3['end'],
                                                ":start6"=>$time3['start'], ":end6"=>$time3['end'],
                                                ":start7"=>$time3['start'], ":end7"=>$time3['end'],
                                               )
                                         );
    
    $fp = fopen($filename, "a");
    fwrite($fp, 'Date-time: '.date("Y-m-d H:i:s")."\n");
    fwrite($fp, 'Num of projects: '.count($projects)."\n\n");
    fclose($fp);
    
    $checked = $this->loopProjects($projects,$filename);

    //$fc = count($projects).' - '.$checked;
    //file_put_contents(Yii::app()->getRuntimePath()."/info-".date("Y-m-d H:i").".txt", $fc);
    
    unlink($filename);
    
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
    $succFailed = 0;
    $ids = '';
    if (!$firsttime){
      if (file_exists(Yii::app()->getRuntimePath()."/".$days."-ok.txt")) return 0; // everything OK
      $fp = fopen($filename, "a");
      fwrite($fp, "\n\nRETRY\n");
      
      $getIds = file_get_contents($filename);
      $ids = array();
      $succFailed = preg_match_all("/<<(\d+)>>/",$getIds,$ids);
      if (isset($ids[0])) $ids = str_replace ("<<", "", str_replace (">>", "", implode(",", $ids[0])));
      else $ids = '';
      // load ids of unfailed projects
      fwrite($fp, 'Exclude: '.$ids."\n\n");
      fclose($fp);
    }else{
      if (file_exists($filename)){
        unlink($filename);
      }
      if (file_exists(Yii::app()->getRuntimePath()."/".$days."-ok.txt")){
        unlink(Yii::app()->getRuntimePath()."/".$days."-ok.txt");
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
          $content .= $c.": FAILED";
          if ($succFailed) $content .= " ".$succFailed." rated in first try";
                  
          $content .="<br />";
          
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
    if ($ids != '') $projects = Project::model()->findAll("(time_added BETWEEN :start AND :end) AND id NOT IN (".$ids.")", array(":start"=>$start, ":end"=>$end));
    else $projects = Project::model()->findAll("time_added BETWEEN :start AND :end", array(":start"=>$start, ":end"=>$end));
    
    
    $fp = fopen($filename, "a");
    fwrite($fp, 'Num of projects: '.count($projects)."\n\n");
    fclose($fp);
    
    // do the magic
    $checked = $this->loopProjects($projects,$filename,$ids);
    
    
    //summary
    $filename = Yii::app()->getRuntimePath()."/".$days."-ok.txt";
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