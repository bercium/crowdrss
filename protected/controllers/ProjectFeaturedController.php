<?php

class ProjectFeaturedController extends GxController {

  /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow admins only
				'users'=>array("@"),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
  
	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'ProjectFeatured'),
		));
	}

	public function actionCreate() {
		$model = new ProjectFeatured;


		if (isset($_POST['ProjectFeatured'])) {
			$model->setAttributes($_POST['ProjectFeatured']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'ProjectFeatured');


		if (isset($_POST['ProjectFeatured'])) {
			$model->setAttributes($_POST['ProjectFeatured']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'ProjectFeatured')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('msg', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('ProjectFeatured');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new ProjectFeatured('search');
		$model->unsetAttributes();

		if (isset($_GET['ProjectFeatured']))
			$model->setAttributes($_GET['ProjectFeatured']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
  
  
  public function actionAutocomplete(){
    $return = array();
    if (isset($_GET['term'])){
      $result = Project::model()->findAllByAttributtes('');
      if ($result)
      foreach ($result as $data){
        $return[] = array('id'=>$data->id,'label'=>$data->title,'value'=>$data->title);
      }
      
    }
    return json_encode ($return);
  }

}