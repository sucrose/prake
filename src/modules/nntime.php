<?php
    class nntime {
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
            $pages = 1; // # of pages to rake [max: 70]
            for ($page = 1; $page <= $pages; $page++) {
                $resp = $this->curl_get('http://nntime.com/proxy-list-' . sprintf('%02d', $page) . '.htm', $user_agent);
                if (0 < strlen($resp)) {
                    // extract encryption variable legend
                    $re = '/script><script type="text\/javascript">[\S\s]*(?<variables>.*)</mU';
                    if (preg_match($re, $resp, $match, PREG_OFFSET_CAPTURE, 0)) {
                        $keys = explode(';', $match[1][0]);
                        if (2 > count($keys)) {
                            return [];
                        }
                        array_pop($keys);
                        foreach ($keys as $k) {
                            list($key, $val) = explode('=', $k);
                            $key = trim($key);
                            $val = trim($val);
                            if ($key == '' || $val == '') {
                                return [];
                            }
                            $values[$key] = $val;
                        }
                    } else {
                        return [];
                    }
                    // extract ip:encrypted(port) -- decrypt port with variable legend
                    $re = '/<\/td><td>([^>]+?)<script type="text\/javascript">document\.write\("\:"\+([\+a-zA-Z]+?)\)</mU';
                    preg_match_all($re, $resp, $matches);
                    if (0 < count($matches)) {
                        $decrypted_matches = [];
                        if (0 < count($matches[1]) && 0 < count($matches[2])) {
                            for ($i = 0; $i < count($matches[1]); $i++) {
                                $ip = $matches[1][$i];
                                $port = $matches[2][$i];
                                foreach ($values as $k => $v) {
                                    $port = str_replace($k, $v, $port);
                                }
                                // trim the concatenations
                                $port = str_replace('+', '', $port);
                                $decrypted_matches[] = "$ip:$port";
                            }
                        }
                        return array_unique($decrypted_matches);
                    }
                    return [];
                }
            }
            return $arr;
        }
    }
