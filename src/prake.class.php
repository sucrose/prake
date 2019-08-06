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

require __DIR__ . '/vendor/autoload.php';

class Prake
{
    private $arr_proxies = [];
    private $debug_mode = false;
    private $module_dir = '';

    /**
     * initiation
     */
    function __construct()
    {
        $a = func_get_args();
        $l = func_num_args();
        if (method_exists($this, $f = '__construct' . $l)) {
            call_user_func_array(array($this, $f), $a);
        }
    }

    /**
     * initiation -> debug mode
     *
     * @param   dbg     boolean for debug mode
     * @return  string  debug mode state
     */
    function __construct1($dbg)
    {
        $this->debug_mode = $dbg;
        return ($this->debug_mode) ? '<pre>[ + ] prake: initiated | debug mode</pre>' : '';
    }

    /**
     * termination
     *
     * @return  string  debug mode state
     */
    function __destruct()
    {
        return ($this->debug_mode) ? '<pre>[ + ] prake: terminated</pre>' : true;
    }

    /**
     * @return boolean
     */
    public function debug_status()
    {
        return ($this->debug_mode) ? true : false;
    }

    /**
     * @param string $dir [ex: $prake->set_module_dir(dirname(__FILE__) . '\modules\\')]
     */
    public function set_module_dir($dir)
    {
        $this->module_dir = $dir;
    }

    /**
     * @return string
     */
    public function get_module_dir()
    {
        return $this->module_dir;
    }

    /**
     * @return integer
     */
    public function get_proxy_count()
    {
        return count($this->arr_proxies);
    }

    /**
     * @return array
     */
    public function rake()
    {
        spl_autoload_register(function ($class_name) {
            $filepath = $this->get_module_dir() . $class_name . '.php';
            if (file_exists($filepath)) {
                require $filepath;
                return false;
            }
        });
        $user_agent = \Campo\UserAgent::random();
        $dir = new \DirectoryIterator($this->get_module_dir());
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $basename = $fileinfo->getBasename('.php');
                $basenameClass = new $basename();
                $arr = $basenameClass->get_proxies($user_agent);
                $this->arr_proxies[$basename] = $arr;
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
    public function export_list($format, $arr_proxies)
    {
        $list_output = '';
        switch ($format) {
            case 'json':
            case 'csv':
                //
                break;
            case 'txt':
            default:
                $list_output = implode('\n', $arr_proxies);
        }
        return $list_output;
    }
}
