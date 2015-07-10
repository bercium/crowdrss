<?php

class TestController extends Controller {
    
    public function actionIndex(){
        echo '<form method="post">';
        echo 'St. projektou: <input type="text" name="num_proj" value="10">';
        echo 'Proxy: <input type="text" name="proxy_ip" value="">';
        //echo 'Proxy: <input type="text" name="proxy_ip" value="">';
        echo '<input type="submit" value="Submit">';
        echo '</form></br></br></br>';
        if(isset($_POST["proxy_ip"])) { 
            $proxy_ip = $_POST["proxy_ip"];
            $num_proj = $_POST["num_proj"];
            $link = "https://www.indiegogo.com/private_api/explore?experiment=true&filter_funding=&filter_percent_funded=&filter_quick=new&filter_status=&locale=en&per_page=$num_proj";
            $httpClient = new elHttpClient();
            $httpClient->enableRedirects();
            $httpClient->setProxy($proxy_ip, 80);
            $httpClient->setUserAgent("ff3");
            $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
            $htmlData = $httpClient->get($link, array());
            $htmlDataSplit = explode('{"campaigns":', $htmlData);
            $htmlData = '{"campaigns":'.$htmlDataSplit[1];
            $json = html_entity_decode($htmlData);
            $jsonData = json_decode($json);
            var_dump($jsonData); die;
            if ($jsonData == null){ return false; }
            if (count($jsonData->campaigns)>10) {
                for ($j=0; $j<=count($jsonData->campaigns)-1; $j++) {
                    $link = "https://www.indiegogo.com".$jsonData->campaigns[$j]->url;
                    $image = $jsonData->campaigns[$j]->compressed_image_url;
                    $title = $jsonData->campaigns[$j]->title;
            $httpClient = new elHttpClient();
            $httpClient->enableRedirects();
            $httpClient->setProxy($proxy_ip, 80);
            $httpClient->setUserAgent("ff3");
            $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
            $htmlData = $httpClient->get($link, array());
            $httpClient = new elHttpClient();
            $httpClient->enableRedirects();
            $httpClient->setProxy($proxy_ip, 80);
            $httpClient->setUserAgent("ff3");
            $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
            $htmlData .= $httpClient->get($link . "/show_tab/home", array("X-Requested-With" => "XMLHttpRequest"));
            var_dump($htmlData); die;
                }
            }
        }
    }
    
    
  public function actionTest($proxy_ip) {
    //$proxy_ip = "111.1.36.166";
    $link = "https://www.indiegogo.com/private_api/explore?experiment=true&filter_funding=&filter_percent_funded=&filter_quick=new&filter_status=&locale=en&per_page=12";
    $httpClient = new elHttpClient();
    $httpClient->enableRedirects();
    $httpClient->setProxy($proxy_ip, 80);
    $httpClient->setUserAgent("ff3");
    $httpClient->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"));
    var_dump($httpClient->get($link, array()));
 }
}

