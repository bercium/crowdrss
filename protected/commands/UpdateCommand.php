<?php
//set_time_limit(60*5); //5 min
class UpdateCommand extends CConsoleCommand {
    
//  Function for emailing of problematic project
    function errorMail($link, $category, $id) {
        $message = new YiiMailMessage;
        $message->view = 'system';
        $message->subject = 'Missing original category';
        $content = 'Category: ' . $category . '<br>Id: ' . $id . '<br>Link to project: ' . $link;
        $message->setBody(array("content" => $content, "title" => "Added new original category"), 'text/html');
        $message->to = Yii::app()->params['scriptEmail'];
        $message->from = Yii::app()->params['noreplyEmail'];
        Yii::app()->mail->send($message);
    }

//  Check if category exists
    function checkCategory($category_check, $link, $platform){
        $category_check = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $category_check);
        if ($platform == "PledgeMusic") { $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '15')); }
        elseif ($platform == "PubSlush"){ $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '24')); }
        else{$category = OrigCategory::model()->findByAttributes(array('name' => $category_check));}
        if ($category) { return $category; }
        else {
            $updateOrigCategory = new OrigCategory();
            if ($platform == "PledgeMusic") { $updateOrigCategory->category_id = 15; }
            elseif ($platform == "PubSlush"){ $updateOrigCategory->category_id = 24; }
            $updateOrigCategory->name = $category_check;
            $updateOrigCategory->save();
            if ($platform == "PledgeMusic") { $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '15')); }
            elseif ($platform == "PubSlush"){ $category = OrigCategory::model()->findByAttributes(array('name' => $category_check, 'category_id' => '24')); }
            else{ $category = OrigCategory::model()->findByAttributes(array('name' => $category_check)); }
            //$this->errorMail($link, $category_check, $category->id);
            return $category;
        }
    }

//  Kickstarter store in to DB
    public function actionKickstarter() {
        $parser = new KickstarterParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        $platform = Platform::model()->findByAttributes(array('name' => 'Kickstarter'));
        if (!$platform->download) return;
        
        $id = $platform->id;
        while (($i <= 50) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("https://www.kickstarter.com/discover/advanced?page=$i&state=live&sort=newest"));
            if (isset($data['links'])&&isset($data['images'])) {
                for ($j=0; $j< 20; $j++) {
                    $link = "https://www.kickstarter.com".$data['links'][$j];
                    if (strpos($link,"?") !== false) $link = substr($link, 0, strpos($link,"?"));
                    $link_parts = explode("/", $link);
                    $count_link_parts = count($link_parts);
                    $project_check = Project::model()->find("link LIKE :link1  OR  link LIKE :link2  OR  link LIKE :link3 OR image LIKE :image",
                                                            array(':link1' => '%/' . $link_parts[$count_link_parts - 1],
                                                                  ':link2' => $data['links'][$j], 
                                                                  ':link3' => $link,
                                                                  ':image' => $data['images'][$j]));

                    if ($project_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $count = 0;
                        $htmlData = $web->getHtml($link);
                        $data_single = $parser->projectParser($htmlData);
                        if ($data_single == false) { continue; }
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $link;
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $link, ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['start_date'])) $insert->start = $data_single['start_date'];
                        if (isset($data_single['end_date'])) $insert->end = $data_single['end_date'];
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['created'])) $insert->creator_created = $data_single['created'];
                        if (isset($data_single['backed'])) $insert->creator_backed = $data_single['backed'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $link, "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        // get rating 
                        $KsRating = new KickstarterRating($link, $insert->id, $htmlData);
                        $rating = $KsRating->firstAnalize();
                        $insert->rating = $rating;
                        $insert->save();

//                      print_r($insert->getErrors());
                    }
                    if ($count >= 30) {
                        $check = true;
                        break;
                    }
                }
            }//else echo "NO";
            $i = $i + 1;
        }
    }

//  Indiegogo store to DB
    public function actionIndiegogo() {
        $parser = new IndiegogoParser();
        $web = new webText();
        $platform = Platform::model()->findByAttributes(array('name' => 'Indiegogo'));
        if (!$platform->download) return;
        $id = $platform->id;
        $numberOfPages = 100;
        $jsonData = $parser->linkParser($web->getHtml("https://www.indiegogo.com/private_api/explore?experiment=true&filter_funding=&filter_percent_funded=&filter_quick=new&filter_status=&locale=en&per_page=$numberOfPages"));
        if ($jsonData == null){ return false; }
        if (count($jsonData->campaigns)>$numberOfPages/2) {
            for ($j=0; $j<=count($jsonData->campaigns)-1; $j++) {
                $link = "https://www.indiegogo.com".$jsonData->campaigns[$j]->url;
                $image = $jsonData->campaigns[$j]->compressed_image_url;
                $title = $jsonData->campaigns[$j]->title;
                $project_check = Project::model()->find("link LIKE :link OR  image LIKE :image",
                                                        array(':link' => $link, ':image' => $image));
                if (!$project_check) {
                    $htmlData = $web->getHtml($link);
                    $data_single = $parser->projectParser($htmlData);
                    if ($data_single == false) { continue; }
                    $insert = new Project;
                    $insert->title = $title;
                    $insert->description = $data_single['description'];
                    $insert->image = $image;
                    $insert->link = $link;
                    $insert->internal_link = toAscii($title);
                    $insert->time_added = date("Y-m-d H:i:s");
                    $insert->platform_id = $id;
                    $category = $this->checkCategory($data_single['category'], $link, "");
                    $insert->orig_category_id = $category->id;
                    if (isset($data_single['start_date'])) $insert->start = $data_single['start_date'];
                    if (isset($data_single['end_date'])) $insert->end = $data_single['end_date'];
                    if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                    if (isset($data_single['location'])) $insert->location = $data_single['location'];
                    if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                    if (isset($data_single['type_of_funding'])) {
                        if ($data_single['type_of_funding'] == "Fixed Funding") { $typeOfFunding = 0; }
                        else {$typeOfFunding = 1; }
                        $insert->type_of_funding = $typeOfFunding;
                    }
                    $insert->save();


                    $id_project = $insert->id;
                    // Category add
                    $insert_category = new ProjectOrigcategory;
                    $insert_category->project_id = $id_project;
                    $category = $this->checkCategory($data_single['category'], $link, "");
                    $insert_category->orig_category_id = $category->id;
                    $insert_category->save();

                    // get rating 
                    $IggRating = new IndiegogoRating($link, $insert->id, $htmlData);
                    $rating = $IggRating->firstAnalize();
                    $insert->rating = $rating;
                    $insert->save();
//                  print_r($insert->getErrors());
                }
            }
        }
    }

//  GoGetFunding store to DB
    public function actionGoGetFunding() {
        $parser = new GoGetFundingParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        $platform = Platform::model()->findByAttributes(array('name' => 'Go get funding'));
        if (!$platform->download) return;
        $id = $platform->id;
        while (($i <= 10) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("http://gogetfunding.com/wp-content/themes/ggf/campaigns.php", array(), false, array("campaign_type" => "recent_campaigns", "page" => "$i", "step" => "get_campaigns_by_campaign_type")));
            if (isset($data['link'])) {
                for ($j=0; $j < (count($data['link'] )); $j++) {
                    $link_check = Project::model()->findByAttributes(array('link' => $data['link'][$j]));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['link'][$j]);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data['title'][$j];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['image'][$j];
                        $insert->link = $data['link'][$j];
                        $insert->internal_link = toAscii($data['title'][$j]);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $data['link'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $data['link'][$j], "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        $count = 0;
            //	    print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            $i = $i + 1;
        }
    }

//  FundAnything store in to DB
    public function actionFundAnything() {
        $parser = new FundAnythingParser();
        $web = new webText();
        $i = 1;
        $platform = Platform::model()->findByAttributes(array('name' => 'Fund anything'));
        if (!$platform->download) return;
        $id = $platform->id;
        while ($i <= 3) {
            $data = $parser->linkParser($web->getHtml("http://fundanything.com/en/search/category?cat_id=29&page=$i"));
            if (isset($data['link'])) {
                for($j=0; $j < (count($data['link'])); $j++) {
                    $link_check = Project::model()->findByAttributes(array('link' => $data['link'][$j]));
                    if ($link_check) {  } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['link'][$j]);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['image'][$j];
                        $insert->link = $data['link'][$j];
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory(html_entity_decode($data['categorie'][$j]), $data['link'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($$data['location'][$j])) $insert->location = $$data['location'][$j];
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory(html_entity_decode($data['categorie'][$j]), $data['link'][$j], "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

//                      print_r($insert->getErrors());
                    }
                }
            }
            $i = $i + 1;
        }
    }

//  FundRazr store in to DB
    public function actionFundRazr() {
        $parser = new FundRazrParser();
        $web = new webText();
        $check = false;
        $count = 0;
        $i = 1;
        $platform = Platform::model()->findByAttributes(array('name' => 'Fundrazr'));
        if (!$platform->download) return;
        $id = $platform->id;
        while ($i <= 5) {
            $data = $parser->linkParser($web->getHtml("https://fundrazr.com/find?type=newest&page=$i"));
            if (isset($data['link'])) {
                for ($j=0; $j < (count($data['link'])); $j++) {
                    $link = "https:" . $data['link'][$j]; 
                    $link_check = Project::model()->findByAttributes(array('link' => $link));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($link);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = "https:" . $data['image'][$j];
                        $insert->link = $link;
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $link, ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['start_date'])) $insert->start = date("Y-m-d H:i:s", strtotime($data_single['start_date']));
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", strtotime($data_single['end_date']));
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $link, "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();
//                      print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            $i = $i + 1;
        }
    }

//  PledgeMusic store to DB
    public function actionPledgeMusic(){
        $parser = new PledgeMusicParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        $platform = Platform::model()->findByAttributes(array('name'=>'Pledge music'));
        if (!$platform->download) return;
        $id = $platform->id;
        while (($i <= 10) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("http://www.pledgemusic.com/projects/index/launched?page=$i"));
            for ($j=0; $j < (count($data['link'])); $j++) {
                $link = "http://www.pledgemusic.com".$data['link'][$j];
                if (strpos($link,"?") !== false) $link = substr($link, 0, strpos($link,"?"));
                $link_parts = explode("/", $link);
                $count_link_parts = count($link_parts);
                $project_check = Project::model()->find("link LIKE :link1  OR  link LIKE :link2  OR  link LIKE :link3",
                                                          array(':link1' => '%/' . $link_parts[$count_link_parts - 1],
                                                                ':link2' => $data['link'][$j], 
                                                                ':link3' => $link));
                if ($project_check) {$count = $count+1;} // Counter for checking if it missed some project in the next few projects
                else{
                    $htmlData = $web->getHtml($link);
                    $data_single = $parser->projectParser($htmlData);
                    $category_all = explode(', ', $data_single['category']);
                    $insert=new Project;
                    $insert->title=$data_single['title'];
                    $insert->description=$data_single['description'];
                    $insert->image=$data['image'][$j];
                    $insert->link=$link;
                    $insert->time_added=date("Y-m-d H:i:s");
                    $insert->platform_id=$id;
                    $category = $this->checkCategory($category_all[0], $link, "PledgeMusic"); // ZAČASNO*****************************************************************
                    $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                    if (isset($data_single['creator'])) {
                        $insert->creator = $data_single['creator'];
                        $internal_link = $data_single['creator'];
                    }
                    if (isset($data_single['time'])) {
                      if ($data_single['time'] <> "In Progress"){
                        $insert->end=date("Y-m-d H:i:s", strtotime("+" . $data_single['time'] . "days"));
                      }
                    }
                    if (isset($data_single['location'])) $insert->location=$data_single['location'];
                    $internal_link .= " " . $data_single['title'];
                    $insert->internal_link = toAscii($internal_link);
                    $insert->save();

                    $id_project = $insert->id;
                    // Category add
                    for ($i=0; $i< count($category_all); $i++){
                      $insert_category = new ProjectOrigcategory;
                      $insert_category->project_id = $id_project;
                      $category = $this->checkCategory($category_all[$i], $link, "PledgeMusic");
                      $insert_category->orig_category_id = $category->id;
                      $insert_category->save();
                    }
                    $count = 0;
                    // print_r($insert->getErrors());
                }
                if ($count >= 40){ $check=true; break; }
                $i=$i+1;
            }
        }
    }
    
// Ulule store to DB
    public function actionUlule() {
        $platform = Platform::model()->findByAttributes(array('name' => 'Ulule'));
        if (!$platform->download) return;
        $id = $platform->id;
        $parser = new UluleParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        while (($i <= 5) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("http://www.ulule.com/discover/filter/new/$i/"));
            if (isset($data['links'])) {
                for ($j=0; $j < (count($data['links'] )); $j++) {
                    $link_check = Project::model()->findByAttributes(array('link' => $data['links'][$j]));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['links'][$j]);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $data['links'][$j];
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data['categories'][$j], $data['links'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", strtotime("+".$data_single['end_date']." days"));
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
//                        if (count($data_single['categories']) > 1){
//                            for ($k = 0; $k < count($data_single['categories']); $k++){
//                                $insert_category = new ProjectOrigcategory;
//                                $insert_category->project_id = $id_project;
//                                $category = $this->checkCategory($data_single['categories'][$k], $data['links'][$j], "");
//                                $insert_category->orig_category_id = $category->id;
//                                $insert_category->save(); 
//                            }
//                        }else{
                            $insert_category = new ProjectOrigcategory;
                            $insert_category->project_id = $id_project;
                            $category = $this->checkCategory($data['categories'][$j], $data['links'][$j], "");
                            $insert_category->orig_category_id = $category->id;
                            $insert_category->save();
//                        }

                        $count = 0;
            //	    print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            $i = $i + 1;
        }
    }

// Pozible store to DB
    public function actionPozible() {
        $platform = Platform::model()->findByAttributes(array('name' => 'Pozible'));
        if (!$platform->download) return;
        $id = $platform->id;
        $parser = new PozibleParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        while (($i <= 5) and ($check == false)) {
            if ($i == 1) $data = $parser->linkParser($web->getHtml("http://www.pozible.com/list/new/0/all/0"));
            else $data = $parser->linkParser($web->getHtml("http://www.pozible.com/list/new/0/all/0/0/". (15*($i-1))));
            //$data = $parser->linkParser($web->getHtml("http://www.pozible.com/list/new/0/all/0/0/30"));
            if (isset($data['links'])) {
                for ($j=0; $j < (count($data['links'] )); $j++) {
                    $link_check = Project::model()->findByAttributes(array('link' => $data['links'][$j]));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['links'][$j]);
                        $split_link = explode("/",$data['links'][$j]);
                        $description_link = $split_link[0]."//".$split_link[2]."/".$split_link[3]."/pnav/".$split_link[4]."/description/0";
                        $htmlData .= $web->getHtml($description_link);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $data['links'][$j];
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", strtotime("+".$data_single['end_date']." days"));
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        $count = 0;
            //	    print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            $i = $i + 1;
        }
    }

// Pledge Me store to DB
    public function actionPledgeMe() {
        $platform = Platform::model()->findByAttributes(array('name' => 'PledgeMe'));
        if (!$platform->download) return;
        $id = $platform->id;
        $parser = new PledgeMeParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        //while (($i <= 5) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("https://www.pledgeme.co.nz/campaigns?type=current&category=&campaign_type=Projects"));
            if (isset($data['links'])) {
                for ($j=0; $j < (count($data['links'] )); $j++) {
                    $link = "https://www.pledgeme.co.nz".$data['links'][$j];
                    $link_check = Project::model()->findByAttributes(array('link' => $link));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($link);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $link;
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $link, ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", $data_single['end_date']);
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $link, "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        $count = 0;
            //	    print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            //$i = $i + 1;
        //}
    }
    
// FundedByMe store to DB
    public function actionFundedByMe() {
        $platform = Platform::model()->findByAttributes(array('name' => 'FundedByMe'));
        if (!$platform->download) return;
        $id = $platform->id;
        $parser = new FundedByMeParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
//        while (($i <= 5) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("https://www.fundedbyme.com/en/browse/?type=r&multiselect_multiselect-type=r&state=live&multiselect_multiselect-state=live&sorted-by=recently-added&index=0"));
            if (isset($data['links'])) {
                for ($j=0; $j < (count($data['links'] )); $j++) {
                    $data['links'][$j] = "https://www.fundedbyme.com".$data['links'][$j];
                    $link_check = Project::model()->findByAttributes(array('link' => $data['links'][$j]));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['links'][$j]);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $data['links'][$j];
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", $data_single['end_date']);
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        $count = 0;
//                      print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
//            $i = $i + 1;
//        }
    }
    
// Crowdfunder store to DB
    public function actionCrowdfunderUK() {
        $platform = Platform::model()->findByAttributes(array('name' => 'CrowdfunderUK'));
        if (!$platform->download) return;
        $id = $platform->id;
        $parser = new CrowdfunderUKParser();
        $web = new webText();
        $i = 1;
        $check = false;
        $count = 0;
        while (($i <= 5) and ($check == false)) {
            $data = $parser->linkParser($web->getHtml("http://www.crowdfunder.co.uk/projects/search/category:all/order/latest/page/".(($i-1)*12)));
            if (isset($data['links'])) {
                for ($j=0; $j < (count($data['links'] )); $j++) {
                    $data['links'][$j] = "http://www.crowdfunder.co.uk".$data['links'][$j];
                    $link_check = Project::model()->findByAttributes(array('link' => $data['links'][$j]));
                    if ($link_check) { $count = $count + 1; } // Counter for checking if it missed some project in the next few projects
                    else {
                        $htmlData = $web->getHtml($data['links'][$j]);
                        $data_single = $parser->projectParser($htmlData);
                        $insert = new Project;
                        $insert->title = $data_single['title'];
                        $insert->description = $data_single['description'];
                        $insert->image = $data['images'][$j];
                        $insert->link = $data['links'][$j];
                        $insert->internal_link = toAscii($data_single['title']);
                        $insert->time_added = date("Y-m-d H:i:s");
                        $insert->platform_id = $id;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], ""); // ZAČASNO*****************************************************************
                        $insert->orig_category_id = $category->id; // ZAČASNO*****************************************************************
                        if (isset($data_single['creator'])) $insert->creator = $data_single['creator'];
                        if (isset($data_single['location'])) $insert->location = $data_single['location'];
                        if (isset($data_single['goal'])) $insert->goal = $data_single['goal'];
                        if (isset($data_single['end_date'])) $insert->end = date("Y-m-d H:i:s", $data_single['end_date']);
                        $insert->save();

                        $id_project = $insert->id;
                        // Category add
                        $insert_category = new ProjectOrigcategory;
                        $insert_category->project_id = $id_project;
                        $category = $this->checkCategory($data_single['category'], $data['links'][$j], "");
                        $insert_category->orig_category_id = $category->id;
                        $insert_category->save();

                        $count = 0;
//                      print_r($insert->getErrors());
                    }
                    if ($count >= 10) {
                        $check = true;
                        break;
                    }
                }
            }
            $i = $i + 1;
        }
    }
}