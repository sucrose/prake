<?php
    class freeproxy {
        function __construct() {
        }

        function __destruct() {
        }

        private function curl_get($url, $user_agent) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_MAXREDIRS       => 5,
                CURLOPT_TIMEOUT         => 5,
                CURLOPT_URL             => $url,
                CURLOPT_USERAGENT       => $user_agent,
                CURLOPT_REFERER         => 'https://www.google.com/',
                CURLOPT_HEADER          => false,
                CURLINFO_HEADER_OUT     => false,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_SSL_VERIFYHOST  => 0,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_ENCODING        => '',
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_HTTPHEADER      => array(
                    'Host: free-proxy.cz',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Cookie: fp=0abc49b3e4c97e04b07525dce77ee2e5;',
                    'DNT: 1',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 0',
                    'Pragma: no-cache',
                    'Cache-Control: no-cache'
                )
            ));
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

        public function get_proxies($user_agent) {
            $arr = [];
            //for ($i = 1; $i <= 5; $i++) {
            for ($i = 1; $i <= 1; $i++) {
                $resp = $this->curl_get("http://free-proxy.cz/en/proxylist/country/US/https/speed/all/$i", $user_agent);
                preg_match_all('/decode\("(?<encodedip>.+)"(.|\n)*fport.*>(?<port>.+)</U', $resp, $matches, PREG_PATTERN_ORDER);
                if (!empty($matches)) {
                    foreach ($matches['encodedip'] as $idx => $val) {
                        $arr[] = base64_decode($matches['encodedip'][$idx]) . ':' . $matches['port'][$idx];
                    }
                }
            }
            return array_unique($arr);
        }
    }
