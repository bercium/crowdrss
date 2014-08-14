<?php

class MailerCommand extends CConsoleCommand{

  private function createSQL($sub, $days = 1){
    $sql = '';
    if ($sub->category){
    //if ($this->validateId($sub->category)){
      //$orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($sub->category)."))");
      
      $subcat = array();
      if ($sub->exclude_orig_category){
  //    if ($this->validateId($sub->exclude_orig_category)){
        $subcat = explode(",",$sub->exclude_orig_category);
  //      $subcat = explode(",",$this->validateId($sub->exclude_orig_category));
      }
      
      $orgCat = OrigCategory::model()->findAll("(category_id IN (".$sub->category."))");

      $allCats = array();
      foreach ($orgCat as $cat){
        if (!in_array($cat->id, $subcat)) $allCats[$cat->id] = $cat->id;
      }
      $sql .= " (orig_category_id IN (".implode(',',$allCats).")) AND ";
    }
    if ($sub->platform) $sql .= " (platform_id IN (".$sub->platform.")) AND ";
    //if ($this->validateId($sub->platform)) $sql .= " (platform_id IN (".$this->validateId($sub->platform).")) AND ";
    else{
      $platforms = Platform::model()->findAll("active = :active",array(":active"=>0));
      $selplat = '';
      foreach ($platforms as $platform){
        if ($selplat) $selplat .= ',';
        $selplat .= $platform->id;
      }
      if ($selplat) $sql .= " (platform_id NOT IN (".$selplat.")) AND ";
    }
    //$sql .= ' 1 ';
    $hours = $days*24+3;
    $sql .= " (time_added < DATE_ADD(NOW(),INTERVAL -3 HOUR)) AND (time_added >= DATE_ADD(NOW(),INTERVAL -".$hours." HOUR)) ";  // one day slot with 3h delay
    if ($sub->rating > 0)  $sql .= " AND (rating IS NULL OR rating >= ".$sub->rating.") ";

    $sql .= " ORDER BY rating DESC, time_added ASC";
    //$sql .= " LIMIT 12";
    
    return $sql;
  }
  
  
  /**
   * daily digest
   */
	public function actionDailyDigest(){
    $subscriptions = Subscription::model()->findAllByAttributes(array('daily_digest'=>1));
    if ($subscriptions){
      foreach ($subscriptions  as $sub){

        $sql = $this->createSQL($sub, 1);

        // get projects
        $projects = Project::model()->findAll($sql);
        $count = count($projects);
        
        $regularNull = array();
        $i = 0;
        foreach ($projects as $project){
          $i++;
          if ($i <= 4) $featured[] = $project;
          else{
            if ($project->rating == null) $regularNull[] = $project;
            else $regular[] = $project;
          }
        }
        
        shuffle($regularNull);
        $regular = array_merge(array_slice($regular, 0,8-count($regularNull)),$regularNull);
        
        if (count($regular) < 4) $regular = array();
        else if (count($regular) < 8) $regular = array_slice($regular, 0, 4);
        
        
        //set mail tracking
        $tc = mailTrackingCode();
        $ml = new MailLog();
        $ml->tracking_code = mailTrackingCodeDecode($tc);
        $ml->type = 'daily-digest';
        $ml->subscription_id = $sub->id;
        $ml->save();

        $date = addOrdinalNumberSuffix(date("j", strtotime("-1 days")))." ".date("M", strtotime("-1 days"));
        // create message
        $message = new YiiMailMessage;
        $message->view = 'digest';
        $message->subject = "Your Daily Dose Of Crowdfunding Projects for ".$date;  // 11 Dec title change
        $message->from = Yii::app()->params['noreplyEmail'];

        $title = 'Top crowdfunding projects for '.$date;
        $content = '';

        // not enough projects
        if ($count < 4){
          $content = 'We found just a few projects for you. <br />Maybe your rules are too strict? Consider editing your feed.<hr>';
        }

        $editLink = absoluteURL()."/?id=".$sub->hash;

        $message->setBody(array("tc"=>$tc,"user_id"=>$sub->id,
                                "content"=>$content, "title"=>$title,
                                "featuredProjects"=>$featured, "projects"=>$regular,
                                "showEdit"=>true,"editLink"=>$editLink
                                ), 'text/html');
        $message->setTo($sub->email);
        Yii::app()->mail->send($message);
        
      }
    }
  }
  
  /**
   * 
   */
  public function actionWeeklyDigest(){
    $subscriptions = Subscription::model()->findAllByAttributes(array('weekly_digest'=>1));
    if ($subscriptions){
      foreach ($subscriptions  as $sub){

        $sql = $this->createSQL($sub, 7);

        // get projects
        $projects = Project::model()->findAll($sql);
        $count = count($projects);
        
        $regularNull = array();
        $i = 0;
        foreach ($projects as $project){
          $i++;
          if ($i <= 4) $featured[] = $project;
          else{
            if ($project->rating == null) $regularNull[] = $project;
            else $regular[] = $project;
          }
        }
        
        shuffle($regularNull);
        $regular = array_merge(array_slice($regular, 0,8-count($regularNull)),$regularNull);
        
        if (count($regular) < 4) $regular = array();
        else if (count($regular) < 8) $regular = array_slice($regular, 0, 4);
        
        
        //set mail tracking
        $tc = mailTrackingCode();
        $ml = new MailLog();
        $ml->tracking_code = mailTrackingCodeDecode($tc);
        $ml->type = 'weekly-digest';
        $ml->subscription_id = $sub->id;
        $ml->save();

        
        if (date("M", strtotime("-1 days")) == date("M", strtotime("-8 days"))){
          $date = addOrdinalNumberSuffix(date("j", strtotime("-8 days")))." - ".addOrdinalNumberSuffix(date("j", strtotime("-1 days")))." ".date("M", strtotime("-1 days"));
        }else{
          $date = addOrdinalNumberSuffix(date("j", strtotime("-8 days")))." ".date("M", strtotime("-8 days"))." - ".addOrdinalNumberSuffix(date("j", strtotime("-1 days")))." ".date("M", strtotime("-1 days"));
        }
        // create message
        $message = new YiiMailMessage;
        $message->view = 'digest';
        $message->subject = "Your Weekly Dose Of Crowdfunding Projects for ".$date;  // 11 Dec title change
        $message->from = Yii::app()->params['noreplyEmail'];

        $title = 'Top crowdfunding projects for week '.$date;
        $content = '';

        // not enough projects
        //if ($count < 4){
          $content = 'We found just a few projects for you. <br />Maybe your rules are too strict? Consider editing your feed.<hr>';
        //}

        $editLink = absoluteURL()."site/index?id=".$sub->hash;

        $message->setBody(array("tc"=>$tc,"user_id"=>$sub->id,
                                "content"=>$content, "title"=>$title,
                                "featuredProjects"=>$featured, "projects"=>$regular,
                                "showEdit"=>true,"editLink"=>$editLink
                                ), 'text/html');
        $message->setTo($sub->email);
        Yii::app()->mail->send($message);
        
      }
    }

  }
    
}