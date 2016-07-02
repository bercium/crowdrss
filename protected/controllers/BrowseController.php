<?php

class BrowseController extends Controller
{
    
    /**
     * 
     * @param type $category
     * @return string
     */
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
		
        $category = str_replace("-", " ", str_replace("_", "&", $category));
		$sqlcategory = $this->getCategories($category);
        $platform_id_sql = Platform::model()->findByAttributes(array("name" => str_replace("-", " ", $platform)));
        if ($platform_id_sql) $platform_id = $platform_id_sql->id;
        
        $date = date('Y-m-d',strtotime('-1 week'));
        if ($platform_id && $sqlcategory) $date = date('Y-m-d',strtotime('-6 months'));
        else
        if ($platform_id || $sqlcategory) $date = date('Y-m-d',strtotime('-3 months'));
		
		$projects = Project::model()->findAll("time_added >= :date AND (platform_id = :platformid OR :platformid IS NULL)".$sqlcategory." ORDER BY rating DESC, time_added DESC LIMIT :limit",
											  array(":date"=>$date,
													":limit"=>$count,
                                                    ":platformid" => $platform_id));

        $platforms = Platform::model()->findAll();
        $categories = null;
        
		$title = "Top ".$count." projects";
		if ($platform){
            $title = "Top ".$count." ".ucfirst(str_replace("-", " ", $platform))." projects";
            if ($category == '') $platforms = null;
            $categories = Category::model()->findAll();
        }
		if ($category){
            $title = "Top ".$count." ".ucfirst(str_replace("-", " ", $platform))." ".$category." projects";
            $categories = null;
        }
		$this->render('list',array("title"=>$title,"projects"=>$projects,"platform"=>$platform,"listType"=>"top", "platforms" => $platforms, "categories" => $categories, 'count' => $count));
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
