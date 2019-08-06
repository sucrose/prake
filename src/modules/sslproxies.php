<?php
    class sslproxies {
        function __construct() {
        }

        function __destruct() {
        }

        private function curl_get($url, $user_agent) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HEADER => false,
                CURLINFO_HEADER_OUT => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
                CURLOPT_USERAGENT => $user_agent,
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
            $resp = $this->curl_get('https://sslproxies.org/', $user_agent);
            $re = '/<tr><td>(?<ip>.+)<\/td><td>(?<port>.+)<\/td>/mU';
            preg_match_all($re, $resp, $matches, PREG_SET_ORDER, 0);
            if (0 < count($matches)) {
                foreach ($matches as $idx => $val) {
                    if (100 > $idx) {
                        $arr[] = $matches[$idx]['ip'] . ':' . $matches[$idx]['port'];
                    }
                }
                return array_unique($arr);
            } else {
                return [];
            }
        }
    }
