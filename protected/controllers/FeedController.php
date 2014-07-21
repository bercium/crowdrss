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
    $rssResponse .= '<title>Crowdfounding RSS</title>';
    $rssResponse .= '<link>http://crowdrss.eberce.si</link>';
    $rssResponse .= '<description>All your crowdfounding projects at one place</description>';
    $rssResponse .= '<language>en</language>';
    $rssResponse .= '<ttl>15</ttl>';
//    $rssResponse .= '<webMaster>team@eberce.si</webMaster>';
    
    //$data  hash tag for 
    // get subscription type of projects
    $sub = Subscription::model()->findByAttributes(array('hash'=>$data,'rss'=>1));
    if (!$sub){
      throw new CHttpException(404,'The specified feed was not found.');
    }
    
//    Tole je treba link zgenerirat na katerem bo rss od specifičnega uporabnika in ga dat v href
//    $rssResponse .= '<atom:link href="' . $link z hashom do rss . '" rel="self" type="application/rss+xml" />';
    
    // project constrains
    $sql = '';
    if ($sub->category){
      $orgCat = OrigCategory::model()->findAll("(category_id in (".$sub->category."))");
      
      $allCats = array();
      foreach ($orgCat as $cat){
        $allCats[$cat->id] = $cat->id;
      }
      $sql .= " (orig_category_id in (".implode(',',$allCats).")) AND ";
    }
    if ($sub->platform) $sql .= " (platform_id in (".$sub->platform.")) AND ";
    $sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 DAY)";
    
     // get projects
    $projects = Project::model()->findAll($sql);
    // CREATE RSS
    foreach ($projects as $project){
      $rssResponse .= '<item>';
      $rssResponse .= '<title>' . $project->title  . '</title>';
      $rssResponse .= '<pubDate>' . $project->time_added  . '</pubDate>';
      $rssResponse .= '<category>' . $project->origCategory->name  . '</category>';
      $rssResponse .= '<link>' . $project->link  . '</link>';
      $rssResponse .= '<description>&lt;img width="680" height="510" src="' . $project->image  . '" class="attachment-large wp-post-image" alt="Startupbootcamp" /&gt;&amp;&lt br $gt;' . $project->description  . '</description>';
      $rssResponse .= '<author>' . $project->creator  . '</author>';
      $rssResponse .= '</item>';
    }

    $rssResponse .= '</channel>';
    $rssResponse .= '</rss>';
    
    // echo rss
    echo $rssResponse;
    Yii::app()->end();
  }
}
