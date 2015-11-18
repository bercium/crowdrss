<?php

class webText {
    function getHtml($link, $header = array(), $proxy = false, $post = "") {
        $httpClient = new elHttpClient();
        $httpClient->setUserAgent("ff3");
        if ($proxy == true) {
            $proxy_ip = array("101.226.249.237", "117.102.122.218", "119.188.94.145", "120.202.249.230", "122.55.96.83", "148.251.234.73", "162.223.88.243", "175.103.47.130", "177.184.8.123", "180.166.56.47", "182.163.56.88", "183.238.133.43", "190.102.17.240", "190.181.18.232", "190.221.23.158", "197.218.204.202", "198.2.202.55", "198.2.202.58", "198.99.224.134", "200.150.97.27", "219.141.225.149", "31.220.43.28", "50.63.137.198", "58.214.5.229", "63.221.140.143", "80.91.88.36", "83.172.144.19", "83.222.126.179", "89.218.38.202", "91.121.204.88", "94.247.25.163", "94.247.25.164");
            $httpClient->setProxy($proxy_ip[mt_rand(0,count($proxy_ip)-1)], 80);
        }
        $httpClient->enableRedirects();
        $httpClient->setHeaders(array_merge(array("Accept"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")));
        if ($post == "") { $htmlDataObject = $httpClient->get($link, $header);}
        else { $htmlDataObject = $httpClient->post($link, $post, $header); }
        return $htmlDataObject->httpBody;
    }
}

