<?php
    class proxyhttp {
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
            $resp[] = $this->curl_get('https://proxyhttp.net/free-list/proxy-anonymous-hide-ip-address/', $user_agent);
            $resp[] = $this->curl_get('https://proxyhttp.net/free-list/proxy-high-anonymous-hide-ip-address/', $user_agent);
            $resp[] = $this->curl_get('https://proxyhttp.net/free-list/proxy-https-security-anonymous-proxy/', $user_agent);
            for ($i = 1; $i < 9; $i++) { // # of pages to rake [max: 9]
                $resp[] = $this->curl_get("https://proxyhttp.net/free-list/anonymous-server-hide-ip-address/$page#proxylist", $user_agent);
            }
            /*for ($page = 1; $page <= $pages; $page++) {
                $resp = $this->curl_get("https://proxyhttp.net/free-list/anonymous-server-hide-ip-address/$page#proxylist", $user_agent);
                $re = '/chash = \'(?<chash>.*)\'/mU';
                if (preg_match($re, $resp, $matches)) {
                    $chash = $matches['chash'];
                    preg_match_all('/title="(?<ip>.+)"(.|\n)*data-port="(?<encodedport>.+)"/Ui', $resp, $matches, PREG_PATTERN_ORDER);
                    if (0 < count($matches)) {
                        foreach ($matches['ip'] as $idx => $val) {
                            $arr[] = $matches['ip'][$idx] . ':' . $this->decode_port($matches['encodedport'][$idx], $chash);
                        }
                    } else {
                        return [];
                    }
                } else {
                    return [];
                }
            }*/
            //return array_unique($arr);  // Remove duplicates
        }

        private function decode_port($encoded_port, $chash)
        {
            for ($n = [], $i = 0, $r = 0; $i < strlen($encoded_port) - 1; $i += 2, $r++) {
                $n[$r] = intval(substr($encoded_port, $i, 2), 16);
            }
            for ($a = [], $i = 0; $i < strlen($chash); $i++) {
                $a[$i] = $this->charCodeAt($chash, $i);
            }
            for ($i = 0; $i < count($n); $i++) {
                $n[$i] = $n[$i] ^ $a[$i % count($a)];
            }
            for ($i = 0; $i < count($n); $i++) {
                $n[$i] = chr($n[$i]);
            }
            return $n = implode('', $n);
        }

        private function charCodeAt($str, $num) {
            return $this->utf8_ord($this->utf8_charAt($str, $num));
        }

        private function utf8_ord($ch) {
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

        private function utf8_charAt($str, $num) {
            return mb_substr($str, $num, 1, 'UTF-8');
        }
    }
