<?php
header("Content-Type", "text/html");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once("../vendor/uagent.php");
$userAgent = random_uagent();
/*
 * @author CStress IP Stresser Booter
 * @url https://github.com/CStress/proxies
 */
$i = 0;
$arrProxy = [];
/*for ($i = 1; $i <= 5; $i++)
{*/
    $resp = curl_get("http://sslproxies.org", $userAgent);
    preg_match_all('/<tr><td>(?<ip>.+)<\/td><td>(?<port>.+)</Ui', $resp, $matches, PREG_PATTERN_ORDER);
    if (empty($matches))
    {
        die('{"status": false, "message": "proxies not found"}');
    }
    else
    {
        foreach ($matches['ip'] as $idx => $val)
        {
            $arrProxy[] = $matches['ip'][$idx] . ":" . $matches['port'][$idx];
        }
    }
    /*preg_match_all('/decode\("(?<encodedip>.+)"(.|\n)*fport.*>(?<port>.+)</U', $resp, $matches, PREG_PATTERN_ORDER);
    if (empty($matches))
    {
        //echo('{"status": false, "message": "page ' . $i . ' is empty"}');
    }
    else
    {
        foreach ($matches['encodedip'] as $idx => $val)
        {
            $arrProxy[] = base64_decode($matches['encodedip'][$idx]) . ":" . $matches['port'][$idx];
        }
    }*/
//}

$arrProxy = array_unique($arrProxy);  // Remove duplicates
echo implode("\n", $arrProxy);
/*
 * Functions
 */
function curl_get($url, $userAgent)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_MAXREDIRS       => 5,
        CURLOPT_TIMEOUT         => 5,
        CURLOPT_URL             => $url,
        CURLOPT_USERAGENT       => $userAgent,
        CURLOPT_REFERER         => "https://www.google.com/",
        CURLOPT_HEADER          => false,
        CURLINFO_HEADER_OUT     => false,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_SSL_VERIFYHOST  => 0,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_ENCODING        => "",
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_HTTPHEADER      => array(
            "Host: www.sslproxies.org",
            "User-Agent: " . $userAgent,
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Cookie: __cfduid=d1305164d178332957dff0d355c2605b11496084790; _ga=GA1.2.1064853286.1496084759; _gid=GA1.2.1215310245.1496084759; __atuvc=1%7C22; __atuvs=592c711639915bf6000",
            "DNT: 1",
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 0",
            "Pragma: no-cache",
            "Cache-Control: no-cache"
        )
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>