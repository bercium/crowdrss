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
    $rssResponse .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
    $rssResponse .= '<channel>';
    $rssResponse .= '<title></title>';
    $rssResponse .= '<link></link>';
    $rssResponse .= '<description></description>';
    $rssResponse .= '<language>en</language>';
    $rssResponse .= '<ttl>15</ttl>';
    $rssResponse .= '<webMaster></webMaster>';
    
    //$data  hash tag for 
    // get subscription type of projects
    $sub = Subscription::model()->findByAttributes(array('hash'=>$data,'rss'=>1));
    if (!$sub){
      throw new CHttpException(404,'The specified feed was not found.');
    }
    
//    Tole je treba link zgenerirat na katerem bo rss od specifičnega uporabnika in ga dat v href
//    $rssResponse .= '<atom:link href="http://rss.torrentleech.org/9c2e20d5a657fc62cea8" rel="self" type="application/rss+xml" />';

    // get projects
    $sql = '';
    if ($sub->category) $sql .= " (category_id in (".$sub->category.")) AND ";
    if ($sub->platform) $sql .= " (platform_id in (".$sub->platform.")) AND ";
     $sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 DAY)";
    
    $projects = Project::model()->findAll($sql);
    // CREATE RSS
    foreach ($projects as $project){
      $rssResponse .= '<item>';
      $rssResponse .= '<title></title>';
      $rssResponse .= '<pubDate></pubDate>';
      $rssResponse .= '<category></category>';
      $rssResponse .= '<link></link>';
      $rssResponse .= '<description></description>';
      $rssResponse .= '<author></author>';
      $rssResponse .= '</item>';
    }

    $rssResponse .= '</channel>';
    $rssResponse .= '</rss>';
    
    // echo rss
    echo $rssResponse;
    Yii::app()->end();
    
  }
  
  
}
