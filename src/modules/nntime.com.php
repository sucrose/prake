<?php
header("Content-Type", "text/html");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
/*
 * @author Weidi Zhang
 * @license GPL v3
 * @url https://github.com/weidizhang/nntime-proxy-scraper
 */
$pages = 30; // # of pages to scrape (max 30)
$arrProxy = [];
$ch = curl_init();
for ($page = 1; $page <= $pages; $page++)
{
    $url = "http://nntime.com/proxy-list-" . sprintf("%02d", $page) . ".htm";
    curl_setopt_array($ch, array(
        CURLOPT_URL             => $url,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_TIMEOUT         => 15
    ));
    $data = curl_exec($ch);
    if (!empty($data))
    {
        $getVars = get_between($data, '</script><script type="text/javascript">', '</script>');
        $getVars = trim(substr($getVars, 0, -1));
        $getVars = explode(";", $getVars);
        $variables = array();
        foreach ($getVars as $var) {
            $var = explode("=", $var);
            $variables[$var[0]] = $var[1];
        }
        preg_match_all('/onclick="choice\(\)" \/><\/td>(.*?)<\/script><\/td>/si', $data, $getProxies);
        foreach ($getProxies[1] as $proxyRaw) {
            $proxyIP = get_between($proxyRaw, "<td>", "<script type");
            $proxyPort = str_replace("+", "", get_between($proxyRaw, 'document.write(":"+' , ")"));
            $proxyPort = strtr($proxyPort, $variables);

            $arrProxy[] = $proxyIP . ":" . $proxyPort;
        }
    }
}
curl_close($ch);
$arrProxy = array_unique($arrProxy);  // Remove duplicates
echo implode("\n", $arrProxy);
/*
 * Functions
 */
function get_between($content, $start, $end)
{
    $r = explode($start, $content);
    if (isset($r[1])) {
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return "";
}
?>