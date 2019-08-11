<?php
    class proxz {
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
            $resp = [];
            for ($i = 0; $i < 24; $i++) {
                $resp[] = $this->curl_get("http://www.proxz.com/proxy_list_high_anonymous_$i.html", $user_agent);
            }
            $resp_merged = implode(',', $resp);
            $re = '/unescape\(\'(?<encrypted>.+)\'/mU';
            $re_enc = '/<td>(?<ip>.*)<\/td><td>(?<port>.*)<\/td><td/mU';
            if (preg_match_all($re, $resp_merged, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $idx => $val) {
                    $unencrypted = urldecode($matches[$idx]['encrypted']);
                    if (preg_match_all($re_enc, $unencrypted, $matches2, PREG_SET_ORDER, 0)) {
                        foreach ($matches2 as $idx2 => $val2) {
                            $arr[] = $matches2[$idx2]['ip'] . ':' . $matches2[$idx2]['port'];
                        }
                    }
                }
            }
            return array_unique($arr);
        }
    }
