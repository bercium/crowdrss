<?php

class OrigCategoryController extends GxController {
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
			'model' => $this->loadModel($id, 'OrigCategory'),
		));
	}

	public function actionCreate() {
		$model = new OrigCategory;


		if (isset($_POST['OrigCategory'])) {
			$model->setAttributes($_POST['OrigCategory']);

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
		$model = $this->loadModel($id, 'OrigCategory');


		if (isset($_POST['OrigCategory'])) {
			$model->setAttributes($_POST['OrigCategory']);

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
			$this->loadModel($id, 'OrigCategory')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('msg', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('OrigCategory');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new OrigCategory('search');
		$model->unsetAttributes();

		if (isset($_GET['OrigCategory']))
			$model->setAttributes($_GET['OrigCategory']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}