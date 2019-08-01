<?php
header("Content-Type", "text/html");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
/*
 * @author Jon Bales
 */
$resp = file_get_contents("https://hidemy.name/api/proxylist.txt?maxtime=4000&type=s&out=plain&code=804607904097065&country=USCA");
echo $resp;
?>