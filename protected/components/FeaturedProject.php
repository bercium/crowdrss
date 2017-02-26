<?php
// http://stackoverflow.com/questions/5699270/how-to-get-share-counts-using-graph-api
/*

Facebook*: https://api.facebook.com/method/links.getStats?urls=%%URL%%&format=json
Twitter: http://urls.api.twitter.com/1/urls/count.json?url=%%URL%%
Reddit:http://buttons.reddit.com/button_info.json?url=%%URL%%
LinkedIn: http://www.linkedin.com/countserv/count/share?url=%%URL%%&format=json
Digg: http://widgets.digg.com/buttons/count?url=%%URL%%
Delicious: http://feeds.delicious.com/v2/json/urlinfo/data?url=%%URL%%
StumbleUpon: http://www.stumbleupon.com/services/1.01/badge.getinfo?url=%%URL%%
Pinterest: http://widgets.pinterest.com/v1/urls/count.json?source=6&url=%%URL%%

 */

class FeaturedProject {

    private $featuredProject, $sub;

    function __construct($sub = null) {
        $this->featuredProject = ProjectFeatured::model()->findAll("active = 1 AND DATE(feature_date) = :date ORDER BY show_count ASC",array(":date"=>date('Y-m-d')));
        $this->sub = $sub;
    }

    /**
     * featured project or feature our site
     */
    public function featured() {
        $featured = $this->featuredDB();
        
        if ($featured == null){
            $featured = $this->featuredCFrss();
        }
        
        return $featured;
    }
    
    /**
     * set sub
     */
    public function setSub($sub){
        $this->sub = $sub;
    }
    
    /**
     * 
     */
    public function featuredDB(){
        $featuredProject = null;
        if (count($this->featuredProject) > 0){
          foreach ($this->featuredProject as $fp){
              $platA = explode(",",$this->sub->platform);
              $catA = explode(",",$this->sub->category);
              $subCatA = explode(",",$this->sub->exclude_orig_category);

              if ($this->sub->platform && !in_array($fp->project->platform_id, $platA)) continue; // has platforms but not in
              if (in_array($fp->project->orig_category_id, $subCatA)) continue; // exclude list
              if ($this->sub->category && !in_array($fp->project->origCategory->category_id, $catA)) continue; // not in category

              $fp->show_count++;
              $fp->save();//save view
			  
              $featuredProject = $fp->project; //get one project
              // set the rating higher so we know it's special
              if ($featuredProject->rating) $featuredProject->rating += 11;
              else $featuredProject->rating = 11;
			  
			  $h = floor(date("G") / 6)*6; // 4 comercials per day
              $featuredProject->time_added = strtotime(date("Y-m-d ".$h.":00:00"));

              break;
          }
        }
        return $featuredProject;
    }
    
    /**
     * feature our site as featured project
     */
    public function featuredCFrss(){
        $project = new stdClass();
        
        //if ($this->sub->id != 1 && $this->sub->id != 2) return null;
        if ($this->sub->shared > 0) return null; // don't spam those that have shared
        
        $hashids = new Hashids('crowdrss');
        
        //$h = floor(date("G") / 6)*6; // 4 comercials per day
        $d = floor(date("d") / 6)*6; // 1 comercial per week
        
        $project->id = -1;
        $project->title = 'Crowdfunding RSS [★★★★★]'; //☆
        //$project->time_added = strtotime(date("Y-m-d ".$h.":00:00"));
        $project->time_added = strtotime(date("Y-m-".$d." 12:00:00"));
        $project->origCategory = new stdClass();
        $project->origCategory->name = 'Crowdfunding-rss';
        $project->link = 'http://crowdfundingrss.com/site/share?ref='.$this->sub->hash.'&lnk='. substr($hashids->encrypt(date("Ymd").$h),0,6);
        $project->image = 'http://crowdfundingrss.com/images/cf-rss-show-small.png';
        $tracking_link = "http://crowdfundingrss.com/feed/rl?l=".urlencode("/site/share?ref=".$this->sub->hash).'&i='.$this->sub->id;
        $project->description = 'We here at CrowdfundingRSS ❤<strong>LOVE you guys</strong>❤.<br />If you find our service usefull <strong><a href="'.$tracking_link.'">tell your friends</a></strong> about us so they might enjoy it too. By doing that you are helping us build the best crowdfunding digest site. <br /><strong>📢 <a href="'.$tracking_link.'">Invite them NOW</a>.</strong>';
        $project->nolike = true;
        $project->rating = 100;
        //$project->platform->name = '';
        //$project->origCategory->name = '';
        
        return $project;
    }
    
    
}
