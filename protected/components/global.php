<?php


/**
 * append this string to files that you wish to force refresh during version changes
 * it should be used on CSS and JS files that get cached for a long time
 */
function getVersionID(){
  $version = Yii::app()->params['version'];
  
  return "?".substr(md5($version),0,5);
}

/**
 * merge two arrays recursivly
 */
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}


/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
 * @param bool $strip_html if html tags are to be stripped
 * @return string 
 */
function trim_text($input, $length, $ellipses = true, $strip_html = true) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }
  
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}

/**
 * 
 */
//if(!class_exists('elhttpclient'));
function getGMap($country = '', $city = '', $addr = ''){
  //include_once "httpclient.php";
	//if(!class_exists('elhttpclient')){
	//Yii::import('application.helpers.elHttpClient');
	//}
  $httpClient = new elHttpClient();
  $httpClient->setUserAgent("ff3");
 
  
  $zoom = 0;
  $address = '';
  if ($country){
    $zoom = 3;
    $address = $country;
  }
  if ($city){
    $zoom = 8;
    if ($address) $address .= ', ';
    $address .= $city;
  }
  if ($addr){
    $zoom = 14;
    if ($address) $address .= ', ';
    $address .= $addr;
  }
  if ($zoom == 0) return '';
  
  $URL = "maps.googleapis.com/maps/api/staticmap?center=".$address."&zoom=".$zoom."&size=150x150&maptype=roadmap&sensor=true&markers=size:mid|color:green|".$address;
 
  $filename = $address.".png";
  $folder = Yii::app()->basePath.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.Yii::app()->params['mapsFolder'];
  
  if (file_exists($folder.$filename)){
    return Yii::app()->getBaseUrl(true)."/".Yii::app()->params['mapsFolder'].$filename;
  }else{
    //$this->buildRequest($URL, 'GET');
    //return $this->fetch($URL);
    $httpClient->setHeaders(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    //$htmlDataObject = $httpClient->get("maps.googleapis.com");
    $URL = str_replace(" ", "%20", $URL);
    $htmlDataObject = $httpClient->get($URL);
    //change from XML to array
    $htmlData = $htmlDataObject->httpBody;
    
 		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

    @file_put_contents($folder.$filename, $htmlData);
    if (file_exists($folder.$filename)) return Yii::app()->getBaseUrl(true)."/".Yii::app()->params['mapsFolder'].$filename;
    else return false;
  }
}


function add_http($link){
  //return $link;
  if ((strpos($link, "http://") === false) && (strpos($link, "https://") === false)){
    return "http://".$link;
  }
  return $link;
}


/**
 * remove http:// and https://
 */
function remove_http($url) {
   $disallowed = array('http://', 'https://');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}


/**
 * set flash will set flash with some extra parameters
 * @value string $flashName - name of ID to show flash for
 * @value string $flashMesage - string message to show in flash or
 *                              array in format array(msg='',action=array of actions(hint='',action='')) where message should have %s for replacing actions
 * @value string $staus - ['success'] status of message shown can be: alert, success or info
 * @value string $autoHide - weather flash message should be automaticaly hidden after a period of time
 * 
 */
function setFlash($flashName, $flashMessage, $status = 'success', $autoHide = true){
  $flash = array("message"=>$flashMessage, "status"=>$status, "autoHide" => $autoHide);
  Yii::app()->user->setFlash($flashName, $flash);
}

/**
 * will decode message if array or string
 */
function decodeFlashMsg($msg){
  
  if (is_array($msg) && isset($msg['msg'])){
    $actions = array();
    
    if (isset($msg['actions'])){
      foreach ($msg['actions'] as $action){
        $actions[] = '<a href="'.$action['action'].'" class="action button radius tiny secondary ml10" style="margin-bottom: 0;" alt="'.$action['hint'].'" title="'.$action['hint'].'">'.
                     $action['hint'].
                     '</a>';
      }
    }
    $msg['msg'] = str_replace("%%s", "%s", str_replace("%", "%%", $msg['msg']));
    return vsprintf($msg['msg'],$actions);
  }else return $msg;
}

/**
 * will return flash data as a string
 */
function clearFlashes(){
  Yii::app()->user->getFlashes(true);
}

/**
 * will return flash data as a string
 */
function getFlashData($flashName){
  if(Yii::app()->user->hasFlash($flashName)){
    $flash =  Yii::app()->user->getFlash($flashName);
    return decodeFlashMsg($flash['message']);
  }
  return false;
}

/**
 * will return whole flash with styling
 */
function getFlash($flashName){
  $html = '';
  if(Yii::app()->user->hasFlash($flashName)){
    $flash = Yii::app()->user->getFlash($flashName);
    
    $html .= '<div data-alert class="alert-box radius '.$flash['status'].'">';
    $html .= decodeFlashMsg($flash['message']);
    $html .= '<a href="#" class="close">&times;</a></div>';
  }
  return $html;
}

function writeFlash($flashName){
  echo getFlash($flashName);
}

/**
 * will write all the flashes in standard way and assign them a timeout function
 */
function writeFlashes(){
  $flashMessages = Yii::app()->user->getFlashes(false);
  if ($flashMessages) {
    $nh = $i = 0;
    $hide = '';
    $html = '<div class=""><div class="">';
    foreach($flashMessages as $key => $flash) {
      Yii::app()->user->getFlash($key);

      if ($flash["autoHide"]){
        if ($flash['status'] != 'alert') $wait_time = 4000;
        else $wait_time = 10000;
        $hide .=  "$('.flash-hide-".$i."').oneTime(".($wait_time+$i*1000).", function() { $(this).fadeOut(); })"
                . "                                   .hover( function() { $(this).stopTime();}, 
                                                              function() { $(this).oneTime(".(4000+$i*1000).", function() { $(this).fadeOut(); }); });";
      }else $nh++;      

      $html .= '<div class="alert-box mb0 '.$flash['status'].' flash-hide-'.$i.' " style="margin-bottom:0px; font-weight:bold;" data-alert><div class="row">';
      $html .= decodeFlashMsg($flash['message']);
      $html .= '</div><a href="#" class="close">&times;</a></div>';
      //$html .= '</div></div>';

      $i++;
    }

    $html .= '<div></div>';
    if ($nh > 0){
      $html .= '<div></div>';
    }
    if ($i > 0){ 
      echo $html;
      Yii::app()->clientScript->registerScript(
         'myHideEffect',
         $hide,
         CClientScript::POS_READY
      );
    }
  }
}


function absoluteURL($url = ''){
  
  return Yii::app()->params['absoluteHost'];
  
  if (!YII_TESTING) return 'http://www.cofinder.eu'.$url;
  else return 'http://test.cofinder.eu'.$url;
  //$host = require(dirname(__FILE__) . '/../config/local-console-request.php');
  
  //echo $host;
  return  Yii::app()->request->hostInfo;
}


 /**
   * calculate time difference between two times
   *
   * @param $startTime mixed  - start time
   * @param $startTime mixed  - end time
   * @param $type string      - what to return (min, sec, hours,...)
   * @param $signed boolean   - is time difference sign dependant
   * @return integer          - return time difference
   */
  function timeDifference($startTime, $endTime, $type = "min", $signed = false){
    if ($startTime ==  $endTime) return 0;

    $d1 = (is_string($startTime) ? strtotime($startTime) : $startTime);
    $d2 = (is_string($endTime) ? strtotime($endTime) : $endTime);

    if ($signed) $diff_secs = (int)($d2 - $d1);
    else $diff_secs = abs((int)($d2 - $d1));
    $base_year = min(date("Y", $d1), date("Y", $d2));

    $diff = mktime(0, 0, abs($diff_secs), 1, 1, $base_year);

    switch ($type){
      case "years": $result = date("Y", $diff) - $base_year; break;
      case "months_total": $result = (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1; break;
      case "months": $result = date("n", $diff) - 1; break;
      case "days_total": $result = floor($diff_secs / (3600 * 24)); break;
      case "days": $result = date("j", $diff) - 1; break;
      case "hours_total":$result = floor($diff_secs / 3600); break;
      case "hours": $result = date("G", $diff); break;
      case "minutes_total":$result = floor($diff_secs / 60); break;
      case "minutes": $result = (int) date("i", $diff); break;
      case "seconds_total": $result = $diff_secs; break;
      case "seconds": $result = (int) date("s", $diff); break;
      }

    if ($d2 < $d1) $diff_secs = 24*60*60 - $diff_secs;
    if ($type == "min") $result = floor($diff_secs / 60);//(int) ($result / 60);
    if ($type == "hour") $result =  floor($diff_secs / 3600);//(int)($result / 60);

  //	echo $startTime."=".$d1."-".$endTime."=".$d2."=".$diff_secs.".".($diff_secs / 60)."<br>";
    return $result;
  }
  
  /**
   * prety date
   */
  function prettyDate($timeDiffInSec, $ago = false){
    
    if($timeDiffInSec < 60){
      $when = round($timeDiffInSec);
      if ($ago)  return Yii::t('app','{n} second ago|{n} seconds ago',array(round($when)));
      else return Yii::t('app','{n} second|{n} seconds',array(round($when)));
    }elseif($timeDiffInSec < 3600){
      $when = round($timeDiffInSec / 60);
      if ($ago)  return Yii::t('app','{n} minute ago|{n} minutes ago',array(round($when)));
      else return Yii::t('app','{n} minute|{n} minutes',array(round($when)));
    }elseif($timeDiffInSec >= 3600 && $timeDiffInSec < 86400){
      $when = round($timeDiffInSec / 60 / 60);
      if ($ago)  return Yii::t('app','{n} hour ago|{n} hours ago',array(round($when)));
      else return Yii::t('app','{n} hour|{n} hours',array(round($when)));
    }elseif($timeDiffInSec >= 86400 && $timeDiffInSec < 2629743.83){
      $when = round($timeDiffInSec / 60 / 60 / 24);
      if ($ago)  return Yii::t('app','{n} day ago|{n} days ago',array(round($when)));
      else return Yii::t('app','{n} day|{n} days',array(round($when)));
    }elseif($timeDiffInSec >= 2629743.83 && $timeDiffInSec < 31556926){
      $when = round($timeDiffInSec / 60 / 60 / 24 / 30.4375);
      if ($ago)  return Yii::t('app','{n} month ago|{n} months ago',array(round($when)));
      else return Yii::t('app','{n} month|{n} months',array(round($when)));
    }else{
      $when = round($timeDiffInSec / 60 / 60 / 24 / 365);
      if ($ago)  return Yii::t('app','{n} year ago|{n} years ago',array(round($when)));
      else return Yii::t('app','{n} year|{n} years',array(round($when)));
    }
  }
  
  /**
   * 1st, 2nd, 3rd, 4th... used for day in date
   */
  function addOrdinalNumberSuffix($num) {
    if (!in_array(($num % 100),array(11,12,13))){
      switch ($num % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $num.'st';
        case 2:  return $num.'nd';
        case 3:  return $num.'rd';
      }
    }
    return $num.'th';
  }

  
  /**
   * mail link click tracking
   */
  function mailLinkTracking($id,$link,$name){
    if ($id == '') return $link;
    return absoluteURL()."track/ml?tc=".$id."&l=".urlencode($link)."&ln=".$name;
  }

  /**
   * generate tracking code for mail
   */
  function mailTrackingCode($extra = '', $long = false){
    //Yii::import('application.helpers.Hashids');
    $hashids = new Hashids('crowdrss');
    if ($long) return $hashids->encrypt_hex(md5(microtime(true)));
    else return $hashids->encrypt(round(microtime(true)));
  }
  
  /**
   * decode tracking code
   */
  function mailTrackingCodeDecode($tc){
    //Yii::import('application.helpers.Hashids');
    $hashids = new Hashids('crowdrss');
    $tid = $hashids->decrypt($tc);
    if (is_array($tid) && isset($tid[0])) return $tid[0];
    else return $tid;
  }
/**
 * will return you to previously called action
 */
/*function goBackController($this){
  if (Yii::app()->getBaseUrl()."/index.php" === Yii::app()->user->returnUrl)
    $this->redirect(Yii::app()->controller->module->returnUrl);
  else 
    if (strpos(Yii::app()->request->urlReferrer,"user/login") === false) $this->redirect(Yii::app()->request->urlReferrer);
    else $this->redirect(Yii::app()->user->returnUrl);  
}*/


  function mailButton($name, $link, $type='', $tc = '', $tc_name = '') {
    if ($tc_name == '') $tc_name = $name;
    $html = '<a href="'.mailLinkTracking($tc,$link,$tc_name).'" ';

    if ($type == '') $type = 'background-color: #0088bb; color: white;';
    else if ($type == 'secondary') $type = 'background-color: #ee8822; border: 1px solid #d0d0d0; color: #333333;';
    else if ($type == 'alert') $type = 'background-color: #ee4422; color: white;';
    else if ($type == 'success') $type = ' background-color: #44aa66; color: white;';
    
    if($type != 'link'){
        $html .= 'style="border-radius:3px; -webkit-border-radius:3px; border-style: solid;  border-width: 1px;  cursor: pointer;  font-family: inherit;  font-weight: bold;
      line-height: 1;  margin: 0 0 1.25em;  position: relative; text-decoration: none;  text-align: center;  display: inline-block;
      padding-top: 0.5625em; padding-right: 1.125em; padding-bottom: 0.625em; padding-left: 1.125em; font-size: 0.9em;
      '. $type .'"';  

      } 

    $html.= '>'.$name.'</a>';
    return $html;
  }
  
  
  function beautifyLink($link){
    if (strpos($link, "indiegogo.com") !== false){
      $link = str_replace("/pinw", "", $link);
      $link = str_replace("/qljw", "", $link);
      $link = str_replace("/pimf", "", $link);
      $link = str_replace("?sa=0&sp=0", "", $link);
      $link = str_replace("?sa=0&amp;sp=0", "", $link);
    }
    if (strpos($link, "kickstarter.com") !== false){
      if (strpos($link,"?") !== false) $link = substr($link, 0, strpos($link,"?"));
    }
    
    return $link;
  }
  

  /**
   * will create a nice URL like  something-to-do-in-the-meanwhile
   * 
   * @param type $str
   * @param type $replace
   * @param type $delimiter
   * @return type
   */
function toAscii($str, $replace=array(), $delimiter='-') {
    if( !empty($replace) ) {
     $str = str_replace((array)$replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}

/**
 * get stars instead of rating
 * 
 * @param type $rating
 * @return string
 */
function getStars($rating, $long = false){
	return $stars = '';
	if (!empty($rating)){
		$stars = '★★★★★';
		if (!$long) $rating /= 2;
		switch(round($rating)){
			case 10: if ($long) $stars = '★★★★★★★★★★'; break;
			case 9:  if ($long) $stars = '★★★★★★★★★☆'; break;
			case 8:  if ($long) $stars = '★★★★★★★★☆☆'; break;
			case 7:  if ($long) $stars = '★★★★★★★☆☆☆'; break;
			case 6:  if ($long) $stars = '★★★★★★☆☆☆☆'; break;
			case 5: $stars = '★★★★★'; if ($long) $stars .= '☆☆☆☆☆';  break;
			case 4: $stars = '★★★★☆'; if ($long) $stars .= '☆☆☆☆☆'; break;
			case 3: $stars = '★★★☆☆'; if ($long) $stars .= '☆☆☆☆☆'; break;
			case 2: $stars = '★★☆☆☆'; if ($long) $stars .= '☆☆☆☆☆'; break;
			case 1: $stars = '★☆☆☆☆'; if ($long) $stars .= '☆☆☆☆☆'; break;
			case 0: $stars = '☆☆☆☆☆'; if ($long) $stars .= '☆☆☆☆☆'; break;
		}
	}
	return $stars;
}