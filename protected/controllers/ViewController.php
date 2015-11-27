<?php

class ViewController extends Controller
{
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($name){
        //$this->layout = 'default';
        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->baseUrl.'/js/parallax.min.js');

        $project = Project::model()->findByAttributes(array("internal_link"=>$name));
          
        if ($project == null){
            $project = Project::model()->findByAttributes(array("title"=>$name));
        }

        if ($project == null){
          throw new CHttpException(400, Yii::t('msg', 'No such project.'));
        }

        if (isset($_GET['remove']) && ($_GET['remove'] == $project->id) && (!Yii::app()->user->isGuest)){
            $project->removed = 1;
            $project->save();
        }
        
        $this->pageTitle = $project->title;
        $this->pageDesc = $project->description;
        
        $keywords[] = $project->platform->name;
        $keywords[] = $project->origCategory->name;
        $keywords[] = $project->creator;
        $keywords = array_merge($keywords, explode(" ",$project->title));
        $this->keywords = implode(", ", $keywords);
        
        $goal = substr($project->goal,0, strpos($project->goal, ","))."%";
        $rating = $project->rating;
        //find similar
        $similar_project = Project::model()->findAllBySql('SELECT * FROM project 
                                                    WHERE orig_category_id = ? AND rating > ? AND rating < ? AND goal LIKE ? AND id != ? AND removed = false
                                                    ORDER BY end DESC LIMIT 3', 
                                                    array($project->orig_category_id, ($rating-1), ($rating+1), $goal, $project->id )
                                                  );
		$rating_detail = null;
		if (!Yii::app()->user->isGuest) {
			//recalculate rating with details
			switch ($project->platform->name) {
				case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); /* echo "ks ".$project->link; */ break;
				case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); /* echo "igg ".$project->link; */ break;
			}

			$rating_class->save = false;
			$rating_detail = $rating_class->analize();
		}
        
        // redirect to project url in a few seconds
        if (isset($_GET['redirect'])){
            $cs->registerScript("redirect","redirect_link = '".$project->link."';");
        }

        
		$this->render('index',array("project"=>$project, "similar"=>$similar_project, 'rating_detail' => $rating_detail, 'redirect' => (isset($_GET['redirect']) ? true:false) ));
	}
  
 

}
