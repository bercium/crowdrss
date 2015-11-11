<?php

class BrowseController extends Controller
{
    private function getCategories($category){
        if ($category){
			$category_data = Category::model()->find("name = :name", array(":name"=>$category));
			if ($category_data){
                $category_array = OrigCategory::model()->findAll("category_id = :cid", array(":cid"=>$category_data->id));
                $categories = array();
                if ($category_array){
                    foreach ($category_array as $row){
                        $categories[] = $row->id;
                    }
                    $category = " AND orig_category_id IN (".implode(', ',$categories).") ";
                }else $category = '';
            }else $category = '';
		}
        return $category;
    }
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionTop($count = 10, $platform = '', $category = ''){
		//$this->layout = 'default';

		$count += 0;
		//if ($count < 10) $count = 10;
		if ($count > 100) $count = 100;
		
		$category = $this->getCategories($category);
		
		$projects = Project::model()->findAll("time_added >= :date ".$category." ORDER BY rating DESC, time_added DESC LIMIT :limit",
											  array(":date"=>date('Y-m-d',strtotime('-1 week')),
													":limit"=>$count));


		$title = "Top ".$count." projects";
		if ($platform) $title .= " on ".$platform;
		$this->render('list',array("title"=>$title,"projects"=>$projects,"allPlatforms"=>($platform == ''),"listType"=>"top"));
	}
  
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionBottom($count = 10, $platform = '', $category = ''){
		//$this->layout = 'default';

		$count += 0;
		//if ($count < 10) $count = 10;
		if ($count > 100) $count = 100;
        
		$category = $this->getCategories($category);
		
		$projects = Project::model()->findAll("time_added >= :date ".$category." ORDER BY rating ASC, time_added DESC LIMIT :limit",
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
	public function actionTopDaily($count = 10, $platform = '', $category = ''){
		//$this->layout = 'default';

		$count += 0;
		//if ($count < 10) $count = 10;
		if ($count > 50) $count = 50;
        
        $category = $this->getCategories($category);
		
		$projects = Project::model()->findAll("time_added >= :date ".$category." ORDER BY rating DESC, time_added DESC LIMIT :limit",
											   array(":date"=>date('Y-m-d H:00:00',strtotime('-24 hours')),
													":limit"=>$count));

		$title = "Top projects for today";
		$this->render('list',array("title"=>$title,"projects"=>$projects,"allPlatforms"=>true,"listType"=>"top"));
	}  

}
