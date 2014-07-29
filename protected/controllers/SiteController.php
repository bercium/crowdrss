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
      }else{
        setFlash("save", "Subscription not found! Pleas check that you have the right ID.", "alert", false);
      }
    }
    
    
    //subscribe to feed
    if(isset($_POST['subscribe'])){
      $plat = '';
      if (isset($_POST['plat'])) $plat = implode(",",array_keys($_POST['plat']));
      if ($plat == '0') $plat = '';
      $platform_sel = explode(',',$plat);
      
      $cat = '';
      if (isset($_POST['cat'])) $cat = implode(",",array_keys($_POST['cat']));
      $cat_sel = explode(',',$cat);
      $email = $_POST['email']; 
      
      $subscription = Subscription::model()->findByAttributes(array('email'=>$email));
      $subupdate = 'update';
      if (!$subscription){
        $subupdate = 'new';
        $subscription = new Subscription();
        //$subscription->time_created = date("Y-m-d H:i:s");
        $subscription->hash = mailTrackingCode();
      }
      
      
      $subscription->email = $email;
      $subscription->platform = $plat;
      $subscription->category = $cat;
      $subscription->rss = 1;
      $subscription->time_updated = date("Y-m-d H:i:s");
      if ($subscription->save()){
        setFlash("save", "Subscription saved. Please check your email for the link to your personalized RSS feed.", "success", false);
        
        $message = new YiiMailMessage;
        $message->view = 'subscribe';
        $message->subject = 'Crowdfunding RSS subscription link';
        $tc = mailTrackingCode();
        $ml = new MailLog();
        $ml->tracking_code = mailTrackingCodeDecode($tc);
        $ml->type = 'subscription-'.$subupdate;
        $ml->subscription_id = $subscription->id;
        $ml->save();
        
        $rss_link = Yii::app()->createAbsoluteUrl("feed/rss",array("data"=>$subscription->hash));
        $editLink = Yii::app()->createAbsoluteUrl("site/index",array("id"=>$subscription->hash));
        
        $content = 'You have requested the link to personalized RSS feed for crowdfunding campaigns.<br />
                    Copy and paste the following link in your favourite RSS reader and enjoy.';

        $message->setBody(array("content"=>$content,"linkToFeed"=>$rss_link,"editLink"=>$editLink,"tc"=>$tc), 'text/html');

        $message->addTo($subscription->email);
        $message->from = Yii::app()->params['noreplyEmail'];
        Yii::app()->mail->send($message);
        
        $this->refresh();
        Yii::app()->end();
        
      }else{
        if (YII_DEBUG) setFlash("save", "Problem saving your subscription! Please try later or contact us. ".print_r($subscription->getErrors(),true), "alert", false);
        else setFlash("save", "Problem saving your subscription! Please try later or contact us and tell us all about it.", "alert", false);
      }
    }

    
    //platforms
    $platforms = Platform::model()->findAll("active = :active",array(":active"=>1));
    $selplat = array(array("name"=>'All platforms', "id"=>0, "selected"=>true));
    foreach ($platforms as $platform){
      $selplat[] = array("name"=>$platform->name, "id"=>$platform->id, "selected"=>in_array($platform->id, $platform_sel));
      if (in_array($platform->id, $platform_sel)) $selplat[0]['selected'] = false;
    }
    
    //categories
    $categories = Category::model()->findAll(array("order"=>"name"));
    $selcat = array();
    foreach ($categories as $platform){
      $OrigCategories = OrigCategory::model()->findAllByAttributes(array('category_id'=>$platform->id));
      $hint = '';
      if ($OrigCategories){
        foreach ($OrigCategories as $origCat){
          if ($hint) $hint .= '<br />';
          $hint .= $origCat->name;
        }
      }
      $selcat[] = array("name"=>$platform->name, "id"=>$platform->id, "selected"=>in_array($platform->id, $cat_sel), "hint"=>$hint);
    }
    
		$this->render('index',array('platforms'=>$selplat,'categories'=>$selcat,'email'=>$email));
    
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