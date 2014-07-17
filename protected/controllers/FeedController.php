<?php

class FeedController extends Controller
{
  
  protected function beforeAction($action){
    if ($action->id == 'rss')
      foreach (Yii::app()->log->routes as $route){
        //if ($route instanceof CWebLogRoute){
          $route->enabled = false;
        //}
      }
    return true;
  }  
  
  /**
   * all hidden profiles will be notified every second week
   */
  public function actionRss($data){
    Yii::app()->clientScript->reset();
    $this->layout = 'none';
    
    $rssResponse = '<?xml version="1.0" encoding="UTF-8"?>';
    
    //$data  hash tag for 
    // get subscription type of projects
    $sub = Subscription::model()->findByAttributes(array('hash'=>$data,'rss'=>1));
    if (!$sub){
      throw new CHttpException(404,'The specified feed was not found.');
    }
    
    // get projects
    $sql = '';
    if ($sub->category) $sql .= " (category_id in (".$sub->category.")) AND ";
    if ($sub->platform) $sql .= " (platform_id in (".$sub->platform.")) AND ";
     $sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 DAY)";
    
    $projects = Project::model()->findAll($sql);
    // CREATE RSS
    foreach ($projects as $project){
      $rssResponse .= '';
    }
    
    // echo rss
    echo $rssResponse;
    Yii::app()->end();
    
  }
  
  
}
