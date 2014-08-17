<?
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

class shareCount {

  private $url, $timeout;

  function __construct($url, $timeout=10) {
    $this->url = rawurlencode($url);
    $this->timeout = $timeout;
  }

// Twitter
  function get_tweets() {
    $json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
    $json = json_decode($json_string, true);
    return isset($json['count']) ? intval($json['count']) : 0;
  }

// LinkedIn
  function get_linkedin() {
    $json_string = $this->file_get_contents_curl("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
    $json = json_decode($json_string, true);
    return isset($json['count']) ? intval($json['count']) : 0;
  }

// Facebook
  function get_fb() {
    //"share_count":333619,"like_count":459419,"comment_count":259104,"total_count":1052142,"click_count":2236
    $json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $this->url);
    $json = json_decode($json_string, true);
    // maybee calculate independantly - without comment count
    $sc = isset($json[0]['share_count']) ? intval($json[0]['share_count']) : 0;
    $lc = isset($json[0]['like_count']) ? intval($json[0]['like_count']) : 0;
    $tc = isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0; //total count
    return $tc;
  }

// Google Plus
  function get_plusones() {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($this->url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($curl_results, true);
    return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
  }

// StumbleUpon
  function get_stumble() {
    $json_string = $this->file_get_contents_curl('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $this->url);
    $json = json_decode($json_string, true);
    return isset($json['result']['views']) ? intval($json['result']['views']) : 0;
  }

// Delicious
  function get_delicious() {
    $json_string = $this->file_get_contents_curl('http://feeds.delicious.com/v2/json/urlinfo/data?url=' . $this->url);
    $json = json_decode($json_string, true);
    return isset($json[0]['total_posts']) ? intval($json[0]['total_posts']) : 0;
  }

// Pinterest
  function get_pinterest() {
    $return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url=' . $this->url);
    $json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
    $json = json_decode($json_string, true);
    return isset($json['count']) ? intval($json['count']) : 0;
  }

// Digg
  function get_digg() {
//    $return_data = $this->file_get_contents_curl('http://widgets.digg.com/buttons/count?url=' . $this->url);
    return 0;
  }

// Reddit
  function get_reddit() {
    $jason_string = $this->file_get_contents_curl('' . $this->url);
    $json = json_decode($json_string, true);
    $score = isset($json['data']['children'][0]['data']['score']) ? intval($json['data']['children'][0]['data']['score']) : 0;
    $comments = isset($json['data']['children'][0]['data']['num_comments']) ? intval($json['data']['children'][0]['data']['num_comments']) : 0;
    return $score; 
  }
  
  private function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    $cont = curl_exec($ch);
    if (curl_error($ch)) {
      die(curl_error($ch)." ".$url);
    }
    return $cont;
  }

}
