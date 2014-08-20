<?php

class ViewController extends Controller
{
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($name)
	{
    //$this->layout = 'default';

    $project = Project::model()->findByAttributes(array("title"=>$name));
    
    if ($project == null){
      throw new CHttpException(400, Yii::t('msg', 'No such project.'));
    }
    
		$this->render('index',array("project"=>$project));
	}
  
 

}
