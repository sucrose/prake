<?php
    class hidemyname {
        function __construct() {
        }

        function __destruct() {
        }

        private function curl_get($user_agent) {
            $api_key = '';
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "https://hidemy.name/api/proxylist.txt?maxtime=500&type=s&out=plain&code=$api_key",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
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
