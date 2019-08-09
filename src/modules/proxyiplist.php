<?php
    class proxyiplist {
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
            $sources = [
                'http://proxy-ip-list.com/download/proxy-list-port-3128.txt',
                'http://proxy-ip-list.com/download/free-usa-proxy-ip.txt',
                'http://proxy-ip-list.com/download/free-uk-proxy-list.txt'
            ];
            $arr = [];
            $re = '/\s{2,}(?<ip>.*):(?<port>.+);/mU';
            foreach ($sources as &$src) {
                $resp = $this->curl_get($src, $user_agent);
                preg_match_all($re, $resp, $matches, PREG_SET_ORDER, 0);
                if (!empty($matches)) {
                    foreach ($matches as $idx => $val) {
                        $arr[] = $matches[$idx]['ip'] . ':' . $matches[$idx]['port'];
                    }
                }
            }
            return array_unique($arr);
        }
    }
