<?php

class BrowseController extends Controller
{
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionTop($count, $platform = '', $category = ''){
    //$this->layout = 'default';
    
    $count += 0;
    if ($count < 10) $count = 10;
    if ($count > 100) $count = 100;
    $projects = Project::model()->findAll("time_added >= :date ORDER BY rating DESC, time_added DESC LIMIT :limit",
                                          array(":date"=>date('Y-m-d',strtotime('-100 week')),
                                                ":limit"=>$count));
    
    
    $title = "Top ".$count." projects";
    if ($platform) $title .= " on ".$platform;
		$this->render('list',array("title"=>$title,"projects"=>$projects,"allPlatforms"=>($platform == ''),"listType"=>"top"));
	}
  
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionBottom($count, $platform = '', $category = ''){
    //$this->layout = 'default';
    
    $count += 0;
    if ($count < 10) $count = 10;
    if ($count > 100) $count = 100;
    $projects = Project::model()->findAll("time_added >= :date AND NOT ISNULL(rating) ORDER BY rating ASC, time_added DESC LIMIT :limit",
                                          array(":date"=>date('Y-m-d',strtotime('-1 week')),
                                                ":limit"=>$count));
    
    $title = "Bottom ".$count." projects";
    if ($platform) $title .= " on ".$platform;
		$this->render('list',array("title"=>$title,"projects"=>$projects,"allPlatforms"=>($platform == ''),"listType"=>"bottom"));
	}
  
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionTopDaily($count = 10){
    //$this->layout = 'default';

    $count += 0;
    if ($count < 10) $count = 10;
    if ($count > 50) $count = 50;
    $projects = Project::model()->findAll("time_added >= :date ORDER BY rating DESC, time_added DESC LIMIT :limit",
                                          array(":date"=>date('Y-m-d H:00:00',strtotime('-24 hours')),
                                                ":limit"=>$count));
    
    $title = "Top projects for today";
    $this->render('list',array("title"=>$title,"projects"=>$projects,"allPlatforms"=>true,"listType"=>"top"));
	}  

}
