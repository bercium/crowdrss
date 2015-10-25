<?php

class ViewController extends Controller
{
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($name){
        //$this->layout = 'default';

        $project = Project::model()->findByAttributes(array("title"=>$name));

        if ($project == null){
          throw new CHttpException(400, Yii::t('msg', 'No such project.'));
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
                                                    ORDER BY end DESC LIMIT 2', 
                                                    array($project->orig_category_id, ($rating-1), ($rating+1), $goal, $project->id )
                                                  );
        
		$this->render('index',array("project"=>$project, "similar"=>$similar_project));
	}
  
 

}
