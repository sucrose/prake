<?php
header("Content-Type", "text/html");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once("../vendor/uagent.php");
$userAgent = random_uagent();
/*
 * @author Jon Bales
 */
$p = 0;
$arrProxy = [];
for ($p = 1; $p <= 5; $p++)
{
    $resp = curl_get("http://proxyservers.pro/proxy/list/protocol/https/country/US/order/updated/order_dir/desc/page/" . $p, $userAgent);
    die($resp);
    preg_match('/var chash = \'(?<chash>.+)\'/i', $resp, $matches);
    if (empty($matches))
    {
        die('{"status": false, "message": "hash key not found"}');
    }
    $chash = $matches["chash"];
    preg_match_all('/title="(?<ip>.+)"(.|\n)*data-port="(?<encodedport>.+)"/Ui', $resp, $matches, PREG_PATTERN_ORDER);
    if (empty($matches))
    {
        die('{"status": false, "message": "proxies not found"}');
    }
    else
    {
        foreach ($matches['ip'] as $idx => $val)
        {
            $arrProxy[] = $matches['ip'][$idx] . ":" . decode($matches['encodedport'][$idx], $chash);
        }
    }
}
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
            "Host: proxyservers.pro",
            "User-Agent: " . $userAgent,
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Cookie: PHPSESSID=jcn6hp4q5re0ji0ojojsmr0cim; __atuvc=2%7C22; __atuvs=592c3e784d3c9e87001; _ga=GA1.2.1878260710.1496071803; _gid=GA1.2.184832122.1496072887",
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
function charCodeAt($str, $num) {
    return utf8_ord(utf8_charAt($str, $num));
}
function utf8_ord($ch) {
    $len = strlen($ch);
    if ($len <= 0) {
        return false;
    }
    $h = ord($ch{0});
    if ($h <= 0x7F) {
        return $h;
    }
    if ($h < 0xC2) {
        return false;
    }
    if ($h <= 0xDF && $len>1) {
        return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
    }
    if ($h <= 0xEF && $len>2) {
        return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);
    }
    if ($h <= 0xF4 && $len>3) {
        return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
    }
    return false;
}
function utf8_charAt($str, $num) {
    return mb_substr($str, $num, 1, 'UTF-8');
}
function decode($encoded_port, $chash)
{
    for ($n = [], $i = 0, $r = 0; $i < strlen($encoded_port) - 1; $i += 2, $r++)
    {
        $n[$r] = intval(substr($encoded_port, $i, 2), 16);
    }

    for ($a = [], $i = 0; $i < strlen($chash); $i++)
    {
        $a[$i] = charCodeAt($chash, $i);
    }

    for ($i = 0; $i < count($n); $i++)
    {
        $n[$i] = $n[$i] ^ $a[$i % count($a)];
    }

    for ($i = 0; $i < count($n); $i++)
    {
        $n[$i] = chr($n[$i]);
    }

    return $n = implode("", $n);
}