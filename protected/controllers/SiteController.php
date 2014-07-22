<?php

class SiteController extends Controller
{
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
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
    $this->layout = 'blank';
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
    //
    
    $cat_sel = array();
    $platform_sel = array();
    $email = '';
    
    //load previous feed
    if (isset($_GET['id'])){
      $subscription = Subscription::model()->findByAttributes(array('hash'=>$_GET['id']));
      if ($subscription){
        $cat_sel = explode(',',$subscription->category);
        $platform_sel = explode(',',$subscription->platform);
        $email = $subscription->email;
      }
    }
    
    
    //subscribe to feed
    if(isset($_POST['subscribe'])){
      setFlash("save", "SaveOK", "success ", false);
      $plat = implode(",",array_keys($_POST['plat']));
      if ($plat == '0') $plat = '';
      $platform_sel = explode(',',$plat);
      
      $cat = implode(",",array_keys($_POST['cat']));
      $cat_sel = explode(',',$cat);
      $email = $_POST['email']; 
      
      $subscription = Subscription::model()->findByAttributes(array('email'=>$email));
      if (!$subscription){
        $subscription = new Subscription();
        $subscription->time_created = date("Y-m-d H:i:s");
      }
      
      $subscription->hash = md5($email.$plat.$cat);
      $subscription->email = $email;
      $subscription->platform = $plat;
      $subscription->category = $cat;
      $subscription->rss = 1;
      $subscription->time_updated = date("Y-m-d H:i:s");
      $subscription->save();
    }

    
    //platforms
    $platforms = Platform::model()->findAll();
    $selplat = array(array("name"=>'All platforms', "id"=>0, "selected"=>true));
    foreach ($platforms as $platform){
      $selplat[] = array("name"=>$platform->name, "id"=>$platform->id, "selected"=>in_array($platform->id, $platform_sel));
      if (in_array($platform->id, $platform_sel)) $selplat[0]['selected'] = false;
    }
    
    //categories
    $categories = Category::model()->findAll();
    $selcat = array();
    foreach ($categories as $platform){
      $selcat[] = array("name"=>$platform->name, "id"=>$platform->id, "selected"=>in_array($platform->id, $cat_sel));
    }
    
		$this->render('index',array('platforms'=>$selplat,'categories'=>$selcat,'email'=>$email));
    
	}
  
  /**
   * tracking RSS link clicks and redirecting them
   */
  public function actionRl($l) {
    /*Yii::import('application.helpers.Hashids');
    $hashids = new Hashids('cofinder');
    $tid = $hashids->decrypt($id);
    $id = $tid[0];*/
    
    $mailLinkClick = new MailClickLog();
    $mailLinkClick->link = $l;
    $mailLinkClick->time_clicked = date('Y-m-d H:i:s');
    $mailLinkClick->save();
    
    $this->redirect($l);
    Yii::app()->end();
  }   

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
  
  
  /**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}


}