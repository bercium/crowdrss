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
    
    //header('Content-Type', 'application/rss+xml;charset=utf-8'); 
    header('Content-Type: application/rss+xml; charset=ISO-8859-1');
    
    $rssResponse = '';
    $rssResponse .= '<?xml version="1.0" encoding="UTF-8"?>';
    $rssResponse .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" rel="self" type="application/rss+xml">';
//    $rssResponse .= '<rss version="2.0">';
    $rssResponse .= '<channel>';
    $rssResponse .= '<title>Crowdfounding RSS</title>';
    $rssResponse .= '<link>'.Yii::app()->params['absoluteHost'].'</link>';
    $rssResponse .= '<description>All your crowdfunding projects in one place.</description>';
    $rssResponse .= '<language>en-us</language>';
    $rssResponse .= '<ttl>1</ttl>';
//   $rssResponse .= '<webMaster>team@eberce.si</webMaster>';
    
    //$data hash tag for
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
    $i = 0;
    foreach ($projects as $project){
      $rssResponse .= '<item>';
      $rssResponse .= '<title>' . htmlspecialchars($project->title) . '</title>';
      $rssResponse .= '<pubDate>' . date("D, d M Y G:i:s e",strtotime($project->time_added)) . '</pubDate>';
      $rssResponse .= '<category>' . htmlspecialchars($project->origCategory->name) . '</category>';
      $rssResponse .= '<link><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link,'i'=>$sub->id)) . ']]></link>';
      $rssResponse .= '<guid><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link,'i'=>$sub->id)) . ']]></guid>';
  
      $desc = '';
      $desc.= $project->platform->name.": ".$project->origCategory->name."<br />";
      $desc.= '<img src="' . $project->image . '"/><br />';

      $desc.= "<br />".$project->description;
      
      if (!empty($project->creator)) $desc.= "<br />Creator of project: ".$project->creator;
      //if (!empty($project->location)) $desc.= " \nCreator of project: ".$project->location;
      if (!empty($project->goal)) $desc.= "<br />Project goal: ".$project->goal;
      if (!empty($project->type_of_funding)){
        if ($project->type_of_funding == 0) $desc.= " Fixed funding";
        else $desc.= " Flexible funding";
      }
      $rssResponse .= '<description><![CDATA[' . $desc . ']]></description>';
//      $rssResponse .= '<description>' . $project->description . '</description>';
//      $rssResponse .= '<author>' . $project->creator . '</author>';
      $rssResponse .= "</item>\n";
      if ($i++ > 20) break;
    }

    $rssResponse .= '</channel>';
    $rssResponse .= '</rss>';
    
    // echo rss
    echo $rssResponse;
    Yii::app()->end();
  }
  
  
  
  /**
   * tracking RSS link clicks and redirecting them
   */
  public function actionRl($l,$i) {
    // !!!log clicks
    $this->redirect($l);
    Yii::app()->end();
  }
  
  
}
