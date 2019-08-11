<?php
/**
 *
 *  name:       Prake
 *  desc:       PHP class to rake (scrape) public proxy lists for updated proxies
 *  author:     Sucrose
 *  website:    https://github.com/sucrose/prake
 *  email:      sucrose@pm.me
 *
 */

namespace sucrose;

use Campo\UserAgent;

require __DIR__ . '/vendor/autoload.php';

class Prake {
    private $arr_proxies = [];
    private $debug_mode = false;
    private $module_dir = '';
    private $whitelisted_mods = [];

    /**
     * initiation
     */
    function __construct() {
        $a = func_get_args();
        $l = func_num_args();
        if (method_exists($this, $f = '__construct' . $l)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * init -> debug mode
     *
     * @param   dbg     boolean for debug mode
     */
    function __construct1($dbg) {
        $this->debug_mode = $dbg;
        echo $this->debug_output('<pre>[ + ] prake: init | debug mode enabled</pre>');
        return;
    }

    /**
     * init -> debug mode, whitelisted modules
     *
     * @param   dbg     boolean for debug mode
     * @param   mods    array for whitelisted mods
     */
    function __construct2($dbg, $mods) {
        $this->debug_mode = $dbg;
        $this->whitelisted_mods = $mods;
        $tmp = '';
        $tmp .= ($this->debug_mode) ? '<pre>[ + ] prake: init | debug mode enabled</pre>' : '';
        $tmp .= ($this->debug_mode && !empty($this->whitelisted_mods)) ? '<pre>[ + ] prake: init | whitelisted modules: [' . implode(', ', $this->whitelisted_mods) . ']</pre>' : '';
        if (0 < strlen($tmp)) {
            echo $this->debug_output($tmp);
        }
        return;
    }

    /**
     * termination
     *
     * @return  string  debug mode state
     */
    function __destruct() {
        echo $this->debug_output('<pre>[ + ] prake: terminated</pre>');
        return true;
    }

    /**
     * @return boolean
     */
    public function debug_status() {
        return ($this->debug_mode) ? true : false;
    }

    /**
     * @param string $dir [ex: $prake->set_module_dir(dirname(__FILE__) . '\modules\\')]
     */
    public function set_module_dir($dir) {
        $this->module_dir = $dir;
    }

    /**
     * @return string
     */
    public function get_module_dir() {
        return $this->module_dir;
    }

    /**
     * @return integer
     */
    public function get_proxy_count() {
        return count($this->arr_proxies);
    }

    /**
     * @param  str      debug output string
     * @return string
     */
    private function debug_output($str) {
        return ($this->debug_mode) ? $str : '';
    }

    /**
     * @return string
     */
    private function get_useragent() {
        $ua = UserAgent::random();
        echo $this->debug_output("<pre>[ + ] prake: rake() -> get_useragent() | user-agent: $ua</pre>");
        return $ua;
    }

    /**
     * @return array
     */
    public function rake() {
        spl_autoload_register(function ($class_name) {
            $filepath = $this->get_module_dir() . "$class_name.php";
            if (file_exists($filepath)) {
                require $filepath;
                echo $this->debug_output("<pre>[ + ] prake: rake() | module loaded: $class_name</pre>");
                return false;
            }
            return true;
        });
        $ua = $this->get_useragent();
        $dir = new \DirectoryIterator($this->get_module_dir());
        foreach ($dir as $file_info) {
            if (!$file_info->isDot()) {
                $basename = $file_info->getBasename('.php');
                if (empty($this->whitelisted_mods)) {
                    $basenameClass = new $basename();
                    $arr = $basenameClass->get_proxies($ua);
                    echo $this->debug_output("<pre>[ + ] prake: rake() -> get_proxies() | $basename: " . count($arr) . '</pre>');
                    $this->arr_proxies[$basename] = $arr;
                } else {
                    if (isset($this->whitelisted_mods[$basename])) {
                        $basenameClass = new $basename();
                        $arr = $basenameClass->get_proxies($ua);
                        echo $this->debug_output("<pre>[ + ] prake: rake() -> get_proxies() | $basename: " . count($arr) . '</pre>');
                        $this->arr_proxies[$basename] = $arr;
                    } else {
                        echo $this->debug_output("<pre>[ + ] prake: rake() | module ignored: $basename</pre>");
                    }
                }
            }
        }
        return $this->arr_proxies;
    }

    /**
     * exports the proxy list into the desired format
     *
     * @param   string format       3-char abbreviation for the specified output format
     * @param   array arr_proxies   proxy list array
     * @return  string              formatted output content
     */
    public function export_list($format, $arr_proxies) {
        $list_output = '';
        switch ($format) {
            case 'json':
            case 'csv':
                //
                break;
            case 'txt':
            default:
                $list_output = implode('<br>', $arr_proxies);
        }
        return $list_output;
    }
}
