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
  
  
  public function actionSocialAnalize(){
    /*
     
    */
    
    $sql = "SELECT rh.`project_id`,rh.`data`,rh.`time_rated`, p.*
            FROM (
               SELECT project_id, max(`time_rated`) AS maxdate
               FROM rating_history
               WHERE time_rated >= '".date("Y-m-d",strtotime("-7 days"))."'
               GROUP BY project_id
            ) AS x INNER JOIN rating_history AS rh ON rh.`project_id` = x.`project_id` AND rh.`time_rated` = x.maxdate
            LEFT JOIN project AS p ON p.id = rh.project_id";

		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);
		$dataReader=$command->query();
		$arrayCount = array(0,0,0,0,0,0,0);

    $daysAgo = array();
    
		foreach($dataReader as $row) {
      $h_lapsed = timeDifference($row['time_added'],$row['time_rated'],"hour");
      
      $data = json_decode($row['data'],true );
      
      if ($h_lapsed < 3) continue;  // hard to evaluate project this young
      if (!isset($data['social'])) continue;
      $social = $data['social'];
      
      $all = 0;
      if (!isset($social['all'])) {
        foreach ($social as $rs){
          $all += $rs; 
        }
      }else $all = $social['all'];
      
      if ($h_lapsed > 0) $all = $all / $h_lapsed;  // per hour
      
      $all = $all*24; // per day
      
      // less than 3 hours statisticaly too little
      /*if ($h_lapsed == 3) $all *= 0.6;
      if ($h_lapsed == 2) $all *= 0.45;*/
      $rating = 0;
      if ($all < 0.15) $arrayCount[0] += 1;
      else
      if ($all >= 391.86){
        $rating = 10;
        $arrayCount[10] += 1;
        if (isset($daysAgo[$h_lapsed]['da'])) $daysAgo[$h_lapsed]['da']++;
        else $daysAgo[$h_lapsed]['da'] = 1;
        $daysAgo[$h_lapsed]['p'][] = $all;
        if ($all > 1000) echo $h_lapsed.": ".round($all,2)." - ".$row['rating']." ".$rating." = ".round(($row['rating']*0.8 + $rating*0.2),3)." | ".$row['link']."<br />";
      }
      if ($all >= 278.88){
        $rating = 9;
        $arrayCount[9] += 1;
      }
      else
      if ($all >= 170.32){
        $rating = 8;
        $arrayCount[8] += 1;
      }
      else
      if ($all >= 108.17){
        $rating = 7;
        $arrayCount[7] += 1;
      }
      else
      if ($all >= 65.08){
        $rating = 6;
        $arrayCount[6] += 1;
      }
      else 
      if ($all >= 39){
        $rating = 5;
        $arrayCount[5] += 1;
      }
      else 
      if ($all >= 22.76){
        $rating = 4;
        $arrayCount[4] += 1;
      }
      else 
      if ($all >= 11.45){
        $rating = 3;
        $arrayCount[3] += 1;
      }
      else 
      if ($all >= 3.3){
        $rating = 2;
        $arrayCount[2] += 1;
      }
      else
      if ($all >= 0.15){
        $rating = 1;
        $arrayCount[1] += 1;
      }
      
      $sr = round(($row['rating']*0.7 + $rating*0.3),3);
      if ($sr > $row['rating']){
        if  ($sr > 7) echo $row['rating']." ".$rating." = <strong>".$sr.": ".$row['link']."</strong><br />";
        else echo $row['rating']." ".$rating." = ".$sr.": ".$row['link']."<br />";
      }
      
      /*if ($row['project_id'] == 32614){
        echo $row['time_added']."-".$row['time_rated']." | ".$social['all']." / ".$h_lapsed." = ".$all;
      }*/
    }
    
    echo "Top days: ";
    asort($daysAgo);
    print_r($daysAgo);
    
    echo "<br /><br /><br />";
    asort($arrayCount);
    $content = print_r($arrayCount, true);
    
    $this->render('//site/message',array('content'=>$content));
  }
	
}
