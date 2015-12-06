<?php

class FeedController extends Controller {

	private $viewRedirectLink = false;

	/**
	 * 
	 * @param type $action
	 * @return boolean
	 */
	protected function beforeAction($action) {
		if (($action->id == 'rss') || ($action->id == 'downloadRss') || ($action->id == 'rl') || ($action->id == 'mailTest') || ($action->id == 'image') ) {
			foreach (Yii::app()->log->routes as $route) {
				//if ($route instanceof CWebLogRoute){
				$route->enabled = false;
				//}
			}
		}
		return true;
	}

	/**
	 * 
	 * @param type $project
	 * @param type $id
	 * @return string
	 */
	private function createRssItem($project, $id, $track = true) {
		$rssResponse = '<item>' . PHP_EOL;
		$rssResponse .= '<title><![CDATA[' . $project->title . ']]></title>' . PHP_EOL;
		$rssResponse .= '<pubDate>' . date("D, d M Y H:i:s e", strtotime($project->time_added)) . '</pubDate>' . PHP_EOL;
		$rssResponse .= '<category>' . htmlspecialchars($project->origCategory->name) . '</category>' . PHP_EOL;
                $rssResponse .= '<author>' . htmlspecialchars($project->creator) . '</author>' . PHP_EOL;
		if ($this->viewRedirectLink) {
			if (!empty($project->internal_link)) {
				$link = Yii::app()->createAbsoluteUrl("view/index", array("name" => $project->internal_link)) . "?redirect";
			} else {
				if (strpos($project->title, "/") === false)
					$link = '<![CDATA[' . str_replace(" ", "+", (Yii::app()->createAbsoluteUrl("view/index", array("name" => $project->title))) . "?redirect") . ']]>';
				else
					$link = '<![CDATA[' . str_replace(" ", "+", (Yii::app()->createAbsoluteUrl("view/index") . "?name=" . $project->title . "&redirect")) . ']]>';
			}
			$rssResponse .= '<link>' . $link . '</link>';
			$rssResponse .= '<guid isPermaLink="true">' . $link . '</guid>';
		}else {
			if ($id) {
				$rssResponse .= '<link><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl", array("l" => $project->link, 'i' => $id)) . ']]></link>';
				$rssResponse .= '<guid isPermaLink="true"><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl", array("l" => $project->link, 'i' => $id)) . ']]></guid>';
			} else {
				$rssResponse .= '<link><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl", array("l" => $project->link)) . ']]></link>';
				$rssResponse .= '<guid isPermaLink="true"><![CDATA[' . Yii::app()->createAbsoluteUrl("feed/rl", array("l" => $project->link)) . ']]></guid>';
			}
		}
		$rssResponse .= PHP_EOL;
		$desc = '';
		//$desc.= "<strong>".$project->platform->name."</strong> - ".$project->origCategory->name." <br />";
		$desc.= '<img src="' . $project->image . '" alt="' . $project->title . ' [' . $project->rating . ']" border="0" style="margin-bottom:8px;"/>';

		$stars = getStars($project->rating);
		if ($stars != '')
			$stars = '[' . $stars . '] <br />';

		if (!empty($project->platform->name)) {
			$desc.= "<p>" . $stars . $project->description . " <br />";
			$desc.= "<br /><strong>" . $project->platform->name . "</strong> - " . $project->origCategory->name . " "; //." <br />";
		} else
			$desc.= "<p>" . $project->description . " <br />";

		//if (!empty($project->platform->name)) $desc.= "<br /><strong>".$project->platform->name."</strong> - ".$project->origCategory->name." ";//." <br />";
		if (!empty($project->creator))
			$desc.= "<br />Creator of project: <i>" . $project->creator . "</i> ";
		//if (!empty($project->location)) $desc.= " \nCreator of project: ".$project->location;
		if (!empty($project->goal))
			$desc.= "<br />Project goal: <strong>" . $project->goal . "</strong>";
		if (!empty($project->type_of_funding)) {
			if ($project->type_of_funding == 0)
				$desc.= " Fixed funding";
			else
				$desc.= " Flexible funding";
		}

		// voting and tracking
		if ($id) {
			//if ($id == 1 || $id == 2){
			if (!isset($project->nolike)) {
				$desc .= '</p><p style="text-align:right;">
                      <a href="' . Yii::app()->createAbsoluteUrl("feed/vote", array("l" => $project->link, 'ra' => 1, 'i' => $id)) . '">like</a> | ';
				$desc .= '<a href="' . Yii::app()->createAbsoluteUrl("feed/vote", array("l" => $project->link, 'ra' => 0, 'i' => $id)) . '">dislike</a>';
			}
			//}
			// track opening of feed
			if ($track)
				$desc .= '<img src="' . Yii::app()->createAbsoluteUrl("track/of", array("l" => $project->link, 'i' => $id)) . '" />';
		}else if ($track)
			$desc .= '<img src="' . Yii::app()->createAbsoluteUrl("track/of", array("l" => $project->link)) . '" />';

		$desc .= "</p>";
		$rssResponse .= '<description><![CDATA[' . $desc . ']]></description>' . PHP_EOL;
//      $rssResponse .= '<description>' . $project->description . '</description>';
//      $rssResponse .= '<author>' . $project->creator . '</author>';
		$rssResponse .= "</item>" . PHP_EOL;

		return $rssResponse;
	}

	/**
	 * 
	 * @param type $projects
	 * @param type $id
	 * @param type $featured
	 * @return string
	 */
	function createRssFeed($projects, $id = null, $featured = null, $track = true) {
		$rssResponse = '';
		$rssResponse .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$rssResponse .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . PHP_EOL;

//    $rssResponse .= '<rss version="2.0">';
		$rssResponse .= '<channel>' . PHP_EOL;
		//$rssResponse .= '<atom:link href="http://dallas.example.com/rss.xml" rel="self" type="application/rss+xml" />';
		if (isset($_SERVER["REQUEST_URI"]))
			$rssResponse .= '<atom:link rel="self" type="application/rss+xml" href="http://www.crowdfundingrss.com' . $_SERVER["REQUEST_URI"] . '" />' . PHP_EOL;
		else
			$rssResponse .= '<atom:link rel="self" type="application/rss+xml" href="http://www.crowdfundingrss.com/feed/previewRss" />' . PHP_EOL;

		$rssResponse .= '<title>Crowdfounding RSS</title>' . PHP_EOL;
		if (isset($_SERVER["REQUEST_URI"]))
			$rssResponse .= '<link>http://www.crowdfundingrss.com' . $_SERVER["REQUEST_URI"] . '</link>' . PHP_EOL;
		else
			$rssResponse .= '<link>' . Yii::app()->params['absoluteHost'] . '</link>' . PHP_EOL;
		$rssResponse .= '<description>All your crowdfunding projects in one place.</description>' . PHP_EOL;
		$rssResponse .= '<language>en-us</language>' . PHP_EOL;
		$rssResponse .= '<ttl>10</ttl>' . PHP_EOL;

		if ($featured != null) {
			$rssResponse .= $this->createRssItem($featured, $id, $track);
		}

		// CREATE RSS
		$i = 0;
		foreach ($projects as $project) {
			$rssResponse .= $this->createRssItem($project, $id, $track);
			//if ($i++ > 20) break;
		}

		$rssResponse .= '</channel>' . PHP_EOL;
		$rssResponse .= '</rss>' . PHP_EOL;

		return $rssResponse;
	}

	/**
	 * validate if just ID's in table just to be safe
	 */
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
	 * return payed project
	 */
	private function getPayedProject($sub) {
		$paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_date = :date ORDER BY show_count ASC", array(":date" => date('Y-m-d')));
		$paidProject = null;
		if (count($paidProjects) > 0) {
			foreach ($paidProjects as $pp) {
				$platA = explode(",", $sub->platform);
				$catA = explode(",", $sub->category);
				$subCatA = explode(",", $sub->exclude_orig_category);

				if ($sub->platform && !in_array($pp->project->platform_id, $platA))
					continue; // has platforms but not in
				if (in_array($pp->project->orig_category_id, $subCatA))
					continue; // exclude list
				if ($sub->category && !in_array($pp->project->origCategory->category_id, $catA))
					continue; // not in category

				$pp->show_count++;
				//    $pp->save();
				$paidProject = $pp->project; //get one project
				// set the rating higher so we know it's special
				if ($paidProject->rating)
					$paidProject->rating += 11;
				else
					$paidProject->rating = 11;
				$paidProject->time_added = time();

				break;
			}
		}
		return $paidProject;
	}

	/**
	 * 
	 */
	public function actionRss($data) {
		Yii::app()->clientScript->reset();
		$this->layout = 'none';

		//header('Content-Type', 'application/rss+xml;charset=utf-8'); 
		header('Content-Type: application/rss+xml; charset=UTF-8');
		mb_internal_encoding("UTF-8");


//   $rssResponse .= '<webMaster>team@eberce.si</webMaster>';
		//$data hash tag for
		// get subscription type of projects
		$sub = Subscription::model()->findByAttributes(array('hash' => $data, 'rss' => 1));
		if (!$sub) {
			throw new CHttpException(404, 'The specified feed was not found.');
		}

		// project constrains

		$subcat = array();
		if ($sub->exclude_orig_category) {
//    if ($this->validateId($sub->exclude_orig_category)){
			$subcat = explode(",", $sub->exclude_orig_category);
//      $subcat = explode(",",$this->validateId($sub->exclude_orig_category));
		}

		$sql = '';
		if ($sub->category) {
			//if ($this->validateId($sub->category)){
			//$orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($sub->category)."))");
			$orgCat = OrigCategory::model()->findAll("(category_id IN (" . $sub->category . "))");

			$allCats = array();
			foreach ($orgCat as $cat) {
				if (!in_array($cat->id, $subcat))
					$allCats[$cat->id] = $cat->id;
			}
			$sql .= " (orig_category_id IN (" . implode(',', $allCats) . ")) AND ";
		}
		if ($sub->platform)
			$sql .= " (platform_id IN (" . $sub->platform . ")) AND ";
		//if ($this->validateId($sub->platform)) $sql .= " (platform_id IN (".$this->validateId($sub->platform).")) AND ";
		else {
			$platforms = Platform::model()->findAll("active = :active", array(":active" => 0));
			$selplat = '';
			foreach ($platforms as $platform) {
				if ($selplat)
					$selplat .= ',';
				$selplat .= $platform->id;
			}
			if ($selplat)
				$sql .= " (platform_id NOT IN (" . $selplat . ")) AND ";
		}
		//$sql .= ' 1 ';
		$sql .= " time_added > DATE_ADD(NOW(),INTERVAL -3 HOUR) ";
		if ($sub->rating > 0)
			$sql .= " AND (rating IS NULL OR rating >= " . $sub->rating . ") ";

		$sql .= " ORDER BY time_added DESC";
		//$sql .= " LIMIT 10";
		// echo $sql;
		// get projects
		$projects = Project::model()->findAll($sql);

		$featured = new FeaturedProject($sub);

		$featured_proj = $featured->featuredDB();
		if ($featured_proj == null) {
			// check if someone has shared already
			$featured_proj = $featured->featuredCFrss();
		}

		// echo rss
		echo $this->createRssFeed($projects, $sub->id, $featured_proj);
		Yii::app()->end();
	}

	/**
	 * 
	 */
	public function actionDownloadRss() {

		Yii::app()->clientScript->reset();
		$this->layout = 'none';

		header('Content-Type: application/rss+xml; charset=UTF-8');
		mb_internal_encoding("UTF-8");

		$subcat = array();
		if (!empty($_POST['subcategory']) && ($this->validateId($_POST['subcategory']))) {
			$subcat = explode(",", $this->validateId($_POST['subcategory']));
		}

		$sql = '';
		if (!empty($_POST['category']) && ($this->validateId($_POST['category']))) {
			$orgCat = OrigCategory::model()->findAll("(category_id IN (" . $this->validateId($_POST['category']) . "))");

			$allCats = array();
			foreach ($orgCat as $cat) {
				if (in_array($cat->id, $subcat))
					$allCats[$cat->id] = $cat->id;
			}
			if (implode(',', $allCats) != '')
				$sql .= " (orig_category_id IN (" . implode(',', $allCats) . ")) AND ";
		}
		if (!empty($_POST['platform']) && ($_POST['platform'] != '0') && ($this->validateId($_POST['platform']))) {
			$sql .= " (platform_id IN (" . $this->validateId($_POST['platform']) . ")) AND ";
		} else {
			$platforms = Platform::model()->findAll("active = :active", array(":active" => 0));
			$selplat = '';
			foreach ($platforms as $platform) {
				if ($selplat)
					$selplat .= ',';
				$selplat .= $platform->id;
			}
			if ($selplat)
				$sql .= " (platform_id NOT IN (" . $selplat . ")) AND ";
		}

		$rating = 0;
		if (isset($_POST['preview_rating']))
			$rating = $_POST['preview_rating'];
		if (!is_numeric($rating))
			$rating = 0;

		//$sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 HOUR)";
		if (isset($_POST['preview_rating']))
			$sql .= " (rating IS NULL OR rating >= " . $rating . ") ";
		$sql .= " ORDER BY time_added DESC"
				. " LIMIT 10";

		$projects = Project::model()->findAll($sql);
		//echo $sql;
		echo $this->createRssFeed($projects, null, null, false);
		Yii::app()->end();
	}

	/**
	 * 
	 */
	public function actionPreviewRss() {
		//$this->layout = 'blank';

		$subcat = $cat = $plat = '';
		$rating = 0;
		if (isset($_POST['category']))
			$cat = $_POST['category'];
		if (isset($_POST['platform']))
			$plat = $_POST['platform'];
		if (isset($_POST['preview_rating']))
			$rating = $_POST['preview_rating'];
		if (!is_numeric($rating))
			$rating = 0;

		//$subcat = array();
		if (!empty($_POST['subcategory']) && ($this->validateId($_POST['subcategory']))) {
			$subcat = explode(",", $this->validateId($_POST['subcategory']));
		}

		$sql = '';
		if (!empty($_POST['category']) && ($this->validateId($_POST['category']))) {
			$orgCat = OrigCategory::model()->findAll("(category_id IN (" . $this->validateId($_POST['category']) . "))");

			$allCats = array();
			foreach ($orgCat as $cat) {
				if (in_array($cat->id, $subcat))
					$allCats[$cat->id] = $cat->id;
			}
			if (implode(',', $allCats) != '')
				$sql .= " (orig_category_id IN (" . implode(',', $allCats) . ")) AND ";
		}
		if (!empty($_POST['platform']) && ($_POST['platform'] != '0') && ($this->validateId($_POST['platform']))) {
			$sql .= " (platform_id IN (" . $this->validateId($_POST['platform']) . ")) AND ";
		} else {
			$platforms = Platform::model()->findAll("active = :active", array(":active" => 0));
			$selplat = '';
			foreach ($platforms as $platform) {
				if ($selplat)
					$selplat .= ',';
				$selplat .= $platform->id;
			}
			if ($selplat)
				$sql .= " (platform_id NOT IN (" . $selplat . ")) AND ";
		}

		if (isset($_POST['preview_rating']))
			$sql .= " (rating IS NULL OR rating >= " . $rating . ") AND ";

		$numOfresults = Yii::app()->db->createCommand("SELECT COUNT(*) FROM project WHERE " . $sql . " time_added > DATE_ADD(NOW(), INTERVAL -168 HOUR)")->queryScalar();
		$numOfresults = round($numOfresults / 7);

		//$sql .= " time_added > DATE_ADD(NOW(),INTERVAL -1 HOUR)";
		$sql .= " 1 ";

		$sql .= " ORDER BY time_added DESC"
				. " LIMIT 10";

		//if (!Yii::app()->user->isGuest) echo "SQL:".$sql;

		$projects = Project::model()->findAll($sql);
		if (isset($_POST['subcategory']))
			$subcat = $_POST['subcategory'];


		$this->render('previewRss', array('projects' => $projects, 'cat' => $cat, 'plat' => $plat, 'subcat' => $subcat, 'rating' => $rating, 'numOfDailyResults' => $numOfresults));
	}

	/**
	 * 
	 */
	public function actionRssIFTTT($data = '') {
		Yii::app()->clientScript->reset();
		$this->layout = 'none';

		//header('Content-Type', 'application/rss+xml;charset=utf-8'); 
		header('Content-Type: application/rss+xml; charset=UTF-8');
		mb_internal_encoding("UTF-8");

		$count = 1;
		if ($data) {
			$category_data = Category::model()->find("name = :name", array(":name" => $data));
			if ($category_data) {
				$category_array = OrigCategory::model()->findAll("category_id = :cid", array(":cid" => $category_data->id));
				$categories = array();
				if ($category_array) {
					foreach ($category_array as $row) {
						$categories[] = $row->id;
					}
					$data = " AND orig_category_id IN (" . implode(', ', $categories) . ") ";
				} else
					$data = '';
			} else
				$data = '';
		}

		$this->viewRedirectLink = true;
		$projects = Project::model()->findAll("time_added >= :date " . $data . " ORDER BY rating DESC, time_added DESC LIMIT :limit", 
												array(":date" => date('Y-m-d H:00:00', strtotime('-24 hours')),":limit" => $count));

		foreach ($projects as &$project) {
			$project->image = Yii::app()->createAbsoluteUrl("feed/image", array("data" => $project->id . ''));
			//$project->image = short_url_bitly($project->image);
		}
		echo $this->createRssFeed($projects, null, null, false);
		Yii::app()->end();
	}

	/**
	 * tracking RSS link clicks and redirecting them
	 */
	public function actionRl($l, $i = null) {
		// !!!log clicks
		$project = Project::model()->findByAttributes(array('link' => $l));
		if ($project) {
			$feedClick = new FeedClickLog();
			$feedClick->project_id = $project->id;
			$feedClick->subscription_id = $i;
			$feedClick->save();
		}

		$this->redirect($l);
		Yii::app()->end();
	}

	/**
	 * used for voting on feeds
	 */
	public function actionVote($l, $ra, $i = null) {

		$project = Project::model()->findByAttributes(array('link' => $l));
		if ($project) {
			$feedClick = FeedRate::model()->findByAttributes(array('project_id' => $project->id, 'subscription_id' => $i));
			if (!$feedClick) {
				$feedClick = new FeedRate();
				$feedClick->project_id = $project->id;
				$feedClick->subscription_id = $i;
				$feedClick->vote = $ra;
				$feedClick->save();
				//print_r($feedClick->getErrors());
			}
		}

		$this->layout = 'blank';
		$this->render('vote');
	}

	/**
	 * test mail digest
	 */
	public function actionMailTest() {
		Yii::app()->clientScript->reset();
		$this->layout = 'none';
		$project = Project::model()->findAll("1 LIMIT 12");

		$featured = array_slice($project, 0, 4);
		$regular = array_slice($project, 4, 12);

		$this->render('//layouts/mail/digest', array("title" => "Naslov",
			"user_id" => 1, "featuredProjects" => $featured, "projects" => $regular, "tc" => "adfsdf", "showEdit" => true, "editLink" => ""));
		// $content
// $title
// $user_id
// $featuredProjects
// $projects
// $tc
// $showEdit
// $editLink
	}

	/**
	 * 
	 * @param type $data
	 */
	public function actionImage($id) {
		
		Yii::app()->clientScript->reset();
		$this->layout = 'none';

		//header('Content-Type: application/rss+xml; charset=UTF-8');
		//mb_internal_encoding("UTF-8");
		//echo $data;
		//exit;

		//$id = str_replace(".jpg", "", $data);
		$project = Project::model()->findByAttributes(array("id" => $id));

		$this->redirect($project->image);
		Yii::app()->end();
	}

}
