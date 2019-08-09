<?php
    class socks24 {
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

        private function curl_download($url, $zip_rec) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_FAILONERROR => true,
                CURLOPT_HEADER => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_FILE => $zip_rec,
            ));
            $resp = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                echo "cURL Error #: $err";
            }
            return $resp;
        }

        private function get_GUID() {
            if (function_exists('com_create_guid')) {
                return trim(com_create_guid(), '{}');
            }
            return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }

        public function get_proxies($user_agent) {
            $arr = [];
            // Get latest blog post *only* TODO: add logic to grab all posts for current date
            $re = '/entry-title.*>\s<a href=\'(?<addr>.*)\'/mU';
            $resp = $this->curl_get('http://www.socks24.org/', $user_agent);
            if (preg_match($re, $resp, $matches, PREG_OFFSET_CAPTURE, 0)) {
                // Get blog post content (either a download link or raw text)
                $url = trim($matches['addr'][0]);
                $resp = $this->curl_get($url, $user_agent);
                $re_dl = '/<br><a href="(?<addr>.*)"/mU';
                $re_ip = '/(?<ip>\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(?<port>\d{2,5})\s/m';
                if (preg_match($re_dl, $resp, $matches, PREG_OFFSET_CAPTURE, 0) ||
                    preg_match_all($re_ip, $resp, $matches, PREG_SET_ORDER, 0)) {
                    if (0 < count($matches)) {
                        if (array_key_exists('addr', $matches)) {
                            $url = trim($matches['addr'][0]);
                            // Proxy list zip archive
                            $resp = $this->curl_get($url, $user_agent);
                            $re = '/block"\s+href="(?<addr>.*)"/mU';
                            if (preg_match($re, $resp, $matches, PREG_OFFSET_CAPTURE, 0)) {
                                $url = trim($matches['addr'][0]);
                                $guid = $this->get_GUID();
                                $zip_file = 'tmp' . DIRECTORY_SEPARATOR . "archive-$guid.zip";
                                $zip_rec = fopen($zip_file, 'w') or die('ERROR: Failed to create zip archive');
                                $resp = $this->curl_download($url, $zip_rec);
                                if ($resp) {
                                    // Extract zip archive
                                    $zip = new ZipArchive;
                                    $extract_path = 'tmp' . DIRECTORY_SEPARATOR . 'files';
                                    if ($zip->open($zip_file)) {
                                        $zip->extractTo($extract_path);
                                        foreach (glob($extract_path . DIRECTORY_SEPARATOR . '*.txt') as $file_name) {
                                            if (0 < filesize($file_name)) {
                                                $txt_file = fopen($file_name,'r');
                                                while ($line = fgets($txt_file)) {
                                                    $arr[] = trim($line);
                                                }
                                                fclose($txt_file);
                                            }
                                        }
                                    } else {
                                        echo 'ERROR: Failed to open the zip archive';
                                    }
                                    $zip->close();
                                    $extracted_files = glob($extract_path . DIRECTORY_SEPARATOR . '*');
                                    foreach($extracted_files as $file) {
                                        if (is_file($file)) {
                                            unlink($file);
                                        }
                                    }
                                } else {
                                    echo 'ERROR: Failed to download zip archive';
                                    return $arr;
                                }
                            } else {
                                echo 'ERROR: No zip archive download link';
                                return $arr;
                            }
                        } else {
                            // Proxy list raw text
                            foreach ($matches as $idx => $val) {
                                $arr[] = $matches[$idx]['ip'] . ':' . $matches[$idx]['port'];
                            }
                        }
                    } else {
                        // Shouldn't ever happen
                        echo 'ERROR: No blog post content matches...?';
                        return $arr;
                    }
                } else {
                    echo 'ERROR: No blog post content found';
                    return $arr;
                }
            } else {
                echo 'ERROR: No blog post found';
                return $arr;
            }
            return array_unique($arr);
        }
    }
