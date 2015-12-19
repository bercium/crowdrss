<?php

class MailerCommand extends CConsoleCommand {

	/**
	 * mark project removed from DB
	 */
	private function removeFromDB($id) {
		$update = Project::model()->findByPk($id);
		if ($update) {
			$update->removed = 1;
			$update->save();
		}
	}

	/**
	 * check project link if it's OK
	 */
	private function checkProjectLink($link, $id = '') {
		$httpClient = new elHttpClient();
		$httpClient->setUserAgent("ff3");
		$httpClient->enableRedirects();
		$httpClient->setHeaders(array_merge(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")));
		$htmlDataObject = $httpClient->get($link, array("X-Requested-With" => "XMLHttpRequest"));
		$text = $htmlDataObject->httpBody;
		if (strpos($link, "indiegogo.com") !== false) {
			if (strpos($text, "i-illustration-not_found") !== false) {
				$this->removeFromDB($id);
				return false;
			}
		} else {
			if (strpos($link, "kickstarter.com") !== false) {
				if (strlen($text) < 2000) {
					$this->removeFromDB($id);
					return false;
				}
			}
		}

		return true;
	}

	private function createSQL($sub, $days = 1) {
		$sql = '';
		if ($sub->category) {
			//if ($this->validateId($sub->category)){
			//$orgCat = OrigCategory::model()->findAll("(category_id IN (".$this->validateId($sub->category)."))");

			$subcat = array();
			if ($sub->exclude_orig_category) {
				//    if ($this->validateId($sub->exclude_orig_category)){
				$subcat = explode(",", $sub->exclude_orig_category);
				//      $subcat = explode(",",$this->validateId($sub->exclude_orig_category));
			}

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
		$hours = $days * 24 + 3;
		$sql .= " (removed = 0) AND (time_added < DATE_ADD(NOW(),INTERVAL -3 HOUR)) AND (time_added >= DATE_ADD(NOW(),INTERVAL -" . $hours . " HOUR)) ";  // one day slot with 3h delay
		if ($sub->rating > 0)
			$sql .= " AND ((rating IS NULL) OR (rating >= " . $sub->rating . ")) ";

		$sql .= " ORDER BY rating DESC, time_added ASC";
		//$sql .= " LIMIT 12";
		return $sql;
	}

	/**
	 * 
	 */
	private function sortProjects($sub, $projects, $checkProject = false) {
		
		//featured project
		$featuredProject = new FeaturedProject($sub);
		$featuredProject->setSub($sub);
		$paidProject = $featuredProject->featuredDB();
		$cfrss_promotion = false;
		if ($paidProject == null) {
			// check if someone has shared already
			$paidProject = $featuredProject->featuredCFrss();
			$cfrss_promotion = true;
		}


		$featured = $regular = $regularNull = $regularPlatforms = $platformInFeatured = array();
		$i = 0;
		foreach ($projects as $project) {
			$i++;
			if (($paidProject) && ($paidProject->id == $project->id)) continue; // skip featured project from the list

			if ($checkProject) //recheck validity of links
				if (!$this->checkProjectLink($project->link, $project->id)) continue;

			if ($i <= 4){
				$featured[] = $project;
				//$platformInFeatured[] = $project->platform_id;
			}
			else {
				/*if ($project->rating == null){
					$regularNull[] = $project;
				}else{*/
				//$regular[] = $project;
				$regularPlatforms[$project->platform_id][] = $project;
				//}
			}
		}

		// add paid projects
		if ($paidProject) {
			if (!$cfrss_promotion)
				array_unshift($featured, $paidProject);  //add to the beginning of the queue
			else {
				$paidProject->rating = 0; // don't make it special cause we have stars in the title
				//$featured[rand(0, 3)] = $paidProject; //overwrite one project
				array_splice($featured, rand(0, 3), 0, array($paidProject)); // insert into featured
			}
		}

		// one from each platform
        $diffPlatforms = count($regularPlatforms);
		//$projectsInPlatformProjects = array();
        if ($diffPlatforms == 0) $repeat = 4;
        else if ($diffPlatforms > 4) $repeat = ceil(12 / $diffPlatforms);
        else $repeat = ceil(8 / $diffPlatforms);
        
        $data['repeat'] = $repeat;
        $data['count'] = $diffPlatforms;
        $data['countall'] = count($projects);
        
        foreach ($regularPlatforms as $key => $val){
            //if (count($val) <= 0) continue;
            //if (in_array($key, $platformInFeatured)) continue; //skip platforms in featured section

            if ($val[0]->rating == null){
                $data['shuffle'][$key] = true;
                shuffle($val);
            }else $data['shuffle'][$key] = false;
            //$projectsInPlatformProjects[] = $val[0]->id;
            for ($index = 0; $index < count($repeat); $index++){
                if (isset($val[$index])) $regular[] = $val[$index];
                else break;
            }
            //unset($val[0]);
        }
        
        $featured[0]->description = print_r($data,true);
		// $platformProjects  0 - 12
		/*
		$theRest = array();
		foreach ($regular as $val){
			if (in_array($val->id, $projectsInPlatformProjects)) continue; //project already in platform project
			$theRest[] = $val;
			if (count($val)+count($platformProjects) >= 12) break;
		}
		
		$regular = array_merge($theRest, $platformProjects); //add the rest and patform projects
        */
		//shuffle($regularNull);
		//$regularNull = array_slice($regularNull, 0, 4);
		//$regular = array_merge(array_slice($regular, 0, 8 - count($regularNull)), $regularNull);

		if (count($regular) < 4)
			$regular = array();
		else if (count($regular) < 8)
			$regular = array_slice($regular, 0, 4);
		else if (count($regular) < 12)
			$regular = array_slice($regular, 0, 8);
		
		return array("featured" => $featured, "regular" => $regular);
	}

	/**
	 * 
	 */
	private function sendNewsletter($sub, $title, $subject, $trackingCode, $projects) {
		//set mail tracking
		$tc = mailTrackingCode();
		$ml = new MailLog();
		$ml->tracking_code = mailTrackingCodeDecode($tc);
		$ml->type = $trackingCode;
		$ml->subscription_id = $sub->id;
		$ml->save();


		// create message
		$message = new YiiMailMessage;
		$message->view = 'digest';
		$message->subject = $subject;
		$message->from = Yii::app()->params['adminEmail'];

		$content = '';

		$count = count($projects['featured']) + count($projects['regular']);
		// not enough projects
		if ($count < 4) {
			$content = 'We found just a few projects for you. <br />Maybe your rules are too strict? Consider editing your feed.<hr>';
		}

		$editLink = absoluteURL() . "site/index?id=" . $sub->hash;

		$message->setBody(array("tc" => $tc, "user_id" => $sub->id,
			"content" => $content, "title" => $title,
			"featuredProjects" => $projects['featured'], "projects" => $projects['regular'],
			"showEdit" => true, "editLink" => $editLink
				), 'text/html');
		$message->setTo($sub->email);
		if ($count > 0) {
			Yii::app()->mail->send($message);
			$filename = Yii::app()->getRuntimePath() . "/dry-emails.txt";
			$fc = '';
			if (file_exists($filename))
				$fc = file_get_contents($filename);
			$fc .= date("Y-m-d [H:i]") . " (" . $trackingCode . "): " . $sub->email . "\n";
			file_put_contents($filename, $fc);
		}
	}

	/**
	 * 
	 * @param string $type - type of log tracking code
	 */
	private function getSubsSent($type) {
		$mailsRec = MailLog::model()->findAll("type = :type AND DATE(time_send) = :date", array(":type" => $type, ":date" => date('Y-m-d')));
		$mails = array();
		foreach ($mailsRec as $mail) {
			$mails[$mail->subscription_id] = 1;
		}
		return $mails;
	}

	/**
	 * daily digest
	 */
	public function actionDailyDigest($test = false) {

		/* if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
		  else */$subscriptions = Subscription::model()->findAllByAttributes(array('daily_digest' => 1));

		if ($subscriptions) {


			//$paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 1 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));

			$date = addOrdinalNumberSuffix(date("j", strtotime("-1 days"))) . " " . date("M", strtotime("-1 days"));

			$sentMails = $this->getSubsSent('daily-digest');
			$i = 0;
			foreach ($subscriptions as $sub) {
				// only send arround 100 emails per hour
				if (isset($sentMails[$sub->id]) && !$test) continue;
				if ($i++ > 95) break;

				$sql = $this->createSQL($sub, 1);

				// get projects
				$projects = Project::model()->findAll($sql);

				$sorted = $this->sortProjects($sub, $projects, true);

				if (!$test || $sub->id == 1 || $sub->id == 2)
					$this->sendNewsletter($sub, 'Top crowdfunding projects for ' . $date, "Your Daily Dose Of Crowdfunding Projects for " . $date, 'daily-digest', $sorted);
			}
		}
	}

	public function actionTestDailyDigest() {
		$this->actionDailyDigest(true);
        return print_r(array('neki'=>'neki'));
	}

	/**
	 * 
	 */
	public function actionWeeklyDigest($test = false) {
		$week_day = date("w");
		if ($week_day != 1)
			exit; // mondays only

			/* if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
			  else */
		$subscriptions = Subscription::model()->findAllByAttributes(array('weekly_digest' => 1));

		if ($subscriptions) {

			//$paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 2 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));

			if (date("M", strtotime("-1 days")) == date("M", strtotime("-8 days"))) {
				$date = addOrdinalNumberSuffix(date("j", strtotime("-8 days"))) . " - " . addOrdinalNumberSuffix(date("j", strtotime("-1 days"))) . " " . date("M", strtotime("-1 days"));
			} else {
				$date = addOrdinalNumberSuffix(date("j", strtotime("-8 days"))) . " " . date("M", strtotime("-8 days")) . " - " . addOrdinalNumberSuffix(date("j", strtotime("-1 days"))) . " " . date("M", strtotime("-1 days"));
			}

			$sentMails = $this->getSubsSent('weekly-digest');
			$i = 0;
			foreach ($subscriptions as $sub) {
				// only send arround 100 emails per hour
				if (isset($sentMails[$sub->id]) && !$test) continue;
				if ($i++ > 95) break;

				$sql = $this->createSQL($sub, 7);

				// get projects
				$projects = Project::model()->findAll($sql);


				$sorted = $this->sortProjects($sub, $projects);

				if (!$test || $sub->id == 1 || $sub->id == 2)
					$this->sendNewsletter($sub, 'Top crowdfunding projects for week ' . $date, "Your Weekly Dose Of Crowdfunding Projects for " . $date, 'weekly-digest', $sorted);
			}
		}
	}

	public function actionTestWeeklyDigest() {
		$this->actionWeeklyDigest(true);
	}

	/**
	 * daily digest
	 */
	public function actionTwiceAWeekDigest($test = false) {
		// only on sundays and wednesdays  
		$week_day = date("w");
		if ($week_day != 0 && $week_day != 3)
			exit;

		/* if ($test) $subscriptions = Subscription::model()->findAll("id = 1 OR id = 2");
		  else */$subscriptions = Subscription::model()->findAllByAttributes(array('two_times_weekly_digest' => 1));

		if ($subscriptions) {


			//$paidProjects = ProjectFeatured::model()->findAll("active = 1 AND feature_where = 1 AND feature_date = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));

			$date = addOrdinalNumberSuffix(date("j", strtotime("-1 days"))) . " " . date("M", strtotime("-1 days"));

			$sentMails = $this->getSubsSent('weekly-digest-twice');
			$i = 0;
			foreach ($subscriptions as $sub) {

				// sunday
				if ($week_day == 0) {
					$sql = $this->createSQL($sub, 4);
					$date = "last 4 days";
				} else	//wednesday
				if ($week_day == 3) {
					$date = "last 3 days";
					$sql = $this->createSQL($sub, 3);
				}

				// only send arround 100 emails per hour
				if (isset($sentMails[$sub->id]) && !$test) continue;
				if ($i++ > 95) break;

				// get projects
				$projects = Project::model()->findAll($sql);

				$sorted = $this->sortProjects($sub, $projects);

				if (!$test || $sub->id == 1 || $sub->id == 2)
					$this->sendNewsletter($sub, 'Top crowdfunding projects for ' . $date, "Your Dose Of Crowdfunding Projects for " . $date, 'weekly-digest-twice', $sorted);
			}
		}
	}

	public function actionTestTwiceAWeekDigest() {
		$this->actionTwiceAWeekDigest(true);
	}

	/**
	 * validates that parser has parsed something in the last few hours
	 */
	public function actionValidateParsers() {
		$hours = 8;
		$ksCount = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -" . $hours . " HOUR) AND platform_id = 1");
		$iggCount = Project::model()->countBySql("SELECT COUNT(*) FROM project WHERE time_added > DATE_ADD(NOW(), INTERVAL -" . $hours . " HOUR) AND platform_id = 2");

		if (($ksCount < 15) || ($iggCount < 15)) {

			// create message
			$message = new YiiMailMessage;
			$message->view = 'system';
			$message->subject = "Parser validation failed";  // 11 Dec title change
			$message->from = Yii::app()->params['scriptEmail'];

			$content = 'Not enough projects parsed.<br /><br />';
			if ($ksCount < 15)
				$content .= 'Kickstarter has ' . $ksCount . " new projects in last 8h!<br />";
			if ($iggCount < 15)
				$content .= 'Indiegogo has ' . $iggCount . " new projects in last 8h!<br />";

			$message->setBody(array("content" => $content), 'text/html');
			$message->setTo('info@crowdfundingrss.com');
			Yii::app()->mail->send($message);
		}

		return 0;
	}

}
