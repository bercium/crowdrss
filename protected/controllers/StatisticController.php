<?php

class StatisticController extends Controller
{
  //public $layout="//layouts/default";
  
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
  
  protected function beforeAction($action){
    $baseUrl = Yii::app()->baseUrl; 
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile('https://www.google.com/jsapi');
    return true;
  }  
  
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			/*'page'=>array(
				'class'=>'CViewAction',
			),*/
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($id = 1){
    $this->render('index');
 	}
  
  
  public function actionDatabase(){
    //$this->layout = 'none';
    
    $model = new DatabaseQueryForm();
    
    $dataProvider = null;
    $rawData = $columns = $tableData =  array();
    
    $loadID = null;
    if (isset($_GET['load'])) $loadID = $_GET['load'];
    if (isset($_POST['load'])) $loadID = $_POST['load'];
    
    
		if(isset($_POST['DatabaseQueryForm'])){
			$model->attributes=$_POST['DatabaseQueryForm'];
			if($model->validate()){
      
        if ((stripos($model->sql, "delete ") !== false) || (stripos($model->sql, "update ") !== false) ||
            (stripos($model->sql, "drop ") !== false) || (stripos($model->sql, "empty ") !== false) ||
            (stripos($model->sql, "truncate ") !== false) || (stripos($model->sql, "empty ") !== false)){
          setFlash("dbExecute", "Not allowed to change database here", 'alert');
        }else{
          if (($model->graph && $model->x && $model->y) || (!$model->graph)){
            try {
              $rawData = Yii::app()->db->createCommand($model->sql)->queryAll();
            } catch (Exception $exc) {
              setFlash("dbExecute", "Problem executing SQL statement: ".$exc->getMessage(), 'alert');
            }

            if ($rawData){
              $columns = array_keys($rawData[0]);

              $id = 'id';
              if (!isset($columns['id'])) $id = $columns[0];

              $dataProvider=new CArrayDataProvider($rawData, array(
                  'id'=>$id,
                  'keyField' => $id,
                  /*'sort'=>array(
                      'attributes'=>array(
                           'id', 'username', 'email',
                      ),
                  ),*/
                  'pagination'=>false
              ));
              
              if (!empty($_POST['action'])){
                setFlash("dbExecute", "Saving not implemented",'alert');
                /*if ($model->title){
                  if ($_POST['action'] == 'save'){
                    $dbquery = new DbQuery();
                    $dbquery->title = $model->title;
                    $dbquery->model = serialize($model);
                    $dbquery->time_created = date('c');
                    $dbquery->save();
                    setFlash("dbExecute", "SQL Saved");
                    $loadID = $dbquery->id;
                  }
                  if (($_POST['action'] == 'modify') && ($loadID)){
                    $dbquery = DbQuery::model()->findByPk($loadID);
                    $dbquery->title = $model->title;
                    $dbquery->model = serialize($model);
                    $dbquery->save();
                    setFlash("dbExecute", "SQL Saved");
                  }
                }else{
                  setFlash("dbExecute", "When saving SQL you must specify title",'alert');
                }*/
              }else{
                setFlash("dbExecute", "Executing SQL");
                $loadID = '';
              }
            }
          }else{
            setFlash("dbExecute", "Enter X and Y values for graph", 'alert');
          }
        }
      }else setFlash("dbExecute", "Problem validating request", 'alert');
    }else{
      if ($loadID){
        setFlash("dbExecute", "Loading not implemented",'alert');
        //$dbquery = DbQuery::model()->findByPk($loadID);
        //$model = unserialize($dbquery->model);
      }
    }
    
    
//    $model = unserialize(file_get_contents('uploads/test.txt'));
    
    // show tables and rows
    /*$result = Yii::app()->db->createCommand("SELECT * FROM information_schema.columns WHERE table_schema = 'slocoworking' ORDER BY table_name,ordinal_position")->queryAll();
    $tableData = array();
    foreach ($result as $row){
      if (isset($tableData[$row['TABLE_NAME']])) $tableData[$row['TABLE_NAME']] .= "<br /> ".$row['COLUMN_NAME'];
      else $tableData[$row['TABLE_NAME']] = $row['COLUMN_NAME'];
    }*/
    $this->render('dbExecute',array('model'=>$model,'dataProvider'=>$dataProvider,'rawData'=>$rawData,'loadID'=>$loadID));
  }
  
	
}
