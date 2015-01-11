<?php

class MailerCommand extends CConsoleCommand{
  
  /** 
   * mark project removed from DB
   */
  private function removeFromDB($id){
    $update = Project::model()->findByPk($id);
    if ($update){
      $update->removed=1;
      $update->save();
    }
  }
  
  /** 
   * check project link if it's OK
   */
  private function checkProjectLink($link, $id = ''){
    $httpClient = new elHttpClient();
    $httpClient->setUserAgent("ff3");
    $httpClient->enableRedirects();
    $httpClient->setHeaders(array_merge(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")));
    $htmlDataObject = $httpClient->get($link,array("X-Requested-With" => "XMLHttpRequest"));
    $text = $htmlDataObject->httpBody;
    if (strpos($link, "indiegogo.com") !== false){
      if (strpos($text, "i-illustration-not_found") !== false){
        $this->removeFromDB($id);
        return false;
      }
    }else{
      if (strpos($link, "kickstarter.com") !== false){
        if (strlen($text) < 2000){
          $this->removeFromDB($id);
          return false;
        }
      }
    }
    
    return true;
  }

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
    $sql .= " (removed = 0) AND (time_added < DATE_ADD(NOW(),INTERVAL -3 HOUR)) AND (time_added >= DATE_ADD(NOW(),INTERVAL -".$hours." HOUR)) ";  // one day slot with 3h delay
    if ($sub->rating > 0)  $sql .= " AND ((rating IS NULL) OR (rating >= ".$sub->rating.")) ";

    $sql .= " ORDER BY rating DESC, time_added ASC";
    //$sql .= " LIMIT 12";
    return $sql;
  }
  
  
  /**
   * daily digest
   */
	public function actionDailyDigest($test = false){
    
    if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
    else $subscriptions = Subscription::model()->findAllByAttributes(array('daily_digest'=>1));
    
    if ($subscriptions){
      
      $hasPP = true;
      foreach ($subscriptions  as $sub){

        $paidProject = null;
        
        if ($hasPP){ // skip if there are no paid projects in a day
          $paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 1 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));
          if (count($paidProjects) > 0){
            foreach ($paidProjects as $pp){
              $platA = explode(",",$sub->platform);
              $catA = explode(",",$sub->category);
              $subCatA = explode(",",$sub->exclude_orig_category);

              if ($sub->platform && !in_array($pp->project->platform_id, $platA)) continue; // has platforms but not in
              if (in_array($pp->project->orig_category_id, $subCatA)) continue; // exclude list
              if ($sub->category && !in_array($pp->project->origCategory->category_id, $catA)) continue; // not in category

              $pp->show_count++;
              $pp->save();
              $paidProject = $pp->project; //get one project
              // set the rating higher so we know it's special
              if ($paidProject->rating) $paidProject->rating += 11;
              else $paidProject->rating = 11;
              break;
            }
          }else $hasPP = false;
        }
        
        $sql = $this->createSQL($sub, 1); 
        
        // get projects
        $projects = Project::model()->findAll($sql);
        $count = count($projects);
        
        $featured = $regular = $regularNull = array();
        $i = 0;
        foreach ($projects as $project){
          $i++;
          if (($paidProject) && ($paidProject->id == $project->id)) continue; // skip featured project from the list
          
          if (!$this->checkProjectLink($project->link,$project->id)) continue;
          
          if ($i <= 4) $featured[] = $project;
          else{
            if ($project->rating == null) $regularNull[] = $project;
            else $regular[] = $project;
          }
        }

        if ($paidProject) array_unshift($featured,$paidProject);  //add to the beginning of the queue
        
        
        shuffle($regularNull);
        $regularNull = array_slice($regularNull,0,4);
        $regular = array_merge(array_slice($regular,0,8-count($regularNull)),$regularNull);
        
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
  
  public function actionTestDailyDigest(){
    $this->actionDailyDigest(true);
  }
  
  /**
   * 
   */
  public function actionWeeklyDigest($test = false){
    
    if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
    else $subscriptions = Subscription::model()->findAllByAttributes(array('weekly_digest'=>1));

    if ($subscriptions){

      $paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 2 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));
      
      foreach ($subscriptions  as $sub){

        $paidProject = null;
        
        foreach ($paidProjects as $pp){
          $platA = explode(",",$sub->platform);
          $catA = explode(",",$sub->category);
          $subCatA = explode(",",$sub->exclude_orig_category);

          if ($sub->platform && !in_array($pp->project->platform_id, $platA)) continue; // has platforms but not in
          if (in_array($pp->project->orig_category_id, $subCatA)) continue; // exclude list
          if ($sub->category && !in_array($pp->project->origCategory->category_id, $catA)) continue; // not in category

          $pp->show_count++;
          $pp->save();
          $paidProject = $pp->project; //get one project
          // set the rating higher so we know it's special
          if ($paidProject->rating) $paidProject->rating += 11;
          else $paidProject->rating = 11;
          break;
        }
        
        
        $sql = $this->createSQL($sub, 7);
        
        // get projects
        $projects = Project::model()->findAll($sql);
        $count = count($projects);
        
        $featured = $regular = $regularNull = array();
        $i = 0;
        foreach ($projects as $project){
          $i++;
          if (($paidProject) && ($paidProject->id == $project->id)) continue; // skip featured project from the list
          
          if (!$this->checkProjectLink($project->link,$project->id)) continue;
          
          if ($i <= 4) $featured[] = $project;
          else{
            if ($project->rating == null) $regularNull[] = $project;
            else $regular[] = $project;
          }
        }
        
        if ($paidProject) array_unshift($featured,$paidProject);  //add to the beginning of the queue
        
        
        shuffle($regularNull);
        $regularNull = array_slice($regularNull,0,4);
        $regular = array_merge(array_slice($regular,0,8-count($regularNull)),$regularNull);
        
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
        if ($count < 4){
          $content = 'We found just a few projects for you. <br />Maybe your rules are too strict? Consider editing your feed.<hr>';
        }

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
  
  public function actionTestWeeklyDigest(){
    $this->actionWeeklyDigest(true);
  }  
  
  /**
   * validates that parser has parsed something in the last few hours
   */
  public function actionValidateParsers(){
    $hours = 8;
    $ksCount = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -".$hours." HOUR) AND platform_id = 1");
    $iggCount = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -".$hours." HOUR) AND platform_id = 2");
    
    if (($ksCount < 15) || ($iggCount < 15)){
    
      // create message
      $message = new YiiMailMessage;
      $message->view = 'system';
      $message->subject = "Parser validation failed";  // 11 Dec title change
      $message->from = Yii::app()->params['scriptEmail'];
      
      $content = 'Not enough projects parsed.<br /><br />';
      if ($ksCount < 15) $content .= 'Kickstarter has '.$ksCount." new projects in last 8h!<br />";
      if ($iggCount < 15) $content .= 'Indiegogo has '.$ksCount." new projects in last 8h!<br />";

      $message->setBody(array("content"=>$content), 'text/html');
      $message->setTo('info@crowdfundingrss.com');
      Yii::app()->mail->send($message);
    }
    
    return 0;
  }
    
}