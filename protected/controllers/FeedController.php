<?php

class FeedController extends Controller
{
  
  protected function beforeAction($action){
    //if ($action->id == 'rss')
      foreach (Yii::app()->log->routes as $route){
        //if ($route instanceof CWebLogRoute){
          $route->enabled = false;
        //}
      }
    return true;
  }
  
  function createRssFeed($sql, $id = null){
    $rssResponse = '';
    $rssResponse .= '<?xml version="1.0" encoding="UTF-8"?>';
    $rssResponse .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
    
//    $rssResponse .= '<rss version="2.0">';
    $rssResponse .= '<channel>';
    //$rssResponse .= '<atom:link href="http://dallas.example.com/rss.xml" rel="self" type="application/rss+xml" />';
    $rssResponse .= '<atom:link rel="self" type="application/rss+xml" href="http://www.crowdfundingrss.com/feed/previewRss" />';
    
    $rssResponse .= '<title>Crowdfounding RSS</title>';
    $rssResponse .= '<link>'.Yii::app()->params['absoluteHost'].'</link>';
    $rssResponse .= '<description>All your crowdfunding projects in one place.</description>';
    $rssResponse .= '<language>en-us</language>';
    $rssResponse .= '<ttl>5</ttl>';
    
// get projects
    $projects = Project::model()->findAll($sql);
    // CREATE RSS
    $i = 0;
    foreach ($projects as $project){
      $rssResponse .= '<item>';
      $rssResponse .= '<title>' . htmlspecialchars($project->title) . '</title>';
      $rssResponse .= '<pubDate>' . date("D, d M Y H:i:s e",strtotime($project->time_added)) . '</pubDate>';
      $rssResponse .= '<category>' . htmlspecialchars($project->origCategory->name) . '</category>';
      if ($id){
        $rssResponse .= '<link><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link,'i'=>$id)) . ']]></link>';
        $rssResponse .= '<guid><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link,'i'=>$id)) . ']]></guid>';
      }else{
        $rssResponse .= '<link><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link)) . ']]></link>';
        $rssResponse .= '<guid><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl",array("l"=>$project->link)) . ']]></guid>';
      }
      
      $desc = '';
      //$desc.= "<strong>".$project->platform->name."</strong> - ".$project->origCategory->name." <br />";
      $desc.= '<img src="' . $project->image . '" alt=""/><br />';

      $desc.= "<br />".$project->description." <br />";
      
      $desc.= "<br /><strong>".$project->platform->name."</strong> - ".$project->origCategory->name." ";//." <br />";
      if (!empty($project->creator)) $desc.= "<br />Creator of project: <i>".$project->creator."</i> ";
      //if (!empty($project->location)) $desc.= " \nCreator of project: ".$project->location;
      if (!empty($project->goal)) $desc.= "<br />Project goal: <strong>".$project->goal."</strong>";
      if (!empty($project->type_of_funding)){
        if ($project->type_of_funding == 0) $desc.= " Fixed funding";
        else $desc.= " Flexible funding";
      }
      $rssResponse .= '<description>' . htmlspecialchars($desc,ENT_COMPAT | ENT_HTML401,'UTF-8') . '</description>';
//      $rssResponse .= '<description>' . $project->description . '</description>';
//      $rssResponse .= '<author>' . $project->creator . '</author>';
      $rssResponse .= "</item>\n";
      //if ($i++ > 20) break;
    }

    $rssResponse .= '</channel>';
    $rssResponse .= '</rss>';
    
    return $rssResponse;
  }
  
  /**
   * validate if just ID's in table just to be safe
   */
  function validateId($string){
    $array = explode(",", $string);
    $string = '';
    foreach ($array as $value){
      if (is_numeric($value)){
        if ($string) $string .= ',';
        $string .= $value;
      }
    }
    return $string;
  }
  
  /**
* all hidden profiles will be notified every second week
*/
  public function actionRss($data){
    Yii::app()->clientScript->reset();
    $this->layout = 'none';
    
    //header('Content-Type', 'application/rss+xml;charset=utf-8'); 
    header('Content-Type: application/rss+xml; charset=UTF-8');
    mb_internal_encoding("UTF-8"); 
    
    
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
    
    $subcat = array();
    if ($this->validateId($sub->exclude_orig_category)){
      $subcat = explode(",",$this->validateId($sub->exclude_orig_category));
    }
    
    $sql = '';
    if ($this->validateId($sub->category)){
      $orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($sub->category)."))");
      
      $allCats = array();
      foreach ($orgCat as $cat){
        if (!in_array($cat->id, $subcat)) $allCats[$cat->id] = $cat->id;
      }
      $sql .= " (orig_category_id IN (".implode(',',$allCats).")) AND ";
    }
    if ($this->validateId($sub->platform)) $sql .= " (platform_id IN (".$this->validateId($sub->platform).")) AND ";
    else{
      $platforms = Platform::model()->findAll("active = :active",array(":active"=>0));
      $selplat = '';
      foreach ($platforms as $platform){
        if ($selplat) $selplat .= ',';
        $selplat .= $platform->id;
      }
      if ($selplat) $sql .= " (platform_id NOT IN (".$selplat.")) AND ";
    }
    $sql .= " time_added > DATE_ADD(NOW(),INTERVAL -3 HOUR)";  
    $sql .= " ORDER BY time_added DESC";
    
    
    // echo rss
    echo $this->createRssFeed($sql,$sub->id);
    Yii::app()->end();
  }
  
  /**
   * 
   */
  public function actionDownloadRss(){
    
    Yii::app()->clientScript->reset();
    $this->layout = 'none';
    
    header('Content-Type: application/rss+xml; charset=UTF-8');
    mb_internal_encoding("UTF-8"); 

    $subcat = array();
    if (!empty($_POST['subcategory']) && ($this->validateId($_POST['subcategory'])) ){
      $subcat = explode(",",$this->validateId($_POST['subcategory']));
    }
    
    $sql = '';
    if (!empty($_POST['category']) && ($this->validateId($_POST['category'])) ){
      $orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($_POST['category'])."))");
      
      $allCats = array();
      foreach ($orgCat as $cat){
        if (in_array($cat->id, $subcat))  $allCats[$cat->id] = $cat->id;
      }
      if (implode(',',$allCats) != '')  $sql .= " (orig_category_id IN (".implode(',',$allCats).")) AND ";
    }
    if (!empty($_POST['platform']) && ($_POST['platform'] != '0') && ($this->validateId($_POST['platform'])) ){
      $sql .= " (platform_id IN (".$this->validateId($_POST['platform']).")) AND ";
    }else{
      $platforms = Platform::model()->findAll("active = :active",array(":active"=>0));
      $selplat = '';
      foreach ($platforms as $platform){
        if ($selplat) $selplat .= ',';
        $selplat .= $platform->id;
      }
      if ($selplat) $sql .= " (platform_id NOT IN (".$selplat.")) AND ";
    }
    //$sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 HOUR)";
    $sql .= " 1";
    $sql .= " ORDER BY time_added DESC"
           ." LIMIT 10";
    
    //echo $sql;
    echo $this->createRssFeed($sql);
    Yii::app()->end();
  }
  
  
  public function actionPreviewRss(){
    $this->layout = 'blank';
    
    $subcat = array();
    if (!empty($_POST['subcategory']) && ($this->validateId($_POST['subcategory'])) ){
      $subcat = explode(",",$this->validateId($_POST['subcategory']));
    }
    
    $sql = '';
    if (!empty($_POST['category']) && ($this->validateId($_POST['category'])) ){
      $orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($_POST['category'])."))");
      
      $allCats = array();
      foreach ($orgCat as $cat){
        if (in_array($cat->id, $subcat))  $allCats[$cat->id] = $cat->id;
      }
      if (implode(',',$allCats) != '')  $sql .= " (orig_category_id IN (".implode(',',$allCats).")) AND ";
    }
    if (!empty($_POST['platform']) && ($_POST['platform'] != '0') && ($this->validateId($_POST['platform'])) ){
      $sql .= " (platform_id IN (".$this->validateId($_POST['platform']).")) AND ";
    }else{
      $platforms = Platform::model()->findAll("active = :active",array(":active"=>0));
      $selplat = '';
      foreach ($platforms as $platform){
        if ($selplat) $selplat .= ',';
        $selplat .= $platform->id;
      }
      if ($selplat) $sql .= " (platform_id NOT IN (".$selplat.")) AND ";
    }
    
    $numOfresults = Yii::app()->db->createCommand("SELECT COUNT(*) FROM project WHERE ".$sql." time_added > DATE_ADD(CURDATE(), INTERVAL -4 DAY)")->queryScalar();
    $numOfresults = round($numOfresults / 4);
    
    //$sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 HOUR)";
    
    $sql .= " 1";
    $sql .= " ORDER BY time_added DESC"
           ." LIMIT 10";
    
    //if (!Yii::app()->user->isGuest) echo "SQL:".$sql;
    
    $projects = Project::model()->findAll($sql);
    $cat = $plat = '';
    if (isset($_POST['category'])) $cat = $_POST['category'];
    if (isset($_POST['platform'])) $plat = $_POST['platform'];
    if (isset($_POST['subcategory'])) $subcat = $_POST['subcategory'];
    $this->render('previewRss',array('projects'=>$projects,'cat'=>$cat,'plat'=>$plat, 'subcat'=>$subcat, 'numOfDailyResults'=>$numOfresults));
  }  
  
  
  
  /**
   * tracking RSS link clicks and redirecting them
   */
  public function actionRl($l,$i = null) {
    // !!!log clicks
    $project = Project::model()->findByAttributes(array('link'=>$l));
    if ($project){
      $feedClick = new FeedClickLog();
      $feedClick->project_id = $project->id;
      $feedClick->subscription_id = $i;
      $feedClick->save();
    }
    
    $this->redirect($l);
    Yii::app()->end();
  }
  
  
}
