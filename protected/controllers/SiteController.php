<?php

class SiteController extends Controller {

    public $social = false;

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
                // captcha action renders the CAPTCHA image displayed on the contact page
                'captcha' => array(
                        'class' => 'CCaptchaAction',
                        'backColor' => 0xFFFFFF,
                ),
                // page action renders "static" pages stored under 'protected/views/site/pages'
                // They can be accessed via: index.php?r=site/page&view=FileName
                'page' => array(
                        'class' => 'CViewAction',
                ),
        );
    }

    function validateId($string) {
        $array = explode(",", $string);
        $string = '';
        foreach ($array as $value) {
            if (is_numeric($value)) {
                if ($string)
                    $string .= ',';
                $string .= $value;
            }
        }
        return $string;
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        $this->layout = 'blank';
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->baseUrl . '/js/social-locker.min.js');
        $cs->registerScriptFile(Yii::app()->baseUrl.'/js/parallax.min.js');

        $cat_sel = array();
        $platform_sel = array();
        $subcat_sel = array();
        $email = '';
        $subscription = null;

        $OrigCategories = array();
        $SelOrigCategories = OrigCategory::model()->findAll();
        foreach ($SelOrigCategories as $origcat) {
            $OrigCategories[$origcat->category_id][] = $origcat;
        }

        //load previous feed
        if (isset($_GET['id'])) {
            $subscription = Subscription::model()->findByAttributes(array('hash' => $_GET['id']));
            if ($subscription) {
                $cat_sel = explode(',', $subscription->category);
                $platform_sel = explode(',', $subscription->platform);
                $subcat_sel = explode(',', $subscription->exclude_orig_category);

                $email = $subscription->email;
            } else {
                setFlash("save", "Subscription not found! Please check that you have the right ID.", "alert", false);
            }
        }


        //subscribe to feed
        if (isset($_POST['subscribe'])) {
            $plat = '';
            if (isset($_POST['plat']))
                $plat = implode(",", array_keys($_POST['plat']));
            if ($plat == '0')
                $plat = '';
            $platform_sel = explode(',', $plat);

            $cat = '';
            if (isset($_POST['cat']))
                $cat = implode(",", array_keys($_POST['cat']));
            $cat_sel = explode(',', $cat);

            $subcat = '';
            $subcat_sel_inv = array();
            if (isset($_POST['subcat']))
                $subcat_sel_inv = array_keys($_POST['subcat']);
            //$subcat_sel_inv = explode(',',$subcat);

            $subcat_sel = array();
            foreach ($cat_sel as $id) {
                //if (!isset($OrigCategories[$id])) echo "---".$id."---"; else
                foreach ($OrigCategories[$id] as $origCat) {
                    //echo ",".$origCat->id." ";
                    if (!in_array($origCat->id, $subcat_sel_inv)) {
                        $subcat_sel[] = $origCat->id;
                    }
                }
            }

            $subcat = implode(",", $subcat_sel);

            $email = $_POST['email'];

            $subscription = Subscription::model()->findByAttributes(array('email' => $email));
            $subupdate = 'update';
            if (!$subscription) {
                $subupdate = 'new';
                $subscription = new Subscription();
                //$subscription->time_created = date("Y-m-d H:i:s");
                $subscription->hash = mailTrackingCode();
            }


            $subscription->email = $email;
            $subscription->platform = $this->validateId($plat);
            $subscription->category = $this->validateId($cat);
            $subscription->exclude_orig_category = $this->validateId($subcat);

            if (isset($_POST['rss_feed']))
                $subscription->rss = 1;
            else
                $subscription->rss = 0;
            if (isset($_POST['daily_digest']))
                $subscription->daily_digest = 1;
            else
                $subscription->daily_digest = 0;
            if (isset($_POST['weekly_digest']))
                $subscription->weekly_digest = 1;
            else
                $subscription->weekly_digest = 0;

            if (isset($_POST['two_times_weekly_digest']))
                $subscription->two_times_weekly_digest = 1;
            else
                $subscription->two_times_weekly_digest = 0;

            if (isset($_POST['rating']))
                $subscription->rating = $_POST['rating'];
            $subscription->time_updated = date("Y-m-d H:i:s");

            if ($subscription->save()) {
                setFlash("save", "Subscription saved. Please check your email for the link to your personalized RSS feed.", "success", false);

                $message = new YiiMailMessage;
                $message->view = 'subscribe';
                $message->subject = 'Crowdfunding RSS subscription link';
                $tc = mailTrackingCode();
                $ml = new MailLog();
                $ml->tracking_code = mailTrackingCodeDecode($tc);
                $ml->type = 'subscription-' . $subupdate;
                $ml->subscription_id = $subscription->id;
                $ml->save();

                $rss_link = Yii::app()->createAbsoluteUrl("feed/rss", array("data" => $subscription->hash));
                $editLink = Yii::app()->createAbsoluteUrl("site/index", array("id" => $subscription->hash));

                if ($subscription->rss) {
                    $content = 'Thank you for subscribing to Crowdfunding RSS.<br /><br />
						  We have send you the link to personalized RSS feed for crowdfunding campaigns.<br />
						  Just copy and paste the following link in your favourite RSS reader and enjoy.';

                    $message->setBody(array("content" => $content, "linkToFeed" => $rss_link, "editLink" => $editLink, "tc" => $tc), 'text/html');
                } else {
                    $content = "Thank you for subscribing to Crowdfunding RSS.<br />
						  We hope you will enjoy our service.";

                    $message->setBody(array("content" => $content, "editLink" => $editLink, "tc" => $tc), 'text/html');
                }

                $message->addTo($subscription->email);
                $message->from = Yii::app()->params['noreplyEmail'];
                Yii::app()->mail->send($message);

                //$this->refresh();
                $this->redirect($editLink);
                Yii::app()->end();
            } else {
                if (YII_DEBUG)
                    setFlash("save", "Problem saving your subscription! Please try later or contact us. " . print_r($subscription->getErrors(), true), "alert", false);
                else
                    setFlash("save", "Problem saving your subscription! Please try later or contact us and tell us all about it.", "alert", false);
            }
        } // end subscription
        //platforms
        $platforms = Platform::model()->findAll("active = :active", array(":active" => 1));
        $selplat = array(array("name" => 'All platforms', "id" => 0, "selected" => true, "projPerDay" => 0));
        $all = 0;
        foreach ($platforms as $platform) {
            $numofp = round(Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -168 HOUR) AND platform_id = :platform", array(":platform" => $platform->id)) / 7);
            //$numofp = round(count(Project::model()->findAll("time_added > DATE_ADD(NOW(), INTERVAL -168 HOUR) AND platform_id = :platform",array(":platform"=>$platform->id))) / 7);
            $all += $numofp;
            $selplat[] = array("name" => $platform->name, "id" => $platform->id, "selected" => in_array($platform->id, $platform_sel), "projPerDay" => $numofp);
            if (in_array($platform->id, $platform_sel))
                $selplat[0]['selected'] = false;
        }
        $selplat[0]['projPerDay'] = $all;

        //categories
        $categories = Category::model()->findAll(array("order" => "name"));
        $selcat = array();
        foreach ($categories as $category) {
            //$OrigCategories = OrigCategory::model()->findAllByAttributes(array('category_id'=>$platform->id));
            $hint = '';
            $subCat = array();
            if (isset($OrigCategories[$category->id])) {
                foreach ($OrigCategories[$category->id] as $origCat) {
                    if ($hint)
                        $hint .= '<br />';
                    $hint .= $origCat->name;
                    $subCat[] = array("name" => $origCat->name, "id" => $origCat->id, "selected" => !in_array($origCat->id, $subcat_sel));
                }
            }
            $selcat[] = array("name" => $category->name, "id" => $category->id, "selected" => in_array($category->id, $cat_sel), "hint" => $hint, "subcat" => $subCat);
        }

        $subscribers = Subscription::model()->countBySql("SELECT COUNT(*) FROM subscription");
        $this->social = true;
        $this->render('index', array('platforms' => $selplat, 'categories' => $selcat, 'subscription' => $subscription, "subscribers" => $subscribers));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionOwners($s = '') {

        $inPlatform = $onPage = $project = null;
        $error = '';
        $link = '';
        $rating_detail = null;
        $rating = null;

        if (isset($_POST['checkLink']) || $s != '') {
            if ($s != '')
                $link = $s;
            else
                $link = $_POST['link'];

            if ((strpos($link, "www.") !== false) ||
                    (strpos($link, "http://") !== false) ||
                    (strpos($link, "https://") !== false) ||
                    (strpos($link, ".com") !== false)
            ) {
                //setFlash ("sdfsdf",$link);
                $project = Project::model()->find("link LIKE :link", array(':link' => beautifyLink($link) . "%"));
                /*        $project = Project::model()->find("link LIKE :link1",
                  array(':link1' => $link,
                  ':name' => $link
                  )); */
            } else {
                $project = Project::model()->find("title LIKE :name", array(':name' => $link));
            }

            if ($project) {
                if ($project->rating != null)
                    $rating = $project->rating;
                else {
                    $rating = 0;
                    setFlash("projectCompare", "We haven't rated this project yet. Positions are aproximated.", "info", false);
                }
                $onPage = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > :date AND rating > :rating", array(":rating" => $rating, ":date" => date('Y-m-d', strtotime('-1 week')))) + 1;
                $inPlatform = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > :date AND rating > :rating AND platform_id = :platform", array(":rating" => $rating, ":platform" => $project->platform_id, ":date" => date('Y-m-d', strtotime('-1 week')))) + 1;
                //$inCategory = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -168 HOUR) AND rating > :rating AND category_id = :category",array(":rating"=>$project->rating,":category"=>$project->category_id));


                if (!Yii::app()->user->isGuest) {
                    //recalculate rating with details
                    switch ($project->platform->name) {
                        case "Kickstarter": $rating_class = new KickstarterRating($project->link, $project->id); /* echo "ks ".$project->link; */ break;
                        case "Indiegogo": $rating_class = new IndiegogoRating($project->link, $project->id); /* echo "igg ".$project->link; */ break;
                    }

                    $rating_class->save = false;
                    $rating_detail = $rating_class->analize();
                }
            } else {
                setFlash("projectCompare", "Sorry we couldn't find this project in our database! Positions are aproximated.", "info", false);
                //if (!Yii::app()->user->isGuest){
                //recalculate rating with details
                if (strpos($link, "kickstarter.com") !== false) {
                    $rating_class = new KickstarterRating($link);
                    $plat_id = 1;
                } else
                if (strpos($link, "indiegogo.com") !== false) {
                    $rating_class = new IndiegogoRating($link);
                    $plat_id = 2;
                }

                $rating_class->save = false;

                $rating_detail = $rating_class->analize();

                //print_r($rating_detail);
                $rating = $rating_detail['rating'];
                if (Yii::app()->user->isGuest)
                    $rating_detail = '';

                $onPage = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > :date AND rating > :rating", array(":rating" => $rating, ":date" => date('Y-m-d', strtotime('-1 week')))) + 1;
                $inPlatform = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > :date AND rating > :rating AND platform_id = :platform", array(":rating" => $rating, ":platform" => $plat_id, ":date" => date('Y-m-d', strtotime('-1 week')))) + 1;

                //}
            }
        }

        $this->render('owners', array("project" => $project, "link" => $link, "onPage" => $onPage, "inPlatform" => $inPlatform, 'rating_detail' => $rating_detail, "rating" => $rating));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionRating() {
        $this->render('rating');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * hide toolbar
     */
    protected function beforeAction($action) {
        if ($action->id == 'sitemap')
            foreach (Yii::app()->log->routes as $route) {
                //if ($route instanceof CWebLogRoute){
                $route->enabled = false;
                //}
            }
        return true;
    }

    /**
     * create sitemap for the whole site
     */
    public function actionSitemap() {
        // don't allow any other strings before this
        Yii::app()->clientScript->reset();
        $this->layout = 'none'; // template blank

        $sitemapResponse = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
      <loc>http://crowdfundingrss.com/</loc>
      <changefreq>monthly</changefreq>
      <priority>1</priority>
    </url>
EOD;

        // all platforms and number of projects
        $platforms = Platform::model()->findAll();
        foreach (array(10, 25, 50, 100) as $c) {
            $sitemapResponse .= "
        <url>
          <loc>http://crowdfundingrss.com/top" . $c . "</loc>
          <changefreq>weekly</changefreq>
          <priority>0.9</priority>
        </url>";
            $sitemapResponse .= "
        <url>
          <loc>http://crowdfundingrss.com/bottom" . $c . "</loc>
          <changefreq>weekly</changefreq>
          <priority>0.8</priority>
        </url>";
            foreach ($platforms as $platform) {
                $sitemapResponse .= "
          <url>
            <loc>" . str_replace(" ", "+", "http://crowdfundingrss.com/top" . $c . "/" . $platform->name) . "</loc>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
          </url>";
                $sitemapResponse .= "
          <url>
            <loc>" . str_replace(" ", "+", "http://crowdfundingrss.com/bottom" . $c . "/" . $platform->name) . "</loc>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
          </url>";
            }
        }


        // go trough projects newer than 1 month
        $projects = Project::model()->findAll("time_added > :datum ORDER BY id DESC LIMIT 6000", array(":datum" => date("Y-m-d H:i:s", strtotime("-1 month"))));
        foreach ($projects as $project) {
            if ($project) {
                $priority = 0.35;
                if ($project->rating)
                    $priority = round(($project->rating / 20) + 0.35, 3);
                $sitemapResponse .= "
        <url>
          <loc>";

                if (!empty($project->internal_link)){
                    $sitemapResponse .= Yii::app()->createAbsoluteUrl("view/index", array("name" => $project->internal_link));
                }else{
                    if (strpos($project->title, "/") === false)
                        $sitemapResponse .= htmlspecialchars(str_replace(" ", "+", (Yii::app()->createAbsoluteUrl("view/index", array("name" => $project->title)))));
                    else
                        $sitemapResponse .= htmlspecialchars(str_replace(" ", "+", (Yii::app()->createAbsoluteUrl("view/index") . "?name=" . $project->title)));
                }
                $sitemapResponse .= "</loc>
          <changefreq>weekly</changefreq>
          <priority>" . $priority . "</priority>
        </url>";
            }
        }

        $sitemapResponse .= "\n</urlset>"; // end sitemap

        $this->render("//layouts/none", array("content" => $sitemapResponse));
    }

    public function actionTest() {
        $rating_class = new IndiegogoRating('https://www.indiegogo.com/projects/new-pc--34', 117840);
        $rating_class->analize();
    }

    /**
     * 
     */
    public function actionCheckMail() {
        $email = Yii::app()->request->getParam('email','');
        
        if ($email){
            $subscription = Subscription::model()->findByAttributes(array('email' => $email));
            
            if ($subscription){
                echo json_encode(true);
                exit;
            }
        }
        return json_encode(false);
        exit;
    }

}
