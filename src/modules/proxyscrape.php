<?php
    class proxyscrape {
        function __construct() {
        }

        function __destruct() {
        }

        private function curl_get($user_agent) {
            $api_key = 'JNAC4-TI0LJ-IAOQM-DYTOU';
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "https://api.proxyscrape.com/?request=displayproxies&proxytype=all&timeout=500&anonymity=all&ssl=yes&serialkey=$api_key",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
                CURLOPT_COOKIE => 'SPSI=3791bf99d0403ad9e38a2a1e74a22ede',
            ));
            $resp = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                echo "cURL Error #: $err";
            }
            return $resp;
        }

        public function get_proxies($user_agent) {
            $arr = [];
            $resp = $this->curl_get($user_agent);
            if (0 < strlen($resp)) {
                $arr = array_filter(explode(PHP_EOL, $resp));
            }
            return array_unique($arr);
        }
    }
